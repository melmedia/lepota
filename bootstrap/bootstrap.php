<?php

define('CONFIG_DIR', APP_DIR . '/config');
defined('RUNTIME_DIR') or define('RUNTIME_DIR', ROOT_DIR . '/runtime');
define('VENDOR_DIR', ROOT_DIR . '/vendor');

$config = require(CONFIG_DIR . '/config.php');

if (file_exists(VENDOR_DIR . '/autoload.php')) {
    require(VENDOR_DIR . '/autoload.php');
}

require(VENDOR_DIR . '/yiisoft/yii2/Yii.php');

mb_internal_encoding('UTF-8');

chdir(ROOT_DIR);

return $config;
