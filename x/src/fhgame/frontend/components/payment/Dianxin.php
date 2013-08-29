<?php
class Dianxin {
    
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
    public $fullAmountFlag;
    public $bossType;


    public function __construct($orderAmount){
        $this->theAmount = $orderAmount;
        $this->merchantAcctId = "1002152027604";
        $this->key = "RMUY9BANLYW5XMGX";
        $this->inputCharset = "1";
        $this->bgUrl = "http://www.jingame.com/dbweb/new/99bill_dx/receive.asp";
        $this->version = "v2.0";
        $this->language = "1";
        $this->signType = "1";
        $this->orderId = date('YmdHis').rand(1000,9999);
        $this->orderAmount = $this->theAmount * 100;
        $this->orderTime = date('YmdHis');
        $this->payType = "42";
        $this->fullAmountFlag = "0";
        $this->bossType = "3";
        
    }


    public function output(){
        $signMsgVal = "inputCharset=".$this->inputCharset."&bgUrl=".$this->bgUrl."&version=".$this->version."&language=".$this->language."&signType=".$this->signType."&merchantAcctId=".$this->merchantAcctId."&orderId=".$this->orderId."&orderAmount=".$this->orderAmount."&payType=".$this->payType."&fullAmountFlag=".$this->fullAmountFlag."&orderTime=".$this->orderTime."&bossType=".$this->bossType."&key=".$this->key;
        $signMsg= strtoupper(md5($signMsgVal));
        return array('merchantAcctId'=>$this->merchantAcctId,'inputCharset'=>$this->inputCharset,
                                                'bgUrl'=>$this->bgUrl,'version'=>$this->version,'language'=>$this->language,
                                                'signType'=>$this->signType,'orderId'=>$this->orderId,'orderAmount'=>$this->orderAmount,
                                                'theAmount'=>$this->theAmount,'orderTime'=>$this->orderTime,'payType'=>$this->payType,
                                                'fullAmountFlag'=>$this->fullAmountFlag,'bossType'=>$this->bossType,'signMsg'=>$signMsg);
    }
    
}