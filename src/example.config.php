<?php
return array (
  'debug' => false,
  'timezone' => 'Asia/Shanghai',
  'default_route' => true,
  'domain' => 
  array (
    0 => '0.0.0.0',
  ),
  'version' => '1.0.0',
  'ip' => '127.0.0.1',
  'port' => 10211,
  'framework_start' => 
  array (
    0 => 'nova\\plugin\\login\\LoginManager',
    1 => 'nova\\plugin\\tpl\\Handler',
    2 => 'nova\\plugin\\webdav\\WebdavManager',
  ),
  'db' => 
  array (
    'type' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'username' => 'your_username',
    'password' => 'your_password',
    'db' => 'image',
    'charset' => 'utf8mb4',
  ),
);
