<?php
require_once("AppChannelAbstract.php");
class HaoDan extends AppChannelAbstract {

    public $chn_name = "haodan";
    protected $_gate_url = "http://www.pokersc.cn/api/getflash.do";

    // protected $_gate_url = "http://183.60.108.100:8080/game/game.php";

    protected $_siteId = "";

    protected $_key = "";

    protected $_type = 0;

    public $game_type = "";

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
    public function getGateUrl($uid, $username,$appid = null) {
        $sign = $this->getLoginSign($uid,$username);
        return $this->_gate_url."?".http_build_query(array(
                'sign'=> $sign,
                'appid' => $this->_siteId,
                'name' => $uid,
                'nick' => $username,
                'type' => $this->_config['type'][$appid],
            ));
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    protected function getLoginSign($uid,$username) {
        $sign = "appid=".$this->_siteId."&name=".$uid."&nick=".$username.$this->_key;
        return sha1($sign);
    }
    /**
     * [getStyle description]
     * @return [type] [description]
     */
    public function getStyle($app_id = null){
        $gp = $this->_config['game_type'][$app_id];
        return array("width"=>$this->width,"height"=>$this->height,"name"=>$this->name,"gp"=>$gp);
    }
}