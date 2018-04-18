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
 * payapitController
 *
 * @package controllers
 * @author  qrangechen <qrangechen@tencent.com>
 * @version defaultController.class.php 2015-04-07 by Jerom
 */

class WechatPayApi{

        private $body = null;//商品描述
        private $Outtradeno = null;//商户系统内部的订单号,32个字符内、可包含字母
        private $Totalfee = null;
        private $Spbillcreateip = null;//订单生成的机器IP
        private $Timestart = null;//订单生成时间
        private $Timeexpire = null;//订单失效时间
        private $Notifyurl = null;//接收微信支付成功异步通知
        private $Tradetype = 'JSAPI';//交易类型JSAPI、NATIVE、APP
        private $Openid = null;//用户在商户appid下的唯一标识，trade_type为JSAPI时，此参数必传
        private $Goodstag = null;//商品标记
        private $noncestr = null;//随机字符串
        private $transaction_id = null;//申请退款是使用。
        private $refund_fee = null;
        /**
         * 服务实例
         * @var RankService
         */
        private static $instance;

        public static function getInstance($total,$openid,$body,$Goodstag,$transaction_id=null,$refund_fee=null) {
            if (empty(self::$instance)) {
                self::$instance = new self($total,$openid,$body,$Goodstag,$transaction_id,$refund_fee);
            }
            return self::$instance;
        }
        public function __construct($total,$openid,$body,$Goodstag,$transaction_id=null,$refund_fee=null){
            $this->body = $body;
            $this->Openid=$openid;
            $this->Totalfee= $total;
            $this->Notifyurl=TMConstant::$weixinauth['NOTIFY_URL'];
            $this->Outtradeno=OMUtil::wxMchBillno();
            $this->Timestart=date('YmdHis');
            $this->Timeexpire=date("YmdHis", time() + 600);
            $this->Spbillcreateip=TMUtil::getClientIp();
            $this->Goodstag=$Goodstag;
            $this->noncestr=OMUtil::getNoncestr(32);
            $this->transaction_id=$transaction_id;
        }
        //发起支付
        public  function apiPay(){
            $paryorder = new PayUnifiedOrder();
            $total = $this->Totalfee * 100;
            $paryorder->SetBody($this->body);//商品描述
            $paryorder->SetOut_trade_no($this->Outtradeno);//商户系统内部的订单号,32个字符内、可包含字母
            $paryorder->SetTotal_fee($total);//订单总金额，单位为分，不能带小数点
            $paryorder->SetSpbill_create_ip(TMUtil::getClientIp());//订单生成的机器IP
            $paryorder->SetTime_start($this->Timestart);//订单生成时间
            $paryorder->SetTime_expire($this->Timeexpire);
            $paryorder->SetNotify_url($this->Notifyurl);//微信支付成功的回调地址
            $paryorder->SetGoods_tag($this->Goodstag);//商品标记
            $paryorder->SetTrade_type($this->Tradetype);
            $paryorder->SetOpenid($this->Openid);
            $paryorder->SetNonce_str($this->noncestr);
            $order = WxPayApi::unifiedOrder($paryorder);
            return $order;
        }
        //申请退款
        public function apirefund(){
            $refoundata = new PayRefund();
            //若同时微信订单号和Out_trade_no存在则优先级微信订单号
            if(!empty($this->transaction_id) && !empty($this->Outtradeno)){
                $refoundata->SetTransaction_id($this->transaction_id);
            }
            if(isset($this->transaction_id) && !empty($this->transaction_id)){
                $refoundata->SetTransaction_id($this->transaction_id);
            }
            if(isset($this->Outtradeno) && !empty($this->Outtradeno)){
                $refoundata->SetOut_trade_no($this->Outtradeno);
            }
            $refoundata->SetOut_refund_no(TMConstant::$weixinauth['MATCHID'].date("YmdHis"));
            $refoundata->SetRefund_fee($this->refund_fee);
            $refoundata->SetOp_user_id(TMConstant::$weixinauth['MATCHID']);
            $refoundata->SetNonce_str(OMUtil::getNoncestr(32));
            $refund = WxPayApi::refund($refoundata);
            return $refund;
        }
        /**
         *
         * 获取jsapi支付的参数
         * @param array $UnifiedOrderResult 统一支付接口返回的数据
         * @throws WxPayException
         *
         * @return json数据，可直接填入js函数作为参数
         */
        public static  function GetJsApiParameters($OrderResult)
        {
            if(!array_key_exists("appid",$OrderResult)||!array_key_exists("prepay_id",$OrderResult)||$OrderResult['prepay_id']=="")
            {
               return array();
            }
            $jsapi = new WxPayJsApiPay();
            $jsapi->SetAppid($OrderResult["appid"]);
            $jsapi->SetTimeStamp(time());
            $jsapi->SetNonceStr(OMUtil::getNoncestr());
            $jsapi->SetPackage("prepay_id=" . $OrderResult['prepay_id']);
            $jsapi->SetSignType("MD5");
            $jsapi->SetPaySign($jsapi->MakeSign());
            $parameters = json_encode($jsapi->GetValues());
            return $parameters;
        }
}