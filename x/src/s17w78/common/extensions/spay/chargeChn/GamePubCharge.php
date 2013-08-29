<?php
http://dice.6998.com/passport/1378.aspx?pid=1378&sid=888881&uid=Z8zJAtw2yVPmitcrZKmJk0Wl8ZSfl3Kk&nickname=%25E6%2588%2591%25E4%25B8%258D%25E6%2598%25AF%25281111%2529&timestamp=1375673388&sign=cedef6a46a29b2969f01dafcdb539ce35e464bd1

require_once("ChargeChannel.php");
class GamePubCharge extends ChargeChannel {
    public $chn_name = "gamepub";

    protected $_serverId = "";
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
        $this->_serverId = $this->getServerid($this->_order->app_id);
    	$charge_key = App::model()->getChargeKeyById($this->_order->app_id);
    	$s = $charge_key;
    	$req_data = array(
    		"pid" => App::model()->getGateIdById($this->_order->app_id),
            "sid" => $this->_serverId,
            "uid" => Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount()),
            "amount" => (int)$this->_order->charge_amt,
            "tradeno" => $this->_order->id,
            "timestamp" => time(),
    	);
    	// ksort($req_data);
        $temp_str = ""; 
        foreach ($req_data as $k => $v) {
            $temp_str .= $v;
        } 
        $req_data['sign'] = sha1($temp_str.$charge_key);
    	return array(
    	    $this->getReturnUrl(),
    	    $req_data,
    	    Payment::REQUEST_METHOD_GET
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
    /**
     * get game or server id
     * @param  [type] $appid [description]
     * @return string
     */
    public function getServerid($appid){
        $app = $this->_config[$appid];
        if(!isset($app)){
            throw new AppChannelException('app id can not be null');
        }
        $serverid = $app['serverid'];
        if(!isset($serverid)){
            throw new AppChannelException('serverid can not be null');
        }
        return $serverid;
    }

    public function getChargeRate($appid){
        return [$this->_config[$appid]['rate'],$this->_config[$appid]['game_money_name']];
    }

}