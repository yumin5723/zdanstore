<?php
require_once("PlatformChannelAbstract.php");
class Renren extends PlatformChannelAbstract {
	public $chn_name = "renren";

	protected $_app_id = "";

	protected $_app_secret = "";

	protected $_requestUrl = "";

	protected $_tokenUrl = "";

	protected $_graphUrl = "";

	protected $_userInfoUrl = "";
	public function init(){
		parent::init();
		$this->_key = @$this->_config['app_secret']?:'';
        $this->_siteId = @$this->_config['app_id']?:'';
        $this->_requestUrl = @$this->_config['request_url'] ?:'';
        $this->_tokenUrl = @$this->_config['token_url']?:'';
        $this->_graphUrl = @$this->_config['graph_url']?:'';
        $this->_userInfoUrl = @$this->_config['userinfo_url']?:'';
	}
	/**
	 * get login url to redirect
	 */
	public function getLoginUrl(){
		$_SESSION['state'] = md5(uniqid(rand(), TRUE)); 
		$url = $this->_requestUrl."&client_id=".$this->_siteId."&redirect_uri=".urlencode($this->getCallbackUrl()).'&state='.$_SESSION['state'];
		return $url;
	}
	/**
	 * get third part platform return userinfo
	 * include nickname and avatar
	 * @return [type] [description]
	 */
	public function getPlatformUserInfo($receive_params){
		if(isset($receive_params['code'])){
			//get token
			$token_url = $this->_tokenUrl."&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl()."&client_secret=".$this->_key."&code=".$receive_params['code'];
			$url ="https://graph.renren.com/oauth/token?grant_type=authorization_code&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl()."&client_secret=".$this->_key."&code=".$receive_params['code'];
			$response = @file_get_contents($url);
			$userInfo = json_decode($response,true);
	        if (isset($userInfo->error)){
	          	return [false, "false", $userInfo->error_description];
	        }
		    $userdata =array();
		    $userdata['platform']=$this->chn_name;
			$userdata['platform_uid']=$userInfo['user']['id'];
			$userdata['platform_name']=$userInfo['user']['name'];
			$userdata['nickname']=$userInfo['user']['name'];
			$userdata['avatar']=$userInfo['user']['avatar'][0]['url'];
			$userdata['gender']=1;
		    return [true,"0",$userdata];
		}else {
			return [false, "false", "The state does not match. You may be a victim of CSRF."];
  		}
	}
}