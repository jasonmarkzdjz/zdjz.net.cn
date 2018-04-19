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
  'memcached' => 
  array (
    'enable' => true,
    'server' => 
    array (
      0 => 
      array (
        'host' => '127.0.0.1',
        'port' => 1978,
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
);