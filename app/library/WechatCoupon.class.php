<?php
/**
 * Created by PhpStorm.
 * User: Jerome 微信卡券接口
 * Date: 2016/1/5
 * Time: 14:02
 */
class WechatCoupon {

    /*
     * 上传卡券的logo
     *
     * */
    public static function uploadCouponImg($buffer){
        $curl = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.OMUtil::getAccessToken();
        $param = array(
            'buffer'=>$buffer,//@D:\\workspace\\htdocs\\yky_test\\logo.jpg
        );
        $result = OMUtil::requestByCURL($curl,true,$param);
        return json_decode($result,true);
    }
    //获取卡券颜色列表
    public static function getCouponColors(){
        $url = 'https://api.weixin.qq.com/card/getcolors?access_token='.OMUtil::getAccessToken();
        $Colorslist = OMUtil::requestByCURL($url,true);
        $Colorslist = json_decode($Colorslist,true);
        return $Colorslist;
    }
    //创建卡券
    public static function creatCoupon(){
        $curl = new TMCurl();
        $curl->setOptions(
            array(
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_SSL_VERIFYPEER => FALSE
            )
        );
        $paramjson = OMUtil::encode(TMConstant::$couponSet);
        $result = $curl->sendByPost($paramjson,TMConstant::$weixinauth['CouponUrl'].'?access_token='.OMUtil::getAccessToken());
        return json_decode($result,true);
    }
    /*
     *
     * 设置买单接口
     * 创建卡券之后，开发者可以通过设置微信买单接口设置该card_id支持微信买单功能。
     * 值得开发者注意的是，设置买单的card_id必须已经配置了门店，否则会报错
     *
     * */

    public static function setPayCell($card_id,$is_open = false){
        $curl = 'https://api.weixin.qq.com/card/paycell/set?access_token='.OMUtil::getAccessToken();
        $params = json_encode(array('card_id'=>$card_id,'is_open'=>$is_open));
        $result = OMUtil::requestByCURL($curl,true,$params);
        return json_decode($result,true);
    }

    /***********************投放接口*******************************/

    /*
     * 创建二维码接口
     *
     * */
    public static function createQcode($card_id,$openid,$code,$is_unique_code = false,$outer_id = 1){
        $curl = 'https://api.weixin.qq.com/card/qrcode/create?access_token='.OMUtil::getAccessToken();
        $params = array(
            'action_name'=>'QR_CARD',
            'expire_seconds'=>'1800',
            'action_info'=>array(
                'card'=>array(
                    'card_id'=>$card_id,
                    'code'=>$code,
                    'openid'=>$openid,
                    'is_unique_code'=>$is_unique_code,
                    'outer_id'=>$outer_id
                ),
            ),
        );
        $result = OMUtil::requestByCURL($curl,true,json_encode($params));
        return json_decode($result,true);
    }

    /*************************线下核销卡券***************************************/

    //查询code接口
    public static function getCardCode($code,$card_id = '',$check_consume = false){
        $url = 'https://api.weixin.qq.com/card/code/get?access_token='.OMUtil::getAccessToken();
        $param = json_encode(array(
            'code'=>$code,
            'card_id'=>$card_id,
            'check_consume'=>$check_consume
        ));

        $result = OMUtil::requestByCURL($url,true,$param);
        return json_decode($result,true);
    }
    /*
     * 线下核销卡券
     *消耗code接口是核销卡券的唯一接口，仅支持核销有效期内的卡券，否则会返回错误码invalid time。
     * */
    public static function consumeQoupon($code,$card_id = ''){
        $curl = 'https://api.weixin.qq.com/card/code/consume?access_token='.OMUtil::getAccessToken();
        $param = json_encode(array(
            'code'=>$code,
            'card_id'=>$card_id,
        ));
        $result = OMUtil::requestByCURL($curl,true,$param);
        return json_decode($result,true);
    }

    /*
     * api_ticket 是用于调用微信卡券JS API的临时票据，有效期为7200 秒，
     * 通过access_token 来获取。 api_ticket
     *
     * */

    public static function getTicket(){
         $ticket = OMUtil::getMemcache();
        if(!empty($ticket)){
            return $ticket;
        }else{
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.OMUtil::getAccessToken().'&type=wx_card';
            $result = OMUtil::requestByCURL($url,false);
            $result = json_decode($result,true);
            if($result['errcode'] == 0){
                OMUtil::setMemcache($result['ticket']);
                return $result['ticket'];
            }
        }
    }

    /*
     * 线上核销接口
     *
     * 拉取卡券列表接口
     * */
    //获取微信用户详细信息
    public static function getAuthUserInfo($openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info';
        $param = OMUtil::encode(array(
            'access_token'=>OMUtil::getAccessToken(),
            'openid'=>$openid,
            'lang'=>'zh_CN'
        ));
        $result = OMUtil::requestByCURL($url,false,$param);
        return json_decode($result,true);
    }
    //获取用户已领取卡券接口
    public static function getUersCardList($openid,$card_id=''){
        $url = 'https://api.weixin.qq.com/card/user/getcardlist?access_token='.OMUtil::getAccessToken();
        $param = OMUtil::encode(array(
            'openid'=>$openid,
            'card_id'=>$card_id
        ));
        $result = OMUtil::requestByCURL($url,true,$param);
        return json_decode($result,true);
    }
    //查询卡列表
    public static function getCardList($offset = 0,$count = 0){
        $url = 'https://api.weixin.qq.com/card/batchget?access_token='.OMUtil::getAccessToken();
        $param = OMUtil::encode(array(
            'offset'=>$offset,
            'count'=>$count
        ));
        $result = OMUtil::requestByCURL($url,true,$param);
        return json_decode($result,true);
    }
}