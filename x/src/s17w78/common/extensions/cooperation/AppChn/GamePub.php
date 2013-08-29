<?php
require_once("AppChannelAbstract.php");
class AppChannelException extends CException {}
class GamePub extends AppChannelAbstract {

    public $chn_name = "gamepub";

    protected $_siteId = "";

    protected $_key = "";

    protected $_serverId = "";
    protected $_gate_url = "";


    public function init(){
        parent::init();
    }
    /**
     * function_description
     *
     *
     * @return url or false
     */
    public function getGateUrl($uid, $username,$appid = null) {
        //get game secret
        $this->_key = App::model()->getGateKeyById($appid);
        //get site id
        $this->_siteId = App::model()->getGateIdById($appid);
        // get gate url
        $this->_gate_url = App::model()->getGateUrlById($appid);

        $this->_serverId = $this->getServerid($appid);

        $request_time = time();
        $sign = $this->getLoginSign($uid,$username,$request_time);
        return $this->_gate_url."?".http_build_query(array(
                'pid' => $this->_siteId,
                'sid' => $this->_serverId,
                'uid' => $uid,
                'nickname' => urlencode($username),
                'timestamp'=>$request_time,
                'sign'=> $sign,
            ));
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getLoginSign($uid,$username,$time) {
        $sign = $this->_siteId.$this->_serverId.$uid.$username.$time.$this->_key;
        return sha1($sign);
    }
    /**
     * [getStyle description]
     * @return [type] [description]
     */
    public function getStyle($app_id = null){
        $style = $this->_config[$app_id]['style'];
        return array("width"=>$style['width'],"height"=>$style['height']);
    }
    /**
     * get game or server id
     * @param  [type] $appid [description]
     * @return string
     */
    public function getServerid($appid){
        $app = $this->_config[$appid];
        if(!isset($app)){
            throw new AppChannelException('app id can not be null');
        }
        $serverid = $app['serverid'];
        if(!isset($serverid)){
            throw new AppChannelException('serverid can not be null');
        }
        return $serverid;
    }
}