<?php

namespace App\Http\Controllers\V1\Authorization;

use App\Exceptions\InternalException;
use App\Http\Controllers\V1\Controller;
use App\Models\User;

/**
 * WechatController 微信认证
 */
class WechatController extends Controller
{

    /**
     * weapp 小程序
     *
     * @return void
     */
    public function weapp(){
        $request = $this->checkRequest();

        $miniProgram = \EasyWeChat\Factory::miniProgram(config("wechat.mini_program.default"));

        $data = $miniProgram->auth->session($request->code);

        if (isset($data['errcode'])) {
            return $this->errorInternal("110001");
        }
        // 创建和更新
        User::updateOrCreate(
            [
                "weapp_openid" => $data['openid']
            ],
            [
                "weixin_session_key" => $data['session_key'],
                "name" => $request->name,
                "avatar" => $request->avatar,
                "weixin_avatar" => $request->avatar,
                "gender" => $request->gender,
                "country" => $request->country,
                "province" => $request->province,
                "city" => $request->city,
                "language" => $request->language,
            ]
        );

        return $this->array([
            'openid' => $data['openid'],
            'session_key' => $data['session_key'],
        ])->setStatusCode(201);
    }
}
