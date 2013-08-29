<?php

require_once("ChargeChannel.php");
class HongxiangCharge extends ChargeChannel {
    public $chn_name = "hongxiang";

    protected $_key = "";

    protected $_rate = "";
    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	   parent::init();
       $this->_key = @$this->_config['key']?:'';
       $this->_rate = @$this->_config['rate']?:'';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
        $charge_key = Cpuser::model()->getCpuserKeyById($this->_order->cp_id);
        $UserID = Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount());

        $amount = (int)$this->_order->charge_amt;
        $gameId = $this->getGameId($this->_order->app_id);
        $str = $UserID.'|'.$amount.'|'.$this->_order->id.'|'.$gameId.'|'.$charge_key;
        $sign = md5($str);
        $req_data = array(
            "strUserID" => $UserID,
            'nAmount'=>$amount,
            'strShopOrderID'=>$this->_order->id,
            'strProductID'=>$gameId,
            'strSign'=>$sign, 
        );
        return array(
    	    $this->getChargeUrl(),
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
        if ($ret['strStatusCode'] == "0000") {
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

    public function getChargeUrl(){
        return $this->_config['game']['chargeUrl'];
    }
      /**
     * get gameID
     */
    protected function getGameId($app_id){
        return $this->_config['gameID'][$app_id]['id'];
    }
}