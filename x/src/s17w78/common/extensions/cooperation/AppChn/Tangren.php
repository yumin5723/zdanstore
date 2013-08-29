<?php
require_once("AppChannelAbstract.php");
class Tangren extends AppChannelAbstract {

    public $chn_name = "tangren";

    protected $_siteId = "";

    protected $_key = "";

    protected $_secret = "";

    protected $_width = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['api_key']?:'';
        $this->_secret = @$this->_config['appsecret']?:'';
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $request_time = time();
        $sign = $this->getLoginSign($uid,$username,$request_time);
        if (!$sign) {
            return false;
        }
        $this->_gate_url = $this->getUrl($appid);
        $url = $this->_gate_url."?".http_build_query(array(
                'user_id' => $uid,
                'user_name' => urlencode($username),
                'nick_name' => urlencode($username),
                'sex'=>2,
                'iffcm'=>1,
                'timestamp'=>$request_time,
                'sign' => $sign,
            ));
        $data = file_get_contents($url);
        $data = json_decode($data,true);
        if($data['result'] == 1){
            return $data['url'];
        }
        return $data['error_code'];
    }
    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getLoginSign($uid,$username,$time) {
        $str = "api_key=".$this->_key."iffcm=1nick_name=".urlencode($username)."sex=2"."timestamp=".$time."user_id=".$uid."user_name=".urlencode($username);
        // $str = "api_key=".$this->_key."user_id=".$uid."user_name=".$username."nick_name=".$username."sex=2iffcm=1timestamp=".$time;
        return strtolower(md5($str));
    }
    /**
     * get login request url
     */
    protected function getUrl($app_id){
        return $this->_config['game'][$app_id]['loginUrl'];
    }
}