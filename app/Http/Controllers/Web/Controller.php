<?php

namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * 控制器基类
 *
 * Class Controller
 * @package App\Http\Controllers\Web
 */
class Controller extends BaseController
{
    /**
     * 请求权限验证
     *
     * 分发任务
     *
     * 验证请求类
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
