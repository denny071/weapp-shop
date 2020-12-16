<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\ExpressCompany;
use Illuminate\Http\Request;

use App\Models\CrowdfundingProduct;
use App\Models\Order;
use Carbon\Carbon;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\JsonResponse;

/**
 * OrderController 订单
 */
class OrderController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Order(), function (Grid $grid) {
            // 只展示已支付的订单，并且默认按支付时间倒序排序
            $grid->model()->whereNotNull('paid_at')->orderBy('paid_at','desc');
            $grid->column('no');
            // 展示关联关系的字段时，使用 column 方法
            $grid->column('user.name');
            $grid->column('total_amount')->sortable();
            $grid->column('paid_at')->display(function ($value) {
                return Carbon::parse($value)->format("Y-m-d H:i:s");
            })->sortable();
            $grid->column('ship_status')->display(function ($value) {
                return Order::$shipStatusMap[$value];
            });
            $grid->column('refund_status')->display(function ($value) {
                return Order::$refundStatusMap[$value];
            });
            // 禁用创建按钮，后台不需要创建订单
            $grid->disableCreateButton();
            $grid->actions(function ($actions){
                // 禁用删除和编辑按钮
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
       return Show::make($id,Order::with(['user']),function (Show $show) {

           $show->html(function ()  use ($show){
               return view("order.show",[
                   "order" => $this,
                   "expressCompanyList" => ExpressCompany::where("status",1)->get()
               ]);
           });
       });
    }

    /**
     * 订单发货
     *
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ship(Order $order, Request $request)
    {
        // 判断当前订单是否已支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }
        // 判断当前订单发货状态是否为未发货
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING){
            throw new InvalidRequestException('该订单已发货');
        }

        // 众筹订单只有在众筹成功之后发货
        if ($order->type === Order::TYPE_CROWDFUNDING &&
            $order->crowdfunding_status === CrowdfundingProduct::STATUS_SUCCESS) {
            throw new InvalidRequestException('众筹订单只能在众筹成功之后发货');
        }

        $data = validator($request->all(),[
            'express_company' => ['required'],
            'express_no' => ['required'],
        ],[],[
            'express_company' => '物流公司',
            'express_no' => '物流单号'
        ]);

        $shipData = [
            'express_company' => $request->input("express_company"),
            'express_no' => $request->input("express_no")
        ];
        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是个数组
            // 因此这里可以直接把数组传过去
            'ship_data' => $shipData
        ]);
        // 返回上一页

        return JsonResponse::make()->success('成功！')->send();;
    }

    /**
     * 退款处理
     *
     * @param Order $order
     * @param HandleRefundRequest $request
     * @param OrderService $orderService
     * @return Order
     * @throws InternalException
     * @throws InvalidRequestException
     */
    public function handleRefund(Order $order, HandleRefundRequest $request, OrderService $orderService)
    {
        // 判断订单状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // 是否同意退款
        if ($request->input('agree')) {
            // 同意退款的逻辑
            $orderService->refundOrder($order);
        } else {
            // 将拒绝退款理由放到订单的 extra 字段中
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            // 将订单的退款状态改为未退款
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra'         => $extra,
            ]);
        }

        return $order;
    }


    /**
     * 微信退款通知
     *
     * @return string
     */
    public function wechatRefundNotify()
    {
        // 给微信的失败响应
        $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></return_code></xml>';
        $data = app('wechat_pay')->verify(null,true);

        // 没有找到对应的订单，原则上不可能发生，保证代码健壮性
        if (!$order = Order::where('no',$data['out_trade_no'])->first()) {
            return $failXml;
        }

        if ($data['refund_status'] === 'SUCCESS'){
            // 退款成功，将订单退款状态改成退款成功
            $order->update([
                'refund_status' => Order::REFUND_STATUS_SUCCESS,
            ]);
        } else {
            // 退款失败，将集体状态存入 extra 字段，并表退款桩体改为失败
            $extra = $order->extra;
            $extra['refund_failed_code'] = $data['refund_status'];
            $order->update([
                'refund_status' => Order::REFUND_STATUS_FAILED,
            ]);
        }
        return app('wechawt_pay')->success();
    }

}
