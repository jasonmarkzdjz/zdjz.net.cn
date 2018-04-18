<?php
/**
 * common function of development
 *
 * @version 1.0
 */
class OMPublicToolkit {

    /**
     * 加锁服务
     *
     * @param string $className class name
     * @param string $methodName action name
     * @param string $uin user qq number
     * @param int $seconds expire time
     * @return boolean
     */
    public static function setLock($key, $seconds = 1) {
        if (empty ( $key )) return false;
        if (LockService::lock ( TMConfig::get ( 'tame_id' ) . '_' . $key . '_lockFilter', $seconds )) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * 请求接口服务
     *
     * @param string $URI 接口服务RUI e.g: /weibo/t/add, /im/get_userinfo
     * @param array $param 请求参数
     * @return array 返回结果同接口服务
     */
    public static function requestInterfaceService($URI, $param = array(), $isPost = true) {
        $url = 'http://labs.api.act.qq.com/' . TMConfig::get ( 'tams_id' ) . $URI;
        $curl = new TMCurl ( $url );
        $curl->setHttpProxy ();
        if ($isPost) $curl->sendByPost ( $param );
        else $curl->sendByGet ( $param );
        return $curl->execute ();
    }
    
    public static function requestInterfaceServiceByHost($URI, $param = array(), $isPost = true) {
        $url = 'http://14.17.43.211/' . TMConfig::get ( 'tams_id' ) . $URI;
        $curl = new TMCurl ( $url );
        $curl->setVHost('labs.api.act.qq.com');
        //$curl->setHttpProxy ();
        if ($isPost) $curl->sendByPost ( $param );
        else $curl->sendByGet ( $param );
        return $curl->execute ();
    }

    /**
     * 计算两日期间的时间差
     *
     * @param date $begin 开始时间 如：2013-09-12 13：45：48 或 2013-09-12
     * @param date $end 结束时间 如：2013-09-12 13：45：48 或 2013-09-12
     * @param string $part 初始化字符如：y,m,w,d,h,n,s
     * @return integer boolean
     */
    public static function dateDiff($begin, $end, $part = 'd') {
        $beginTimeStamp = strtotime ( $begin );
        $endTimeStamp = strtotime ( $end );
        if (! $beginTimeStamp || ! $endTimeStamp) {
            return false;
        }
        $diff = $endTimeStamp - $beginTimeStamp;
        switch ($part) {
            case "y" :
                $retval = floor ( $diff / (60 * 60 * 24 * 365) );
                break;
            case "m" :
                $retval = floor ( $diff / (60 * 60 * 24 * 30) );
                break;
            case "w" :
                $retval = floor ( $diff / (60 * 60 * 24 * 7) );
                break;
            case "d" :
                $retval = floor ( $diff / (60 * 60 * 24) );
                break;
            case "h" :
                $retval = floor ( $diff / (60 * 60) );
                break;
            case "n" :
                $retval = floor ( $diff / 60 );
                break;
            case "s" :
                $retval = $diff;
                break;
        }
        return $retval;
    }

    /**
     * 为某个固定日期增加日期
     *
     * @param date $date 原有固定的日期 如：2013-09-11 23:12:32
     * @param int $number 增加日期長度
     * @param string $part 增加日期格式 如y,q,m,w,d,h,n,s 分别为年（默认），季度，月，星期，天，小时，分钟，秒
     * @param string $format 輸出格式
     * @return string 按指定的輸出格式返回日期字符串
     */
    public static function dateAdd($date, $number, $part = 'y', $format = 'Y-m-d H:i:s') {
        $date_array = getdate ( strtotime ( $date ) );
        $hor = $date_array ["hours"];
        $min = $date_array ["minutes"];
        $sec = $date_array ["seconds"];
        $mon = $date_array ["mon"];
        $day = $date_array ["mday"];
        $yar = $date_array ["year"];
        switch ($part) {
            case "y" :
                $yar += $number;
                break;
            case "q" :
                $mon += ($number * 3);
                break;
            case "m" :
                $mon += $number;
                break;
            case "w" :
                $day += ($number * 7);
                break;
            case "d" :
                $day += $number;
                break;
            case "h" :
                $hor += $number;
                break;
            case "n" :
                $min += $number;
                break;
            case "s" :
                $sec += $number;
                break;
        }
        return date ( $format, mktime ( $hor, $min, $sec, $mon, $day, $yar ) );
    }

    /**
     * 二维数组排序
     *
     * @param array $arrayData 被排序的数组 注：必须为二维数组，否则将原样返回
     * @param string $keyName 需要按照排序的键名
     * @param string $sortOrder1 排序标准
     *            (SORT_ASC,SORT_DESC,SORT_NATURAL,SORT_FLAG_CASE)
     * @param string $sortOrder2 排序类型( SORT_REGULAR,SORT_NUMERIC,SORT_STRING)
     * @return array boolean
     */
    static function sortArray($arrayData, $keyName, $sortOrder1 = "SORT_ASC", $sortOrder2 = "SORT_REGULAR") {
        if (! is_array ( $arrayData )) {
            return false;
        }

        $newKeyArray = array();
        $sortArr = array();
        $sortedArr = array();

        foreach ( $arrayData as $key => $val ) {
            $secretKey = base64_encode ( $key );
            $newKeyArray [$secretKey] = $val;
            if (isset ( $val [$keyName] )) $sortArr [$secretKey] = $val [$keyName];
            else $sortArr [$secretKey] = '';
        }

        array_multisort ( $sortArr, $sortOrder1, $sortOrder2 );
        foreach ( $sortArr as $key => $val ) {
            $sortedArr [base64_decode ( $key )] = $newKeyArray [$key];
        }

        return $sortedArr;
    }

    /**
     * 檢查日期格式是否正確
     *
     * @param date $date 验证的日期
     * @param string $format 格式类型
     * @return boolean
     */
    public static function validateDate($date, $format = 'YYYY-MM-DD') {
        switch ($format) {
            case 'YYYY/MM/DD' :
            case 'YYYY-MM-DD' :
                list( $y, $m, $d ) = preg_split ( '/[-.\/ ]/', $date );
                break;

            case 'YYYY/DD/MM' :
            case 'YYYY-DD-MM' :
                list( $y, $d, $m ) = preg_split ( '/[-.\/ ]/', $date );
                break;

            case 'DD-MM-YYYY' :
            case 'DD/MM/YYYY' :
                list( $d, $m, $y ) = preg_split ( '/[-.\/ ]/', $date );
                break;

            case 'MM-DD-YYYY' :
            case 'MM/DD/YYYY' :
                list( $m, $d, $y ) = preg_split ( '/[-.\/ ]/', $date );
                break;

            case 'YYYYMMDD' :
                $y = substr ( $date, 0, 4 );
                $m = substr ( $date, 4, 2 );
                $d = substr ( $date, 6, 2 );
                break;

            case 'YYYYDDMM' :
                $y = substr ( $date, 0, 4 );
                $d = substr ( $date, 4, 2 );
                $m = substr ( $date, 6, 2 );
                break;

            default :
                return false;
        }
        return checkdate ( $m, $d, $y );
    }

    /**
     * 检查是否过期
     *
     * @param date $date 验证的日期
     * @param string $format 格式类型
     * @return boolean
     */
    public static function isExpired($expire = '2014-12-25 23:59:59') {
        return time() > strtotime($expire);
    }
}