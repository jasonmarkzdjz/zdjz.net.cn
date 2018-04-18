<?php
/*
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2008 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&D Team, Advertising Platform & Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */

/**
 * TMConstant
 * The constant class for util class
 *
 * @package config
 * @author  felixqiao <felixqiao@tencent.com>
 * @version TMConstant.class.php 2008-9-11 by ianzhang
 */
class TMConstant {
    //-----------------------CUSTOM CONSTANT------------------------------//
    

    //-----------------LIB CONSTANT------------------------------------//
    static $_uploadTypes = array ('.gif' => 'GRAPH', '.jpg' => 'GRAPH', '.png' => 'GRAPH', '.psd' => 'GRAPH', '.bmp' => 'GRAPH', '.tiff' => 'GRAPH', '.wbmp' => 'GRAPH',

    '.avi' => 'VIDEO', '.swf' => 'VIDEO', '.mpg' => 'VIDEO', '.mgeg' => 'VIDEO', '.wmv' => 'VIDEO', '.rm' => 'VIDEO', '.rmvb' => 'VIDEO', '.flv' => 'VIDEO',

    '.mp3' => 'AUDIO', '.wma' => 'AUDIO' );
    public static function uploadType($mixed) {
        return self::$_uploadTypes [$mixed];
    }

    public static function uploadTypes() {
        return self::$_uploadTypes;
    }
    
    //----------------ERROR CONSTANT---------------------------//
    //上传图片错误
    //上传图片错误
    const UPLOAD_ERROR_SYSTEM = 1;
    const UPLOAD_ERROR_WATER = 2;
    const UPLOAD_ERROR_THUMB = 3;
    const UPLOAD_ERROR_PIX = 4;
    const UPLOAD_ERROR_SIZE = 5;
    const UPLOAD_ERROR_COUNT = 6;
    const UPLOAD_ERROR_ONE_DAY = 7;
    const UPLOAD_ERROR_HEIGHT_WIDTH = 8;

    static $weixinauth = array(
        'WEIXIN_OAUTH_URL'=>'https://open.weixin.qq.com/connect/oauth2/authorize',//微信授权地址,
        'OAUTH_REDIRECT' => '/weixin/response',//微信授权成功之后的回调处理接口
        'appID' => 'wx3d6fdd839ce4a50d',
        'appsecret'=>'c097c26031f1ba68ec29a33f02a563b9',
        'WEIXIN_ACCESS_TOKEN'  => 'https://api.weixin.qq.com/sns/oauth2/access_token',//AppID和AppSecret网页授权获取用户基本信息获取用户信息
        'WEIXIN_USERINFO' => 'https://api.weixin.qq.com/sns/userinfo',//网页授权获取用户基本信息获取用户信息
//        'ACCTOKEN_URl'=>'https://api.weixin.qq.com/cgi-bin/token',//AppID和AppSecret调用本接口来获取access_token
        'CouponUrl' => 'https://api.weixin.qq.com/card/create',//创建卡券地址
        'SENDREADPACKURL' => "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack",//红包发放接口地址
        'USERINFO'=>'snsapi_userinfo',//网页授权作用域
        'scope' =>'snsapi_userinfo',//应用授权作用域不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息
        'LANG' =>'zh_CN',//语言
        'JS_TYPE'=> 'jsapi',
        'NOTIFY_URL'=>'',//微信jsapi异步通知url
        'MATCHID' =>'21039214',//商户ID，
        'KEY' => 'e10adc3949ba59abbe56e057f20f883e',//商户支付密钥
        'APPSECRET' => '01c6d59a3f9024db6336662ac95c8e74',//公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
        'TOKEN'=>'zdjz',
        //以下为微信发红包的相关配置信息
        'actName'=>'活动名称',
        'nickName'=>'资金提供方名称',
        'sendName'=>'红包发送方的名称',
        'wishing'=>'祝福语',
        'spScrect'=>'商家秘钥',
        'remark'=>'商家备注信息',
        'logoImgUrl'=>'商户logo的Url',
        'shareContent'=>'分享文案',
        'shareUrl'=>'分享链接',
        'shareImgUrl'=>'分享图片url'
    );
    //消息队列名称
    const MQ_REDPACK='jerome6102015';
    //memcache key
    const CACHEKEY = 'jerome6102015';

    static $trust_servers = array('http://zdjz.net.cn');

    const SERVER_NAME = 'http://zdjz.net.cn';
    static $DB_TABLE_NAME=array(
        'DB_TABLE_SENDREDPACK_HISTORY'=>'Tbl_FSendRedhistory',
    );
    static $couponSet = array(
        'card'=>array(
            'card_type'=>'GENERAL_COUPON',
            'general_coupon'=>array(
                'base_info'=>array(
                    'logo_url'=>'http://shp.qpic.cn/app_actsec/6a335576a1d92cb0e3356c0391abbd0ab5db892e30e0e861e38802e9b12844b36531b18385facc59985ea6c351f895e9/0',
                    'code_type'=>'CODE_TYPE_TEXT',//0文本，1条形码，2二维码
                    'brand_name'=>'上汽MG GT',//商户名称, 上限6个字
                    "title"=>"132 元双人火锅套餐",
                    'sub_title'=>'MG GT积分营 抢积分 赢GT高性能风尚中级车',//券名的副标题，上限18个字
                    'color'=>'Color101',//券颜色，color010-color100
                    'notice'=>'132 元双人火锅套餐',//使用提醒，字数上限9个字
                    'service_phone'=>'400-828-1088',//客服电话
                    'source'=>'上汽MG GT',
                    'description'=>'卡券支持互赠功能，一人券少，众人协助，赶快呼朋唤友参与活动吧。更多详情请关注“上汽集团MG”公共账号',//使用说明，长文本描述，上限1000个汉字
                    'use_limit'=>10000,
                    'get_limit'=>2,
                    ''=>true,
                    'bind_openid'=>false,
                    'can_share'=>false,//领取卡券原生页面是否可分享
                    'can_give_friend'=>true,//卡券是否可转增
                    'location_id_list'=>array(),//门店的ID列表
                    'date_info'=>array(
                        'type'=>1,
                        'begin_timestamp'=>'1451923200',//2014年11月4日
                        'end_timestamp'=>'1481472000'//2015年1月11日
                    ),
                    'sku'=>array(
                        'quantity'=>100000 //1000:80,500:600,200:2220,100:11620,50:18680
                    ),
                    'url_name_type'=>'URL_NAME_TYPE_EXCHANGE',
                    'custom_url'=>'mggt.act.qq.com/card_change.html'
                ),
                'default_detail'=>'点击领取按钮可以即刻领用卡券，卡券可以在MG GT腾讯手机平台（http://mggt.act.qq.com）兑换各种炫酷大礼， 1万颗腾讯特有的虚拟奖品，MG 精美车模、4台iphone 6、更有2辆高性能风尚中级车MG GT一年使用权。'
            )
        )
    );
    const Page_Size=20;
    const taecounterid=12;
    const WORKSTRKEY = '1234';
}

?>