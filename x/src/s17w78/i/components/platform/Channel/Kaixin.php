<?php
require_once("PlatformChannelAbstract.php");
class Kaixin extends PlatformChannelAbstract {
	public $chn_name = "kaixin";

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
		$state = md5(uniqid(rand(), TRUE));
		$url = $this->_requestUrl."&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl()."&state=".$state;
		return $url;
	}
	/**
	 * get third part platform return userinfo
	 * include nickname and avatar
	 * @return [type] [description]
	 */
	public function getPlatformUserInfo($receive_params){
		if (isset($receive_params['code'])) {
			$token_url = $this->_tokenUrl."&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl()."&client_secret=".$this->_key."&code=".$receive_params['code'];
			if($response = @file_get_contents($token_url)) {
			    $msg = json_decode($response,true);
				if (isset($msg->error_code)){
			          	return [false, "false", $msg->error];
			    }
			    
			    // $graph_url = $this->_graphUrl.$msg['access_token'];
			    $userinfo_url = $this->_userInfoUrl.$msg['access_token'];
	     		$str  = @file_get_contents($userinfo_url);
	     		$userinfo = json_decode($str,true);
	     		$userdata =array();
	            $userdata['platform']=$this->chn_name;
				$userdata['platform_uid']=$userinfo['uid'];
				$userdata['platform_name']=$userinfo['name'];
				$userdata['nickname']=$userinfo['name'];
				$userdata['avatar']=$userinfo['logo50'];
				if($userinfo['gender']==1){
					$userdata['gender']=3;
				}else{
					$userdata['gender']=2;
				}
				return [true,"0",$userdata];

			}else {
			    return [false, "false", "The url can't visit."];
			}
			
		}else {
			return [false, "false", "The state does not match. You may be a victim of CSRF."];
  		}
	}
}
