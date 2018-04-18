<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/6/12
 * Time: 16:34
 * desc:公众号menue 创建
 * array(
"1"=>array("type"=>"click","name"=>"今日歌曲","sub_button"=>array(array("type"=>"view","name"=>"搜索"),array("type"=>"view","name"=>"赞一下我们"),array("type"=>"view","name"=>"视频")))
);
 */
class WechatMenu{

    //创建菜单
    public static function createMenue($data){
        try{
            $curl = new TMCurl();
            $params = '{"button":[{
                                  "type":"click",
                                  "name":"今日歌曲",
                                  "key":"V1001_TODAY_MUSIC"
                                 },
                                  {
                                       "name":"菜单",
                                       "sub_button":[
                                  {
                                       "type":"view",
                                        "name":"搜索",
                                        "url":"http://www.soso.com/"
                                  },
                                  {
                                      "type":"view",
                                      "name":"视频",
                                      "url":"http://v.qq.com/"
                                  },
                                  {
                                      "type":"click",
                                      "name":"赞一下我们",
                                      "key":"V1001_GOOD"
                                        }]
                                   }]
                             }';//组合好的post接口数据
            $curl->setOptions(array(
                'CURLOPT_SSL_VERIFYPEER'=>false,
                'CURLOPT_SSL_VERIFYHOST'=>false,
                'CURLOPT_AUTOREFERER'=>1,
                'CURLOPT_RETURNTRANSFER'=>1,
                'CURLOPT_CONNECTTIMEOUT'=>2
                ));
            $result = $curl->sendByPost('','https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.OMUtil::getAccessToken());
            $result = json_decode($result,true);
            if($result['errcode'] == 0){
                return true;
            }
            return false;
        }catch (TMException $e){
            return false;
        }
    }

    /*
     * 获取菜单
     *
     * */
    public static function getMenu(){
        try{
            $result = OMUtil::requestByCURL('https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.OMUtil::getAccessToken(),false);
            return json_decode($result,true);
        }catch (TMException $e){
            return array();
        }
    }
}