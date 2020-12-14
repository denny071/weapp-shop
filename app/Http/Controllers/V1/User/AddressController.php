<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\V1\Controller;
use App\Models\UserAddress;
use App\Transformers\UserAddressesTransformer;

/**
 * AddressController 地址控制器
 */
class AddressController extends Controller
{

     /**
     * 地址列表
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->collection($this->user()->addresses,UserAddressesTransformer::class);
    }

    /**
     * 获得用户地址
     *
     * @param int $userAddressId
     * @return \Dingo\Api\Http\Response|void
     */
    public function get(int $userAddressId)
    {
        $userAddress = $this->checkAddress($userAddressId);

        return $this->response->item($userAddress,UserAddressesTransformer::class);
    }


    /**
     * 添加用户地址
     *
     * @param UserAddressRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function store()
    {
        $request = $this->checkRequest();

        $userId = $this->user()->id;

        if ($request->is_default == 1) {
            UserAddress::where("user_id",$userId)->update(["is_default" => 0 ]);
        }

        UserAddress::create([
            "user_id" => $userId,
            "province" => $request->province,
            "province_code" => $request->province_code,
            "city" => $request->city,
            "city_code" => $request->city_code,
            "district" => $request->district,
            "district_code" => $request->district_code,
            "address" => $request->address,
            "is_default" => $request->is_default,
            "contact_name" => $request->contact_name,
            "contact_phone" => $request->contact_phone,
        ]);

        return $this->response->created();
    }

    /**
     * 更新用户地址
     *
     * @param int $userAddressId
     * @param UserAddressRequest $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function update(int $userAddressId )
    {
        $request = $this->checkRequest();

        $userId = $this->user()->id;

        if ($request->is_default == 1) {
            UserAddress::where("user_id",$userId)->update(["is_default" => 0 ]);
        }
        $userAddress = $this->checkAddress($userAddressId);

        $userAddress->update([
            "user_id" => $userId,
            "province" => $request->province,
            "province_code" => $request->province_code,
            "city" => $request->city,
            "city_code" => $request->city_code,
            "district" => $request->district,
            "district_code" => $request->district_code,
            "address" => $request->address,
            "is_default" => $request->is_default,
            "contact_name" => $request->contact_name,
            "contact_phone" => $request->contact_phone,
        ]);

        return $this->response->item($userAddress, UserAddressesTransformer::class);
    }

    /**
     * 删除用户地址
     *
     * @param int $userAddressId
     * @return \Dingo\Api\Http\Response|void
     */
    public function destroy(int $userAddressId)
    {
        $userAddress = $this->checkAddress($userAddressId);

        $userAddress->delete();

        return $this->response->noContent();
    }


    /**
     * checkAddress 检查地址
     *
     * @param  mixed $userAddressId
     * @return void
     */
    public function checkAddress(int $userAddressId)
    {
        $userAddress = UserAddress::find($userAddressId);
        if (!$userAddress) {
            $this->errorInternal("109004");
        }

        if ($userAddress->user_id != $this->user()->id) {
            $this->errorInternal("109005");
        }

        return $userAddress;
    }
}
