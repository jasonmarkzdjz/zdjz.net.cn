<?php
/**
 * HTTP RESPONSE 响应方法类
 *
 * @version 1.0
 */
class OMHttpResponse {

    /**
     * 错误信息
     *
     * @var array
     */
    private static $errorMsg = array(
            'success' => array(
                    'code' => 0,
                    'message' => '操作成功'
            ),
            'notLogin' => array(
                    'code' => 101,
                    'message' => '您还未登录'
            ),
            'verifyCodeError' => array(
                    'code' => 105,
                    'message' => '验证码错误'
            ),
            'paramError' => array(
                    'code' => 106,
                    'message' => '请填写正确的信息'
            ),
            'exchangeCodeError' => array(
                    'code' => 107,
                    'message' => '请输入正确的QQ号'
            ),
            'systemError' => array(
                'code' => 108,
                'message' => '网络繁忙，请稍后再试'
            ),
            'cMoreLimit' => array(
            	'code' => 109,
            	'message' => '分享内容超过限制'
            ),
            'TMServiceError' => array(
            	'code' => 110,
            	'message' => '网络错误，请稍后再试'
            ),
            'QQLimitError' => array(
                'code'    => 111,
                'message' => '同一个QQ号只能领一次奖'
            ),
            'MobileLimitError' => array(
                'code'    => 112,
                'message' => '同一个手机号只能领一次奖'
            ),
            'FrequentError' => array(
                'code' => 113,
                'message' => '您操作太频繁了，请稍后重试'
            ),
            'UploadWorkError' => array(
            		'code' => 114,
            		'message' => '您今天已经没有上传作品的机会'
            ),
            'noVote' => array(
            		'code' => 118,
            		'message' => '投票信息不存在'
            ),
            'noData' => array(
            		'code' => 119,
            		'message' => '暂时没有数据'
            ),
            'noinit' => array(
            		'code' => 120,
            		'message' => '请勿重复初始化操作'
            ),
            'illegal'  => array(
            		'code' =>121,
            		'message'=>'活动已结束！'
            ),
            'activstart'  => array(
                'code' =>141,
                'message'=>'活动正在进行！'
            ),
            'noactivstart'  => array(
                'code' =>151,
                'message'=>'活动未开始'
            ),
            'nochange'  => array(
            		'code' =>122,
            		'message'=>'您已经没有投票机会了！'
            ),
            'noshare'  => array(
            		'code' =>123,
            		'message'=>'您今天已经没有分享机会了！'
            ),
            'mobileError'  => array(
                'code' =>212,
                'message'=>'手机号为空或格式错误'
            ),
            'emailError'  => array(
                'code' =>213,
                'message'=>'邮箱为空或格式错误'
            ),
            'noAwardsError'  => array(
            	'code' =>210,
            	'message'=>'对不起，您未中奖或不需要填写手机号'
            ),
            'noNeedUpdateError' => array(
            	'code' =>211,
            	'message'=>'对不起，您未抽中话费或电影票，无需更新手机号'
            ),
            'overLimit' => array(
                'code' => 221,
                'message' => '今日已达到提交上限，请明日再来',
            ),
            'tmOrderError' => array(
                'code'    => 240,
                'message' => '请输入正确的订单编号'
            ),
            'TaeError' => array(
                'code'    => 260,
                'message' => '网络错误，请稍后再试'
            ),
            'checkPicSize'=>array(
                'code'    => -523,
                'message' =>'图片尺寸超过最大限制'
            ),
            'checkPicType'=>array(
                'code'    => -520,
                 'message' =>'上传图片类型错误'
            ),
             'NOWRONGURL'=>array(
                'code'    => -522,
                 'message' =>'不合法图片URL'
            ),
            'YESPRIZE'=>array(
                'code'    => 169,
                'message' =>'已中奖'
            ),
            'NOPRIZE'=>array(
                'code'    => 189,
                'message' =>'未中奖'
            ),
            'payerror'=>array(
                'code'    => 000,
                'message' =>'支付参数错误'
            ),
            'agentError'=>array(
                'code'=>190,
                'message'=>'请在微信浏览器中打开'
            )
    );

    /**
     * 输出头信息
     *
     * @var array
     */
    private static $contentType = array(
            'json' => 'application/json',
            'html' => 'text/html',
            'xml' => 'text/xml',
            'txt' => 'text/plain'
    );

    /**
     * 设置返回http返回类型
     *
     * @param string $name 提示信息对应的键值
     * @return void
     */
    public static function setHttpHeaderContentType($name) {
        $contentType = self::$contentType [$name];
        if (! $contentType) {
            throw new TMException ( "Specified content type is not defined: $name" );
        }
        TMDispatcher::getInstance ()->getResponse ()->setHttpHeader ( 'Content-Type', $contentType );
    }

    /**
     * json数据组装
     *
     * @param string $name 返回数据key，对应HttpResponse.yml 中的returnInfo:
     * @param array $data 返回客户端数据
     */
    public static function getReturnJson($name = 'success', $data = array(), $expire = 0) {
        self::setHttpHeaderContentType ( 'json' );
        if ($expire = intval ( $expire )) {
            TMBrowserCache::cache ( $expire );
        }
        else {
            TMBrowserCache::nonCache ();
        }

        $returnInfo = self::$errorMsg [$name];
        if (! is_array ( $returnInfo )) {
            throw new TMException ( "Specified error name $name is not defined" );
        }
        if (is_array ( $data ) && count ( $data ) > 0) {
            $returnInfo = array_merge ( $returnInfo, $data );
        }
        echo exit(json_encode ( $returnInfo ));
    }

    /**
     * 设置cookie值
     *
     * @param string $name cookie名
     * @param string $value cookie值
     * @param int $expire 过期时间
     * @param string $path 存放路径
     * @param string $domain 有效域名
     * @param boolean $secure 是否加密
     * @param boolean $httpOnly 是否只适用于http
     * @return void
     */
    public static function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false) {
        TMDispatcher::getInstance ()->getResponse ()->setCookie ( $name, $value, $expire, $path, $domain, $secure, $httpOnly );
    }
}
