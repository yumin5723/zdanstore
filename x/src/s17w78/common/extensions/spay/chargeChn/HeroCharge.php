<?php
require('ChargeChannel.php');

/**
 * basic charge channel for apps that have no their own interfaces
 */

class HeroCharge extends ChargeChannel {
    public $chn_name = "hero";

    protected $_return_url="http://wow.youxi181.com/pay.aspx";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	parent::init();
    }

    protected function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
	    $arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);

	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
	$req_data = array(
	    'app_id' => $this->_order->app_id,
	    'game_account' => $this->_order->getChargeParam()->getGameAccount(),
	    'order_id' => $this->_order->id,
	    'amount' => $this->_order->charge_amt,
	);
	ksort($req_data);
	reset($req_data);
	$t_query = $this->createLinkString($req_data);
	$cp_key = CPUser::model()->getCpuserKeyById($this->_order->cp_id);
	$req_data['sign'] = sha1($t_query.$cp_key);
	return array(
	    $this->_return_url,
	    $req_data,
	    Payment::REQUEST_METHOD_GET,
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