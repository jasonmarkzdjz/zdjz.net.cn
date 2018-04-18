<?php
/**
 * defaultController
 *
 * @package controllers
 * @author  qrangechen <jason@yeah.com>
 * @version defaultController.class.php 2015-04-07 by qrangechen
 */
class weixinController extends TMController{
    //微信授权开始
    public function WeixinAuthAction(){
        $redirect = OMHttpRequest::getPostParameter('redirect');
        if(!OMUtil::isWeixinRequest()){
            return OMHttpResponse::getReturnJson('agentError');
        }
        if(!WechatAuth::checkAuth()){
            $oauth_url = WechatAuth::weixinAuthUrl($redirect);
            return  OMHttpResponse::getReturnJson('notLogin', array('data' => array('oauth_url'=>$oauth_url)));
        }
        return OMHttpResponse::getReturnJson('success');
    }

    //微信授权成功之后的jsapi的回调地址 需要在微信公众管理平台进行配置
    public function responseAction(){
        //判断是否是微信from
        if(!OMUtil::isWeixinRequest()){
            return OMHttpResponse::getReturnJson('agentError');
        }
        $redirect = OMHttpRequest::getGetParameter('redirect');
        //新漏洞修复
        $redirect = str_replace('%0d', '', $redirect);
        $redirect = str_replace('%0a', '', $redirect);
        $redirect = str_replace('%250d', '', $redirect);
        $redirect = str_replace('%250a', '', $redirect);
        $redirect = urldecode($redirect);

        $code = OMHttpRequest::getGetParameter('code');
        $state = OMHttpRequest::getGetParameter('state');
        if(!$code){
            $this->redirect(WechatAuth::weixinAuthUrl($redirect,$state));
        }
        //获取access_token
        $authInfo = WechatAuth::getAuthInfo($code);
        if(is_array($authInfo) && isset($authInfo['access_token'])){
               $userauthinfo = WechatAuth::getUserInfo($authInfo['access_token'],$authInfo['openid']);
               $userdata = array(
                   'FOpenId'=>$userauthinfo['openid'],
                   'FNick'=>base64_encode($userauthinfo['nickname']),
                   'FNickname'=>$userauthinfo['nickname'],
                   'FSex'=>$userauthinfo['sex'],
                   'FProvince'=>$userauthinfo['province'],
                   'FCity'=>$userauthinfo['city'],
                   'FCountry'=>$userauthinfo['country'],
                   'FHead'=>$userauthinfo['headimgurl'],
                   'FTime'=>date('Y-m-d H:i:s'),
                   'FDate'=>date('Y-m-d'),
                   'FIp'=>TMUtil::getClientIp()
               );
                $redirect = TMFilterUtils::filterTag($redirect);
                $receive_server = TMConstant::SERVER_NAME;
                $trust = FALSE;
                foreach (TMConstant::$trust_servers as $server) {
                    if (0 === strpos($redirect,$server)) {
                        $receive_server = $server;
                        $trust = TRUE;
                        break;
                    }
                }
                if (!$trust){
                    $redirect = TMConstant::SERVER_NAME;
                }
                $query = http_build_query(
                    array(
                        'data'=>base64_encode(json_encode($userdata)),
                        'redirect'=>urlencode($redirect)
                    )
                );
                //跳转处理用户信息数据
                return $this->redirect($receive_server.'/weixin/receive?'.$query);
            }else{
               $this->redirect(WechatAuth::weixinAuthUrl($redirect));
        }
    }
    //二次授权
    public function receiveAction(){
        $userdata = json_decode(base64_decode(OMHttpRequest::getGetParameter('data')),true);
        $redirect = urldecode(OMHttpRequest::getGetParameter('redirect'));
        //新漏洞修复
        $redirect = str_replace('%0d', '', $redirect);
        $redirect = str_replace('%0a', '', $redirect);
        $redirect = str_replace('%250d', '', $redirect);
        $redirect = str_replace('%250a', '', $redirect);
        $redirect = urldecode($redirect);
        $redirect = WechatAuth::checkactiverurl($redirect);
        //写授权记录
        $weixininfo = WechatAuth::getWeixinAuthRecodeByOpenId($userdata['FOpenId']);

        if(!empty($weixininfo)){
            WechatAuth::setUserAuth($userdata['FOpenId']);
            return $this->redirect(WechatAuth::weixinAuthUrl($redirect));
        }else{
            WechatAuth::saveWeixinAuthRecode($userdata);
            WechatAuth::setUserAuth($userdata['FOpenId']);
            return $this->redirect($redirect);
        }
    }

    public function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TMConstant::$weixinauth['TOKEN'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    public function responseMsg(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            if($keyword == "?" || $keyword == "？"){
                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
        }else{
            echo "";
            exit;
        }
    }
}