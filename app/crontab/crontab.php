<?php
/***********************************
如果在beta下，需要增加以下代码
$_ENV['SERVER_TYPE'] = 'beta';
************************************/
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../') . '/');
define('FRAMEWORK_PATH', '/www/web/jerome.live.com/taesdk/src/framework/');
define('CACHE_PATH', ROOT_PATH.'cache/');

set_include_path(get_include_path() . PATH_SEPARATOR. FRAMEWORK_PATH);

require_once 'core/TMAutoload.class.php';

TMAutoload::getInstance()
    ->setDirs(array(ROOT_PATH."library/", ROOT_PATH."controllers/", FRAMEWORK_PATH))
    ->setSavePath(CACHE_PATH.'autoload/')->execute();
try{
    TMConfig::initialize();

    //TODO 完成业务逻辑
    $MQClient = TMMQClient::getInstance();
    $result = $MQClient->getMessage(TMConfig::get('tams_id').'project_votes');
    //$result = json_decode($result,true);
    if($result){
        $ts = new TMService();
        $ts->insert(array('log_info'=>$result),'tbl_mq_log');
    }
}catch(TMException $ae){

}
?>