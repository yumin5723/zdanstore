<?php

defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once(dirname(__FILE__).'/../../gcommon/lib/yii/yii.php');
require_once(dirname(__FILE__).'/../../gcommon/lib/load_config_func.php');
$config = require_with_local(dirname(__FILE__).'/config/main.php');

require(dirname(__FILE__).'/../../gcommon/lib/yii/yiic.php');