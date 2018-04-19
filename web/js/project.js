/**
 * Created by Jason on 2016/12/26.
 */
var wx = {};
wx.netError = {
    "code": 108,
    "message": "网络不给力，请稍后重试"
};
//检查是否是微信浏览器
wx.isweixin = function(callback){
    if(typeof (callback) == 'function') {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            callback({"code": true});
        } else {
            callback({"code": false});
        }
    }else{
        callback({"code": false});
    }
}

/*
 * 微信认证授权 接口
 * redirect：回调url地址
 * */
wx.weixinauth = function(callback,redirect){
    // if(typeof (callback) == 'function') {
        var url = '/weixin/WeixinAuth';
        $.ajax({
            url:url,
            type:'POST',
            dataType:'json',
            data:{redirect:redirect},
            timeout:3000,
            success:function(response){
                callback(response);
            },
            // error:function(){
            //     callback(wx.netError);
            // }
        });
    // }else{
    //     callback(wx.netError);
    // }
};





