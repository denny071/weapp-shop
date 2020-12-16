<?php

namespace App\Http\Controllers\V1\Cart;

use App\Http\Controllers\V1\Controller;
use App\Services\CartService;
use App\Transformers\CartTransformer;

/**
 * 购物车
 *
 * CartController
 */
class CartController extends Controller
{

    /**
     * @var CartService 购物车服务
     */
    protected $cartService;

    /**
     * 利用 laravel 的自动解析功能注入 CartService 类
     *
     * CartController constructor.
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    /**
     * 购物车列表
     *
     * @param Request $request
     * @return View
     */
    public function index()
    {
        return $this->response->collection(
            $this->user()->cartItems()->get(),
            CartTransformer::class
        );
    }

    /**
     * 添加购物车
     *
     * @param AddCartRequest $request
     * @return array
     */
    public function add()
    {
        $request = $this->checkRequest();

        $this->cartService->add($request->sku_id, $request->amount, $this->user());

        return $this->response->created();
    }


    /**
     * update 购物车更新
     *
     * @param  mixed $skuId
     * @return void
     */
    public function update(int $skuId)
    {
        $request = $this->checkRequest();

        $this->cartService->update($skuId, $request->amount, $this->user(), $request->input("checked"));

        return $this->response->created();
    }


    /**
     * 移除购车商品
     *
     * @param int $skuId
     * @return \Dingo\Api\Http\Response
     */
    public function delete(int $skuId)
    {

        // $this->checkRequest();

        $this->cartService->remove($skuId, $this->user());

        return $this->response->noContent();
    }
}
