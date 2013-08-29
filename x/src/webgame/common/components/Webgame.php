<?php
Yii::import('common.models.CPUser');
Yii::import('common.models.User');
class WebgameException extends CException {}

class Webgame extends CApplicationComponent {

    const REQUEST_PARAMS_EMPTY = -1;
    const GAME_ID_NOT_EXISTS = -2;
    const SITE_ID_NOT_EXISTS = -3;
    const USER_INFO_CAN_NOT_EMPTY = -4;
    const SIGN_IS_ERROR = -5;
    const SYSTERM_ERROR = -6;
    const TIME_ERROR = -7;
    protected $_configs = array();

    public function init(){
        $this->loadConfig();
        parent::init();
    }
    /**
     * load config
     *
     *
     * @return
     */
    protected function loadConfig() {
        $this->_configs = require(Yii::getPathOfAlias("application.config")."/webgame.php");
    }
    /**
     * check user from partner login
     * @return [type] [description]
     */
    public function checkUserLogin($request_params){
        //check params
        if(empty($request_params)){
            return[false,self::REQUEST_PARAMS_EMPTY];
        }
        //check game id
        $game_id = intval($request_params['sid']);
        if(!array_key_exists($game_id, $this->_configs)){
            return [false,self::GAME_ID_NOT_EXISTS];
        }
        //check siteid
        $cp = CPUser::model()->findByPk($request_params['pid']);
        if(empty($cp)){
            return [false,self::SITE_ID_NOT_EXISTS];
        }
        //check login name and nick name
        if(empty($request_params['uid']) || empty($request_params['nickname'])){
            return [false,self::USER_INFO_CAN_NOT_EMPTY];
        }
        //check request time
        if(empty($request_params['timestamp']) || (time() - $request_params['timestamp']) > 300 || ($request_params['timestamp'] - time()) > 300 ){
            return [false,self::TIME_ERROR];
        }
        //check sign
        $sign = $this->getSign($request_params);
        if($sign != $request_params['sign']){
            return [false,self::SIGN_IS_ERROR];
        }
        //if user is newrecord create one record on 1378 database then request game
        $pid = $request_params['pid'];
        $nickname = urldecode($request_params['nickname']);
        $username = $request_params['uid']."@".$pid;
        $user = User::model()->checkUser($username,$nickname,$pid);
        if($user !== false){
            //request game user login 
            $gameChannel = $this->_configs[$request_params['sid']]['className'];
            if(!isset($gameChannel)){
                throw new WebgameException("class not found:".$gameChannel);
            }
            include_once(Yii::getPathOfAlias("common.components")."/".$gameChannel.".php");
            $game = new $gameChannel($user->username,$user->nickname,$user->pass_str);
            $game->createAccount();
            //user login 
            $identity = new WebgameUserIdentity($user->username, "");
            $identity->authenticate();
            Yii::app()->user->login($identity);
            return [true,$this->_configs[$request_params['sid']]['redirectUrl']];
        }else{
            return [false,self::SYSTERM_ERROR];
        }
    }
    /**
     * get sign
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getSign($params){
        $cp = CPUser::model()->findByPk($params['pid']);
        $str = $params['pid'].$params['sid'].$params['uid'].urldecode($params['nickname']);
        if(isset($params['gender'])){
            $str .= $params['gender'];
        }
        if(isset($params['avatar'])){
            $str .= $params['avatar'];
        }
        $str .= $params['timestamp'];
        $str .= $cp->cp_key;
        $sign = sha1($str);
        return $sign;
    }

}