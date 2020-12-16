<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\V1\Controller;
use App\Models\Catalog;
use App\Transformers\CatalogTransformer;

class CatalogController extends Controller
{
     /**
     * 控制器列表
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $data = Catalog::where("level",0)->get();

        return $this->response->collection($data, new CatalogTransformer());
    }

    /**
     * 获得子类
     *
     * @param int $parentId 父类ID
     * @return \Dingo\Api\Http\Response
     */
    public function get($catalogId)
    {
        $data = Catalog::where("parent_id",$catalogId)->where("level",1)->get();

        return $this->response->collection($data, new CatalogTransformer());
    }

}
