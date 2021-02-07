<?php

namespace App\Http\Controllers\Web;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Installment;
use App\Models\InstallmentItem;
use App\Models\Order;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;

/**
 * 分期付款控制器
 *
 * Class InstallmentsController
 * @package App\Http\Controllers\Web
 */
class InstallmentsController extends Controller
{
    /**
     * 分期列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $installments = Installment::query()
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return view('installments.index', ['installments' => $installments]);
    }

    /**
     * 显示分期详细
     *
     * @param Installment $installment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Installment $installment)
    {
        // 取出当前分期付款的所有的还款计划，并按还款顺序排序
        $items = $installment->items()->orderBy('sequence')->get();
        return view('installments.show', [
            'installment' => $installment,
            'items'       => $items,
            // 下一个未完成还款的还款计划
            'nextItem'    => $items->where('paid_at', null)->first(),
        ]);
    }

    /**
     * 支付宝支付
     *
     * @param Installment $installment
     * @return mixed
     * @throws InvalidRequestException
     */
    public function payByAlipay(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        }
        if ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该分期订单已结清');
        }
        // 获取当前分期付款最近的一个未支付的还款计划
        if (!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()) {
            // 如果没有未支付的还款，原则上不可能，因为如果分期已结清则在上一个判断就退出了
            throw new InvalidRequestException('该分期订单已结清');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            // 支付订单号使用分期流水号+还款计划编号
            'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject'      => '支付 Laravel Shop 的分期订单：'.$installment->no,
            // 这里的 notify_url 和 return_url 可以覆盖掉在 AppServiceProvider 设置的回调地址
            'notify_url'   => ngrok_url('installments.alipay.notify'),
            'return_url'   => route('installments.alipay.return'),
        ]);
    }


    /**
     * 支付宝前端回调
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alipayReturn()
    {
        // 开发中处理支付不成功问题
        if ($this->paid(request()->out_trade_no, 'alipay', request()->trade_no)) {
            return view('web.pages.success', ['msg' => '付款成功']);;
        }

        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('web.pages.error', ['msg' => '数据不正确']);
        }

        return view('web.pages.success', ['msg' => '付款成功']);
    }


    /**
     * 支付宝异步通知，改为调用 paid 方法，
     *
     * @return string
     */
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        if ($this->paid($data->out_trade_no, 'alipay', $data->trade_no)) {
            return app('alipay')->success();
        }

        return 'fail';
    }


    /**
     * 微信支付
     *
     * @param Installment $installment
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws InvalidRequestException
     */
    public function payByWechat(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        }
        if ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该分期订单已结清');
        }
        if (!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()) {
            throw new InvalidRequestException('该分期订单已结清');
        }

        $wechatOrder = app('wechat_pay')->scan([
            'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_fee'    => $nextItem->total * 100,
            'body'         => '支付 Laravel Shop 的分期订单：'.$installment->no,
            'notify_url'   => ngrok_url('installments.wechat.notify'),
        ]);
        // 把要转换的字符串作为 QrCode 的构造函数参数
        $qrCode = new QrCode($wechatOrder->code_url);

        // 将生成的二维码图片数据以字符串形式输出，并带上相应的响应类型
        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }


    /**
     * 微信异步通知
     *
     * @return string
     */
    public function wechatNotify()
    {
        $data = app('wechat_pay')->verify();
        if ($this->paid($data->out_trade_no, 'wechat', $data->transaction_id)) {
            return app('wechat_pay')->success();
        }

        return 'fail';
    }

    /**
     * 支付完成后处理
     *
     * @param $outTradeNo
     * @param $paymentMethod
     * @param $paymentNo
     * @return bool
     */
    protected function paid($outTradeNo, $paymentMethod, $paymentNo)
    {
        list($no, $sequence) = explode('_', $outTradeNo);
        if (!$installment = Installment::where('no', $no)->first()) {
            return false;
        }
        if (!$item = $installment->items()->where('sequence', $sequence)->first()) {
            return false;
        }
        if ($item->paid_at) {
            return true;
        }

        $item->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => $paymentMethod,
            'payment_no'     => $paymentNo,
        ]);

        if ($item->sequence === 0) {
            $installment->update(['status' => Installment::STATUS_REPAYING]);
            $installment->order->update([
                'paid_at'        => Carbon::now(),
                'payment_method' => 'installment',
                'payment_no'     => $no,
            ]);
            event(new OrderPaid($installment->order));
        }
        if ($item->sequence === $installment->count - 1) {
            $installment->update(['status' => Installment::STATUS_FINISHED]);
        }

        return true;
    }

    /**
     * 微信退款通知
     *
     * @param Request $request
     * @return string
     */
    public function wechatRefundNotify(Request $request)
    {
        // 给微信的失败响应
        $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
        // 校验微信回调参数
        $data = app('wechat_pay')->verify(null, true);
        // 根据单号拆解出对应的商品退款单号及对应的还款计划序号
        list($no, $sequence) = explode('_', $data['out_refund_no']);

        $item = InstallmentItem::query()
            ->whereHas('installment', function ($query) use ($no) {
                $query->whereHas('order', function ($query) use ($no) {
                    $query->where('refund_no', $no); // 根据订单表的退款流水号找到对应还款计划
                });
            })
            ->where('sequence', $sequence)
            ->first();

        // 没有找到对应的订单，原则上不可能发生，保证代码健壮性
        if (!$item) {
            return $failXml;
        }

        // 如果退款成功
        if ($data['refund_status'] === 'SUCCESS') {
            // 将还款计划退款状态改成退款成功
            $item->update([
                'refund_status' => InstallmentItem::REFUND_STATUS_SUCCESS,
            ]);
            $item->installment->refreshRefundStatus();
        } else {
            // 否则将对应还款计划的退款状态改为退款失败
            $item->update([
                'refund_status' => InstallmentItem::REFUND_STATUS_FAILED,
            ]);
        }

        return app('wechat_pay')->success();
    }
}
