<?php

namespace App\Transformers;

use App\Models\Advert;
use League\Fractal\TransformerAbstract;

class AdvertsTransformer extends TransformerAbstract
{

    public function transform(Advert $advert)
    {
        return [
            'picture_url' => $advert->picture_url,
            'link_url' => $advert->link_url
        ];
    }

}
