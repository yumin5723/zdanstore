<?php

return array(
    "methods" => array(
        /* only for support pay method */
        "telecom" => array(
            "name" => "电信卡充值",
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
    "gateway" =>"https://www.99bill.com/szxgateway/recvMerchantInfoAction.htm",
    "szx_key" => "",
    "szx_partner_id" => "",

    "unicom_key" => "",
    "unicom_partner_id" => "",

    "telecom_key" => "WCA9Z6FSYJ8T6YXH",
    "telecom_partner_id" => "1002268827704",
);