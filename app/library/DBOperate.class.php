<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2017/2/10
 * Time: 16:53
 */

class DBOperate{

     public static function getWeixinUser(){
         try{
             $ts = new TMService();
             $result = $ts->select(array(),'FUserId,FOpenId,FNick,FNickname,FHead','Tbl_WeixinAuthUser',array(0,10),array('orderby'=>'FUserId asc'),MYSQL_ASSOC);
             return $result;
         }catch (TMMysqlException $e){
            return array();
         }
     }
}