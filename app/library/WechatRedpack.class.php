<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/1/8
 * Time: 14:24
 */
class WechatRedpack{

    //红包发放基础类
    private static $instance;//红包单例对象

    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->actName = TMConstant::$weixinauth['actName'];
        $this->nickName = TMConstant::$weixinauth['nickName'];
        $this->sendName = TMConstant::$weixinauth['sendName'];
        $this->wishing = TMConstant::$weixinauth['wishing'];
        $this->spScrect = TMConstant::$weixinauth['spScrect'];
        $this->randomStr = OMUtil::getNoncestr();
        $this->mchBillNo = $this->wxMchBillno();
        $this->remark = TMConstant::$weixinauth['remark'];
        $this->logoImgUrl = TMConstant::$weixinauth['logoImgUrl'];
        $this->shareContent = TMConstant::$weixinauth['shareContent'];
        $this->shareUrl = TMConstant::$weixinauth['shareUrl'];
        $this->shareImgUrl=TMConstant::$weixinauth['shareImgUrl'];
    }
    //红包发放
    public function redPackSend($reOpenid,$money){
        $postfiled = $this->getfileds($reOpenid,$money);
        $postfiled['sign'] = OMUtil::getSign($postfiled);
        $postxml = OMUtil::arrayToXml($postfiled);
        $ch = new TMCurl();
        $url = TMConstant::$weixinauth['SENDREADPACKURL'];
        $ch->setHttpProxy();
        $arrayOption = array(
            CURLOPT_SSLCERTTYPE=>'pem',
            CURLOPT_SSLCERT=>ROOT_PATH.'library/cert/apiclient_cert.pem',
            CURLOPT_SSLKEYTYPE=>'pem',
            CURLOPT_SSLKEY=>ROOT_PATH.'library/cert/apiclient_key.pem',
        );
        if ('test' == TMUtil::getServerType()) {
            $arrayOption[CURLOPT_SSL_VERIFYPEER] = false;
            $arrayOption[CURLOPT_SSL_VERIFYHOST] = false;
        }
        $ch->setOptions($arrayOption);
        $result = $ch->sendByPost($postxml,$url);
        $result = OMUtil::xmlToArray($result);
        return $result;
    }

    /*
   * 发送请求的参数
   * */
    public  function getfileds($openid,$total_amout){
        $filed = array(
            'nonce_str'=>OMUtil::getNoncestr(),
            'mch_billno'=>$this->wxMchBillno(),
            'mch_id'=>TMConstant::$weixinauth['MATCHID'],
            'wxappid'=>TMConstant::$weixinauth['appID'],
            'nick_name'=>TMConstant::$weixinauth['nickName'],
            'send_name'=>TMConstant::$weixinauth['sendName'],
            're_openid'=>$openid,
            'total_amount'=>$total_amout,
            'min_value'=>$total_amout,
            'max_value'=>$total_amout,
            'total_num'=>1,
            'wishing'=>TMConstant::$weixinauth['wishing'],
            'client_ip'=>TMUtil::getClientIp(),
            'act_name'=>TMConstant::$weixinauth['actName'],
            'remark'=>TMConstant::$weixinauth['remark'],
            'logo_imgurl'=>TMConstant::$weixinauth['logoImgUrl'],
            'share_content'=>TMConstant::$weixinauth['shareContent'],
            'share_imgurl'=>TMConstant::$weixinauth['shareImgUrl'],
            'share_url'=>TMConstant::$weixinauth['shareUrl']
        );
        return $filed;
    }

    /**
     *微信红包发送成功记录流水记录
     *
     */
    public static function redpackhostory($result)
    {
        $ts = new TMService();
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            //记录流水信息
            $data=array(
                'FMchBillNo'=>$result['mch_billno'],
                'FReOpenId'=>$result['re_openid'],
                'FTotalAmount'=>$result['total_amount'],
                'FReturnCode'=>$result['return_code'],//通信标示
                'FReturnMsg'=>$result['return_msg'],//返回信息
                'FResultCode'=>$result['result_code'],//交易结果
                'FSendTime'=>date('Y-m-d H:i:s'),
                'FSendDate'=>date('Y-m-d')
            );
            $ts->insert($data,TMConstant::$DB_TABLE_NAME['DB_TABLE_SENDREDPACK_HISTORY']);
            return $ts->getAffectedRowNum();
        }else{
            return 0;
        }
    }
    //生成28位的订单号
    public  function wxMchBillno() {
        return date("Ymd",time()).time().TMConstant::$weixinauth['MATCHID'];
    }
}