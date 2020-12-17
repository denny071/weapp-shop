<?php

namespace App\Http\Controllers\V1\User;


use App\Models\Image;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Http\Controllers\V1\Controller;
/**
 * UserController 用户控制器
 */
class UserController extends Controller
{

    /**
     * 当前用户信息
     *
     * @return \Dingo\Api\Http\Response
     */
    public function get()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    /**
     * 更新用户信息
     *
     * @param UserRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function update()
    {
        $request = $this->checkRequest();

        $attributes = $request->only(['name', 'email', 'introduction']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }
        $this->user()->update($attributes);

        return $this->response->item($this->user(), new UserTransformer());
    }

    /**
     * 绑定手机号
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function bindMobile()
    {
        $request = $this->checkRequest();
        // 缓存中是否存在对应的 key
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {

            $this->errorInternal("109001",$verifyData);

        }
        // 判断验证码是否想相等，不相等返回 401 错误
        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            $this->errorInternal("109002");
        }
        $mobile = $request->mobile;
        $user = $this->user();
        if (User::where("mobile",$mobile)->where("id","<>",$user->id)->count() == 0) {
            $user->mobile = $mobile;
            $user->save();
            return $this->response->created();
        } else {
            $this->errorInternal("109003");
        }
    }



}
