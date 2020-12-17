import api from '@/api/api';
import tip from './tip'
import wepy from 'wepy';
import regeneratorRuntime from '@/utils/runtime.js'

const payOrder = async(openid, orderNo,totalFee) => {
    //保存成功了后进行微信支付
    const pay = await api.payment({
        uri:"/"+ orderNo + "/wechat",
        query:{
            openid: openid,
        }
    });
    if (pay.statusCode == 200) {
        // //以下是微信支付
        wx.requestPayment({
            appId: pay.data.appId,
            timeStamp: pay.data.timeStamp,
            nonceStr: pay.data.nonceStr,
            package: pay.data.package,
            signType: 'MD5',
            paySign: pay.data.paySign,
            success: function (res) {
                console.log('pay', res)
                setTimeout(() => {
                    //支付成功 关闭loadding 跳转到支付成功页面
                    tip.loaded();
                    wepy.navigateTo({
                        url: "/pages/pay_success?orderNo="+orderNo+"&totalFee="+totalFee
                    })
                }, 2000)
            },
            fail: function (res) {
                tip.alert('支付失败');
                setTimeout(() => {
                    //支付成功 关闭loadding 跳转到支付成功页面
                    tip.loaded();
                    wepy.navigateTo({
                        url: "/pages/order"
                    })
                }, 2000)
            }
        })
    } else {
        tip.alert('支付失败');
        setTimeout(() => {
            //支付成功 关闭loadding 跳转到支付成功页面
            tip.loaded();
            wepy.navigateTo({
                url: "/pages/order"
            })
        }, 2000)
    }
};

module.exports = {
    payOrder
}
