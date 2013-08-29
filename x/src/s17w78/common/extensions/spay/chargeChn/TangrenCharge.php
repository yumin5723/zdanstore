<?php

require_once("ChargeChannel.php");
class TangrenCharge extends ChargeChannel {
    public $chn_name = "tangren";

    protected $_key = "";
    protected $_server_id = "";
    protected $_appsecret = "";
    protected $_validate_url = "";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	   parent::init();
       $this->_key = @$this->_config['api_key']?:'';
       $this->_server_id = @$this->_config['serverid']?:'';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
        $request_time = time();
    	$charge_key = Cpuser::model()->getCpuserKeyById($this->_order->cp_id);
        $user = User::model()->findByPk($this->_order->getChargeParam()->getGameAccount());
        if(empty($user)){
            return false;
        }
    	$req_data = array(
            "func" => "paynotify",
            "user_id" => Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount()),
            "user_name" =>urlencode($user->nickname),
            'role_id'=>'',
            'areaserver_id'=>'',
            'product_code'=>'',
            'order_id'=>$this->_order->id,
            'amount'=>(int)$this->_order->charge_amt,
            'currency'=>'RMB',
            'result'=>'1',
            'back_send'=>'Y',
            'timestamp'=>$request_time,
    	);
        $str = "amount=".(int)$this->_order->charge_amt."api_key=".$this->_key."areaserver_id=back_send=Ycurrency=RMBfunc=paynotifyorder_id=".$this->_order->id."product_code=result=1role_id=timestamp=".$request_time."user_id=".Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount())."user_name=".urlencode($user->nickname).$charge_key;
        $req_data['sign'] = md5($str);
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
        if ($ret['result'] == 1) {
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
        return $this->_config['game'][$this->_order->app_id]['chargeUrl'];
    }
}