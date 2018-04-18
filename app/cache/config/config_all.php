<?php
return array (
  'db' => 
  array (
    'default' => 
    array (
      'host' => '127.0.0.1',
      'username' => 'root',
      'password' => NULL,
    ),
  ),
  'master' => 
  array (
    'host' => '10.8.10.190',
    'username' => 'root',
    'password' => 'root',
  ),
  'slave' => 
  array (
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => 'root',
  ),
  'memcache' => 
  array (
    'enable' => true,
    'server' => 
    array (
      0 => 
      array (
        'host' => '127.0.0.1',
        'port' => 11211,
      ),
    ),
    'persistent' => false,
  ),
  'mq' => 
  array (
    'httpsqs' => 
    array (
      'host' => '127.0.0.1',
      'port' => 1218,
    ),
  ),
  'redis' => 
  array (
    'phpredis' => 
    array (
      'host' => '127.0.0.1',
      'port' => 6379,
      'timeout' => 0,
      'auth' => 0,
    ),
  ),
  'debug_mode' => true,
  'namespace' => 'zdjz',
  'tams_id' => 641011273,
  'appid' => 4007203,
  'domain' => 'zdjz.net.cn',
  'base_url' => 'http://zdjz.net.cn',
  'dbname' => 'DB_WJ_MPZDJZ',
  'isMaserSlave' => true,
  'error_log' => 
  array (
    'path' => 'log/error_log',
    'size' => 33554432,
  ),
  'queue_consumer' => 
  array (
    0 => 
    array (
      'queue' => 'shfred',
      'processor' => 'RedPackProcessor',
      'status' => 1,
    ),
  ),
  'template_path' => 
  array (
    'layout' => 'templates/layout/',
    'template' => 'templates/',
  ),
  'layout' => 
  array (
    'enable' => false,
    'default' => '',
  ),
  'controller' => 
  array (
    'key' => 'controller',
    'default_name' => 'default',
  ),
  'action' => 
  array (
    'key' => 'action',
    'default_name' => 'default',
  ),
  'component' => 
  array (
    'key' => 'component',
  ),
  'AppTams_id' => 800000047,
  'appkey' => 'a75c33d29f7c2aa1526989e7919f64bd',
);