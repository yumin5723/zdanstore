<?php

require_once("ChargeChannel.php");
class PokerKingCharge extends ChargeChannel {
    public $chn_name = "pokerking";

    protected $_return_url = "http://www.dzpk.cn/platform/share/pay.ashx";

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
        // $s = $cp_key;
    	$req_data = array(
    		"partner_id" => $this->_siteId,
            "server_id" => "100",
            "login_name" => Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount()),
            "money" => (int)$this->_order->charge_amt,
            "order_no" => $this->_order->id,
    	);
    	// ksort($req_data);
        $s = ""; 
        foreach ($req_data as $val) {
            $s .= $val;
        }
        $req_data['token'] = md5($s.$cp_key);
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
	   return '1';
    }

}