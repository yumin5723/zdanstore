<?php

return array(
	"methods" => array(
        /* only for support pay method */
        "telecom" => array(
            "name" => "充值",
            "price" => array(
                'real' => 1,
                'charge' => 1,
            ),
        ),
        "unicom" => array(
            "name" => "联通卡充值",
            "price" => array(
                'real' => 1,
                'charge' => 1,
            ),
        ),
        "szx" => array(
            "name" => "神州行卡充值",
            "price" => array(
                'real' => 1,
                'charge' => 1,
            ),
        ),
    ),

    'merId' => "177400",
    'privateKey' => "menghuantianxia",
    'reqUrl' => "http://pay3.shenzhoufu.com/interface/version3/serverconnszx/entry-noxml.aspx",
    'des' => "7zE04KSUIxU=",
);