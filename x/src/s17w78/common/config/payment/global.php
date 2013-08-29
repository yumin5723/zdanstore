<?php

return array(
    "channels" => array(
        /* 
         * "yeecardpay" => array(
         *     "name" => "易宝(卡)",
         *     "className" => "YeeCardPay",
         * ),
         */
        "99bill" => array(
            "name" => "快钱",
            "className" => "S99billDirect",
            "type" => "web"
        ),
        "mhpay" => array(
            "name" => "梦幻币充值",
            "className" => "Mhpay",
            "type" => "mhgold"
        ),
        "s99billcard" => array(
            "name" => "快钱卡充值",
            "className" => "S99billCard",
            "type" => "web"
        ),
        "alipaydirect" => array(
            "name" => "支付宝充值",
            "className" => "AlipayDirect",
            "type" => "web"
        ),
        "shenzhoufu" => array(
            "name" => "神州付充值",
            "className" => "ShenZhouFu",
            "type" => "card"
        ),
    ),

    "methods" => array(
        "99bill" => array(
            "name" => "快钱",
            "channel" => "s99billdirect",
        ),
        "mhpay" => array(
            "name" => "梦幻币充值",
            "channel" => "mhpay",
        ),
        "s99billcard" => array(
            "name" => "快钱卡充值",
            "channel" => "s99billcard",
        ),
        "alipay" => array(
            "name" => "支付宝充值",
            "channel" => "alipaydirect",
        ),
        "shenzhoufu" => array(
            "name" => "神州付充值",
            "channel" => "shenzhoufudirect",
        ),
    ),
    'charges'=>array(
        'green'=>array(
          'className'=>'GreenCharge'
        ),
        'haodan'=>array(
          'className'=>'HaoDanCharge',
        ),
        'pokerking'=>array(
          'className'=>'PokerKingCharge',
        ),
        'tangren'=>array(
          'className'=>'TangrenCharge',
        ),
        'qianshou'=>array(
          'className'=>'QianshouCharge',
        ),
        'lianzhong'=>array(
          'className'=>'LianzhongCharge',
        ),
        'huangjiadp'=>array(
          'className'=>'HjDzpokerCharge',
        ),
        'sanguo'=>array(
          'className'=>'SanguoCharge',
        ),
        'huangjia'=>array(
          'className'=>'HuangjiaCharge',
        ),
        'qiangdp'=>array(
          'className'=>'QiangdpCharge',
        ),
        'yinian'=>array(
          'className'=>'YinianCharge',
        ),
        'chenchang'=>array(
          'className'=>'ChenchangCharge',
        ),
        'gamepub'=>array(
          'className'=>'GamePubCharge',
        ),
        'hongxiang'=>array(
          'className'=>'HongxiangCharge',
        ),
    ),



    /* for generate bank list */
    'banklist' => array(
         
          'ICBC' => array('name'=>"工商银行",
                          "logo"=>"/images/pay/bank/bank1.gif",
                          "channel" => "99bill",
                          ),
          'CMB' => array('name'=>"招商银行",
                          "logo"=>"/images/pay/bank/bank2.gif",
                          "channel" => "99bill",
                          ),
          'ABC' => array('name'=>"农业银行",
                          "logo"=>"/images/pay/bank/bank3.gif",
                          "channel" => "99bill",
                          ),
          'CCB'=> array('name'=>"建设银行",
                          "logo"=>"/images/pay/bank/bank4.gif",
                          "channel" => "99bill",
                          ),
          'BCOM' => array('name'=>"交通银行",
                          "logo"=>"/images/pay/bank/bank5.gif",
                          "channel" => "99bill",
                          ),
          'BOC' => array('name'=>"中国银行",
                          "logo"=>"/images/pay/bank/bank6.gif",
                          "channel" => "99bill",
                          ),
          'PSBC' => array('name'=>"中国邮政储蓄",
                          "logo"=>"/images/pay/bank/bank7.gif",
                          "channel" => "99bill",
                          ),
          'CIB' => array('name'=>"兴业银行",
                          "logo"=>"/images/pay/bank/bank8.gif",
                          "channel" => "99bill",
                          ),
          'CITIC' => array('name'=>"中信银行",
                          "logo"=>"/images/pay/bank/bank9.gif",
                          "channel" => "99bill",
                          ),
          'CMBC' => array('name'=>"民生银行",
                          "logo"=>"/images/pay/bank/bank10.gif",
                          "channel" => "99bill",
                          ),
          'CEB' => array('name'=>"光大银行",
                          "logo"=>"/images/pay/bank/bank11.gif",
                          "channel" => "99bill",
                          ),
          'NJCB' => array('name'=>"南京银行",
                          "logo"=>"/images/pay/bank/bank12.gif",
                          "channel" => "99bill",
                          ),
          'BOB' => array('name'=>"北京银行",
                          "logo"=>"/images/pay/bank/bank13.gif",
                          "channel" => "99bill",
                          ),
          'PAB' => array('name'=>"平安银行",
                          "logo"=>"/images/pay/bank/bank14.gif",
                          "channel" => "99bill",
                          ),
          'BEA' => array('name'=>"BEA东亚银行",
                          "logo"=>"/images/pay/bank/bank15.gif",
                          "channel" => "99bill",
                          ),
          'NBCB' => array('name'=>"宁波银行",
                          "logo"=>"/images/pay/bank/bank16.gif",
                          "channel" => "99bill",
                          ),
          'SDB' => array('name'=>"深圳发展银行",
                          "logo"=>"/images/pay/bank/bank17.gif",
                          "channel" => "99bill",
                          ),
          'GDB' => array('name'=>"广发发展银行",
                          "logo"=>"/images/pay/bank/bank18.gif",
                          "channel" => "99bill",
                          ),
          'SPDB' => array('name'=>"浦发银行",
                          "logo"=>"/images/pay/bank/bank_spdb.gif",
                          "channel" => "99bill",
                          ),
          'BJRCB' => array('name'=>"北京农村商业银行",
                          "logo"=>"/images/pay/bank/bank21.gif",
                          "channel" => "99bill",
                          ),
          'SHB' => array('name'=>"上海银行",
                          "logo"=>"/images/pay/bank/bank22.gif",
                          "channel" => "99bill",
                          ),
          'HSB' => array('name'=>"徽商银行",
                          "logo"=>"/images/pay/bank/bank_hsb.gif",
                          "channel" => "99bill",
                          ),
          'HZB' => array('name'=>"杭州银行",
                          "logo"=>"/images/pay/bank/bank24.gif",
                          "channel" => "99bill",
                          ),
          'HXB' => array('name'=>"华夏银行",
                          "logo"=>"/images/pay/bank/bank26.gif",
                          "channel" => "99bill",
                          ),
          'CZB' => array('name'=>"浙商银行",
                          "logo"=>"/images/pay/bank/bank_czb.gif",
                          "channel" => "99bill",
                          ),
         'CBHB' => array('name'=>"渤海银行",
                          "logo"=>"/images/pay/bank/bank30.gif",
                          "channel" => "99bill",
                          ),
         'SRCB' => array('name'=>"上海农村商业银行",
                          "logo"=>"/images/pay/bank/bank29.gif",
                          "channel" => "99bill",
                          ),
        // "ICBC" => array(
        //     "name" => "工商银行",
        //     "logo" => "/images/pay/bank/bank1.gif",
        //     "channel" => "99bill",
        // ),
    ),
    /* for alipay,qqpay... channel direct pay */
    'chnlist' => array(
        'unicom' => array("name"=>'联通卡充值',
                  "logo"=>"/images/chn/chn_2.png",
                  "channel" => "shenzhoufu"
        ),'szx' => array("name"=>'神州行',
                  "logo"=>"/images/chn/chn_1.png",
                  "channel" => "shenzhoufu"
        ),'telecom' => array("name"=>'电信',
                  "logo"=>"/images/chn/chn_3.png",
                  "channel" => "shenzhoufu"
        ),
    ),
    'alipay' => array(
        'alipay' => array("name"=>'支付宝',
                  "logo"=>"/images/pay/bank/alipay_logo.gif",
                  "channel" => "alipaydirect"
        )
    ),
    // 'shenzhoufu' => array(
    //     'szf_unicom' => array("name"=>'联通卡充值',
    //               "logo"=>"/images/chn/chn_2.png",
    //               "channel" => "shenzhoufu"
    //     ),'szf_szx' => array("name"=>'神州行',
    //               "logo"=>"/images/chn/chn_1.png",
    //               "channel" => "shenzhoufu"
    //     ),'szf_telecom' => array("name"=>'电信',
    //               "logo"=>"/images/chn/chn_3.png",
    //               "channel" => "shenzhoufu"
    //     ),
    // ),
    'amtlist' => array(
        '10',
        '20',
        '30',
        '50',
        '100',
        '300',
        '500',
        '800',
        '1500',
        '2000',
        '3000',
        '5000',
        '8000',
        '10000',
    ),
);