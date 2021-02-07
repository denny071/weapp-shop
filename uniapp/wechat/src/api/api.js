import {
    wxRequest
} from '@/utils/wxRequest';


const apiMall = 'http://weapp-shop.test/api'

// 用户认证
const authorizationsWeapp = (params) => wxRequest(params, apiMall + "/authorization/weapp");
// 商品
const product = (params) => wxRequest(params, apiMall + '/product');
// 分类
const category = (params) => wxRequest(params, apiMall + '/product/catalog');
// 广告
const adList = (params) => wxRequest(params, apiMall + '/marketing/bannerAdList');
// 购物车
const shopCart = (params) => wxRequest(params, apiMall + '/cart');
// 添加购车
const settlement = (params) => wxRequest(params, apiMall + '/settlement');
// 用户地址
const userAddress = (params) => wxRequest(params, apiMall + '/user_addresses');
// 订单
const orders = (params) => wxRequest(params, apiMall + '/orders');
// 支付
const payment = (params) => wxRequest(params, apiMall + '/payment');
// 快递
const express = (params) => wxRequest(params, apiMall + '/express');

// 查询词
const searchWord = (params) => wxRequest(params, apiMall + '/search_word');
// 用户
const user = (params) => wxRequest(params, apiMall + '/user');
// 发送验证码
const sendSMSCode = (params) => wxRequest(params, apiMall + '/sendSMSCode');
// 图片验证码
const captchas = (params) => wxRequest(params, apiMall + '/captchas');

// 绑定手机号
const bindMobile = (params) => wxRequest(params, apiMall + '/bind_mobile');

// 浏览记录
const browseRecord = (params) => wxRequest(params, apiMall + '/browse_record');

// 浏览记录
const review = (params) => wxRequest(params, apiMall + '/review');

// 浏览记录
const images = (params) => wxRequest(params, apiMall + '/images');




export default {
    apiMall,
    adList,
    product,
    authorizationsWeapp,
    shopCart,
    settlement,
    userAddress,
    orders,
    payment,
    express,
    category,
    searchWord,
    user,
    sendSMSCode,
    captchas,
    bindMobile,
    browseRecord,
    review,
    images

}