<?php

// change the following paths if necessary
$yiit=(dirname(__FILE__)).'/../../../gcommon/lib/yii/yiit.php';
require_once($yiit);
require_once(dirname(__FILE__).'/../../../gcommon/lib/load_config_func.php');

$config = require_with_local(dirname(__FILE__).'/../config/test.php');


require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createConsoleApplication($config);
