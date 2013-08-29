<?php
return array(
    'Administrator' => array(
        'auth' => array(
            'name' => '权限分配',
            'url' => 'srbac/authitem',
            'subs' => array(
                array(
                    'name' => '管理',
                    'url' => 'srbac/authitem/manage',
                ),
                array(
                    'name' => '分配权限',
                    'url' => 'srbac/authitem/assign',
                ),
                array(
                    'name' => '查询',
                    'url' => 'srbac/authitem/assignments',
                ),
            ),
        ),
        'manager'=>array(
            'name'=>'管理员',
            'url'=>'/manager/admin',
            'icon'=>'user',
        ),
    ),
    'front'=>array(
        'page'=>array(
            'name'=>'页面',
            'url'=>'/other/admin',
            'icon'=>'list-alt',
        ),
        'template'=>array(
            'name'=>'模板',
            'url'=>'/template/admin',
            'icon'=>'share',
        ),
        'block'=>array(
            'name'=>'模块',
            'url'=>'/block/admin',
            'icon'=>'tasks',
        ),
        'activity'=>array(
            'name'=>'活动',
            'url'=>'/activity/admin',
            'icon'=>'book',
        ),
    ),
    'editor'=>array(
        'news'=>array(
            'name'=>'新闻',
            'url'=>'/news/admin',
            'icon'=>'globe',
        ),
        'downloadpage'=>array(
            'name'=>'游戏下载',
            'url'=>'/game/index',
            'icon'=>'music',
        ),
        'homepage'=>array(
            'name'=>'首页',
            'url'=>'/homepage/index',
            'icon'=>'home',
        ),
        'category'=>array(
            'name'=>'分类',
            'url'=>'/oterm/index',
            'icon'=>'indent-left',
        ),
    ),
);