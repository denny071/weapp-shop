<?php

namespace App\Http\Controllers\API\V1\Common;

use Illuminate\Support\Str;
use App\Http\Controllers\API\V1\Controller;
use Carbon\Carbon;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class ImageCodeController extends Controller
{

    /**
     * 验证图形验证码
     *
     * @param CaptchaBuilder $captchaBuilder
     * @return mixed
     */
    public function index()
    {
        // 图形验证码key
        $key = 'captcha-'.Str::random(15);

        $phraseBuilder = new PhraseBuilder(5, '0123456789');

        $captchaBuilder  = new CaptchaBuilder(null,$phraseBuilder);

        $captchaBuilder->setBackgroundColor(255,255,255);
        // 验证码类
        $captcha = $captchaBuilder->build();
        // 验证码过期时间 秒
        $expiredAt =  Carbon::now()->addMinutes(env("CAPTCHA_EXPIRED_MINUTES",2));
        // 添加到缓存
        \Cache::put($key, ['code' => $captcha->getPhrase()], $expiredAt);
        // 输出结果
        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];
        // 响应信息
        return $this->response->array($result)->setStatusCode(201);
    }
}
