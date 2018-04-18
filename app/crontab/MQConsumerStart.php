#! /usr/local/php/bin/php
<?php
/***********************************
 * 如果在beta下，需要增加以下代码
 * $_ENV['SERVER_TYPE'] = 'beta';
 ************************************/

define('PHP_PATH', '/usr/local/php/bin/php');
define('ARGS', 'pid root_path queue_name handle_class env');

function showError($msg='', $showUsage=true)
{
    if ($msg !== '') {
        echo $msg. PHP_EOL;
    }
    if ($showUsage) {
        echo 'usage: '. PHP_PATH. ' ' . __FILE__ . ' '. ARGS. PHP_EOL;
    }
    exit(1);
}

if ($argc < 5) {
    showError();
}

if (empty($argv[2])) {
    showError('ROOT_PATH is needed');
}
$dir = $argv[2];
if (!file_exists($dir)) {
    showError("ROOT_PATH doesn't exist", false);
}

if (isset($argv[5])) {
    $_ENV['SERVER_TYPE'] = $argv[5];
}

if (empty($argv[1])) {
    showError('pid is needed');
}
if (empty($argv[3])) {
    showError('queue name is needed');
}
if (empty($argv[4])) {
    showError('queue handle class is needed');
}

// 根据项目路径重设根目录
define('ROOT_PATH', realpath($dir) . '/');
define('FRAMEWORK_PATH', '/www/web/jerome.live.com/taesdk/src/framework/');

define('CACHE_PATH', ROOT_PATH . 'cache/');

set_include_path(get_include_path() . PATH_SEPARATOR . FRAMEWORK_PATH);

ini_set('display_errors', 1);
error_reporting(E_ALL);

/** @noinspection PhpIncludeInspection */
require_once FRAMEWORK_PATH . 'core/TMAutoload.class.php';

TMAutoload::getInstance()
    ->setDirs(array(ROOT_PATH . "library/", ROOT_PATH . "controllers/", FRAMEWORK_PATH))
    ->setSavePath(CACHE_PATH . 'autoload/')->execute();

try {
    TMConfig::initialize(false);
    $pid = $argv[1];
    if ($pid != TMConfig::get('tams_id')) {
        showError('pid not match', false);
    }

    declare(ticks = 1);
    $queue = $argv[3];
    $class = $argv[4];
    $env = $argv[5];
    $consumer = new MQConsumer($queue, $class);
    $consumer->init();

} catch (TMException $ae) {
    echo $ae->getMessage(). PHP_EOL;
}

