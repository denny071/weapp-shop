<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\OrderItem;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    public function transform(OrderItem $orderItem)
    {
        return [
            'id' => $orderItem->id,
            'rating' => $orderItem->rating,
            'review' => $orderItem->review,
            'name' => $orderItem->order->user->name,
            'avatar' => $orderItem->order->user->avatar?:$orderItem->order->user->wx_avatar,
            'reviewed_at' => $orderItem->reviewed_at->toDateTimeString(),
        ];
    }
}
