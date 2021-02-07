<?php

namespace App\Http\Controllers\API\V1\Notify;

use App\Events\OrderPaid;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\V1\Controller;


/**
 * WechatController 微信
 */
class WechatController extends Controller
{
   /*
    * 微信支付异步通知
    *
    * @return string
    */
   public function wechatPayNotify()
   {
       Log::info('微信通知');
       // 校验回调参数是否正确
       $data = app('wechat_pay')->verify();
       // 找到对应的订单
       $order = Order::where('no', $data->out_trade_no)->first();
       // 订单不存在则告知微信支付
       if (!$order) {
           return 'fail';
       }
       // 订单已支付
       if ($order->paid_at) {
           // 告知微信支付此订单已处理
           return app('wechat_pay')->success();
       }
       // 订单标记为已支付
       $order->update([
           'paid_at' => Carbon::now(),
           'payment_method' => 'wechat',
           'payment_no' => $data->transaction_id,
       ]);

       Log::info("支付成功:".json_encode($_REQUEST));

       event(new OrderPaid($order));

       return app('wechat_pay')->success();
   }


   /**
    * 微信退款通知
    *
    * @return string
    */
   public function wechatRefundNotify()
   {
       // 给微信的失败响应
       $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
       $data = app('wechat_pay')->verify(null, true);

       // 没有找到对应的订单，原则上不可能发生，保证代码健壮性
       if (!$order = Order::where('no', $data['out_trade_no'])->first()) {
           return $failXml;
       }

       if ($data['refund_status'] === 'SUCCESS') {
           // 退款成功，将订单退款状态改成退款成功
           $order->update([
               'refund_status' => Order::REFUND_STATUS_SUCCESS,
           ]);
       } else {
           // 退款失败，将具体状态存入 extra 字段，并表退款状态改成失败
           $extra = $order->extra;
           $extra['refund_failed_code'] = $data['refund_status'];
           $order->update([
               'refund_status' => Order::REFUND_STATUS_FAILED,
           ]);
       }

       return app('wechat_pay')->success();
   }

}
