<?php

require_once("ChargeChannel.php");
class LianzhongCharge extends ChargeChannel {
    public $chn_name = "lianzhong";

    protected $_key = "";
    protected $_server_id = "";
    protected $_appsecret = "";
    protected $_validate_url = "";

    protected $_ticket_url = "http://wgh.lianzhong.com/Services/RequestTicket.ashx";


    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	   parent::init();
       $this->_channelID = @$this->_config['ChannelID']?:'';
       $this->_secret = @$this->_config['appsecret']?:'';
       // $this->_server_id = @$this->_config['serverid']?:'';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeRequest() {
        $request_time = date("YmdHis");
    	$charge_key = Cpuser::model()->getCpuserKeyById($this->_order->cp_id);
        $user = User::model()->findByPk($this->_order->getChargeParam()->getGameAccount());
        if(empty($user)){
            return false;
        }
        $gameID = $this->getGameid($this->_order->app_id);
        $PayUser = Yii::app()->openid->getUserOpenidForApp($this->_order->app_id,$this->_order->getChargeParam()->getGameAccount());
        $Ticket = $this->getTicket($gameID,$request_time);
    	$req_data = array(
            "ChannelID" => $this->_channelID,
            'GameID' =>$gameID,
            "PayUser" => $PayUser,
            'Amount'=>(int)$this->_order->charge_amt,
            'OrderID'=>$this->_order->id,
            'Timestamp'=>$request_time,
            'Ticket'=>$Ticket,
            "Version" =>1,
            'Charset'=>'UTF8',
    	);
        $str = $this->_channelID.$gameID.$PayUser.(int)$this->_order->charge_amt.$this->_order->id.$request_time.$charge_key;
        $req_data['sign'] = md5(strtoupper($str));
        return array(
    	    $this->getChargeUrl(),
    	    $req_data,
    	    Payment::REQUEST_METHOD_GET
    	);
    }
    /**
     * function_description
     * //为渠道商颁发请求服务用一次性Ticket的服务
     * @param $Ticket:
     *
     * @return
     */
    protected function getTicket($gameID,$request_time)
    {
        $sign = md5(strtoupper($this->_channelID.$gameID.$request_time.$this->_secret));
        if (!$sign) {
            return false;
        }
        $url = $this->_ticket_url."?".http_build_query(array(
                'ChannelID' => $this->_channelID,
                'GameID' => $gameID,
                'Timestamp' => $request_time,
                'Sign'=> $sign,
            ));
        $data = @file_get_contents($url);
        $xml_array=simplexml_load_string($data); //将XML中的数据,读取到数组对象中 
        if($xml_array->State ==1){//1调用参数错误  0 操作成功 
            return false;//$xml_array->Message;
        }
        $arr = (array)$xml_array->Data;
        return $arr[0];
    }
    /**
     * function_description
     *
     * @param $ret:
     *
     * @return
     */
    public function checkResponse($ret) {
        $xml_array=simplexml_load_string($ret); //将XML中的数据,读取到数组对象中 
        if($xml_array->State ==0){//1调用参数错误  0 操作成功 
            return true;//$xml_array->Message;
        }
        return false;

        // $ret = json_decode($ret,true);
        // if ($ret['result'] == 1) {
        //     return true;
        // }
        // return false;
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
     * get ChargeUrl
     */
    public function getChargeUrl(){
        return $this->_config['game']['chargeUrl'];
    }
     /**
     * get gameID
     */
    protected function getGameid($app_id){
        return $this->_config['gameID'][$app_id]['id'];
    }
}