<?php
// uncomment the following to define a path alias
Yii::setPathOfAlias('gcommon', dirname(__FILE__) . '/../../../gcommon');

// include error constant
include(Yii::getPathOfAlias('gcommon.config').'/error.php');


return array(
    'preload' => array(
        'log',
    ),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'common.models.*',
        'gcommon.components.*',
        'gcommon.components.error.*',
        'gcommon.extensions.gautoloader.*',
        'gcommon.cms.models.*',
        'gcommon.cms.components.*',
    ),
    'modules' => array(
    ),

    'components' => array(
        'autoloader' => array(
            'class' => 'gcommon.extensions.gautoloader.EAutoloader',
        ),
        'curl' => array(
            'class' => 'common.components.Curl',
            'options' => array(),
        ),
        'redis' => array(
            'class' => 'common.extensions.RedisConnection',
            'masterServer'=>array(
                'host'=>'127.0.0.1',
                'port'=>6379,
                'timeout'=>2,
            ),
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
    ),
    'params' => require_with_local(Yii::getPathOfAlias('common.config') . '/params.php'),
);