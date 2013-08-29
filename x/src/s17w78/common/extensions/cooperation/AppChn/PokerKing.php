<?php
require_once("AppChannelAbstract.php");
class PokerKing extends AppChannelAbstract {

    public $chn_name = "pokerking";
    protected $_gate_url = "http://www.dzpk.cn/platform/share/login.ashx";

    // protected $_gate_url = "http://183.60.108.100:8080/game/game.php";

    protected $_siteId = "";

    protected $_key = "";

    protected $_time = "";
    
    protected $_server_id = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['loginkey']?:'';
        $this->_siteId = @$this->_config['siteid']?:'';
        $this->_time = time();
        $this->_server_id = 100;
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $login_token = $this->getLoginToken($uid,$username);
        return $this->_gate_url."?".http_build_query(array(
                'token'=> $login_token,
                'partner_id' => $this->_siteId,
                'login_name' => $uid,
                'nick_name' => $username,
                'time' => $this->_time,
                'server_id' => $this->_server_id,
            ));
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getLoginToken($uid,$username) {
        $login_token = md5($this->_siteId.$this->_server_id.$uid.$username.$this->_time.$this->_key);
        return $login_token;
    }

}