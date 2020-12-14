<?php

namespace App\Transformers;

use App\Models\CartItem;
use App\Models\ProductSku;
use League\Fractal\TransformerAbstract;

class CartTransformer extends TransformerAbstract
{

    public function transform(CartItem $cartItem)
    {
        return [
            'id'                    => $cartItem->id,
            "product_sku_id"        => $cartItem->product_sku_id,
            "amount"                => $cartItem->amount,
            "product_sku_price"     => $cartItem->productSku->price,
            "total_amount"          => $cartItem->amount * $cartItem->productSku->price,
            "product_cover_image"   => $cartItem->productSku->product->cover_image,
            "product_cover_image"   => $cartItem->productSku->product->cover_image,
            "checked"               => $cartItem->checked,
            "created_at"            => $cartItem->created_at,
            "updated_at"            => $cartItem->updated_at,
        ];
    }
}
