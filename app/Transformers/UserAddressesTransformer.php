<?php

namespace App\Transformers;

use App\Models\UserAddress;
use League\Fractal\TransformerAbstract;

class UserAddressesTransformer extends TransformerAbstract
{

    public function transform(UserAddress $userAddress)
    {
        return [
            'id' => $userAddress->id,
            'province' => $userAddress->province,
            'province_code' => $userAddress->province_code,
            'city' => $userAddress->city,
            'city_code' => $userAddress->city_code,
            'district' => $userAddress->district,
            'district_code' => $userAddress->district_code,
            'address' => $userAddress->address,
            'is_default' => $userAddress->is_default,
            'contact_name' => $userAddress->contact_name,
            'contact_phone' => $userAddress->contact_phone,
            'last_used_at' => $userAddress->last_used_at,
            'created_at' => $userAddress->created_at->toDateTimeString(),
            'updated_at' => $userAddress->updated_at->toDateTimeString(),
        ];
    }

}
