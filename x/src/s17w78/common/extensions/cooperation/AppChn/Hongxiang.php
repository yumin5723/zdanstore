<?php
require_once("AppChannelAbstract.php");
class Hongxiang extends AppChannelAbstract {

    public $chn_name = "hongxiang";

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
        $request_time = time();//"1374486024";//time();
        $gameID = $this->getGameid($appid);
        $sign = $this->getLoginSign($uid, $request_time);
        $loginUrl = $this->getUrl();
        $prm = base64_encode($uid.'|||||'.$request_time.'|'.$sign);
        $url = $loginUrl."df=".$gameID."&prm=".$prm;
        return $url;
    }
    
    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getLoginSign($uid,$time) {
        $strs = $uid.$time.$this->_key;
        $str = base64_encode(hex2bin(md5($strs)));
        return $str;
    }
    /**
     * get login request url
     */
    protected function getUrl(){
        return $this->_config['game']['loginUrl'];
    }
    /**
     * get gameID
     */
    protected function getGameid($app_id){
        return $this->_config['gameID'][$app_id]['id'];
    }
    // /**
    //  * [getStyle description]
    //  * @return [type] [description]
    //  */
    // public function getStyle($app_id = null){
    //     return $this->_config['gameID'][$app_id]['style'];
    // }
}