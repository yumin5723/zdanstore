<?php
require_once("AppChannelAbstract.php");
class Yinian extends AppChannelAbstract {

    public $chn_name = "yinian";

    protected $_key = "";

    protected $_siteid = "";


    public function init(){
        parent::init();
        $this->_key = @$this->_config['key']?:'';
        $this->_siteid = @$this->_config['siteid']?:'';
    }
    /**
     * function_description
     *
     * @param $uid:
     * @param $username:
     * @param $appid:
     * @return url or false
     */
    public function getGateUrl($uid, $username, $appid = null,$avatar=false,$gender=false) {
        $request_time = time();
        $loginUrl = $this->getUrl();
        if($gender==1){
            $gender=2;
        }elseif($gender==2){
            $gender=0;
        }else{
            $gender=1;
        }
        $sign = $this->getLoginSign($uid,$username,$avatar,$gender,$request_time);
        $url = $loginUrl."?".http_build_query(array(
                'userid' => $uid,
                'usernick' => $username,
                'head' => $avatar,
                'gender' => $gender,
                'siteid' => $this->_siteid,
                // 'secretkey' => $this->_key,
                'stime'=>$request_time,
                'sign' => $sign,
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
    protected function getLoginSign($uid,$username,$avatar,$gender,$time) {
        $str = 'siteid='.$this->_siteid.'&secretkey='.$this->_key.'&stime='.$time.'&userid='.$uid.'&usernick='.$username.'&head='.$avatar.'&gender='.$gender;
        return md5($str);
    }
    /**
     * get login request url
     */
    protected function getUrl(){
        return $this->_config['game']['loginUrl'];
    }
}