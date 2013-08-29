<?php
require_once("PlatformChannelAbstract.php");
class Douban extends PlatformChannelAbstract {
	public $chn_name = "douban";

	protected $_app_id = "";

	protected $_app_secret = "";

	protected $_requestUrl = "";

	protected $_tokenUrl = "";

	protected $_graphUrl = "";

	// protected $_userInfoUrl = "";
	public function init(){
		parent::init();
		$this->_key = @$this->_config['app_secret']?:'';
        $this->_siteId = @$this->_config['app_id']?:'';
        $this->_requestUrl = @$this->_config['request_url'] ?:'';
        $this->_tokenUrl = @$this->_config['token_url']?:'';
        $this->_graphUrl = @$this->_config['graph_url']?:'';
        // $this->_userInfoUrl = @$this->_config['userinfo_url']?:'';
	}
	/**
	 * get login url to redirect
	 */
	public function getLoginUrl(){
		$url = $this->_requestUrl."&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl();
		return $url;
	}
	/**
	 * get third part platform return userinfo
	 * include nickname and avatar
	 * @return [type] [description]
	 */
	public function getPlatformUserInfo($receive_params){
		if (isset($receive_params['code'])) {
			$token_url = $this->_tokenUrl;//."&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl()."&client_secret=".$this->_key."&code=".$receive_params['code'];
			$data = array(
				"client_id" => $this->_siteId,
				"redirect_uri" => $this->getCallbackUrl(),
				"client_secret" => $this->_key,
				"code" => $receive_params['code'],
				"grant_type" => "authorization_code",
			);
			$response = Yii::app()->curl->post($token_url,$data);
			$msg = json_decode($response,true);
			if (isset($msg->code)){
		          	return [false, "false", $msg->msg];
		    }
			$params=array();
            $graphUrl=$this->_graphUrl;
            $userinfo = $this->api($graphUrl, $params,'GET',$msg['access_token']);
            $userdata =array();
            $userdata['platform']=$this->chn_name;
			$userdata['platform_uid']=$userinfo['id'];
			$userdata['platform_name']=$userinfo['name'];
			$userdata['nickname']=$userinfo['name'];
			$userdata['avatar']=$userinfo['avatar'];
			$userdata['gender']=1;
			return [true,"0",$userdata];
		}else {
			return [false, "false", "The state does not match. You may be a victim of CSRF."];
  		}

		
	}
	public function api($url, $params, $method='GET',$token){
		$headers[]="Authorization: Bearer ".$token;
		if($method=='GET'){
			$result=$this->http($url.'?'.http_build_query($params), '', 'GET', $headers);
		}
		return $result;
	}
	private function http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]="User-Agent: doubanPHP(piscdong.com)";
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		$json_r=array();
		if($response!='')$json_r=json_decode($response, true);
		return $json_r;
	}
}
