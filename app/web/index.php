<?php
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../') . '/');
#在提交代码到svn之前，将4行注释去掉，，6行加上注释
strtoupper(substr(PHP_OS,0,3))==='WIN' ?
    define('FRAMEWORK_PATH', ROOT_PATH.'../service/framework/'):
define('FRAMEWORK_PATH','/www/web/service/framework/');
define('CACHE_PATH', ROOT_PATH.'cache/');
set_include_path(get_include_path() . PATH_SEPARATOR. FRAMEWORK_PATH);
define('BASE_CHARSET','UTF-8');
define('TEMPLATE_DIR_TEMPLATE','templates');
define('TEMPLATE_DIR_COMPILED','temp/compiled/');
require_once 'core/TMAutoload.class.php';
TMAutoload::getInstance()
    ->setDirs(array(ROOT_PATH."library/", ROOT_PATH."controllers/", FRAMEWORK_PATH))
    ->setSavePath(CACHE_PATH.'autoload/')->execute();
try{
    TMConfig::initialize();
    TMDispatcher::createInstance()->dispatch();
}catch(TMException $ae){
    header("HTTP/1.1 404 Not Found");
    echo $ae->getMessage();
}
?>
