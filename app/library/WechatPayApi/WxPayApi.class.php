<?php
/**
 * 
 * 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）
 * @author widyhu
 *
 */
class WxPayApi
{
	/**
	 * 
	 * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param PayUnifiedOrder $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function unifiedOrder($inputObj)
	{
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet()) {
            return json_encode(array('code'=>101,'message'=>'订单号不能为空'));
		}else if(!$inputObj->IsBodySet()){
            return json_encode(array('code'=>102,'message'=>'商品描述不能为空'));
		}else if(!$inputObj->IsTotal_feeSet()) {
			return json_encode(array('code'=>103,'message'=>'充值金额不能为空'));
		}
		if($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()){
            return json_encode(array('code'=>104,'message'=>'openid不能为空'));
		}
		//异步通知url未设置，则使用配置文件中的url
		if(!$inputObj->IsNotify_urlSet()){
			$inputObj->SetNotify_url(TMConstant::$weixinauth['NOTIFY_URL']);//异步通知url
		}
		$inputObj->SetAppid(TMConstant::$weixinauth['appID']);//公众账号ID
		$inputObj->SetMch_id(TMConstant::$weixinauth['MATCHID']);//商户号
		$inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip	  
		$inputObj->SetNonce_str(OMUtil::createNoncestr(32));//随机字符串
		$inputObj->SetSign();//签名
		$xml = $inputObj->ToXml();
		$response = self::postXmlCurl($xml, $url, false, 20);
        //xml转成array数组
		$result = OMUtil::xmlToArray($response);
		return $result;
	}
	
	/**
	 * 
	 * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param PayOrderQuery $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function orderQuery($inputObj, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/orderquery";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            return json_encode(array('code'=>105,'message'=>'微信订单号或商户订单号选填一个'));
		}
		$inputObj->SetAppid(TMConstant::$weixinauth['appid']);//公众账号ID
		$inputObj->SetMch_id(TMConstant::$weixinauth['MCHID']);//商户号
		$inputObj->SetNonce_str(OMUtil::createNoncestr(32));//随机字符串
		$inputObj->SetSign();//签名
		$xml =OMUtil::arrayToXml($inputObj);
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result =OMUtil::xmlToArray($response);

		return $result;
	}
	/**
	 * 
	 * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
	 * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param PayRefund $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function refund($inputObj, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            return json_encode(array('code'=>105,'message'=>'微信订单号或商户订单号选填一个'));
		}else if(!$inputObj->IsOut_refund_noSet()){
            return json_encode(array('code'=>106,'message'=>'商户退款单号不能为空'));
		}else if(!$inputObj->IsTotal_feeSet()){
            return json_encode(array('code'=>107,'message'=>'商户总金额不能为空'));
		}else if(!$inputObj->IsRefund_feeSet()){
            return json_encode(array('code'=>108,'message'=>'退款金额不能为空'));
		}else if(!$inputObj->IsOp_user_idSet()){
            return json_encode(array('code'=>109,'message'=>'操作人员不能为空'));
		}
		$inputObj->SetAppid(TMConstant::$weixinauth['appid']);//公众账号ID
		$inputObj->SetMch_id(TMConstant::$weixinauth['MCHID']);//商户号
		$inputObj->SetNonce_str(OMUtil::createNoncestr(32));//随机字符串
		$inputObj->SetSign();//签名
		$xml = OMUtil::arrayToXml($inputObj);
		$response = self::postXmlCurl($xml, $url, true, $timeOut);
		$result = OMUtil::xmlToArray($response);
		return $result;
	}
	
	/**
	 * 
	 * 查询退款
	 * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
	 * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
	 * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayRefundQuery $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function refundQuery($inputObj, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/refundquery";
		//检测必填参数
		if(!$inputObj->IsOut_refund_noSet() &&
			!$inputObj->IsOut_trade_noSet() &&
			!$inputObj->IsTransaction_idSet() &&
			!$inputObj->IsRefund_idSet()) {
            return json_encode(array('code'=>110,'message'=>'退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个'));
		}
        $inputObj->SetAppid(TMConstant::$weixinauth['appid']);//公众账号ID
        $inputObj->SetMch_id(TMConstant::$weixinauth['MCHID']);//商户号
		$inputObj->SetNonce_str(OMUtil::createNoncestr(32));//随机字符串
		$inputObj->SetSign();//签名
		$xml = OMUtil::arrayToXml($inputObj);
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result =OMUtil::xmlToArray($response);
		return $result;
	}
	

	
	private static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
	{
        try{
            $curl = new TMCurl($url);
            if('test' == TMUtil::getServerType()) {
                $arrayOption[CURLOPT_SSL_VERIFYPEER] = false;
                $arrayOption[CURLOPT_SSL_VERIFYHOST] = false;
            }else{ //开启证书验证
                $arrayOption = array(
                    CURLOPT_SSLCERTTYPE => 'PEM',
                    CURLOPT_SSLCERT => ROOT_PATH . 'library/cert/apiclient_cert.pem',
                    CURLOPT_SSLKEYTYPE => 'PEM',
                    CURLOPT_SSLKEY => ROOT_PATH . 'library/cert/apiclient_key.pem',
                    CURLOPT_HEADER => FALSE,//禁止头包含在输出中
                    CURLOPT_RETURNTRANSFER => TRUE,//把CRUL获取的内容赋值到变量
                    CURLOPT_SSL_VERIFYPEER=>true,
                    CURLOPT_SSL_VERIFYHOST=>2
                );
            }
            $curl->setOptions($arrayOption);
            $result = $curl->sendByPost($xml,$url);
            return $result;
        }catch (TMException $e){
            return array();
        }
	}
}

