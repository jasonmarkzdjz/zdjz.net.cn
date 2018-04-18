<?php
/**
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
 * 棠溪MQ接口访问类
 *
 * @package sdk.src.framework.mq
 * @author  caseycheng <caseycheng@tencent.com>
 * @version $Id: MQ.class.php 68 2016-11-16 08:19:01Z caseycheng $
 */
class MQ
{
    /**
     *
     * 适配器调用实体对象
     * @var mixed
     */
    protected $adaptee;

    /**
     * $env = production|beta|dev
     * @param string $env
     * @throws TMException
     */
    public function __construct($options = array())
    {
        // 自定义参数不需要读取配置参数
        if (empty($options))
        {
            $mqConfig = TMConfig::get("mq", "httpsqs");

            if(empty($mqConfig))
            {
                throw new TMConfigException("mq's config not exist");
            }
        } else {
            $mqConfig = array();
        }

        $mqConfig = array_merge($mqConfig, $options);

        $host = isset($mqConfig["host"]) ? $mqConfig["host"] : '127.0.0.1';
        $port = isset($mqConfig["port"]) ? $mqConfig["port"] : 1218;
        $auth = isset($mqConfig["auth"]) ? $mqConfig["auth"] : '';
        $this->adaptee = new HttpSQS($host, $port, $auth);
    }

    public static function signature($pid, $timestamp) {
        $appkey = TMConfig::get('appkey');
        if(empty($appkey)) {
            throw new TMMQException('appkey配置不存在');
        }
        $tmpArr = array($appkey, $pid, $timestamp);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        return $tmpStr;
    }
    public function put($queue, $body)
    {
        $result = $this->adaptee->put($queue, $body);
        return $result;
    }

    public function get($queue)
    {
        $result = $this->adaptee->get($queue);
        return $result;
    }



    /**
     * 加个加密签名
     * @param $url
     * @param $params
     * @param bool|true $post
     * @return string
     */
    private function request($params, $url, $post = true)
    {

        if($this->domain) {
            $this->curl->setVHost($this->domain);
        }
        $log = new TMLog(ROOT_PATH . 'log/mq/mq.system.log', true, false);
        $this->curl->setHttpProxy();
        try {
            $ret = $this->curl->send($params, $post, $url);
            $ret = json_decode($ret, true);
            return $ret;
        } catch (TMRemoteException $e) {
            $ret = array('code' => -1, 'message' => '网络错误');
            return $ret;
        }
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }

}
