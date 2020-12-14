<?php

namespace App\Transformers;

use App\Models\ProductSku;
use League\Fractal\TransformerAbstract;

class ProductSkuTransformer extends TransformerAbstract
{

    public function transform(ProductSku $productSku)
    {
        return [
            'sku_id' => $productSku->id,
            'product_id' => $productSku->product_id,
            "title" =>  $productSku->title,
            "description" => $productSku->description,
            "price" =>  $productSku->price,
            "stock" => $productSku->stock,
        ];
    }


}
