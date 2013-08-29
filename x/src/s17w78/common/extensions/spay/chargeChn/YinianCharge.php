<?php

require_once("ChargeChannel.php");
class YinianCharge extends ChargeChannel {
    public $chn_name = "yinian";

    protected $_key = "";

    protected $_rate = "";

    protected $_siteid = "";

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
       $this->_siteid = @$this->_config['siteid']?:'';
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
        $str = 'siteid='.$this->_siteid.'&secretkey='.$this->_key.'&stime='.$request_time.'&userid='.$uid.'&orderid='.$this->_order->id.'&amount='.$amount;
        $sign = md5($str);
        $req_data = array(
            "userid" => $uid,
            'orderid' => $this->_order->id,
            'amount'=>$amount,
            'siteid'=>$this->_siteid,
            'stime'=>$request_time,
            'sign'=>$sign, 
        );
        return array(
            $this->getChargeUrl(),
            $req_data,
            Payment::REQUEST_METHOD_POST
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
        $ret = json_decode($ret,true);
        if ($ret['ret'] == 0 && $ret['msg'] == "OK") {
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

    public function getChargeUrl(){
        return $this->_config['game']['chargeUrl'];
    }
}