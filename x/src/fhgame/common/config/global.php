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
        'gcommon.models.*',
        'gcommon.components.*',
        'gcommon.components.error.*',
        'gcommon.extensions.gautoloader.*',
        'gcommon.cms.models.*',
        'gcommon.cms.components.*',
    ),
    'modules' => array(
        'cms' => array(
            'class' => 'gcommon.cms.CmsModule',
            'domain' => 'jz.fhgame.com',
        ),
    ),
    'components' => array(
        'autoloader' => array(
            'class' => 'gcommon.extensions.gautoloader.EAutoloader',
        ),
        'cmsRenderer'=>array(
            'class'=>'gcommon.cms.components.CmsRenderer',
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
        'cmsEvent' => array(
            'class' => 'gcommon.cms.components.CmsEvent',
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