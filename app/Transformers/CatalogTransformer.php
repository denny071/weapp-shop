<?php

namespace App\Transformers;

use App\Models\Catalog;
use League\Fractal\TransformerAbstract;

class CatalogTransformer extends TransformerAbstract
{
    public function transform(Catalog $catalog)
    {
        return [
            'id' => $catalog->id,
            'name' => $catalog->name,
            'cover_image' => $catalog->cover_image,
        ];
    }
}
