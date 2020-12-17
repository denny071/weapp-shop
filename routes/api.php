<?php


// User           10 0001 ~ 10 0001
// Authorization  11 0001 ~ 11 0001
// Cart           12 0001 ~ 12 0001
// Common         13 0001 ~ 13 0001
// Express        14 0001 ~ 14 0001
// Order          15 0001 ~ 15 0001
// Product        16 0001 ~ 16 0001
// Review         17 0001 ~ 17 0001



// EROOR
// User           10 9001 ~ 10 9001
// Authorization  11 9001 ~ 11 9001
// Cart           12 9001 ~ 12 9001
// Common         13 9001 ~ 13 9001
// Express        14 0001 ~ 14 0001
// Order          15 0001 ~ 14 0001
// Product        16 9001 ~ 16 9001
// Product        17 9001 ~ 17 9001

//@F  order
//@F  no	                订单编号
//@F  oupon_id	            优惠券ID
//@F  type	                类型（normal：正常，seckill：秒杀，crowdfund：众筹）
//@F  express_company	    物流公司
//@F  express_freight	    物流费用
//@F  extra	                扩展信息
//@F  is_closed	            是否关闭
//@F  is_reviewed	        是否评论
//@F  paid_at	            支付时间
//@F  pay_deadline	        支付截止时间
//@F  payment_method	    支付方式
//@F  payment_no	        支付单号
//@F  refund_status	        退款状态
//@F  refund_no	            退款单号
//@F  refund_status	        退款状态
//@F  remark	            备注
//@F  ship_data	            物流数据
//@F  ship_status	        物流状态
//@F  status	            状态
//@F  total_amount	        订单总金额
//@F  created_at	        创建时间
//@F  updated_at	        更新时间
//@F  address:              用户地址
//@F    address	            详细地址
//@F    contact_name	    联系人
//@F    contact_phone	    联系电话
//@F    zip	                邮编
//@F  items:                产品列表
//@F    amount	            金额
//@F    created_at	        创建时间
//@F    id	                商品ID
//@F    order_id	        订单ID
//@F    price	            价格
//@F    product_id	        产品ID
//@F    product_sku_id	    产品SKU_ID
//@F    rating	            评价
//@F    review	            评论
//@F    reviewed_at	        评价时间
//@F    updated_at	        更新时间
//


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Http\Controllers\V1'], function ($api) {

        //@V1-authorization-认证-认证
        $api->group(['prefix' => 'authorization','namespace' => 'Authorization'], function ($api) {

            //@Gweapp-微信小程认证-weapp-微信小程认证
            //@A[S-code-认证码]
            //@R[openid-开放ID,session_key-session关键字]
            $api->get('weapp', 'WechatController@weapp');

        });
        //@V1-cart-购物车-购物车
        $api->group(['prefix' => 'cart','namespace' => 'Cart'], function ($api) {
            //@Gindex-购物车列表--购物车列表
            //@R[cartItems-购物车选项,addresses-用户地址]
            //@M[config:user]
            $api->get('/', 'CartController@index');

            //@Padd-添加购物车--添加购物车
            //@A[S-amount-购物车金额,S-sku_id-商品SKU的ID]
            //@R[created]
            //@M[config:user]
            $api->post('/', 'CartController@add');

            //@Uupdate-更新购物车-{I|sku_id|商品SKU的ID}-更新购物车
            //@A[S-amount-购物车金额,?I-checked-是否选择（0：不选择，1已选择），默认：1]
            //@R[created]
            //@M[config:user]
            $api->put('/{skuId}', 'CartController@update');

            //@Ddelete-移除购车商品-{I|sku_id|商品SKU的ID}-移除购车商品
            //@R[noContent]
            //@M[config:user]
            $api->delete('/{skuId}', 'CartController@delete');

        });

        //@V1-common-公共-公共
        $api->group(['prefix' => 'common','namespace' => 'Common'], function ($api) {

            //@GimageCode-图片验证码-imageCode-图片验证码
            //@R[captcha_key-验证key,expired_at-过期时间,captcha_image_content-图片内容]
            $api->get('imageCode', 'ImageCodeController@index');


            //@Pavatar-上传头像-avatar-上传头像
            //@A[F-image-图片文件]
            //@R[created]
            //@M[config:user]
            $api->post('avatar', 'ImageController@avatar');

            //@PmessageCode-短信验证码-messageCode-短信验证码
            //@A[S-captcha_key-图片验证码key,S-captcha_code-图片验证码,S-mobile-手机号]
            //@R[key-关键字,expired_at-过期时间]
            $api->post('messageCode', 'MessageCodeController@messageCode');

        });

        //@V1-express-快递-快递
        $api->group(['prefix' => 'express','namespace' => 'Express'], function ($api) {

            //@Gindex-快递信息--获取快递信息
            //@A[S-order_no-订单编号]
            //@R[key-关键字,expired_at-过期时间]
            $api->get('/', 'ExpressController@index');

        });

        //@V1-marketing-营销-营销
        $api->group(['prefix' => 'marketing','namespace' => 'Marketing'], function ($api) {

            //@GbannerAdList-首页banner广告-bannerAdList-首页banner广告
            //@R[picUrl-图片url,advertUrl-链接url]
            $api->get('bannerAdList', 'AdController@index');

        });

        //@V1-notify-通知-通知
        $api->group(['prefix' => 'notify','namespace' => 'Notify'], function ($api) {

            //@GwechatPayNotify-微信支付异步通知-wechatPayNotify-微信支付异步通知
            //@R[created]
            $api->get('wechatPayNotify', 'WechatController@wechatPayNotify');

            //@GwechatRefundNotify-微信退款异步通知-wechatRefundNotify-微信退款异步通知
            //@R[created]
            $api->get('wechatRefundNotify', 'WechatController@wechatRefundNotify');

        });
        //@V1-order-订单-订单
        $api->group(['prefix' => 'order','namespace' => 'Order'], function ($api) {
            //@Gindex-订单列表--订单列表
            //@A[I-order_status-订单状态（0：全部，1：未发货，2：发货中，3：已发货）]
            //@R[order]
            //@M[config:user]
            $api->get('/', 'OrderController@index');

            //@Gdetail-订单详细-{S|order_no|订单编号}-订单详细
            //@R[order]
            //@M[config:user]
            $api->get('/{orderNo}', 'OrderController@detail')->where(['orderNo' => '[0-9]+']);

            //@Pstore-创建订单--创建订单
            //@A[I-express_id-快递ID,I-address_id-地址ID,S-coupon_code-优惠券码]
            //@A[A-product_sku_ids-商品ID,S-remark-备注]
            //@R[order]
            //@M[config:user]
            $api->post('/', 'OrderController@store');

            //@Dcancel-取消订单-{S|order_no|订单编号}-取消订单
            //@R[noContent]
            //@M[config:user]
            $api->delete('/{orderNo}', 'OrderController@cancel')->where(['orderNo' => '[0-9]+']);

            //@PapplyRefund-申请退款-applyRefund/{S|order_no|订单编号}-申请退款
            //@A[S-reason-退款原因]
            //@R[created]
            //@M[config:user]
            $api->post('/applyRefund/{orderNo}', 'OrderController@applyRefund')->where(['orderNo' => '[0-9]+']);

            //@Preceived-确认收货-received/{S|order_no|订单编号}-确认收货
            //@R[created]
            //@M[config:user]
            $api->post('/received/{orderNo}', 'OrderController@received')->where(['orderNo' => '[0-9]+']);

            //@Psettlement-订单结算-settlement-订单结算
            //@A[I-express_id-快递ID,I-address_id-地址ID,A-product_sku_ids-商品ID]
            //@R[product_name-商品列表(product_list)@商品名称,product_sku_name-商品列表(product_list)@商品SKU名称]
            //@R[id-默认地址(default_address)@地址ID,user_id-默认地址(default_address)@用户ID]
            //@R[province-默认地址(default_address)@省名称,province_code-默认地址(default_address)@省代码]
            //@R[city-默认地址(default_address)@城市,city_code-默认地址(default_address)@城市代码]
            //@R[district-默认地址(default_address)@区名称,district_code-默认地址(default_address)@区代码]
            //@R[address-默认地址(default_address)@详细地址,zip-默认地址(default_address)@邮编]
            //@R[contact_name-默认地址(default_address)@联系人,contact_phone-默认地址(default_address)@联系电话]
            //@R[is_default-默认地址(default_address)@是否默认,last_used_at-默认地址(default_address)@最后使用时间]
            //@R[created_at-默认地址(default_address)@创建时间,updated_at-默认地址(default_address)@更新时间]
            //@R[full_address-默认地址(default_address)@完整地址]
            //@R[express_freight-运输重量,product_price-商品价格,total_price-总价格]
            //@M[config:user]
            $api->post('/settlement', 'SettlementController@store');

            //@Pcrowdfunding-订单结算-crowdfunding-订单结算
            //@A[I-express_id-快递ID,I-address_id-地址ID,I-sku_id-商品ID,I-amount-数量]
            //@R[created]
            //@M[config:user]
            $api->get('/crowdfunding', 'CrowdfundingController@store');

            //@Pseckill-订单结算-seckill-订单结算
            //@A[I-express_id-快递ID,I-address_id-地址ID,I-sku_id-商品ID]
            //@R[created]
            //@M[config:user]
            $api->get('/seckill', 'SeckillController@store');


        });

        //@V1-payment-支付-支付
        $api->group(['prefix' => 'payment','namespace' => 'Payment'], function ($api) {

            //@GwechatGet-查看微信支付-wechat/{S|order_no|订单编号}-查看微信支付订单
            //@R[trade_state-交易状态]
            //@M[config:user]
            $api->get('wechat/{orderNo}', 'WechatController@get')->where(['orderNo' => '[0-9]+']);

            //@PwechatPayment-微信支付-wechat/{S|order_no|订单编号}-微信支付
            //@R[out_trade_no-第三方订单号,total_fee-总共金额（单位分）,body-支付说明]
            //@R[openid-第三方ID,notify_url-响应地址]
            //@M[config:user]
            $api->post('wechat/{orderNo}', 'WechatController@payment')->where(['orderNo' => '[0-9]+']);

        });

        //@V1-product-商品-商品
        $api->group(['prefix' => 'product','namespace' => 'Product'], function ($api) {

            //@Gindex-商品列表--商品描述
            //@A[?I-page-页码]
            //@R[id-商品ID,type-类型,catalog_id-分类ID,title-商品名称,long_title-商品长标题]
            //@R[description-商品详情,image-商品图片,on_sale-是否在销售,rating-评分,sold_count-销售数量]
            //@R[review_count-评论数,price-商品价格,created_at-创建时间,updated_at-更新时间]
            $api->get('/', 'ProductController@index');

            //@Gdetail-商品详情-{I|product_id|商品ID}-商品详情
            //@A[?I-openid-openid]
            //@R[id-商品ID,type-类型,catalog_id-分类ID,title-商品名称,long_title-商品长标题]
            //@R[description-商品详情,image-商品图片,on_sale-是否在销售,rating-评分,sold_count-销售数量]
            //@R[review_count-评论数,price-商品价格,created_at-创建时间,updated_at-更新时间]
            $api->get('/{productId}', 'ProductController@get')->where(['productId' => '[0-9]+']);;

            //@GcatalogTop-顶级分类列表-catalog-顶级分类列表
            //@R[id-分类ID,name-标题,imageUrl-图片URL]
            $api->get('catalog', 'CatalogController@index');

            //@GcatalogChild-子分类列表-catalog/{I|catalog_id|父分类ID}-子分类列表
            //@R[id-分类ID,name-标题,imageUrl-图片URL]
            $api->get('catalog/{catalogId}', 'CatalogController@get')->where(['catalogId' => '[0-9]+']);;


            //@GfavoriteIndex-商品收藏列表-favorite-商品收藏列表
            //@R[id-商品ID,type-类型,catalog_id-分类ID,title-商品名称,long_title-商品长标题]
            //@R[description-商品详情,image-商品图片,on_sale-是否在销售,rating-评分,sold_count-销售数量]
            //@R[review_count-评论数,price-商品价格,created_at-创建时间,updated_at-更新时间]
            //@M[config:user]
            $api->get('/favorite', 'FavoriteController@index');

            //@GfavoriteStore-收藏商品-{I|product_id|商品ID}/favorite-收藏列表
            //@R[created]
            //@M[config:user]
            $api->post('/{productId}/favorite', 'FavoriteController@store')->where(['productId' => '[0-9]+']);

            //@GfavoriteDestroy-取消收藏-{I|product_id|商品ID}/favorite-取消收藏
            //@R[created]
            //@M[config:user]
            $api->delete('/{productId}/favorite', 'FavoriteController@destroy')->where(['productId' => '[0-9]+']);

        });


        //@V1-review-评价-评价
        $api->group(['prefix' => 'review','namespace' => 'Review'], function ($api) {

            //@GorderReview-订单显示评价-order/{S|order_no|订单编号}-通过订单编号显示评价
            //@R[id-评论ID,rating-评价,review-评价内容]
            //@R[name-评论人名称,avatar-头像地址,reviewed_at-评论时间]
            $api->get('/order/{orderNo}', 'ReviewController@showByOrder')->where(['orderNo' => '[0-9]+']);


            //@GproductReview-商品显示评价-product/{S|product_id|商品ID}-商品显示评价
            //@A[?I-page-页码]
            //@R[id-评论ID,rating-评价,review-评价内容]
            //@R[name-评论人名称,avatar-头像地址,reviewed_at-评论时间]
            $api->get('/product/{productId}', 'ReviewController@showByProduct')->where(['productId' => '[0-9]+']);

            //@Pstore-保存评价--保存评价
            //@A[I-order_item_id-订单商品ID,S-content-内容,S-start-评价]
            //@R[created]
            //@M[config:user]
            $api->post('/', 'ReviewController@store');

        });



        //@V1-search-搜索-搜索
        $api->group(['prefix' => 'search','namespace' => 'Search'], function ($api) {

            //@Gindex-查询词列表--查询词列表
            //@R[word-搜索词,times-次数]
            //@M[config:user]
            $api->get('/', 'SearchController@index');

            //@Pstore-添加查询词--添加查询词
            //@A[S-word-搜索词]
            //@R[created]
            //@M[config:user]
            $api->post('/', 'SearchController@store');


            //@Dclear-清空历史记录--清空历史记录
            //@R[created]
            //@M[config:user]
            $api->delete('/', 'SearchController@clear');

        });



         //@V1-user-用户-用户
        $api->group(['prefix' => 'user','namespace' => 'User'], function ($api) {

            //@Gone-查看当前用户信息--查看当前用户信息
            //@R[id-用户ID,name-名称,avatar-头像,introduction-简介]
            //@R[mobile-电话,created_at-创建时间,updated_at-更新时间]
            //@M[config:user]
            $api->get('/', 'UserController@get');

            //@Uupdate-更新用户信息--更新用户信息
            //@A[S-name-用户名称,S-email-邮箱,S-introduction-简介,F-avatar_image_id-头像图片ID]
            //@R[id-用户ID,name-名称,avatar-头像,introduction-简介]
            //@R[mobile-电话,created_at-创建时间,updated_at-更新时间]
            //@M[config:user]
            $api->put('/', 'UserController@update');


            //@PbindMobile-绑定手机-bindMobile-绑定手机
            //@A[S-verification_key-验证key,S-verification_code-验证码,S-mobile-电话]
            //@R[created]
            //@M[config:user]
            $api->post('/bindMobile', 'UserController@bindMobile');

            //@GrecordAddress-获得浏览记录-record-获得浏览记录
            //@A[?I-page-页码]
            //@R[id-记录ID,type-类型,times-次数,created_at-创建时间]
            //@R[product_id-数据(data)@商品ID,title-数据(data)@商品标题]
            //@R[image-数据(data)@商品图片,price-数据(data)@商品价格]
            //@M[config:user]
            $api->get('record', 'RecordController@get');


            //@DrecordDestroy-删除浏览记录-record/{I|id|记录ID}-删除浏览记录
            //@R[noContent]
            //@M[config:user]
            $api->delete('record/{id}', 'RecordController@destroy')->where(['id' => '[0-9]+']);


            //@GaddressIndex-用户地址列表-address-用户地址列表
            //@R[id-地址ID,province-省名称,province_code-省代码,city-城市,city_code-城市代码]
            //@R[district-区名称,district_code-区代码,address-详细地址,is_default-是否默认]
            //@R[contact_name-联系人,contact_mobile-联系电话,last_used_at-最后使用时间]
            //@R[created_at-创建时间,updated_at-更新时间]
            //@M[config:user]
            $api->get('/address', 'AddressController@index');

            //@GaddressOne-获得用户地址-address/{I|user_address_id|用户地址ID}-获得用户地址
            //@R[id-地址ID,province-省名称,province_code-省代码,city-城市,city_code-城市代码]
            //@R[district-区名称,district_code-区代码,address-详细地址,is_default-是否默认]
            //@R[contact_name-联系人,contact_mobile-联系电话,last_used_at-最后使用时间]
            //@R[created_at-创建时间,updated_at-更新时间]
            //@M[config:user]
            $api->get('/address/{userAddressId}', 'AddressController@get')->where(['userAddressId' => '[0-9]+']);


            //@PaddressStore-添加用户地址-address-添加用户地址
            //@A[S-province-省名称,S-province_code-省代码,S-city-城市,S-city_code-城市代码]
            //@A[S-district-区名称,S-district_code-区代码,S-address-详细地址]
            //@R[I-is_default-是否默认,S-contact_name-联系人,S-contact_mobile-联系电话]
            //@M[config:user]
            //@R[created]
            $api->post('/address', 'AddressController@addressStore');


            //@UaddressUpdate-更新用户地址-address/{I|user_address_id|用户地址ID}-更新用户地址
            //@A[S-province-省名称,S-province_code-省代码,S-city-城市,S-city_code-城市代码]
            //@A[S-district-区名称,S-district_code-区代码,S-address-详细地址]
            //@R[I-is_default-是否默认,S-contact_name-联系人,S-contact_mobile-联系电话]
            //@R[id-地址ID,province-省名称,province_code-省代码,city-城市,city_code-城市代码]
            //@R[district-区名称,district_code-区代码,address-详细地址,is_default-是否默认]
            //@R[contact_name-联系人,contact_mobile-联系电话,last_used_at-最后使用时间]
            //@R[created_at-创建时间,updated_at-更新时间]
            //@M[config:user]
            $api->put('address/{userAddressId}', 'AddressController@update')->where(['userAddressId' => '[0-9]+']);


            //@DaddressDestroy-删除用户地址-address/{I|user_address_id|用户地址ID}-删除用户地址
            //@R[noContent]
            //@M[config:user]
            $api->delete('address/{userAddressId}', 'AddressController@destroy');

        });





    });

});
