<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{

    public function transform(Order $order)
    {
        return [
            // 订单编号
            'no' => $order->no,
            // 订单类型
            'type' => $order->type,
            // 订单地址
            "address" =>  $order->address,
             // 物流公司
            "express_company" => $order->express_company,
            // 物流价格
            "express_freight" => $order->express_freight,
            // 商品价格
            "product_amount" => $order->product_amount,
            // 订单总金额
            "total_amount" => $order->total_amount,
            // 优惠券ID
            "coupon_id" => $order->coupon_id,
            // 订单备注
            "remark" =>  $order->remark,
            // 支付时间
            "paid_at" => is_null($order->paid_at)?"":$order->paid_at->toDateTimeString(),
            // 支付截止时间
            "pay_deadline" => $order->pay_deadline,

            // 支付方式
            "payment_method" => $order->payment_method,
            // 支付单号
            "payment_no" => $order->payment_no,
            // 退款状态
            "refund_status" => $order->refund_status,
            // 退款单号
            "refund_no" => $order->refund_no,
            // 订单是否关闭
            "is_closed" => $order->is_closed,
            // 是否评论
            "is_reviewed" => $order->is_reviewed,
            // 物流状态
            "ship_status" => $order->ship_status,
            // 物流数据
            "ship_data" => $order->ship_data,

            // 扩展信息
            "extra" => $order->extra,
            // 订单状态
            "status" => $order->status,
            // 商品列表
            "items" => $order->items,
            // 创建时间
            "created_at" => $order->created_at->toDateTimeString(),
            // 更新时间
            "updated_at" => $order->updated_at->toDateTimeString(),
        ];
    }




}
