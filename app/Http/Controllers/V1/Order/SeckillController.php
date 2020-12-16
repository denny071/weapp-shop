<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\V1\Controller;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\OrderService;

/**
 * SeckillController 秒杀订单
 */
class SeckillController extends Controller
{

    /**
     * 创建秒杀订单
     *
     * @param SeckillOrderRequest $request
     * @param OrderService $orderService
     * @return mixed
     */
    public function store(OrderService $orderService)
    {
        $request = $this->checkRequest();

        $sku = ProductSku::find($request->sku_id);
        $address = UserAddress::find($request->address_id);

        return $orderService->seckill(request()->user, $address, $sku);
    }



}
