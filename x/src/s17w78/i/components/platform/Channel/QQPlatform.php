<?php
require_once("PlatformChannelAbstract.php");
class QQPlatform extends PlatformChannelAbstract {
	public $chn_name = "qq";

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
		$state = Yii::app()->session['state'] = md5(uniqid(rand(),true));
		$url = $this->_requestUrl."&client_id=".$this->_siteId."&redirect_uri=".urlencode($this->getCallbackUrl())."&state=".$state;
		return $url;
	}
	/**
	 * get third part platform return userinfo
	 * include nickname and avatar
	 * @return [type] [description]
	 */
	public function getPlatformUserInfo($receive_params){
		$state = $receive_params['state'];
		if(Yii::app()->session['state'] == $state){
			//get token
			$token_url = $this->_tokenUrl."&client_id=".$this->_siteId."&redirect_uri=".urlencode($this->getCallbackUrl())."&client_secret=".$this->_key."&code=".$receive_params['code'];

			$response = @file_get_contents($token_url);
		    if (strpos($response, "callback") !== false){
		        $lpos = strpos($response, "(");
		        $rpos = strrpos($response, ")");
		        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
		        $msg = json_decode($response);
		        if (isset($msg->error)){
		          	return [false, "false", $msg->error_description];
		        }
		    }
		    //get user openid
		    $params = array();
		    parse_str($response, $params);
            $graph_url = $this->_graphUrl.$params['access_token'];
     		$str  = @file_get_contents($graph_url);
     		if (strpos($str, "callback") !== false){
		        $lpos = strpos($str, "(");
		        $rpos = strrpos($str, ")");
		        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		    }
		    $user = json_decode($str);
		    if (isset($user->error)){
		        return [false, "false", $user->error_description];
		    }
		    $openid = $user->openid;
		    
		    //get user info
		    $userinfo_url = $this->_userInfoUrl."access_token=".$params['access_token']."&oauth_consumer_key=".$this->_siteId."&openid=".$openid.'&format=json';
		    $userInfo = json_decode(@file_get_contents($userinfo_url),true);
		    $userdata =array();
		    $userdata['platform']=$this->chn_name;
			$userdata['platform_uid']=$openid;
			$userdata['platform_name']=$userInfo['nickname'];
			$userdata['nickname']=$userInfo['nickname'];
			$userdata['avatar']=$userInfo['figureurl_2'];
			if($userInfo['gender']=='男'){
				$userdata['gender']=2;
			}else{
				$userdata['gender']=3;
			}
		    return [true,"0",$userdata];

		}else {
			return [false, "false", "The state does not match. You may be a victim of CSRF."];
  		}
	}
}