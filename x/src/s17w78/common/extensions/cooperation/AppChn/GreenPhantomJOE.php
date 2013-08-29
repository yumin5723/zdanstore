<?php
require_once("AppChannelAbstract.php");
class GreenPhantomJOE extends AppChannelAbstract {

    public $chn_name = "green";
    protected $_login_url = "http://dzpk.phantom78.com:8080/game/login.php";

    protected $_gate_url = "http://dzpk.phantom78.com:8080/game/game.php";

    protected $_siteId = "";

    protected $_key = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['loginkey']?:'';
        $this->_siteId = @$this->_config['siteid']?:'';
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $login_token = $this->getLoginToken($uid);
        if (!$login_token) {
            return false;
        }
        $sig = md5($this->_key.$uid.$username);
        return $this->_gate_url."?".http_build_query(array(
                'token'=> $login_token,
                'sid' => $this->_siteId,
                'uid' => $uid,
                'name' => $username,
                'sig' => $sig,
            ));
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getLoginToken($uid) {
        $token = md5($this->_key.$uid.$this->_siteId);
        $login_token = Yii::app()->curl->get($this->_login_url, array(
                'token' => $token,
                'uid' => $uid,
                'sid' => $this->_siteId,
                       ));
        if ($login_token == "-1") {
            return false;
        }
        return trim($login_token);
    }

}