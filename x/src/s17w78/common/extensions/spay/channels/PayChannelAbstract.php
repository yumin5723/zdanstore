<?php

abstract class PayChannelAbstract  {
    public $channel_name = '';

    protected $_method_id = null;
    protected $_order = null;
    protected $_config = null;

    protected $_custom_msg=null;

    protected $_notify_data = null;

    /**
     * function_description
     *
     *
     * @return
     */
    public function __construct() {
	$this->init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	$this->loadConfig();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function loadConfig() {
	$this->_config = require_with_local(Yii::app()->payment->config_path."/channel_".$this->channel_name.".php");
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyUrl() {
	return Yii::app()->payment->notifyUrlPrefix."/".$this->channel_name;
    }

    public function setMethodId($method_id){
	if (!isset($this->_config['methods'][$method_id])) {
	    throw new PaymentException("method not found:".$method_id);
	}
	$this->_method_id = $method_id;
	return $this;
    }

    /**
     * get method id
     *
     *
     * @return
     */
    public function getMethodId() {
	return $this->_method_id;
    }


    /**
     * function_description
     *
     * @param $msg:
     *
     * @return
     */
    public function setCustomMsg(array $msgs) {
	$this->_custom_msg = $msg;
	return $this;
    }

    /**
     * add custom msg
     *
     * @param $key:
     * @param $val:
     *
     * @return
     */
    public function addCustomMsg($key, $val) {
	if (is_null($this->_custom_msg)) {
	    $this->_custom_msg = array();
	}
	$this->_custom_msg[$key] = $val;
	return $this;
    }

    /**
     * custom msg arrty to string
     *
     *
     * @return
     */
    protected function getCustomMsgOutString() {
	return GHelper::urlBase64Encode(json_encode($this->_custom_msg));
    }

    /**
     * set custom msg from string
     *
     *
     * @return
     */
    protected function setCustomMsgFromString($json) {
	$this->_custom_msg = (array) json_decode(GHelper::urlBase64Decode($json));
	return $this;
    }

    /**
     * get request url and data for submit to pay channel
     *
     *
     * @return [request_url, data] or error
     */
    abstract public function getOuterData();

    public function setOrder($order){
	$this->_order = $order;
	return $this;
    }

    public function setNotify($notify_params) {
	$this->_notify_data = $notify_params;
	return $this;
    }

    abstract public function validNotify();

    /**
     * check the notify request is from user browser or from channel server
     * for some channal use same notify url for both request
     *
     * @return
     */
    public function getNotifyReqType() {
	return Payment::NOTIFY_TYPE_UNKNOW;
    }

    abstract public function isNotifySuccess();
    abstract public function getMethodIdFromNotify();
    abstract public function getReturnStringForNotify();
    abstract protected function setCustomMsgFromNotify();

    abstract public function getNotifyOrderId();

    abstract public function getPayAmount();

    public function getRealAmount() {
	$method_id = $this->getMethodIdFromNotify();
	if (empty($method_id)) {
	    return false;
	}
	if (!isset($this->_config['methods'][$method_id]['price']['real'])) {
	    return $this->getPayAmount();
	} else {
	    return floatval($this->getPayAmount()) * floatval($this->_config['methods'][$method_id]['price']['real']);
	}
    }
    public function getChargeAmount() {
	$method_id = $this->getMethodIdFromNotify();
	if (empty($method_id)) {
	    return false;
	}
	if (!isset($this->_config['methods'][$method_id]['price']['charge'])) {
	    return $this->getPayAmount();
	} else {
	    return floatval($this->getPayAmount()) * floatval($this->_config['methods'][$method_id]['price']['charge']);
	}

    }


    public function getNotifyPayId() {
	$this->setCustomMsgFromNotify();
	if (isset($this->_custom_msg['pay_id'])) {
	    return $this->_custom_msg['pay_id'];
	} else {
	    return null;
	}
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyOrder() {
	   return Order::model()->findByPk($this->getNotifyOrderId());
    }

    /**
     * function_description
     *
     * @param $ret_body:
     *
     * @return
     */
    public function getReturnMsg($ret_body) {
	return "";
    }

    /**
     * function_description
     *
     * @param $notify_body:
     *
     * @return
     */
    public function getNotifyMsg() {
	return "";
    }




}