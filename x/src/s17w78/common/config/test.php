<?php


// uncomment the following to define a path alias
Yii::setPathOfAlias('site',dirname(__FILE__).'/../..');
Yii::setPathOfAlias('common',dirname(__FILE__).'/../../common');
Yii::setPathOfAlias('gcommon',dirname(__FILE__).'/../../../gcommon');

// include error constant
include(Yii::getPathOfAlias('gcommon.config').'/error.php');

$global_params = require_with_local(Yii::getPathOfAlias('gcommon.config').'/params.php');
$app_params = require_with_local(dirname(__FILE__).'/params.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Kaqu Test',

    'runtimePath' =>dirname(__FILE__)."/../tests/runtime",
    // preloading 'log' component
    'preload'=>array(
        'log',
    ),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'gcommon.components.*',
        'gcommon.cms.models.*',
        'gcommon.cms.components.*',
        'gcommon.components.error.*',
    ),

    'modules'=>array(
        // uncomment the following to enable the Gii tool
    ),

    // application components
    'components'=>array(
        /* 
         * 'user'=>array(
         *     // enable cookie-based authentication
         *     'allowAutoLogin'=>true,
         * ),
         * // uncomment the following to enable URLs in path-format
         * 'urlManager'=>array(
         *     'urlFormat'=>'path',
         *     'showScriptName'=>false,
         *     'rules'=>array(
         *         'error'=>'site/error',
         *         '<controller:\w+>/<id:\d+>'=>'<controller>/view',
         *         '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
         *         '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
         *     ),
         * ),
         */
        'payment' => array(
            'class'=>'common.extensions.spay.Payment',
            'config_file' => Yii::getPathOfAlias('common.config.payment')."/global.php",
            'notifyUrlPrefix' => "http://api.1378.com/pay/notify/channel_name",
            'returnUrlPrefix' => "http://www.1378.com/pay/return/channel_name",
        ),
        'openid' => array(
            'class' => 'gcommon.extensions.openid.components.Openid',
        ),
        'fixture'=>array(
            'class'=>'system.test.CDbFixtureManager',
        ),
        'curl' => array(
            'class' => 'common.components.Curl',
            'options' => array(),
        ),
        'nestedSetBehavior' => array(
            'class' => 'common.extensions.NestedSetBehavior',
            'options' => array(),
        ),
        // uncomment the following to use a MySQL database
        /*
         * 'db'=>array(
         *  'connectionString' => 'mysql:host=127.0.0.1;dbname=kaqucc_main',
         *  'emulatePrepare' => true,
         *  'username' => 'root',
         *  'password' => '',
         *  'charset' => 'utf8',
         * ),
         */
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
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
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>CMap::mergeArray($global_params, $app_params),
);
