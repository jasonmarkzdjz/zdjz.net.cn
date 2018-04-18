<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/1/7
 * Time: 16:08
 */
class OMUtil{

    //获取access_token
    public static function getAccessToken(){
        $accesstoken = self::getMemcache();
        if(!isset($accesstoken) || empty($accesstoken)){
            $param = array(
                'appid'=>TMConstant::$weixinauth['appid'],
                'secret'=>TMConstant::$weixinauth['appsecret'],
                'grant_type'=>'client_credential'
            );
            $curl = new TMCurl();
            $curl->setOptions(
                array(
                    CURLOPT_CONNECTTIMEOUT => 2,
                    CURLOPT_SSL_VERIFYPEER => FALSE
                )
            );
            $curl->setHttpProxy ();
            $result = $curl->sendByGet($param,TMConstant::$weixinauth['ACCTOKEN_URl']);
            $result = json_decode($result,true);
            $accesstoken = isset($result['access_token']) ? $result['access_token'] :'';
            self::setMemcache($accesstoken);
        }
        return $accesstoken;
    }

    //缓存设置方法
    public static function setMemcache($value){
        $key = TMConstant::CACHEKEY.TMConfig::get('tams_id');
        $mem = TMMemCacheMgr::getInstance();
        $mem->set($key,$value,600);
        return true;
    }

    //获取缓存数据
    public static function getMemcache(){
        $key = TMConstant::CACHEKEY.TMConfig::get('tams_id');
        $mem = TMMemCacheMgr::getInstance();
        $value = $mem->get($key);
        return $value;
    }

    /**
     * 产生随机字符串，不长于32位
     * @param $length —— 字符串的长度
     * @return string
     */
    public static function getNoncestr($length = 32){
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = "";
        for ($i=0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    //生成28位的订单号
    public static function wxMchBillno() {
        return date("Ymd",time()).time().TMConstant::$weixinauth['MATCHID'];
    }

    /**
     * 发送红包生成签名
     * @param $obj —— url键值对
     */
    public static function getSign($Obj)
    {
        $Parameters = array();
        foreach ($Obj as $k => $v)
        {
            $Parameters[strtolower($k)] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = self::formatBizQueryParaMap($Parameters, false);
        //echo "【string】 =".$String."</br>";
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".TMConstant::$weixinauth['MatchSecret'];
        //echo "【string】 =".$String."</br>";
        //签名步骤三：MD5加密
        $result_ = strtoupper(md5($String));
        return $result_;
    }

    /**
     * 	作用：格式化参数，签名过程需要使用
     *  @param $paraMap —— URL键值对
     *  @param $urlencode —— 是否进行url编码
     */
    public static function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= strtolower($k) . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     * 	作用：键值对array转成xml字符串
     *  @param $arr —— 键值对数组
     *  @return $xml —— xml格式的字符串
     */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val))
            {
                $xml.="<".$key.">".$val."</".$key.">";

            }
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     * 将xml转为array
     * @param $xml —— xml格式的字符串
     * @return $array_data —— 数组
     */
    public static function xmlToArray($xml){
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /*
     * 判断是否是微信来源
     *
     * */
    public static function isWeixinRequest(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false) {
            return false;
        }
        return true;
    }

    public static function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }

    //判断是否是本次活动参与的域名已经测试域名
    public static function checkactiverurl($redirect){
        $truck = false;
        $parseurl_list = parse_url($redirect);
        $parseurl = isset($parseurl_list['host']) && !empty($parseurl_list['host']) ? 'http://'.$parseurl_list['host']:'';
        if($parseurl){
            foreach(TMConstant::$trust_servers as $server){
                if(0 === strpos($parseurl,$server)){
                    $truck = true;
                    break;
                }
            }
            if(!$truck){
                $redirect = TMConstant::SERVER_NAME;
            }
        }else{
            $redirect = TMConstant::SERVER_NAME;
        }
        return $redirect;
    }

    public static function requestByCURL($URI, $isPost = true,$param = array()){
        $curl = new TMCurl($URI);
        $curl->setHttpProxy ();
        $curl->setOptions(array(
            CURLOPT_AUTOREFERER=>1,
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_CONNECTTIMEOUT=>2,
            CURLOPT_SSL_VERIFYPEER=>FALSE,
            CURLOPT_SSL_VERIFYHOST=>FALSE,
        ));
        if($isPost){
            return $curl->sendByPost($param);
        }else{
            return $curl->sendByGet($param);
        }
    }
    /*
    * 不转义中文的json_encode方法
    */
    public static function encode ($arr) {
        $str = json_encode($arr);
        $search = "#\\\u([0-9a-f]{4})#ie";
        $replace = "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))";
        return preg_replace($search, $replace, $str);
    }
    /*
    *
    *微信生成二维码ticket
    *
    */
    public static function getQcodeTicket(){
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.OMUtil::getAccessToken();
        $couponresult = OMUtil::requestByCURL($url,false);
        return json_decode($couponresult,true);
    }
    //微信通过ticket生成二维码
    public static function showCouponQcode(){
        $couponticket = self::getQcodeTicket();
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($couponticket['ticket']);
        $result = OMUtil::requestByCURL($url,false);
        return json_decode($result,true);
    }

}