<?php

namespace App\Http\Controllers\API\V1\Order;

use App\Http\Controllers\API\V1\Controller;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\OrderService;

/**
 * CrowdfundingController 众筹订单
 */
class CrowdfundingController extends Controller
{

     /**
     * 创建众筹订单
     *
     * @param CrowdFundingOrderRequest $request 订单请求
     * @param OrderService $orderService 订单服务
     * @return mixed
     */
    public function crowdfunding( OrderService $orderService)
    {
        $request = $this->checkRequest();

        $sku = ProductSku::find($request->sku_id);
        $address = UserAddress::find($request->address_id);
        $amount = $request->amount;

        return $orderService->crowdfunding($this->user(), $address, $sku, $amount);
    }


}
