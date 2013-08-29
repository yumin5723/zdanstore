<?php

require_once('ChargeChannel.php');

/**
 * basic charge channel for apps that have no their own interfaces
 */

class QksgCharge extends ChargeChannel {
    public $chn_name = "qksg";

    protected $_return_url="http://qksgp1.6511m.tj.twsapp.com/tc/purchase/callback/";

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
	    'paymentid'=> $this->_order->getChargeParam()->getCpOrderId(),
	    'count'=> $this->_order->charge_amt,
	    'action' => 1, // 充值、补充值
	    'result' => 1, //成功
	);
	$cp_key = CPUser::model()->getCpuserKeyById($this->_order->cp_id);
	$s = sprintf("action=%s&count=%s&paymentid=%s&result=%s&key=%s",
	     1, $req_data['count'], $req_data['paymentid'],$req_data['result'],$cp_key
	);
	$req_data['md5'] = md5($s);
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
	    'paymentid'=> $this->_order->getChargeParam()->getCpOrderId(),
	    'count'=> 0,
	    'action' => 1, // 充值、补充值
	    'result' => 0, //失败
	);
	$cp_key = CPUser::model()->getCpuserKeyById($this->_order->cp_id);
	$s = sprintf("action=%s&count=%s&paymentid=%s&result=%s&key=%s",
	     1, $req_data['count'], $req_data['paymentid'],$req_data['result'],$cp_key
	);
	$req_data['md5'] = md5($s);
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