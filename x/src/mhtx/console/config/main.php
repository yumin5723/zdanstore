<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('site',dirname(__FILE__).'/../..');
Yii::setPathOfAlias('gcommon',dirname(__FILE__).'/../../../gcommon');
Yii::setPathOfAlias('backend',dirname(__FILE__).'/../../backend');
// include error constant
include(Yii::getPathOfAlias('gcommon.config').'/error.php');
require_once(dirname(__FILE__).'/../../../gcommon/lib/load_config_func.php');
$global_params = require_with_local(Yii::getPathOfAlias('gcommon.config').'/params.php');
$app_params = require_with_local(dirname(__FILE__).'/params.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'mhtx',

    // preloading 'log' component
    'preload'=>array(
        'log',
    ),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'gcommon.components.error.*',
        'gcommon.components.*',
        'gcommon.models.User',
        'backend.models.*',
        'backend.components.*'
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
        'curl' => array(
            'class' => 'common.extensions.Curl',
            'options' => array(),
        ),
        // uncomment the following to use a MySQL database
        
        // 'db'=>array(
        //   'connectionString' => 'mysql:host=127.0.0.1;dbname=cms',
        //   'emulatePrepare' => true,
        //   'username' => 'root',
        //   'password' => '',
        //   'charset' => 'utf8',
        //  ),
         
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
    'commandMap' => array(
        'direct_pay' => array(
            'class' => 'application.commands.DirectCardPayCommand'
        )
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>CMap::mergeArray($global_params, $app_params),
);
