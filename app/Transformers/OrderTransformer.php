<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{

    public function transform(Order $order)
    {
        return [
            'no' => $order->no,
            'type' => $order->type,
            "address" =>  $order->address,
            "total_amount" => $order->total_amount,
            "remark" =>  $order->remark,
            "paid_at" => is_null($order->paid_at)?"":$order->paid_at->toDateTimeString(),
            "pay_deadline" => $order->pay_deadline,
            "coupon_code_id" => $order->coupon_code_id,
            "payment_method" => $order->payment_method,
            "payment_no" => $order->payment_no,
            "refund_status" => $order->refund_status,
            "refund_no" => $order->refund_no,
            "closed" => $order->closed,
            "reviewed" => $order->reviewed,
            "ship_status" => $order->ship_status,
            "ship_data" => $order->ship_data,
            "express" => $order->express,
            "freight" => $order->freight,
            "product_amount" => $order->product_amount,
            "extra" => $order->extra,
            "status" => $order->status,
            "items" => $order->items,
            "created_at" => $order->created_at->toDateTimeString(),
            "updated_at" => $order->updated_at->toDateTimeString(),
        ];
    }




}
