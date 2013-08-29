<?php

require_once("ChargeChannel.php");
Yii::import('common.models.Cpuser');
class GreenCharge extends ChargeChannel {
    public $chn_name = "green";

    protected $_return_url = "http://dzpk.phantom78.com:8080/orderApi/pay.php";

    protected $_siteId = "";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	   parent::init();
       $this->_siteId = @$this->_config['siteid']?:'';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
    	$cp_key = App::model()->getChargeKeyById($this->_order->app_id);
    	$s = $cp_key;
    	$req_data = array(
    		"sid" => $this->_siteId,
    		"uid" => Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount()),
    	    "oid" => $this->_order->id,
            "money" => $this->_order->charge_amt * 100,
    	);
    	// ksort($req_data);
        foreach ($req_data as $val) {
            $s .= $val;
        }
    	$req_data['sig'] = md5($s);
    	return array(
    	    $this->getReturnUrl(),
    	    $req_data,
    	    Payment::REQUEST_METHOD_GET
    	);
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



}