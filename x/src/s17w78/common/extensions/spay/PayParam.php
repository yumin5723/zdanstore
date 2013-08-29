<?php

class PayParam extends CComponent {
    protected $_params = array();

    /**
     * function_description
     *
     * @param $params:
     *
     * @return
     */
    public function __construct($params=null) {
	if (is_string($params) && $t=@unserialize($params)) {
	    $this->_params = $t;
	} elseif (is_array($params)) {
	    $this->_params = $params;
	}
    }

    /**
     * function_description
     *
     * @param $pay_method:
     *
     * @return
     */
    public function setMethod($pay_method) {
	$this->_params['method'] = $pay_method;
	return $this;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethod() {
	if (isset($this->_params['method'])) {
	    return $this->_params['method'];
	}
	// card type equal to pay method
	if (isset($this->_params['card_type'])) {
	    return $this->_params['card_type'];
	}
	return "";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function setChannel($chn) {
	$this->_params['channel'] = $chn;
	return $this;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChannel() {
	return isset($this->params['channel'])?$this->_params['channel']:'';
    }



    /**
     * set card type
     *
     * @param $card_type:
     *
     * @return $this
     */
    public function setCardType($card_type) {
	$this->_params['card_type'] = (string)$card_type;
	return $this;
    }

    /**
     * get card type
     *
     *
     * @return string card type
     */
    public function getCardType() {
	return isset($this->_params['card_type']) ? $this->_params['card_type'] : '';
    }

    /**
     * set card number
     *
     * @param $card_no:
     *
     * @return $this
     */
    public function setCardNumber($card_no) {
	$this->_params['card_no'] = (string) $card_no;
	return $this;
    }

    /**
     * get card number
     *
     *
     * @return string
     */
    public function getCardNumber() {
	return isset($this->_params['card_no']) ? $this->_params['card_no'] : '';
    }

    /**
     * set card password
     *
     * @param $card_pwd:
     *
     * @return $this
     */
    public function setCardPassword($card_pwd) {
	$this->_params['card_pwd'] = (string)$card_pwd;
	return $this;
    }

    /**
     * get card password
     *
     *
     * @return string
     */
    public function getCardPassword() {
	return isset($this->_params['card_pwd']) ? $this->_params['card_pwd'] : '';
    }

    /**
     * set card amount
     *
     * @param $card_amt:
     *
     * @return $this
     */
    public function setCardAmount($card_amt) {
	$this->_params['card_amt'] = (string) $card_amt;
	return $this;
    }

    /**
     * get card amount
     *
     *
     * @return string
     */
    public function getCardAmount() {
	return isset($this->_params['card_amt']) ? $this->_params['card_amt'] : '';
    }

    /**
     * return this params
     *
     *
     * @return
     */
    public function getParams() {
	return $this->_params;
    }

    /**
     * params to string
     *
     *
     * @return
     */
    public function toString() {
	return serialize($this->_params);
    }


}