#! /usr/local/php/bin/php
<?php
define('ROOT_PATH', realpath(dirname(__FILE__)) . '/');
define('FRAMEWORK_PATH', '/www/web/jerome.live.com/taesdk/src/framework/');
define('CACHE_PATH', ROOT_PATH . 'cache/');

set_include_path(get_include_path() . PATH_SEPARATOR . FRAMEWORK_PATH);

/** @noinspection PhpIncludeInspection */
require_once FRAMEWORK_PATH . 'core/TMAutoload.class.php';

TMAutoload::getInstance()->setDirs([ROOT_PATH . "library/", FRAMEWORK_PATH])
    ->setSavePath(CACHE_PATH . 'autoload/')->execute();

define('PHP_PATH', '/usr/local/php/bin/php');
define('SCRIPT_PATH', ROOT_PATH.'MQConsumerStart.php');
//define('ARGS', 'env(dev/beta/production)');
define('MQ_ENABLE', 1);
define('MQ_DISABLE', 2);

function showError($msg='', $showUsage=true, $exit=true)
{
    if ($msg !== '') {
        echo $msg. PHP_EOL;
    }
    if ($showUsage) {
        echo 'usage: '. PHP_PATH. ' ' . __FILE__ . ' '. PHP_EOL;
    }
    if ($exit) {
        exit(1);
    }
}

if ($argc != 2) {
    showError();
}

if(!is_file(SCRIPT_PATH)) {
    showError('Please ensure that MQConsumerStart.php is under project/src directory');
}

TMConfig::initialize();
$configArray = TMConfig::get('queue_consumer');
//$env = $argv[1];
foreach ($configArray as $row) {
    $pid = isset($row['pid'])?:TMConfig::get('tams_id');
    $path = isset($row['path'])?:ROOT_PATH;
    $queue = $row['queue'];
    $processor = $row['processor'];
    $status = $row['status'];
    if(!is_subclass_of($processor, 'MQProcessor')) {
        showError("processor: $processor is not subclass of MQProcessor", false);
    }

    $arguments = implode(' ', [$pid, $path, $queue, $processor]);

//    if ($env !== 'production') {
//        $arguments .= " $env";
//    }
    $searchCmd = 'pgrep "' . $arguments . '" -f';

    unset($result);
    exec($searchCmd, $result);

    if($row['status'] == MQ_ENABLE) {
        //消费者需要运行
        if(empty($result)) {
            //启动消费者
            $cmd = implode(' ', [PHP_PATH, SCRIPT_PATH, $arguments, '>>', '/dev/null']);
            echo $cmd;
            echo "cmd: $cmd \n";
            exec($cmd, $cmdResult);
            echo var_export($cmdResult, true).PHP_EOL;
        }
    }

    if($row['status'] == MQ_DISABLE) {
        if(!empty($result)) {
            //停止消费者
            foreach ($result as $pid) {
                exec("kill $pid", $cmdResult);
            }
        }
    }
}
echo 'all consumers have been processed!'. PHP_EOL;
exit(0);