<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\ProductProperty;
use League\Fractal\TransformerAbstract;

class ProductPropertyTransformer extends TransformerAbstract
{
    public function transform(ProductProperty $property)
    {
        return [
            'name' => $property->name,
            'value' => $property->value,
        ];
    }
}
