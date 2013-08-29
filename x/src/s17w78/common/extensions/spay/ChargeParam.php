<?php

class ChargeParam extends CComponent {
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
     * @param $game_account:
     *
     * @return
     */
    public function setGameAccount($game_account) {
	$this->_params['game_acc'] = (string) $game_account;
	return $this;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getGameAccount() {
	return isset($this->_params['game_acc'])?$this->_params['game_acc']:'';
    }

    /**
     * set cp order id
     *
     * @param $id:
     *
     * @return
     */
    public function setCpOrderId($id) {
	$this->_params['order_id'] = (string) $id;
	return $this;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getCpOrderId() {
	return @$this->_params['order_id']?:0;
    }



    /**
     * set callback url
     *
     * @param $ret_url:
     *
     * @return $this
     */
    public function setReturnUrl($ret_url) {
	$this->_params['ret_url'] = (string) $ret_url;
	return $this;
    }

    /**
     * get return url
     *
     *
     * @return string
     */
    public function getReturnUrl() {
	return isset($this->_params['ret_url']) ? $this->_params['ret_url'] : '';
    }

    /**
     * set callback memo
     *
     * @param $memo:
     *
     * @return $this
     */
    public function setCallbackMemo($memo) {
	$this->_params['memo'] = (string) $memo;
	return $this;
    }

    /**
     * get callback memo
     *
     *
     * @return string
     */
    public function getCallbackMemo() {
	return isset($this->_params['memo']) ? $this->_params['memo'] : '';
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