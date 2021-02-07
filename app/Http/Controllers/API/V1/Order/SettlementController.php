<?php

namespace App\Http\Controllers\API\V1\Order;

use App\Http\Controllers\API\V1\Controller;
use App\Models\CartItem;
use App\Models\ExpressFreight;
use App\Models\UserAddress;

/**
 * SettlementController 结算
 */
class SettlementController extends Controller
{

    /**
     * Settlement 结算
     *
     * @return void
     */
    public function store()
    {
        $request = $this->checkRequest();

        $cartItem = CartItem::where("user_id",$this->user()->id)->with("productSku")
            ->whereIn("product_sku_id",explode(",",$request->product_sku_ids))
            ->get();

        if ($cartItem) {
            $output = [];
            $productPrice = 0;
            foreach ($cartItem as $item) {
                $product['product_name'] =  $item->productSku->title;
                $product['product_sku_name'] =  $item->productSku->product->title;
                $product['price'] =  $item->productSku->price;
                $product['amount'] =  $item->amount;
                $product['image'] =  $item->productSku->product->image;
                $output['product_list'][] = $product;
                $productPrice +=  $item->amount * $item->productSku->price;
            }
            if ($request->address_id == 0) {
                $addressFilter = ['is_default' => 1, 'user_id' => $this->user()->id];
            } else {
                $addressFilter = ['id' => $request->address_id];
            }
            $address = UserAddress::where($addressFilter)->first();
            if ($address) {
                $output["default_address"] = $address;
                $expressFreight = ExpressFreight::where(["express_id" => $request->express_id,
                "province" => $address->province])->first();
                $output["express_freight"] = $expressFreight->freight;
            } else {
                $output["default_address"] = "";
                $output["express_freight"] = 0;
            }

            $output["product_price"] = $productPrice;
            $output["total_price"] = $productPrice + $output["express_freight"];

            return $this->response->array($output);
        }
        return $this->response->errorBadRequest("There are no items in the cart");

    }

}
