<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/1/8
 * Time: 16:04
 * 微信授权接口
 */
   class WechatAuth{

       /**
        * 获取微信授权地址
        * @return string 微信授权地址
        */
       public static function weixinAuthUrl($redirect,$state = ''){
           $redirect =$redirect;
           $redirecturl = TMConstant::SERVER_NAME.TMConstant::$weixinauth['OAUTH_REDIRECT'].'?redirect='.urlencode($redirect);
           $query = http_build_query(array(
                   'appid'=>TMConstant::$weixinauth['appID'],
                   'redirect_uri'=>$redirecturl,
                   'response_type'=>'code',
                   'scope'=>TMConstant::$weixinauth['USERINFO'],
                   'state'=>$state
               )
           );
           return TMConstant::$weixinauth['WEIXIN_OAUTH_URL'].'?'.$query.'#wechat_redirect';
       }
       /**
        * 微信授权后获取openid,access_token
        * @return array(
        * 'access_token'=>ACCESS_TOKEN,    //网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
        * 'expires_in'=>7200,                //access_token接口调用凭证超时时间，单位（秒）
        * 'refresh_token'=>REFRESH_TOKEN,    //用户刷新access_token
        * 'openid'=>OPENID,
        * 'scope'=>SCOPE
        * )
        */
       public static function getAuthInfo($code){

           try{
               $curl = new TMCurl(TMConstant::$weixinauth['WEIXIN_ACCESS_TOKEN']);
               $param = array(
                   'appid'=>TMConstant::$weixinauth['appID'],
                   'secret'=>TMConstant::$weixinauth['appsecret'],
                   'code'=>$code,
                   'grant_type'=>'authorization_code'
               );
               if(TMUtil::getServerType() === 'test'){
                   $curl->setOptions(
                       array(
                           CURLOPT_SSL_VERIFYPEER => false,
                           CURLOPT_SSL_VERIFYHOST => false
                       )
                   );
               }
               $curl->setExtranetHttpProxy();
               $restult = $curl->send($param);
               return json_decode($restult,true);
           }catch (TMException $e){
               return array();
           }
       }
       /**
        *@param 通过access_token和openid拉取用户信息
        *
        */
       public static function getUserInfo($access_token,$openid){
           try{
               $curl = new TMCurl(TMConstant::$weixinauth['WEIXIN_USERINFO']);
               $param = array(
                   'access_token'=>$access_token,
                   'openid'=>$openid,
                   'lang'=>TMConstant::$weixinauth['LANG']
               );
               if(TMUtil::getServerType() === 'test'){
                   $curl->setOptions(
                       array(
                           CURLOPT_SSL_VERIFYPEER => false,
                           CURLOPT_SSL_VERIFYHOST => false
                       )
                   );
               }
               $curl->setExtranetHttpProxy();
               $userinfo = $curl->send($param);
               return json_decode($userinfo,true);
           }catch (TMException $e){
               return array();
           }
       }
        /**
         *@param 写入cookie
         *
         */
       public static function setUserAuth($openid) {
           //保存cookie
           $path = str_replace('.',' ',TMConfig::get('domain'));
           $cipher = self::encrypt($openid);
           if (FALSE === strpos($_SERVER['HTTP_HOST'], 'sh.act.qq.com')) {
               OMHttpResponse::setCookie('openid', $openid);
               OMHttpResponse::setCookie('cipher', $cipher);
           } else {
               OMHttpResponse::setCookie('openid', $openid, 0, '/'.$path.'/','sh.act.qq.com');
               OMHttpResponse::setCookie('cipher', $cipher, 0, '/'.$path.'/','sh.act.qq.com');
           }
       }
       /**
        *@param 检查是否授权
        */
       public static function checkAuth(){
           $openid = OMHttpRequest::getCookie('openid');
           $cipher = OMHttpRequest::getCookie('cipher');
           if(!empty($openid) && !empty($cipher) && (self::encrypt($openid) === $cipher) ){
               $userrecode = self::getWeixinAuthRecodeByOpenId($openid);
               if(!empty($userrecode)){
                   return true;
               }else{
                   return false;
               }
           }else{
               return false;
           }
       }
       /**
        *@param 获取微信opendid
        *@return  string
        */
       public static function getweixinopenid(){
           return OMHttpRequest::getCookie('openid');
       }
       /**
        * 加密
        * @param string $data
        * @return string
        */
       public static function encrypt($data){
           $aes = new AES(TMConstant::$weixinauth['appsecret']);
           return base64_encode($aes->encrypt($data));
       }
       /**
        * 解密
        * @param string $encrypted
        * @return string
        */
       public static function  decrypt($encrypted){
           $aes = new AES(TMConstant::$weixinauth['appsecret']);
           return $aes->decrypt(base64_decode($encrypted));
       }
        /**
         *@param 判断是否是本次活动参与的域名已经测试域名
         * @return bool
         *
         */
        public static function checkactiverurl($redirect){
            $truck = false;
            foreach(TMConstant::$trust_servers as $server){
                if(0 === strpos($redirect,$server)){
                    $truck = true;
                    break;
                }
            }
            if(!$truck){
                $redirect = TMConstant::SERVER_NAME;
            }
            return $redirect;
        }
         /**
          *@param 查询db是否存在授权记录
          *@return array
          */
        public static function getWeixinAuthRecodeByOpenId($openid){
            try{
                $ts =  new TMService();
                $ts->initDb();
                $result = $ts->selectOne(array('FOpenId'=>$openid),'FOpenId,FUserId','Tbl_WeixinAuthUser',null,null,MYSQLI_ASSOC);
                 return $result;
            }catch (TMMysqlException $e){
                return array();
            }
        }
       /**
        * @param 写入授权记录
        * @return bool
        */
       public static function saveWeixinAuthRecode($data){
            try{
                $ts = new TMService();
                $ts->initDb();
                $ts->insert($data,'Tbl_WeixinAuthUser');
                return $ts->getInsertId();
            }catch (TMMysqlException $e){
                return false;
            }
        }
       /**
        *@param 红包发放接口
        * @return  bool
        */
       public static function sendReadPack($reOpenid,$money){
           $pack = WechatRedpack::getInstance();
           $result = $pack->redPackSend($reOpenid,$money);
           //更新订单状态 //记录红包流水记录
           $recode = $pack->redpackhostory($result);
           return $recode;
       }
 }