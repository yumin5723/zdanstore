<?php
require('ChargeChannel.php');

/**
 * basic charge channel for apps that have no their own interfaces
 */

class BasicCharge extends ChargeChannel {
    public $chn_name = "basic";

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
	/* 'r0_Method', */
	/* 'r1_Code', */
	/* 'r3_OrderNo' */
	/* 'r4_RetMsg' */
	/* 'q1_MerId', */
	/* 'q2_OrderNo', */
	/* 'q3_Amount', */
	/* 'qa_Memo', */

	$ret_data = array(
	    'r0_Method'=>Yii::app()->payment->getPayMethodStringForOrderType($this->_order->order_type),
	    'r1_Code' => intval($this->_order->pay_status == Order::PAY_STATUS_SUCCESS_CALLBACK),
	    'r3_OrderNo' => $this->_order->id,
	    'r4_RetMsg' => '',
	    'q1_MerId' => $this->_order->cp_id,
	    'q3_Amount' => $this->_order->charge_amt,
	    'qa_Memo' => '',
	);
	$charge_param = $this->_order->getChargeParam();
	$ret_data['qa_Memo'] = $charge_param->getCallbackMemo();
	$ret_data['hmac'] = Yii::app()->payment->getHashString($ret_data, $this->_order->cp_id, Payment::COMMUNICATE_TYPE_BASIC_CHARGE);
	return [$charge_param->getReturnUrl(), $ret_data, Payment::REQUEST_METHOD_GET];
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