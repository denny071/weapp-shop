<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::post('seckill_orders', 'OrdersController@seckill')->name('seckill_orders.store')->middleware('random_drop:10');

// 首页
Route::get('/', 'PagesController@root')->name('root');

// 产品列表
Route::get('products', 'ProductsController@index')->name('products.index');
// 产品详情
Route::get('products/{product}', 'ProductsController@show')->name('products.show');
// 支付宝支付通知
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');
// 微信支付通知
Route::post('payment/wechat/notify', 'PaymentController@wechatNotify')->name('payment.wechat.notify');
// 微信退款通知
Route::post('payment/wechat/refund_notify', 'PaymentController@wechatRefundNotify')->name('payment.wechat.refund_notify');
// 分期付款支付宝通知
Route::post('installments/alipay/notify', 'InstallmentsController@alipayNotify')->name('installments.alipay.notify');
// 分期付款微信通知
Route::post('installments/wechat/notify', 'InstallmentsController@wechatNotify')->name('installments.wechat.notify');

Route::post('seckill_orders', 'OrdersController@seckill')->name('seckill_orders.store');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
   // 邮箱验证通知
   Route::get('/email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');
   // 验证邮箱验证码
   Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
   // 发送有限验证码
   Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');

   // 开始
   Route::group(['middleware' => 'email_verified'], function () {
       // 用户地址列表
       Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
       // 创建用户地址页面
       Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
       // 创建用户地址
       Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
       // 更新用户地址页面
       Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
       // 更新用户地址
       Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
       // 删除用户地址
       Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');

       // 商品收藏
       Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
       // 商品取消收藏
       Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
       // 商品收藏列表
       Route::get('products/favorites', 'ProductsController@favorites')->name("products.favorites");


       // 购物车列表
       Route::get('cart', 'CartController@index')->name('cart.index');
       // 添加购物车
       Route::post('cart', 'CartController@add')->name('cart.add');
       // 删除购物车
       Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');

       // 订单列表
       Route::get('orders', 'OrdersController@index')->name('orders.index');
       // 创建订单
       Route::post('orders', 'OrdersController@store')->name('orders.store');
       // 订单详情
       Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');

       // 支付宝支付
       Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
       // 支付宝同步回调
       Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
       // 微信支付
       Route::get('payment/{order}/wechat', 'PaymentController@payByWechat')->name('payment.wechat');

       // 订单收货
       Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
       // 订单评价
       Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
       // 创建评价
       Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');
       // 订单退款
       Route::post('orders/{order}/apply_refund', 'OrdersController@applyRefund')->name('orders.apply_refund');


       // 优惠券
       Route::get('coupon_codes/{code}', 'CouponCodesController@show')->name('coupon_codes.show');


       // 众筹订单
       Route::post('crowdfunding_orders', 'OrdersController@crowdfunding')->name('crowdfunding_orders.store');


       // 订单分期付款
       Route::post('payment/{order}/installment', 'PaymentController@payByInstallment')->name('payment.installment');
       // 分期付款页面
       Route::get('installments', 'InstallmentsController@index')->name('installments.index');
       // 分期付款详情
       Route::get('installments/{installment}', 'InstallmentsController@show')->name('installments.show');
       // 分期付款支付宝支付
       Route::get('installments/{installment}/alipay', 'InstallmentsController@payByAlipay')->name('installments.alipay');
       // 分期付款支付宝回调
       Route::get('installments/alipay/return', 'InstallmentsController@alipayReturn')->name('installments.alipay.return');
       // 分期付款支付宝支付
       Route::get('installments/{installment}/wechat', 'InstallmentsController@payByWechat')->name('installments.wechat');
       // 分期付款微信退款
       Route::post('installments/wechat/refund_notify', 'InstallmentsController@wechatRefundNotify')->name('installments.wechat.refund_notify');



   });
});


