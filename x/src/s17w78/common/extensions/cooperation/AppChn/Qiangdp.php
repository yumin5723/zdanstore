<?php
require_once("AppChannelAbstract.php");
class Qiangdp extends AppChannelAbstract {

    public $chn_name = "qiangdp";

    protected $_key = "";

    protected $_fromid = "";

    protected $_serverid = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['key']?:'';
        $this->_fromid = @$this->_config['fromid']?:'';
        $this->_serverid = @$this->_config['serverid']?:'';
    }
    /**
     * function_description
     *
     * @param $uid:
     * @param $username:
     * @param $appid:
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $request_time = time();
        $token = $this->getLoginSign($uid,$request_time);
        $loginUrl = $this->getUrl();
        $url = $loginUrl."?".http_build_query(array(
                'loginname' => $uid,
                'sitemid' => (int)$this->_fromid,
                'serverid' => (int)$this->_serverid,
                'time'=>$request_time,
                'token' => $token,
            ));
        return $url;
    }
    /**
     * function_description
     *
     * @param $username:
     * 
     * @param $time:
     *
     * @return
     */
    protected function getLoginSign($uid,$time) {
        $str = $this->_fromid.$uid.$this->_serverid.$this->_key.$time;
        $str = mb_convert_encoding($str, "gbk","utf8");
        return strtolower(md5(strtolower($str)));
    }
    /**
     * get login request url
     */
    protected function getUrl(){
        return $this->_config['game']['loginUrl'];
    }
}