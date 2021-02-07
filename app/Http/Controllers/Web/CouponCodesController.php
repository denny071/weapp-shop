<?php

namespace App\Http\Controllers\Web;

use App\Models\CouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * 优惠卷控制器
 *
 * Class CouponCodesController
 * @package App\Http\Controllers\Web
 */
class CouponCodesController extends Controller
{
    /**
     * 展示优惠券
     *
     * @param $code
     * @param Request $request
     * @return mixed
     */
    public function show($code,Request $request)
    {
        // 判断优惠券是否存在
        if (!$record = CouponCode::where('code',$code)->first()) {
            abort(404);
        }

        // 如果优惠券没有启用，则等同于优惠券不存在
        $record->checkAvailable($request->user());

        return $record;
    }

}
