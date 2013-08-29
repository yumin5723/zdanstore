<?php

defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once(dirname(__FILE__).'/../../gcommon/lib/yii/yii.php');
$base = require(dirname(__FILE__).'/config/main.php');
$local = require(dirname(__FILE__).'/config/main-local.php');
$config = CMap::mergeArray($base, $local);

require(dirname(__FILE__).'/../../gcommon/lib/yii/yiic.php');
