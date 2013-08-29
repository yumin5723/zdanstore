<?php

abstract class ChargeChannel {
    public $chn_name = "basic";

    protected $_order = null;

    protected $_config = null;

    protected $_return_url="";

    protected $_rate = "";

    protected $_game_money_name = "";

    protected $_company = "";

    /**
     * function_description
     *
     * @param $order:
     * @param $params:
     *
     * @return
     */
    public function __construct($params=array()) {
	//set params
	foreach ($params as $k => $v) {
	    $this->$k = $v;
	}

	$this->init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnUrl() {
	$app = App::model()->findByPk($this->_order->app_id);
	if (!empty($app) && !empty($app->charge_url)) {
	    return $app->charge_url;
	}
	return $this->_return_url;
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
    	$this->loadConfig();
        $this->_rate = @$this->_config['rate']?:'1';
        $this->_game_money_name = @$this->_config['game_money_name']?:'金币';
        $this->_company = @$this->_config['company']?:'';
    }


    /**
     * function_description
     *
     * @param $order:
     *
     * @return
     */
    public function setOrder($order) {
	$this->_order = $order;
	return $this;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function loadConfig() {
	$this->_config = require(Yii::app()->payment->config_path."/charge_channel_".$this->chn_name.".php");
    }

    /**
     * function_description
     *
     * @param $ret:
     *
     * @return
     */
    public function checkResponse($ret) {
	   return $ret == $this->getExpectation();
    }
    /*
     * get charge request for charge
     * return [url, params, method]
     */
    abstract function getChargeRequest();

    /*
     *  get expected return string
     */
    abstract function getExpectation();

    /*
     * return [url, params, method]
     */
    function getFailNotifyRequest() {
	return [null, null, null];
    }

    function getChargeRate($appid){
        if(!empty($this->_rate)){
            return [$this->_rate,$this->_game_money_name];
        }else{
            return [$this->_config['game'][$appid]['rate'],$this->_game_money_name];
        }
    }
    /*
     * return hongxiang pinxiang value list
     */
    function getPxlist($data){
        $html = '';
        foreach ($data as $key => $value) {
            if($key==0){
                $check = 'checked="checked"';
            }else{
                $check ='';
            }
            $html .='<input type="radio" id="lab_'.$key.'" '.$check.' value="'.$value.'" name="Order[order_amt]" >'.$value.'元&nbsp;&nbsp;';
        }
        return $html;
    }
}