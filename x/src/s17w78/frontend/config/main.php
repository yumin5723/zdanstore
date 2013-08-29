<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('site',dirname(__FILE__).'/../..');
Yii::setPathOfAlias('cmswidgets',dirname(__FILE__).'/../../../gcommon/cms/widgets');
Yii::setPathOfAlias('common',dirname(__FILE__).'/../../common');
Yii::setPathOfAlias('backend',dirname(__FILE__).'/../../backend');
Yii::setPathOfAlias('config',dirname(__FILE__).'/../..');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(require_with_local(Yii::getPathOfAlias('common.config') . '/global.php'),
    array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'梦幻天下',
    'language'=>'zh_cn',
    'defaultController'=>'game',

    // preloading 'log' component
    'preload'=>array(
        'log',
        // 'bootstrap'
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
    'cms' => array(
        'class' => 'gcommon.cms.CmsModule',
    ),
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
            'coreCss'=> false,
        ),
        'assets' => array(
            'class' => 'gcommon.components.AppAssetManager',
        ),
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            'loginUrl'=>'http://i.1378.com/user/login',
            'stateKeyPrefix'=>'212412412asfsadfsdf',
            'identityCookie'=>array('domain'=>'.17cms.com'),
        ),
        'request'=>array(
            'class'=>'common.components.SHttpRequest',
        ),
        'openid'=>array(
            'class'=>'gcommon.extensions.openid.components.Openid',
        ),
        'search' => array(
            'class' => "gcommon.extensions.DGSphinxSearch.DGSphinxSearch",
            'server' => '127.0.0.1',
            'port' => 9312,
            'maxQueryTime' => 3000,
            'enableProfiling' => 0,
            'enableResultTrace' => 0,
            'fieldWeights' => array(
                'title' => 10000,
                'keywords' => 100,
            ),
        ),
        'cache' => array(
            'class'=>'system.caching.CMemCache',
           // 'servers' => array(
           //     array('host' => '127.0.0.7', 'port' => 80, //'weight' => 60
           //         ),
           //  ),
           //'class' => 'system.caching.CFileCache',
            //'directoryLevel' => 2,
        ),

        // uncomment the following to enable URLs in path-format
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'caseSensitive'=>false,
            'rules'=>array(
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                /* 'error'=>'site/error', */
                // '<id:\d+>'=>'game/view',
                // '<action:\w+>/<id:\d+>'=>'game/<action>',
                // '<action:\w+>'=>'game/<action>',
                // '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
            ),
        ),
        'TaoHaoRedis' => array(
            'class' => 'common.extensions.RedisConnection',
            'masterServer'=>array(
                'host'=>'127.0.0.1',
                'port'=>6379,
                'timeout'=>2,
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
                'Twig1378Ext' => "Twig1378Ext",
            ),
            'globals' => array(
                'html' => 'CHtml',
            ),
            'functions' => array(
                /* 'rot13' => 'str_rot13', */
                'cmsblock'=>'getCmsBlock'
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
