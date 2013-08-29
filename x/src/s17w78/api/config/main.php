<?php
Yii::setPathOfAlias('site', dirname(__FILE__) . '/../..');
Yii::setPathOfAlias('common', dirname(__FILE__) . '/../../common');
Yii::setPathOfAlias('config', dirname(__FILE__) . '../config');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(require_with_local(Yii::getPathOfAlias('common.config') . '/global.php'),
    array(
        'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
        'name' => 'Pay for Hero',
        // preloading 'log' component
        'defaultController'=>"pay",
        // autoloading model and component classes
        'import' => array(
        ),
        'modules' => array(
        ),
        // application components
        'components' => array(
            'user' => array(
                // enable cookie-based authentication
                'allowAutoLogin' => false,
            ),
            // uncomment the following to enable URLs in path-format
            'urlManager' => array(
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => array(
                    'notify/<channel_name:\w+>' => 'pay/notify',
                    //				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
                    //'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                    //				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ),
            ),
            'readSession' => array(
                'class' => 'gcommon.components.ReadSession',
                'stateKeyPrefix'=>'212412412asfsadfsdf',
            ),
            // uncomment the following to use a MySQL database
            /*
             * 'db'=>array(
             * 	'connectionString' => 'mysql:host=127.0.0.1;dbname=kaqucc_main',
             * 	'emulatePrepare' => true,
             * 	'username' => 'root',
             * 	'password' => '',
             * 	'charset' => 'utf8',
             * ),
             */
            'errorHandler' => array(
                // use 'site/error' action to display errors
                'errorAction' => 'site/error',
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
        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params' => require_with_local(dirname(__FILE__) . '/params.php'),
    ));

