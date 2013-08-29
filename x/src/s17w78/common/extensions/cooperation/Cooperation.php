<?php

class CooperationException extends CException {}

class Cooperation extends CApplicationComponent {
    public $config_file = null;

    public $config_path = null;

    protected $_configs = array();

    protected $_gate_class_path_prefix = "cooperation.AppChn.";
    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        // register the cooperation path alias
        if (Yii::getPathOfAlias("cooperation") === false) {
            Yii::setPathOfAlias("cooperation", realpath(dirname(__FILE__)));
        }

        if (is_null($this->config_file)) {
            throw new CooperationException('Cooperation config_file can not be null');
        }
        include(Yii::getPathOfAlias("cooperation")."/error.php");

        if (is_null($this->config_path)) {
            $this->config_path = dirname($this->config_file);
        }

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
        $this->_configs = require($this->config_file);
    }


    /**
     * function_description
     *
     * @param $app_id:
     *
     * @return url string or Error
     */
    public function getAppGateUrl($app_id, $uid=null) {
        $channel = $this->getAppChannel($app_id);
        if ($channel instanceof IError) {
            return $channel;
        }

        if (empty($uid)) {
            if (Yii::app()->user->isGuest) {
                return new Error(COOPERATION_UNKNOWN_USER, "Unknown user: ". $uid);
            }
            $uid = Yii::app()->user->id;
        }

        $username = $this->getUsernameForApp($uid);
        if ($username instanceof IError) {
            return $username;
        }
        $arr = $this->getAvatarForApp($uid);
        $openId = Yii::app()->openid->getUserOpenidForApp($app_id,$uid);
        $url = $channel->getGateUrl($openId, $username, $app_id,$arr[0],$arr[1]);
        if(empty($url)){
            return new Error(COOPERATION_GET_GATEURL_FAIL,"Url error");
        }
        return $url;
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    public function getUsernameForApp($uid) {
        $user = User::model()->findByPk($uid);
        if (empty($user)) {
            return new Error(COOPERATION_UNKNOWN_USER, "Unknown user: ". $uid);
        }

        return $user->nickname;
    }
    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    public function getAvatarForApp($uid) {
        $user = Profile::model()->findByPk($uid);
        if(isset($user->small_avatar)){
            if(!preg_match('/^(http:\/\/\S*)$/', $user->small_avatar)){
                $avatar = empty($user->small_avatar) ? "" : Yii::app()->params['avatar_url'].$user->small_avatar;
            }else{
                $avatar = $user->small_avatar;
            }
        }
        if($avatar==''){$avatar='avatar non-existent';}
        if($user->gender=='0'){$user->gender=1;}
        return array($avatar,$user->gender);
    }

    /**
     * function_description
     *
     * @param $app_id:
     *
     * @return AppChannel or Error
     */
    protected function getAppChannel($app_id) {
        // get app
        $app = App::model()->findByPk($app_id);
        if (empty($app)) {
            return new Error(COOPERATION_UNKNOWN_APP, "Unknown app: ". $app_id);
        }

        $channel_id = $app->gate_chn;
        if (!isset($this->_configs['gates'][$channel_id]['className'])) {
            return new Error(COOPERATION_UNKNOWN_GATE, "Unknown gate channel: ". $channel_id);
        }

        $className = $this->_configs['gates'][$channel_id]['className'];
        Yii::import($this->_gate_class_path_prefix.$className);
        $channel = new $className($app->gate_url,$app->gate_id,$app->gate_key);
        return $channel;
    }
    /**
     * get app width height style for iframe
     * @param  [type] $app_id [description]
     * @return [type]         [description]
     */
    public function getAppStyle($app_id){
        $channel = $this->getAppChannel($app_id);
        $style = $channel->getStyle($app_id);
        return $style;
    }

}