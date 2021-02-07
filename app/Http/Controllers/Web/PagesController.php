<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

/**
 * 单页控制器
 *
 * Class PagesController
 * @package App\Http\Controllers\Web
 */
class PagesController extends Controller
{
    /**
     * 文件首页
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function root()
    {
        return redirect(route('products.index'));
    }

    /**
     * 邮箱验证
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function emailVerifyNotice(Request $request)
    {
        return view('web.pages.email_verify_notice');
    }
}
