<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

/**
 * 购物车控制器
 *
 * Class CartController
 * @package App\Http\Controllers\Web
 */
class CartController extends Controller
{

    protected $cartService;

    // 利用 laravel 的自动解析功能注入 CartService 类
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * 购物车列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at','desc')->get();

        return view('cart.index',['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    /**
     * 添加购物车
     *
     * @param AddCartRequest $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'),$request->input('amount'));

        return [];
    }

    /**
     * 移除购物车商品
     *
     * @param ProductSku $sku
     * @param Request $request
     * @return array
     */
    public function remove(ProductSku $sku, Request $request)
    {
        $this->cartService->remove($sku->id);

        return [];
    }
}
