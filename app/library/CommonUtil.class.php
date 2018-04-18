<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2016/12/16
 * Time: 11:24
 */

class CommonUtil {

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

    //检查微信是否登录
    public static function checkAuth(){
        $openid = OMHttpRequest::getCookie(WEchatService::OPENID_COOKIE);
        $openidseret = OMHttpRequest::getCookie('openidseret');
        if(!empty($openid) && !empty($openidseret) && (self::encrypt($openid) === $openidseret) ){
            return true;
        }else{
            return false;
        }
    }
    public static function setAuth($openid){
        $openidseret = self::encrypt($openid);
        OMHttpResponse::setCookie(WEchatService::OPENID_COOKIE, $openid);
        OMHttpResponse::setCookie('openidseret', $openidseret);
    }

    /**
     * 加密
     * @param string $data
     * @return string
     */
    public static function encrypt($data){
        $aes = new AES(Constant::$matchkey);
        return base64_encode($aes->encrypt($data));
    }
    /**
     * 解密
     * @param string $encrypted
     * @return string
     */
    public static function  decrypt($encrypted){
        $aes = new AES(Constant::$matchkey);
        return $aes->decrypt(base64_decode($encrypted));
    }


    //判断是否是合法的openid
    public static function ischeckopenid($openid){
        $openid = trim($openid);
        $openidlength = TMUtil::getStringLength($openid);
        if($openidlength == 28){
            return true;
        }
        return false;
    }

    /*
     * 随机生成订单号
     *
     * */
    public static function getorderno(){
        list($usec, $sec) = explode(" ", microtime());
        $usec = substr(str_replace('0.', '', $usec), 0 ,5);
        $orderno = Constant::$mpconfig['MATCHID'].date('Ymd').$usec.rand(10,99999);
        return $orderno;
    }

    //对节目信息进行分组
    public static function getGroupProgramInfo($programinfo){
        $j=0;
        $result = array();
        foreach($programinfo as $k=>$v){
            if($v['FprogramId'] % 2 == 0){
                $result[$j][2] = $v;
            }else{
                $j++;
                $result[$j][1] = $v;
            }
        }
        return $result;
    }

    //入队列
    public static function IntoQueueRedpack($result){
        //发红包 更新微信红包订单状态 写入微信流水记录 全部通过消息队列处理
        try{
            $mq = new MQ(TMEnvConfig::ENV_TYPE_PRODUCTION);
            $rest = $mq->put(Constant::QueueName, $result);
            return $rest['message'];
        }catch (TMMQException $e){
            return 'error';
        }
    }
    //出队列消息处理
    public static function DeQueueRedpack($result){
        //发送红包之前检测一下发送的红包对应的用户信息是否存在或者已发放
        $id = $result['FId'];
        $tablename = $result['tablename'];
        $openid = $result['openid'];
        if(Constant::isredpack == 'on'){
            $log = new TMLog();
            $log->info($openid);
            $rest = WUserService::getRedPackSendOrderInfo($id,$tablename);
            if(!empty($rest) && $rest['FStatus'] == 1){
                //调用发送红包接口
                $packrest = WSendredpack::sendredpack($rest['FTotalAmount'],$rest['FOpenId'],$rest['FMchBillNo']);
                if(is_array($packrest) && $packrest['return_code'] == 'SUCCESS'){
                    if($packrest['result_code'] == 'SUCCESS'){
                        //更新状态以及相应信息
                        WSendredpack::setsendredpackstatus($packrest,2,$tablename);
                        //写入红包流水记录
                        WSendredpack::redpackhostory($packrest);
                    }else{
                        //更新状态以及相应信息
                        WSendredpack::setsendredpackstatus($packrest,3,$tablename);
                        //失败写入流水记录
                        WSendredpack::redpackhostory($packrest);
                    }
                }else{
                    //更新状态以及相应信息
                    WSendredpack::setsendredpackstatus($packrest,3,$tablename);
                }
            }
        }else{
            $log = new TMLog();
            $log->info($openid.$tablename);
        }
    }

    public static function getUserId($openid){
        $userId = substr(base_convert( md5($openid), 16, 10 ),0,11);
        return $userId;
    }
    public static function SetRedisCache($result,$key){
        try{
            $rediscache = TMPHPRedisClientFactory::getClient();
            $jsonstr = json_encode($result);
            $rest = $rediscache->set($key,$jsonstr);
            return $rest;
        }catch (RedisException $e){
            return 0;
        }
    }
    public static function getRedisCache($key){
        try{
            $rediscache = TMPHPRedisClientFactory::getClient();
            $rest = json_decode($rediscache->get($key),true);
            return $rest;
        }catch (RedisException $e){
            return array();
        }
    }

    //计数器记录用户猜中的数量
    public static function setGuessNum($openid,$nums){
        $userid =self::getUserId($openid);
        $rest = TaeCounterService::counterSet(Constant::TaeConunter_id,$userid,$nums);
        return $rest['cur_value'];
    }
    //获取用户盲选的猜中的数量
    public static function getGuessNum($openid){
        $userid =self::getUserId($openid);
        $rest = TaeCounterService::counterQuery(Constant::TaeConunter_id,$userid);
        return $rest['cur_value'];
    }

    //设置是否抢红包的标志
    public static function SetIsRobRedpackMarks($key,$marks){
        $rest = TaeCounterService::counterSet(Constant::TaeConunter_id,$key,$marks);
        return $rest['cur_value'];
    }
    //获取抢红包标志
    public static function getIsRobRedpackMarks($key){
        $rest = TaeCounterService::counterQuery(Constant::TaeConunter_id,$key);
        return $rest['cur_value'];
    }

    //设置领取红包标志
    public static function SetPullRedpackSign($key){
        $rest = TaeCounterService::counterAddExt(Constant::TaeConunter_id,$key,1,0,'PullRedpackSign',TaeCounterService::STRICT_MAX,1);
        return $rest['retcode'];
    }
    //获取领取红包标志
    public static function getPullRedpackSign($key){
        $rest = TaeCounterService::counterQueryExt(Constant::TaeConunter_id,$key,0,'PullRedpackSign');
        return $rest['cur_value'];
    }

    //盲选 投票 抢红包 个人限制   $key：userid
    public static function SetUserVoiceVoteGrabsLimit($key,$extra_strkey){
        $rest = TaeCounterService::counterAddExt(Constant::TaeConunter_id,$key,1,0,$extra_strkey,TaeCounterService::STRICT_MAX,1);
        return $rest['retcode'];
    }

    //盲选 投票 红包限制   常量key+节目id
    public static function SetVoiceVoteGuessRedPackLimit($key,$maxnums,$extra_strkey){
        $rest = TaeCounterService::counterAddExt(Constant::TaeConunter_id,$key,1,0,$extra_strkey,TaeCounterService::STRICT_MAX,$maxnums);
        return $rest;
    }
    //个人用户正常猜中的个数$key：userid
    public static function SetUserGuessNums($key,$guessnums){
        $rest = TaeCounterService::counterSetExt(Constant::TaeConunter_id,$key,$guessnums,0,'userguessnums');
        return $rest['retcode'];
    }
}