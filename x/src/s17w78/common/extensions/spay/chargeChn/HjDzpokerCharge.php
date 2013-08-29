<?php

require_once("ChargeChannel.php");
class HjDzpokerCharge extends ChargeChannel {
    public $chn_name = "huangjiadp";

    protected $_key = "";
    protected $_server_id = "";
    protected $_appsecret = "";
    protected $_validate_url = "";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	   parent::init();
       $this->_siteid = @$this->_config['api_id']?:'';
       $this->_secret = @$this->_config['appsecret']?:'';
       // $this->_server_id = @$this->_config['serverid']?:'';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
        $charge_key = Cpuser::model()->getCpuserKeyById($this->_order->cp_id);
        $calSig = md5($this->_siteid.$charge_key);
        $userid = Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount());
    	$req_data = array(
            "userid" => $userid,
            'Orderid' =>$this->_order->id,
            'amount'=>(int)$this->_order->charge_amt,
            'Siteid'=>$this->_siteid,
            'Secretkey'=>$charge_key,
            "Method" =>'jinbian.pay',
            'calSig'=>$calSig,
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
        if ($ret == 'ok') {
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
     * get ChargeUrl
     */
    public function getChargeUrl(){
        return $this->_config['game'][$this->_order->app_id]['chargeUrl'];
    }
}