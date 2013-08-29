<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('site',dirname(__FILE__).'/../..');
Yii::setPathOfAlias('gcommon',dirname(__FILE__).'/../../../gcommon');
// include error constant
include(Yii::getPathOfAlias('gcommon.config').'/error.php');

$global_params = require_with_local(Yii::getPathOfAlias('gcommon.config').'/params.php');
$app_params = require_with_local(dirname(__FILE__).'/params.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'mhgame',

	// preloading 'log' component
	'preload'=>array(
        'log',
        'bootstrap'
    ),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'gcommon.models.*',
        'gcommon.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/* 
         * 'gii'=>array(
		 * 	'class'=>'system.gii.GiiModule',
		 * 	'password'=>'usegii',
		 * 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
		 * 	'ipFilters'=>array('127.0.0.1','::1'),
         *     'generatorPaths'=>array(
         *         'bootstrap.gii',
         *     ),
		 * ),
         */
	),

	// application components
	'components'=>array(
        'bootstrap' => array(
            'class' => 'gcommon.extensions.bootstrap.components.Bootstrap',
        ),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>false,
		),
        'session'=>array(
            'sessionName'=>'ZZ_ADMIN_SSID',
        ),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
            'caseSensitive'=>false,
			'rules'=>array(
                /* 'error'=>'site/error', */
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
        'viewRenderer' => array(
            'class' => 'gcommon.extensions.ETwigViewRenderer',

            // All parameters below are optional, change them to your needs
            'fileExtension' => '.twig',
            'options' => array(
                'autoescape' => true,
            ),
            'extensions' => array(
                /* 'My_Twig_Extension', */
            ),
            'globals' => array(
                'html' => 'CHtml',
            ),
            'functions' => array(
                /* 'rot13' => 'str_rot13', */
            ),
            'filters' => array(
                /* 'jencode' => 'CJSON::encode', */
            ),
            // Change template syntax to Smarty-like (not recommended)
            'lexerOptions' => array(
                'tag_comment'  => array('{*', '*}'),
                'tag_block'    => array('{%', '%}'),
                'tag_variable' => array('{{', '}}')
            ),
        ),
		// uncomment the following to use a MySQL database
		/*
          'db'=>array(
		 	'connectionString' => 'mysql:host=127.0.0.1;dbname=kaqucc_main',
		 	'emulatePrepare' => true,
		 	'username' => 'root',
		 	'password' => '123456',
		 	'charset' => 'utf8',
		 ),
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
