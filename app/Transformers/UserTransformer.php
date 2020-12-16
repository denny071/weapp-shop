<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?: $user->wx_avatar,
            'introduction' => $user->introduction,
            'phone' => $user->phone,
//            'last_actived_at' => $user->last_actived_at->toDateTimeString(),
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }

}
