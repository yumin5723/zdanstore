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
        'cms' => array(
            'class' => 'gcommon.cms.CmsModule',
            'domain' => 'www.17w78.com',
        ),
    ),

    'components' => array(
        'autoloader' => array(
            'class' => 'gcommon.extensions.gautoloader.EAutoloader',
        ),
        'cmsRenderer'=>array(
            'class'=>'gcommon.cms.components.CmsRenderer',
        ),
        'eventlog' => array(
            'class' => 'common.components.EventLog',
        ),
        'stringRender'=>array(
            'class' => 'gcommon.extensions.ETwigViewRenderer',
            'loader_type' => 'string',
            // Change template syntax to Smarty-like (not recommended)
            'lexerOptions' => array(
                'tag_comment'  => array('{*', '*}'),
                'tag_block'    => array('{%', '%}'),
                'tag_variable' => array('{{', '}}')
            ),
            'filters' => array(
                /* 'jencode' => 'CJSON::encode', */
                'wrap'=>'cutstr',
            ),
        ),
        'payment' => array(
            'class'=>'common.extensions.spay.Payment',
            'config_file' => Yii::getPathOfAlias('common.config.payment')."/global.php",
            'notifyUrlPrefix' => "http://api.1378.com/pay/notify/channel_name",
            'returnUrlPrefix' => "http://www.1378.com/pay/return/channel_name",
        ),
        'cooperation'=>array(
            'class' => 'common.extensions.cooperation.Cooperation',
            'config_file'=>Yii::getPathOfAlias('common.config.cooperation')."/global.php",
        ),
        'cmsEvent' => array(
            'class' => 'gcommon.cms.components.CmsEvent',
        ),
        'attemptlimit' => array(
            'class'=>'common.components.AttemptLimit',
        ),
        'curl' => array(
            'class' => 'common.components.Curl',
            'options' => array(),
        ),
        'openid'=>array(
            'class'=>'gcommon.extensions.openid.components.Openid',
        ),
        'redis' => array(
            'class' => 'common.extensions.RedisConnection',
            'masterServer'=>array(
                'host'=>'127.0.0.1',
                'port'=>6379,
                'timeout'=>2,
            ),
        ),
        'publisher'=>array(
            'class'=>'gcommon.cms.components.Publisher',
            'staticPath'=>'/data0/static_files/s2.17w78.com',
            'staticBaseUrl'=>'http://s2.17w78.com',
            'domains'=>array(
                    "www.17w78.com"=>"/data0/web/www.17w78.com",
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