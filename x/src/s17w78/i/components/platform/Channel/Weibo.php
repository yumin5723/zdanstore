<?php
require_once("PlatformChannelAbstract.php");
require_once("saetv2.ex.class.php");
class Weibo extends PlatformChannelAbstract {
	public $chn_name = "weibo";

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
        // $this->_graphUrl = @$this->_config['graph_url']?:'';
        $this->_userInfoUrl = @$this->_config['userinfo_url']?:'';
	}
	/**
	 * get login url to redirect
	 */
	public function getLoginUrl(){
		$state = null;
		$url = $this->_requestUrl."&client_id=".$this->_siteId."&redirect_uri=".$this->getCallbackUrl()."&state=".$state;
		return $url;
	}
	/**
	 * get third part platform return userinfo
	 * include nickname and avatar
	 * @return [type] [description]
	 */
	public function getPlatformUserInfo($receive_params){
		$o = new SaeTOAuthV2( $this->_siteId , $this->_key );
		$token=array();
		if (isset($receive_params['code'])) {
			$keys = array();
			$keys['code'] = $receive_params['code'];
			$keys['redirect_uri'] = $this->getCallbackUrl();
			try {
				$token = $o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {
				echo "string";
			}
		}
		if ($token) {	
			$_SESSION['token'] = $token;
			// setcookie( 'weibojs_'.$this->_siteId, http_build_query($token) );
			$userinfo=array();
			$userinfo = $this->getUserInfo($this->_siteId , $this->_key,$_SESSION['token']['access_token']);
			$userdata =array();
			$userdata['platform']=$this->chn_name;
			$userdata['platform_uid']=$userinfo['id'];
			$userdata['platform_name']=$userinfo['name'];
			$userdata['nickname']=$userinfo['name'];
			$userdata['avatar']=$userinfo['avatar_large'];
			if($userinfo['gender']=='m'){
				$userdata['gender']=3;
			}else{
				$userdata['gender']=2;
			}
			return [true,"0",$userdata];
		}else {
			return [false, "false", "The state does not match. You may be a victim of CSRF."];
  		}
	}
	/**
	 * getUserInfo get user_message
	 * include uid 
	 * @return [type] [description]
	 */
	public function getUserInfo($app_id,$app_secret,$access_token){
		$c = new SaeTClientV2( $app_id , $app_secret , $access_token );
		$ms  = $c->home_timeline(); // done
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user_message = $c->show_user_by_id( $uid);
		return $user_message;
	}

}