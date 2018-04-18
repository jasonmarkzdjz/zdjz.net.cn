<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/6/14
 * Time: 15:35
 * desc:门店接口
 */
class WechatPoi{

    /*
        * 上传图片
        *
        * */
    public static function uploadPoiImg($buffer){
        $curl = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.OMUtil::getAccessToken();
        $param = array(
            'buffer'=>$buffer,//@/home/sqliuxinliang/app.shiqutech.com/static/zldt/images/card.jpg   @D\card.jpg
        );
        $result = OMUtil::requestByCURL($curl,true,$param);
        return json_decode($result,true);
    }

    /*
     * 创建门店接口调用成功后会返回errcode 0、errmsg ok，但不会实时返回poi_id。
     * 成功创建后，会生成poi_id，但该id不一定为最终id。门店信息会经过审核，审核通过后方可获取最终poi_id，
     * 该id为门店的唯一id，强烈建议自行存储审核通过后的最终poi_id，并为后续调用使用。
     *business_name:门店名称（仅为商户名，如：国美、麦当劳，不应包含地区、地址、分店名等信息，错误示例：北京国美）
     * branch_name：分店名称，
     * province：门店所在的省份，
     * city：门店所在的城市，
     * district:门店所在地区,
     * address:门店所在的详细街道地址（不要填写省市信息）
     * telephone:门店的电话,
     * categories:门店的类型(不同级分类用“,”隔开，如：美食，川菜，火锅),
     * offset_type:坐标类型，1 为火星坐标（目前只能选1）
     * longitude:门店所在地理位置的经度,latitude:门店所在地理位置的纬度（经纬度均为火星坐标，最好选用腾讯地图标记的坐标）
     * photo_list:640*340px。必须为上一接口生成的url
     * special:特色服务，如免费wifi，免费停车，送货上门等商户能提供的特色功能或服务,
     * open_time:营业时间，24 小时制表示，用“-”连接，如 8:00-20:00,
     * avg_price:人均价格，大于0 的整数,
     * sid:商户自己的id，用于后续审核通过收到poi_id 的通知时，做对应关系。请商户自己保证唯一识别性
     * */
    public static function addpoi($data){
        $curl = 'http://api.weixin.qq.com/cgi-bin/poi/addpoi?access_token='.OMUtil::getAccessToken();
        $result = OMUtil::requestByCURL($curl,true,$data);
        return json_decode($result,true);
    }

    //查询门店信息
    public static function getPoiInfo($poiid){
        $url = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token='.OMUtil::getAccessToken();
        $param = OMUtil::encode(array('poi_id'=>$poiid));
        $result = OMUtil::requestByCURL($url,true,$param);
        return json_decode($result,true);
    }
//
//    //获取门店列表信息接口
//    public static function getBatchList($offset=0,$count=0){
//        $curl = new TMCurl();
//        $curl->setOptions(array(
//            CURLOPT_CONNECTTIMEOUT => 2,
//            CURLOPT_SSL_VERIFYPEER=>FALSE,
//            CURLOPT_SSL_VERIFYHOST=>FALSE,
//        ));
//        $paramarry = array(
//            'offset'=>$offset,
//            'count'=>$count
//        );
//        $paramarry = OMUtil::encode($paramarry);
//        $result = $curl->sendByGet($paramarry,'https://api.weixin.qq.com/card/location/batchget?access_token='.OMUtil::getAccessToken());
//        return json_decode($result,true);
//    }

    /*
     *查询门店信息
     * */
    public static function  getPoi($poi_id){
        $curl = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token='.OMUtil::getAccessToken();
        $params= array(
            'poi_id'=>$poi_id
        );
        $result = OMUtil::requestByCURL($curl,true,json_encode($params));
        return json_decode($result,true);
    }

    /*
     *查询门店列表
     *
     * */
    public static function getPoilist($begin=0,$limit=10){
        $curl = 'https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token='.OMUtil::getAccessToken();
        $param = array('begin'=>$begin,'limit'=>$limit);
        $result = OMUtil::requestByCURL($curl,true,json_encode($param));
        return json_decode($result,true);
    }

    /*
     * 修改门店服务信息
     *商户可以通过该接口，修改门店的服务信息，包括：图片列表、营业时间、推荐、
     * 特色服务、简介、人均价格、电话7 个字段（名称、坐标、地址等不可修改）修改后需要人工审核。
     * */
    public static function updatePoi($data){
        $curl = 'https://api.weixin.qq.com/cgi-bin/poi/updatepoi?access_token='.OMUtil::getAccessToken();
        $prams = json_encode($data);
        $result = OMUtil::requestByCURL($curl,true,$prams);
        return json_decode($result,true);
    }
    /*
     * 删除门店
     * */
    public static function delPoi($poi_id){
        $curl = 'https://api.weixin.qq.com/cgi-bin/poi/delpoi?access_token='.OMUtil::getAccessToken();
        $params = json_decode(array('poi_id'=>$poi_id));
        $result = OMUtil::requestByCURL($curl,true,$params);
        return json_decode($result,true);
    }

}