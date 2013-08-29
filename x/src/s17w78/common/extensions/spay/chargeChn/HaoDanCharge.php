<?php

require_once("ChargeChannel.php");
class HaoDanCharge extends ChargeChannel {
    public $chn_name = "haodan";

    protected $_return_url = "http://www.pokersc.cn/api/addorder.do";

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
    	$charge_key = Cpuser::model()->getCpuserKeyById($this->_order->cp_id);
    	$s = $charge_key;
    	$req_data = array(
    		"appid" => $this->_siteId,
            "goodsinfo" => "1_1_".(int)$this->_order->charge_amt,
            "name" => Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount()),
            "status" => 1,
            "tradeno" => $this->_order->id,
            "type" => $this->_config['type'][$this->_order->app_id],
    	);
    	// ksort($req_data);
        $temp_str = ""; 
        foreach ($req_data as $k => $v) {
            $temp_str .= $k."=".$v."&";
        } 
        $temp_str = trim($temp_str,"&");
        $req_data['sign'] = sha1($temp_str.$charge_key);
    	return array(
    	    $this->getReturnUrl(),
    	    $req_data,
    	    Payment::REQUEST_METHOD_POST
    	);
    }
    /**
     * function_description
     *
     * @param $ret:
     *
     * @return
     */
    public function checkResponse($ret) {
        $ret = json_decode($ret,true);
        if ($ret['success'] === true) {
            return true;
        }
        return false;
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