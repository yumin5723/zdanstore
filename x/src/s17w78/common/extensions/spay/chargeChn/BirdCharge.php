<?php

require_once("ChargeChannel.php");

class BirdCharge extends ChargeChannel {
    public $chn_name = "bird";

    protected $_return_url = "http://xnapdp1.2231m.tj.twsapp.com/pay/cb/qc/card.do";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	parent::init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
	$req_data = array(
	    "username" => $this->_order->getChargeParam()->getGameAccount(),
	    "orderId" => $this->_order->getChargeParam()->getCpOrderId(),
	    "money"	=> $this->_order->charge_amt,
	    "status" => 1,
	);
	ksort($req_data);
	$s = "";
	foreach ($req_data as $val) {
	    $s .= $val;
	}
	$cp_key = CPUser::model()->getCpuserKeyById($this->_order->cp_id);
	$s .= $cp_key;
	$req_data['sign'] = md5($s);
	return array(
	    $this->getReturnUrl(),
	    $req_data,
	    Payment::REQUEST_METHOD_POST
	);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getFailNotifyRequest() {
	$req_data = array(
	    "username" => $this->_order->getChargeParam()->getGameAccount(),
	    "orderId" => $this->_order->getChargeParam()->getCpOrderId(),
	    "money"	=> 0,
	    "status" => 0,
	);
	ksort($req_data);
	$s = "";
	foreach ($req_data as $val) {
	    $s .= $val;
	}
	$cp_key = CPUser::model()->getCpuserKeyById($this->_order->cp_id);
	$s .= $cp_key;
	$req_data['sign'] = md5($s);
	if ($pay = $this->_order->getPay()) {
	    $req_data['msg'] = $pay->pay_msg;
	}
	return array(
	    $this->getReturnUrl(),
	    $req_data,
	    Payment::REQUEST_METHOD_POST
	);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getExpectation() {
	return "success";
    }



}