<?php
require_once("AppChannelAbstract.php");
class Sanguo extends AppChannelAbstract {

    public $chn_name = "sanguo";
    // protected $_gate_url = "http://www.webgame.com/user/gate";
    protected $_gate_url = "http://api.17w78.com/user/gate";


    protected $_siteId = "";

    protected $_key = "";

    protected $_server_id = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['loginkey']?:'';
        $this->_siteId = @$this->_config['siteid']?:'';
        $this->_server_id = @$this->_config['serverid']?:'';
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $request_time = time();
        $sign = $this->getSign($uid,$username,$request_time);
        return $this->_gate_url."?".http_build_query(array(
                'pid'=> $this->_siteId,
                'sid'=> $this->_server_id,
                'uid' => $uid,
                'nickname'=> $username,
                'timestamp'=> $request_time,
                'sign' => $sign,
            ));
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getSign($uid,$username,$time) {
        $str = $this->_siteId.$this->_server_id.$uid.$username.$time.$this->_key;
        return sha1($str);
    }

}