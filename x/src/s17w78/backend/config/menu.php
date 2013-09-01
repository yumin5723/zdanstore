<?php
return array(
    'Administrator' => array(
        'auth' => array(
            'name' => '权限分配',
            'url' => '/srbac/authitem',
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
            'url'=>'/pp/manager/admin',
            'icon'=>'user',
        ),
        'brand'=>array(
            'name'=>'品牌管理',
            'url'=>'/pp/brand/admin',
            'icon'=>'th-large',
        ),
        'oterm'=>array(
            'name'=>'商品分类',
            'url'=>'/pp/oterm/index',
            'icon'=>'indent-left',
        ),
        'product'=>array(
            'name'=>'商品管理',
            'url'=>'/pp/product/admin',
            'icon'=>'gift',
        ),
    ),
);