<?php
require_once("AppChannelAbstract.php");
class HjDzpoker extends AppChannelAbstract {

    public $chn_name = "huangjiadp";

    protected $_siteId = "";

    protected $_key = "";

    protected $_secret = "";

    protected $_width = "";

    public function init(){
        parent::init();
        $this->_siteid = @$this->_config['api_id']?:'';
        $this->_secret = @$this->_config['appsecret']?:'';
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null,$avatar=false,$gender=false) {
        $calSig = md5($this->_siteid.$this->_secret);
        if (!$calSig) {
            return false;
        }
        $this->_gate_url = $this->getUrl($appid)."?";
        $param = array(
                'userid' => $uid,
                'usernick' => $username,
                'head' => $avatar,
                'sex'=>$gender,
                'Siteid'=>$this->_siteid,
                'Secretkey'=>$this->_secret,
                'Method'=>"jinbian.Login",
                'calSig' => $calSig,
            );
        $ret = Yii::app()->curl->post($this->_gate_url, $param);
        $data = json_decode($ret,true);
        if($data['code'] == 0){
            return $data['url'];
        }
        return $data['msg'];
    }
   
    /**
     * get login request url
     */
    protected function getUrl($app_id){
        return $this->_config['game'][$app_id]['loginUrl'];
    }
}