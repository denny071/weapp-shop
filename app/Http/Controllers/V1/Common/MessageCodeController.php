<?php

namespace App\Http\Controllers\V1\Common;

use App\Http\Controllers\V1\Controller;
use Carbon\Carbon;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Str;

/**
 * MessageCodeController 短信验证码
 */
class MessageCodeController extends Controller
{

    /**
     * store 生成手机号验证码
     *
     * @param  mixed $easySms
     * @return void
     */
    public function messageCode(EasySms $easySms)
    {

        $request = $this->checkRequest();

        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {
            throw $this->errorInternal("139001");

        }
        if (!hash_equals(Str::lower($captchaData['code']), Str::lower($request->captcha_code))) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            throw $this->errorInternal("139002");
        }
        // 获得手机号
        $phone = $request->phone;
        if (!app()->environment('production')) {
            // 当系统不再线上状态时,验证码为123456,且不发送验证码
            $code = '123456';
        } else {
            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $easySms->send($phone, ['template' => 'SMS_177253141', 'data' => ['code' => $code]]);
            } catch (\GuzzleHttp\Exception\ClientException $exception) {
                $response = $exception->getExceptions();
                $result = json_decode($response->getBody()->getContents(), true);
                throw $this->errorInternal("139003",$result['msg']);
            }
        }
        // 验证码key
        $key = 'verificationCode_'.Str::random(15);
        // 验证码过期时间
        $expiredAt = Carbon::now()->addMinutes(10);
        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        // 清除图片验证码缓存
        \Cache::forget($request->captcha_key);
        // 响应信息
        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }



}
