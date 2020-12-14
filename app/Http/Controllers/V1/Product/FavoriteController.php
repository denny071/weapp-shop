<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\V1\Controller;
use App\Models\Product;
use App\Transformers\ProductTransformer;


class FavoriteController extends Controller
{
    protected $pageSize = 5;

    /**
     * 商品收藏列表
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $products = $this->user()->favoriteProducts()->paginate($this->pageSize);

        return $this->response->paginator($products, ProductTransformer::class);
    }




    /**
     * 收藏商品
     *
     * @param int $productId
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(int $productId)
    {
        $product = Product::find($productId);

        $user = $this->user();

        if ($user->favoriteProducts()->find($productId)) {
            return $this->response->created();
        }
        $user->favoriteProducts()->attach($product);

        return $this->response->created();
    }

    /**
     * 取消收藏
     *
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function destroy(int $productId)
    {
        $product = Product::find($productId);

        $this->user()->favoriteProducts()->detach($product);

        return $this->response->noContent();;
    }



}
