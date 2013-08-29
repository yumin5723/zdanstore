<?php

Yii::setPathOfAlias('site', dirname(__FILE__) . '/../..');
Yii::setPathOfAlias('config', dirname(__FILE__) . '../config');
Yii::setPathOfAlias('common',dirname(__FILE__).'/../../common');
Yii::setPathOfAlias('backend',dirname(__FILE__).'/../../backend');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(require_with_local(Yii::getPathOfAlias('common.config') . '/global.php'),
    array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Kaqu console',

    // preloading 'log' component
    'preload'=>array(
        'log',
    ),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'common.components.error.*',
        'common.models.User',
    ),

    'modules'=>array(
    // uncomment the following to enable the Gii tool
        'cms' => array(
            'class' => 'gcommon.cms.CmsModule',
        ),
    ),

    'commandMap' => array(
        'parsepage' => array(
            'class'=>'gcommon.cms.commands.ParsePageWorker',
        ),
        'parsetemplete' => array(
            'class'=>'gcommon.cms.commands.ParseTempleteWorker',
        ),
        'batchcontentpublish' => array(
            'class'=>'gcommon.cms.commands.BatchContentPublishWorker',
        ),
        'categorymigration' => array(
            'class'=>'gcommon.cms.commands.CategoryMigration',
        ),
        'pageworker' => array(
            'class'=>'gcommon.cms.commands.PageWorker',
        ),
        'blockworker' => array(
            'class'=>'gcommon.cms.commands.BlockWorker',
        ),
        'objectworker' => array(
            'class'=>'gcommon.cms.commands.ObjectWorker',
        ),
        'templateworker' => array(
            'class'=>'gcommon.cms.commands.TemplateWorker',
        ),
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
            'class' => 'gcommon.extensions.Curl',
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
                'levels'=>'error, warning, info',
             ),
         ),
        // uncomment the following to show log messages on web pages
        /*
        array(
            'class'=>'CWebLogRoute',
        ),
        */
        /* ), */
    ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
        'params' => require_with_local(dirname(__FILE__) . '/params.php'),
    ));
