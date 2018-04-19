<?php
/**
 * defaultController
 *
 * @package controllers
 * @author  jason <jason@tencent.com>
 * @version defaultController.class.php 2015-04-07 by jason
 */
class defaultController extends TMController{
    public function defaultAction(){
        $mem = TMMemdCacheMgr::getInstance();

        $votes= $mem->increment('age');
        echo $votes;

        $redis = RedisOtherService::getInstance();
        $redis->cset('ages',2,300);
        $age = $redis->get('ages');
        print_r($age);
//
//        $MQClient = TMMQClient::getInstance();
//        $result = $MQClient->publishMessage(json_encode(array('id'=>rand(1,10000))), TMConfig::get('tams_id').'project_votes');
//        var_dump($result);
//        $MQClient = TMMQClient::getInstance();
//        $result = $MQClient->getMessage(TMConfig::get('tams_id').'project_votes');print_r($result);
//        $mq = new MQ();
//        $rest = $mq->put(TMConfig::get('tams_id').'votes',json_encode(json_encode(array('id'=>rand(1,10000).$votes))));
//        var_dump($rest);
//        $result = $mq->get(TMConfig::get('tams_id').'votes');
//        var_dump($result);
//        $incr = RedisOtherService::getInstance()->increment('vote_'.'920');
//        RedisOtherService::getInstance()->setNoticMessage('user:'.$incr.'_votes','votes',1);
//        echo $incr;
//        RedisOtherService::getInstance()->cset('mem_common_notice',serialize(array('id'=>rand(1,10000).$incr)),1200);
    }
}