import wepy from 'wepy';
import util from './util';
import md5 from './md5';
import tip from './tip'
import regeneratorRuntime from '@/utils/runtime.js'

// const API_SECRET_KEY = 'www.mall.cycle.com'
// const TIMESTAMP = util.getCurrentTime()
// const SIGN = md5.hex_md5((TIMESTAMP + API_SECRET_KEY).toLowerCase())

const wxRequest = async(params = {}, url) => {
    tip.loading();
    let data = params.query || {};
    // data.sign = SIGN;
    // data.time = TIMESTAMP;
    let res = await wepy.request({
        url: url + ("uri" in params ?  params.uri :""),
        method: params.method || 'GET',
        filePath:("filePath" in params ?  params.filePath :""),
        data: data,
        header: {'Content-Type': 'application/json'},

    });
    tip.loaded();
    return res;
};


module.exports = {
    wxRequest
}
