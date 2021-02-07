<?php

namespace App\Http\Controllers\API\V1\Marketing;

use App\Http\Controllers\API\V1\Controller;
use App\Models\Advert;
use App\Transformers\AdvertsTransformer;

/**
 * AdController 广告
 */
class AdController extends Controller
{
    /**
     * 小程序广告
     *
     * @return mixed
     */
    public function index()
    {

        $adverts = Advert::all();

        return $this->response->collection($adverts, AdvertsTransformer::class);
    }

}
