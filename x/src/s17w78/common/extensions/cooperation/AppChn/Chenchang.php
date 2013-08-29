<?php
require_once("AppChannelAbstract.php");
class Chenchang extends AppChannelAbstract {

    public $chn_name = "chenchang";

    protected $_key = "";

    protected $_siteid = "";

    protected $_returnUrl = "";

    public function init(){
        parent::init();
        $this->_key = @$this->_config['key']?:'';
        $this->_siteid = @$this->_config['siteid']?:'';
        $this->_returnUrl = @$this->_config['returnUrl']?:'';
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
        $loginUrl = $this->getUrl();
        if($gender==1){
            $gender=0;
        }elseif($gender==2){
            $gender=0;
        }else{
            $gender=1;
        }
        if($avatar=="avatar non-existent"){
            $avatar='';
        }
        $gameId = $this->getGameid($appid);
        $gamePath = $this->getGamePath($appid);
        $sign = $this->getLoginSign($uid,$username,$avatar,$gender);
        $urlinfo = $loginUrl."?".http_build_query(array(
                'userName' => $uid,
                'nickName' => urlencode(trim($username)),
                'headImage' => $avatar,
                'gender' => $gender,
                'siteId' => $this->_siteid,
                'sign' => $sign,
            ));
        $userinfo = @file_get_contents($urlinfo);
        if($gameId == "TexasPoker"){
            $url = $gamePath.'?player='.$uid.'&hall=1378.com&debug=off&password=test&companyPath=1378.com&host=www.17play8.com&base=http://resource.17play8.com/szcch/hall/';
            return $url;
        }
        $userdata = simplexml_load_string($userinfo);
        if($userdata->returnCode==0){
            $roomurl = $this->getRoom($appid);
            $xml = @file_get_contents($roomurl);
            $t =simplexml_load_string($xml);
            $this->getHtml($t,$uid,$appid);
        }else{
            return "get info error";
        }
        return $this->_returnUrl.$gameId.".html";
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
    protected function getLoginSign($uid,$username,$avatar,$gender) {
        $str = 'userName='.$uid.'&headImage='.$avatar.'&gender='.$gender.'&key='.$this->_key;
        return md5($str);
    }
    /**
     * get login request url
     */
    protected function getUrl(){
        return $this->_config['game']['loginUrl'];
    }
    /**
     * get room url
     */
    protected function getRoomUrl(){
        return $this->_config['game']['roomUrl'];
    }
    /**
     * get gameid
     */
    protected function getGameid($appid){
        return $this->_config['gameID'][$appid]['id'];
    }
    /**
     * get gamePath
     */
    protected function getGamePath($appid){
        return $this->_config['gameID'][$appid]['gamePath'];
    }
    /**
     * get room request url
     */
    public function getRoom($appid){
        $gameId = $this->getGameid($appid);
        $sign = md5("gameId=".$gameId."&key=".$this->_key);
        return $this->getRoomUrl()."?gameId=".$gameId."&sign=".$sign;
    }
    /**
     * get user score
     */
    public function getScore($username,$gameId,$appid){
        $gameId = $this->getGameid($appid);
        $sign = md5("gameId=".$gameId."&key=".$this->_key);
        return $this->getRoomUrl()."?gameId=".$gameId."&sign=".$sign;
    }
    /**
     * Html file generated
     */
    public function getHtml($data,$uid,$appid){
        $app = App::model()->findByPk($appid);
        $str = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><link href="./17play_files/main.css" rel="stylesheet" type="text/css"><div class="hall"><div id="right"><div class="boxes"><div class="vs_center-list"><div class="tab_2_menu"><ul><li class="selected"><div><p>'.$app->app_name.'</p></div></li></ul><div class="game_rule"><p class="game_name"><b></b></p><p></p></div><div class="clear"></div></div><div class="tab_2_box"><div id="vip_game-cont"><div class="boxes"><div class="rooms_center"><div class="room-l"><ul>';
        $gameId = $this->getGameid($appid);
        $gamePath = $this->getGamePath($appid);
        foreach ($data->room as $key => $value) {
            $url = "appid=".$appid."&url=".$gamePath."&game=".$value->bootFile."&desk=".$value->roomId."&hall=1378.com&referrer=".$this->_returnUrl.$gameId.".html"."&port=".$value->port."&debug=off&password=test&host=".$value->host."&base=http://resource.17play8.com/szcch/hall/";
            $str .='<li class="in-room-list"><ul><li class="list_bt"><p>'.$value->roomName.'</p></li><li class="infors"><div><p>底分：'.$value->minScore.'</p></div><div><p>封顶分数：'.$value->limitScore.'</p></div><div><p>准入分数：'.$value->maxScore.'</p></div><div><p class="xx">在线<span>'.$value->currentPeople.'</span>人</p></div><p></p></li><li><a href="http://www.1378.com/webgame/gamehtml?'.$url.'"></a></li></ul></li>';
        }
        $str .='</ul></div></div></div></div></div></div></div></div></div>';
        $filename = "/data0/web/www.17w78.com/gameht/".$gameId.".html";
        file_put_contents($filename, $str);
    }
}