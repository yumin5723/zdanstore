<?php

require_once("ChargeChannel.php");
class QianshouCharge extends ChargeChannel {
    public $chn_name = "qianshou";

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
        $sitemid = Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount());

        $amount = (int)$this->_order->charge_amt;
        $str = $sitemid.$amount.$this->_order->id.$request_time.$charge_key;
        $sign = md5($str);
    	$req_data = array(
            "sitemid" => $sitemid,
            'rmb'=>$amount,
            'gold'=>$amount*$this->_rate,
            'orderid'=>$this->_order->id,
            'ts'=>$request_time,
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
        return $this->_config['game'][$this->_order->app_id]['chargeUrl'];
    }
}