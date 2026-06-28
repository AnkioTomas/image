<?php
return array (
  'debug' => true,
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
    3 => 'nova\\plugin\\tpl\\Handler',
    4 => 'nova\\plugin\\login\\LoginManager',
    5 => 'nova\\plugin\\tpl\\Handler',
    6 => 'nova\\plugin\\webdav\\WebdavManager',
    7 => 'nova\\plugin\\tpl\\Handler',
    8 => 'nova\\plugin\\login\\LoginManager',
    9 => 'nova\\plugin\\tpl\\Handler',
  ),
);
