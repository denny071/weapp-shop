<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    // 用户列表
    $router->get('users', 'UserController@index')->name('admin.user.index');
    // 用户详情
    $router->get('users/{id}', 'UserController@detail');


    // 类目列表
    $router->get('catalog', 'CatalogController@index');
    // 创建类目
    $router->get('catalog/create', 'CatalogController@create');
    // 保存类目
    $router->post('catalog', 'CatalogController@store');
    // 编辑类目
    $router->get('catalog/{id}/edit', 'CatalogController@edit');
    // 更新类目
    $router->put('catalog/{id}', 'CatalogController@update');
    // 删除类目
    $router->delete('catalog/{id}', 'CatalogController@destroy');
    // API类目
    $router->get('api/catalog', 'CatalogController@apiIndex');


    // 优惠券列表
    $router->get('coupon', 'CouponController@index');
    // 创建优惠券页
    $router->get('coupon/create', 'CouponController@create');
    // 保存优惠券
    $router->post('coupon', 'CouponController@store');
    // 编辑优惠券页
    $router->get('coupon/{id}/edit', 'CouponController@edit');
    // 更新优惠券
    $router->put('coupon/{id}', 'CouponController@update');
    // 删除优惠券
    $router->delete('coupon/{id}', 'CouponController@destroy');


    // 商品
    $router->get('products', 'ProductController@index');
    // 创建商品页
    $router->get('products/create', 'ProductController@create');
    // 保存商品
    $router->post('products', 'ProductController@store');
    // 编辑商品
    $router->get('products/{id}/edit', 'ProductController@edit');
    // 更新商品
    $router->put('products/{id}', 'ProductController@update');



    // 订单列表
    $router->get('orders','OrderController@index')->name('admin.orders.index');
    // 订单详细
    $router->get('orders/{order}', 'OrderController@show')->name('admin.orders.show');
    // 订单送货
    $router->post('orders/{order}/ship', 'OrderController@ship')->name('admin.orders.ship');
    // 订单退款
    $router->post('orders/{order}/refund', 'OrderController@handleRefund')->name('admin.orders.handle_refund');



    // 众筹商品列表
    $router->get('crowdfunding_products', 'CrowdfundingProductController@index');
    // 创建众筹商品页
    $router->get('crowdfunding_products/create', 'CrowdfundingProductController@create');
    // 保存众筹商品
    $router->post('crowdfunding_products', 'CrowdfundingProductController@store');
    // 编辑众筹商品
    $router->get('crowdfunding_products/{id}/edit', 'CrowdfundingProductController@edit');
    // 更新众筹商品
    $router->put('crowdfunding_products/{id}', 'CrowdfundingProductController@update');



    // 秒杀商品列表
    $router->get('seckill_products', 'SeckillProductController@index');
    // 创建秒杀商品页
    $router->get('seckill_products/create', 'SeckillProductController@create');
    // 保存秒杀商品页
    $router->post('seckill_products', 'SeckillProductController@store');
    // 编辑秒杀商品页
    $router->get('seckill_products/{id}/edit', 'SeckillProductController@edit');
    // 更新秒杀商品页
    $router->put('seckill_products/{id}', 'SeckillProductController@update');

});
