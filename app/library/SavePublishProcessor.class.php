<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/1/7
 * Time: 16:33
 */
class SavePublishProcessor{

    //消息队列生产者
    public static function publishMessage($message){
        $MQclient = TMMQClient::getInstance();
        //数据入队列
        return $MQclient->publishMessage(new TMMQMessage($message),TMConstant::MQ_REDPACK);
    }
}