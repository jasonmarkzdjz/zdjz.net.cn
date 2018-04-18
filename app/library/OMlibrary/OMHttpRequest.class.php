<?php
/**
 * HTTP REQUEST 请求方法类
 *
 * @version 1.0
 */
class OMHttpRequest {

    /**
     * 获取参数值
     *
     * @param string $name 参数名
     * @param string $default 默认值
     * @return string
     */
    public static function getParameter($name, $default = null) {
        return TMDispatcher::getInstance ()->getRequest ()->getParameter ( $name, $default );
    }

    /**
     * 获取 POST 参数值
     *
     * @param string $name 参数名
     * @param string $default 默认值
     * @return string
     */
    public static function getPostParameter($name, $default = null) {
        return TMDispatcher::getInstance ()->getRequest ()->getPostParameter ( $name, $default );
    }

    /**
     * 获取GET参数值
     *
     * @param string $name 参数名
     * @param string $default 默认值
     * @return string
     */
    public static function getGetParameter($name, $default = null) {
        return TMDispatcher::getInstance ()->getRequest ()->getGetParameter ( $name, $default );
    }

    /**
     * 获取整数参数值
     *
     * @param string $name 参数名
     * @param string $default 默认值
     * @return int
     */
    public static function getIntParam($name, $default = 0) {
        $value = self::getParameter ( $name, $default );
        return intval ( $value );
    }

    /**
     * 获取COOKIE值
     *
     * @param string $name 参数名
     * @param string $default 默认值
     * @return string
     */
    public static function getCookie($name, $default = '') {
        return TMDispatcher::getInstance ()->getRequest ()->getCookie ( $name, $default );
    }

    /**
     * 同时获取多个参数值
     *
     * @param array $names 请求参数数组
     * @return array request name and value pair
     */
    public static function getMutiParams($names) {
        $datas = array();
        if (is_array ( $names )) {
            foreach ( $names as $val ) {
                $datas [$val] = self::getParameter ( $val );
            }
        }
        else {
            $names = strval ( $names );
            $datas [$names] = self::getParameter ( $names );
        }
        return $datas;
    }
}
