<?php
Yii::import("config.alipay.alipay_config");
class Kuaiqian {
    
    public $theAmount;
    public $merchantAcctId;
    public $key;
    public $inputCharset;
    public $bgUrl;
    public $version;
    public $language;
    public $signType;
    public $orderId;
    public $orderAmount;
    public $orderTime;
    public $payType;

    public function __construct($orderAmount){
        $this->theAmount = $orderAmount;
        $this->merchantAcctId = "1002152027601";
        $this->key = "G5HSGSE7IJFI6FD4";
        $this->inputCharset = "1";
        $this->bgUrl = "http://www.jingame.com/dbweb/new/99bill/receive2.asp";
        $this->version = "v2.0";
        $this->language = "1";
        $this->signType = "1";
        $this->orderId = date('YmdHis').rand(1000,9999);
        $this->orderAmount = $this->theAmount * 100;
        $this->orderTime = date('YmdHis');
        $this->payType = "00";
        
    }


    public function output(){
        $signMsgVal = "inputCharset=".$this->inputCharset."&bgUrl=".$this->bgUrl."&version=".$this->version."&language=".$this->language."&signType=".$this->signType."&merchantAcctId=".$this->merchantAcctId."&orderId=".$this->orderId."&orderAmount=".$this->orderAmount."&orderTime=".$this->orderTime."&payType=".$this->payType."&key=".$this->key;
        $signMsg= strtoupper(md5($signMsgVal));
        return array('merchantAcctId'=>$this->merchantAcctId,'inputCharset'=>$this->inputCharset,
                                                'bgUrl'=>$this->bgUrl,'version'=>$this->version,'language'=>$this->language,
                                                'signType'=>$this->signType,'orderId'=>$this->orderId,'orderAmount'=>$this->orderAmount,
                                                'theAmount'=>$this->theAmount,'orderTime'=>$this->orderTime,'payType'=>$this->payType,'signMsg'=>$signMsg);
    }
    
}