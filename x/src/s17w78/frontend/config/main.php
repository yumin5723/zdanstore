<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('site',dirname(__FILE__).'/../..');
Yii::setPathOfAlias('cmswidgets',dirname(__FILE__).'/../../../gcommon/cms/widgets');
Yii::setPathOfAlias('common',dirname(__FILE__).'/../../common');
Yii::setPathOfAlias('backend',dirname(__FILE__).'/../../backend');
Yii::setPathOfAlias('adlog',dirname(__FILE__).'/../../adlog');
Yii::setPathOfAlias('config',dirname(__FILE__));

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(require_with_local(Yii::getPathOfAlias('common.config') . '/global.php'),
    array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'梦幻天下',
    'language'=>'en_us',
    'defaultController'=>'default',

    // preloading 'log' component
    'preload'=>array(
        'log',
        'bootstrap'
    ),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'common.models.*',
        'common.components.*',
        'ext.restfullyii.components.*',
    ),

    'modules'=>array(
        // uncomment the following to enable the Gii tool
        /*
         * 'gii'=>array(
         *  'class'=>'system.gii.GiiModule',
         *  'password'=>'usegii',
         *  // If removed, Gii defaults to localhost only. Edit carefully to taste.
         *  'ipFilters'=>array('127.0.0.1','::1'),
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
            'coreCss'=>false,
        ),
        'ip' => array(
            'class' => 'common.extensions.IpAddress',
        ),
        'GcommonAssets' => array(
            'class' => 'gcommon.components.AppAssetManager',
            'asset_dir' => Yii::getPathOfAlias("gcommon.assets"),
        ),
        'assets' => array(
            'class' => 'gcommon.components.AppAssetManager',
        ),
        'shoppingcart' => array(
            'class' => 'common.components.ShoppingCart',
        ),
        'RoleMenu' => array(
            'class' => "application.components.RoleMenu",
        ),
        'session'=>array(
            'sessionName'=>'P1_ADMIN_SSID',
            'cookieParams'=>array(
                'domain'=>'p1.17w78.com',
            ),
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
                '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
            ),
        ),
        'authManager' => array(
            'class' => 'gcommon.extensions.srbac.components.SDbAuthManager',
            'connectionID' => 'adminDb',
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
                'Twig1378Ext' => "Twig1378Ext",
            ),
            'globals' => array(
                'html' => 'CHtml',
            ),
            'functions' => array(
                /* 'rot13' => 'str_rot13', */
                'str_repeat'=>'str_tree',
                'pro_image'=>'getProfileImage',
            ),
            'filters' => array(
                /* 'jencode' => 'CJSON::encode', */
                'cut'=>'cutstr',
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
                    'levels'=>'error, warning,info',
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
