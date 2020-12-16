<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

/**
 * 购物车排名
 *
 * Class CartService
 * @package App\Services
 */
class CartService
{
    /**
     * 获得客户
     *
     * @param string $user
     * @return mixed
     */
    public function get($user)
    {
        return $user->cartItems()->with(['productSku.product'])->get();
    }

    /**
     * 添加购物车
     *
     * @param int $skuId  商品skuId
     * @param string $amount  金额
     * @param string $user 用户
     * @return CartItem
     */
    public function add($skuId, $amount,$user)
    {
        // 从数据库中查询该产品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id',$skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->update([
                'amount' => $item->amount + $amount
            ]);
        } else {
            // 否则创建一个新的购车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }
        return $item;
    }

    /**
     * 更新购物车
     *
     * @param int $skuId  商品skuId
     * @param string $amount  金额
     * @param string $user 用户
     * @return mixed
     */
    public function update($skuId, $amount,$user,$checked = 1)
    {
        // 从数据库中查询该产品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id',$skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->update([
                'amount' =>  $amount,
                'checked' => $checked
            ]);
        }
        return $item;
    }
    /**
     * 移除购物车
     *
     * @param int $skuId 商品skuId
     * @param string $user 用户
     * @return mixed
     */
    public function remove($skuIds,$user)
    {
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        $user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();
    }
}
