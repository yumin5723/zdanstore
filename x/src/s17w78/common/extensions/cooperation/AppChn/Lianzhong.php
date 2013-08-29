<?php
require_once("AppChannelAbstract.php");
class Lianzhong extends AppChannelAbstract {
	public $chn_name = "lianzhong";

    protected $_siteId = "";

    protected $_key = "";

    protected $_secret = "";

    protected $_width = "";

    protected $_ticket_url = "http://wgh.lianzhong.com/Services/RequestTicket.ashx";

    public function init(){
        parent::init();
        $this->_channelID = @$this->_config['ChannelID']?:'';
        $this->_secret = @$this->_config['appsecret']?:'';
    }
    /**
     * function_description
     *
     * 联众Web游戏接入平台提供的与渠道商对接的游戏接入服务
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $request_time = date("YmdHis");
		$this->_gameID = $this->getGameid($appid);
        $Ticket = $this->getTicket($this->_gameID,$request_time);
        // $Ticket = '6F9619FF8B86D011B42D00C04FC964FF';
        if (!$Ticket) {
            return false;
        }
		$CMStatus=0;
		$gamesign = $this->getLoginSign('game',$this->_gameID,$request_time,$uid,$CMStatus);
		$this->_gate_url = $this->getUrl();
        $url = $this->_gate_url."?".http_build_query(array(
                'ChannelID' => $this->_channelID,
                'GameID' => $this->_gameID,
                'UserID' => $uid,
                'CMStatus'=>$CMStatus,
                'Timestamp'=>$request_time,
                'Ticket'=>$Ticket,
                'Version' => 1,
                'Charset' =>'UTF8',
                'Sign'=>$gamesign,
            ));

        return $url;
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
    	$sign = $this->getLoginSign('ticket',$gameID,$request_time);
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
     * @param $sign:
     *
     * @return
     */
    protected function getLoginSign($type,$gameID,$time,$uid=false,$CMStatus=false) {
        if($type =='ticket'){
            $str = $this->_channelID.$gameID.$time.$this->_secret;
        }else{
            $str = $this->_channelID.$gameID.$uid.$CMStatus.$time.$this->_secret;
        }
        return md5(strtoupper($str));
    }
     /**
     * get login request url
     */
    protected function getUrl(){
        return $this->_config['game']['loginUrl'];
    }
    /**
     * get gameID
     */
    protected function getGameid($app_id){
        return $this->_config['gameID'][$app_id]['id'];
    }
    
     public function getStyle($app_id = null){
        return $this->_config['gameID'][$app_id]['style'];
    }
}