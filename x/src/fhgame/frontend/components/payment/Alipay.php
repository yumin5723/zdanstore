<?php
Yii::import("config.alipay.alipay_config");
class Alipay {
    
    public $subject;
    public $alipay_config;
    public $orderId;
    public $orderTime;
    public $orderAmount;
    public $parameter = array();

    
    public function __construct($orderAmount){
        $this->orderAmount = $orderAmount;
        $this->subject = '账号充值';
        $this->alipay_config = require(Yii::getPathOfAlias("config")."/alipay/alipay_config.php");
        $this->orderId = date('YmdHis').rand(1000,9999);
        $this->orderTime  = date('YmdHis');
        $this->parameter = array(
                    "service"           => "create_direct_pay_by_user",
                    "payment_type"      => "1",
                    
                    "partner"           => trim($this->alipay_config['partner']),
                    "_input_charset"    => trim(strtolower($this->alipay_config['input_charset'])),
                    "seller_email"      => trim($this->alipay_config['seller_email']),
                    "return_url"        => trim($this->alipay_config['return_url']),
                    "notify_url"        => trim($this->alipay_config['notify_url']),
                    
                    "out_trade_no"      => $this->orderId,
                    "subject"           => $this->subject,
                    "body"              => '',
                    "total_fee"         => $this->orderAmount,
                    
                    "paymethod"         => '',
                    "defaultbank"       => '',
                    
                    "anti_phishing_key" => '',
                    "exter_invoke_ip"   => '',
                    
                    "show_url"          => 'http://jz.fhgame.com/prepaid/pay/type/alipay',
                    "extra_common_param"=> '',
                    
                    "royalty_type"      => '',
                    "royalty_parameters"=> ''
            );
    }


    public function output(){
        $alipay = new Alipayservice($this->alipay_config);
        $form_text = $alipay->create_direct_pay_by_user($this->parameter);
        return array('orderId'=>$this->orderId,'orderTime'=>$this->orderTime,'orderAmount'=>$this->orderAmount,'form_text'=>$form_text);
    }
    
}