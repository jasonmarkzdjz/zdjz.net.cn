<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 2016/6/15
 * Time: 11:02
 */

class wechatpayapiController extends TMController{

    public function wechatpayapiAction(){
        //调用微信JS api 支付 统一下单
        $payinfo = WechatPayApi::getInstance('0.01','oHJRbs4r7TOaNRIMw4OgayFZftUM','测试','测试');
        $order = $payinfo->apiPay();
        $jsapiparamets = $payinfo::GetJsApiParameters($order);
        return OMHttpResponse::getReturnJson('success',array('jsapiparamets'=>$jsapiparamets));
    }

    /*
     * 微信支付异步处理
     * */
    public function wechatnotifyAction(){

    }

    /*
     * 卡券扩展字段及签名生成算法
     *
     * */
    public function getwechatsiginAction(){
        $ticket = WechatCoupon::getTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = OMUtil::getNoncestr();
        $string = 'jsapi_ticket='.$ticket.'&noncestr='.$nonceStr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => TMConstant::$weixinauth['appID'],
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
    }
}