<?php

require_once("ChargeChannel.php");
class ChenchangCharge extends ChargeChannel {
    public $chn_name = "chenchang";

    protected $_key = "";

    protected $_rate = "";
    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
       parent::init();
       $this->_key = @$this->_config['key']?:'';
       $this->_rate = @$this->_config['rate']?:'';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
        $request_time = time();
        $charge_key = Cpuser::model()->getCpuserKeyById($this->_order->cp_id);
        $uid = Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount());
        $amount = (int)$this->_order->charge_amt;
        $gameRate = $this->getRate();
        $gameId = $this->getGameId();
        $str = "userName=".$uid."&score=".$amount*$gameRate."&key=".$charge_key."&siteId=1378.com";
        $sign = md5($str);
        $req_data = array(
            "userName" => $uid,
            "siteId" => "1378.com",
            'gameId'=>$gameId,
            'score'=>$amount*$gameRate,
            'sign'=>$sign, 
            );
        return array(
            $this->getChargeUrl(),
            $req_data,
            Payment::REQUEST_METHOD_GET
        );
    }
    /**
     * function_description
     *
     * @param $ret:
     *
     * @return
     */
    public function checkResponse($ret) {
        $xml_array=simplexml_load_string($ret); //将XML中的数据,读取到数组对象中 
        if($xml_array->returnCode ==0){
            return true;
        }
        return false;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getFailNotifyRequest() {
        return array(
            null,null,null
        );
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getExpectation() {
       return '{"status":"true"}';
    }
     /**
     * function_description
     * getchargeurl 
     * 
     * @return
     */
    public function getChargeUrl(){
        return $this->_config['game']['chargeUrl'];
    }
     /**
     * function_description
     * param = appid
     *
     * @return
     */
    public function getRate() {
        return $this->_config['game'][$this->_order->app_id]['rate'];
    }
    /**
     * function_description
     * get gameid
     * param = appid
     * @return
     */
    public function getGameId() {
        return $this->_config['game'][$this->_order->app_id]['gameID'];
    }
}