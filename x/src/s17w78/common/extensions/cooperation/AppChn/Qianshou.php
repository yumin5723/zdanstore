<?php
require_once("AppChannelAbstract.php");
class Qianshou extends AppChannelAbstract {

    public $chn_name = "qianshou";

    protected $_key = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['key']?:'';
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null) {
        $request_time = time();
        $sign = $this->getLoginSign($uid,$request_time);
        $loginUrl = $this->getUrl($appid);
        return $loginUrl."?".http_build_query(array(
                'sitemid' => $uid,
                'name' => $username,
                'ts' => $request_time,
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
    protected function getLoginSign($uid,$time) {
        $str = $uid.$time.$this->_key;
        return md5($str);
    }
    /**
     * get login request url
     */
    protected function getUrl($app_id){
        return $this->_config['game'][$app_id]['loginUrl'];
    }
}