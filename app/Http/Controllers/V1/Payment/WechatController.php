<?php

namespace App\Http\Controllers\V1\Payment;

use App\Events\OrderPaid;
use App\Http\Controllers\V1\Controller;
use App\Http\Controllers\V1\Order\OrderController;
use App\Models\Order;
use Carbon\Carbon;

class WechatController extends Controller
{

    /**
     * 微信支付
     *
     * @param string $orderNo 订单编号
     */
    public function payment(string $orderNo)
    {
        $request = $this->checkRequest();

        $order =  (new OrderController())->checkOrder($orderNo);

        // 校验订单状态
        if ($order->paid_at || $order->closed) {
            $this->errorInternal("140003");
        }
        // scan 方法为来气微信扫码支付
        $wechatOrder = app('wechat_pay')->miniapp([
            'out_trade_no' => $order->no,
            'total_fee' => $order->total_amount * 100,
            'body' => '支付'.env("APP_NAME").'的订单：' . $order->no,
            'openid' => $request->openid,
            'notify_url' => env("WECHAT_PAYMENT_NOTIFY_URL","")
        ]);
        return $this->response->array($wechatOrder);

    }


    /**
     * 查询订单信息
     *
     * @param $orderNo
     * @return \Dingo\Api\Http\Response|void
     */
    public function get($orderNo)
    {
        $this->checkRequest();

        $order =  (new OrderController())->checkOrder($orderNo);

        $data = app('wechat_pay')->find($orderNo);

        $updateData['payment_method'] = "wechat";
        $updateData['payment_no'] = $data->transaction_id;
        // 支付成功
        if($data->trade_state == "SUCCESS"){
            $updateData['paid_at'] =Carbon::now();
            $order->update($updateData);
            event(new OrderPaid($order));
            return $this->response->created();
        } else {
            $order->update($updateData);
            return $this->response->array([
                "trade_state" => $data->trade_state
            ]);
        }
    }
}
