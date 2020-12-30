<?php

/*
 * This file is part of the overtrue/laravel-wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',

        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => true,

        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file' => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
        ],
    ],

    /*
     * 小程序
     */
    'mini_program' => [
        'default' => [
            'app_id' => env('WECHAT_MINI_PROGRAM_APPID', ''),
            'secret' => env('WECHAT_MINI_PROGRAM_SECRET', ''),
            'token' => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
            'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
        ],
    ],

    /*
     * 微信支付
     */
    'payment' => [
        'default' => [
            'sandbox' => env('WECHAT_PAYMENT_SANDBOX', false),
            'appid' => env('WECHAT_PAYMENT_APPID', ''),
            'app_id' => env('WECHAT_PAYMENT_APPID', ''),
            'miniapp_id' => env('WECHAT_PAYMENT_APPID', ''),
            'mch_id' => env('WECHAT_PAYMENT_MCH_ID', 'your-mch-id'),
            'key' => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
            'cert_client' => env('WECHAT_PAYMENT_CERT_PATH', ''),
            'cert_key'    =>  env('WECHAT_PAYMENT_KEY_PATH', ''),
            'log' => [
                'file' => storage_path('logs/wechat_pay.log'),
            ],
        ],
        // ...
    ],

];
