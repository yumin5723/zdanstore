<?php

require_once("ChargeChannel.php");
class QiangdpCharge extends ChargeChannel {
    public $chn_name = "qiangdp";

    protected $_key = "";

    protected $_rate = "";

    protected $_fromid = "";

    protected $_serverid = "";
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
       $this->_fromid = @$this->_config['fromid']?:'';
       $this->_serverid = @$this->_config['serverid']?:'';
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
        $uid = Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount());

        $amount = (int)$this->_order->charge_amt;
        $str = $this->_fromid.$amount.$amount*$this->_rate.$this->_order->id.$request_time.$charge_key;
        $str = mb_convert_encoding($str, "gbk","utf8");
        $sign = strtolower(md5(strtolower($str)));
        $req_data = array(
            "sitemid" => (int)$this->_fromid,
            'loginname' => $uid,
            'rmb'=>$amount,
            'gold'=>$amount*$this->_rate,
            'orderid'=>$this->_order->id,
            'time'=>$request_time,
            'sign'=>$sign, 
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
        if ($ret['success'] == true && $ret['msg'] == "OK") {
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
}