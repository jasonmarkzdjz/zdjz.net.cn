<?php
return array (
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
);