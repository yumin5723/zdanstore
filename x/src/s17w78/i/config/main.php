<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('site',dirname(__FILE__).'/../..');
Yii::setPathOfAlias('cmswidgets',dirname(__FILE__).'/../../../gcommon/cms/widgets');
Yii::setPathOfAlias('gcommon',dirname(__FILE__).'/../../../gcommon');
Yii::setPathOfAlias('common',dirname(__FILE__).'/../../common');
Yii::setPathOfAlias('config',dirname(__FILE__));

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(require_with_local(Yii::getPathOfAlias('common.config') . '/global.php'),
    array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'1378',
    'language'=>'zh_cn',
    'defaultController'=>'user',

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
        'gcommon.cms.models.*',
        'gcommon.extensions.srbac.controllers.SBaseController',
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
    // 'cms' => array(
    //     'class' => 'gcommon.cms.CmsModule',
    //     'domain' => 'www.17w78.com',
    // ),
    ),
    'controllerMap' => array(
        'captcha'=>array(
            'class' => 'common.components.CaptchaController',
        ),
    ),
    // application components
    'components'=>array(
        'bootstrap' => array(
            'class' => 'gcommon.extensions.bootstrap.components.Bootstrap',
            'coreCss'=>false,
            //'enableJS'=>false,
        ),
        'assets' => array(
            'class' => 'gcommon.components.AppAssetManager',
        ),
        'GcommonAssets' => array(
            'class' => 'gcommon.components.AppAssetManager',
            'asset_dir' => Yii::getPathOfAlias("gcommon.cms.assets"),
        ),
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            'loginUrl'=>'/user/login',
            'stateKeyPrefix'=>'212412412asfsadfsdf',
            'identityCookie'=>array('domain'=>'.i.1378.com'),
        ),

        'smsRedis' => array(
            'class' => 'common.extensions.RedisConnection',
            'masterServer'=>array(
                'host'=>'127.0.0.1',
                'port'=>6379,
                'timeout'=>2,
            ),
        ),
        'redis' => array(
            'class' => 'common.extensions.RedisConnection',
            'masterServer'=>array(
                'host'=>'127.0.0.1',
                'port'=>6379,
                'timeout'=>2,
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
        'platform'=>array(
            'class' => 'application.components.platform.PlatformLogin',
            'config_file'=>Yii::getPathOfAlias('config.platform')."/global.php",
        ),
        'sendMail'=>array(
            'class'=>'common.components.SendMail',
            'config_file'=>dirname(__FILE__)."/mail.conf",
        ),
        'viewRenderer' => array(
            'class' => 'gcommon.extensions.ETwigViewRenderer',

            // All parameters below are optional, change them to your needs
            'fileExtension' => '.twig',
            'options' => array(
                'autoescape' => true,
            ),
            'extensions' => array(
                'Twig1378Ext' => "Twig1378Ext",
            ),
            'globals' => array(
                'html' => 'CHtml',
            ),
            'functions' => array(
                /* 'rot13' => 'str_rot13', */
                'str_repeat'=>'str_tree',
                'cmsblock'=>'getCmsBlock',
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
        'params' => require_with_local(dirname(__FILE__) . '/params.php'),
    ));
