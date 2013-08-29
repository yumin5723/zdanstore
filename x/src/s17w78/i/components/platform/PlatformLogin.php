<?php

class PlatformLoginException extends CException {}

class PlatformLogin extends CApplicationComponent {
    public $config_file = null;

    public $config_path = null;

    protected $_configs = array();

    protected $_login_class_path_prefix = "platform.Channel.";
    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        // register the cooperation path alias
        if (Yii::getPathOfAlias("platform") === false) {
            Yii::setPathOfAlias("platform", realpath(dirname(__FILE__)));
        }

        if (is_null($this->config_file)) {
            throw new CooperationException('Platform config_file can not be null');
        }
        include(Yii::getPathOfAlias("platform")."/error.php");

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
    public function getPlatformLoginUrl($plat) {
        $channel = $this->getPlatformChannel($plat);
        if ($channel instanceof IError) {
            return $channel;
        }
        $url = $channel->getLoginUrl();
        if(empty($url)){
            return new Error(PLATFORM_GET_LOGINURL_FAIL,"Url error");
        }
        return $url;
    }

    /**
     * function_description
     *
     * @param $app_id:
     *
     * @return AppChannel or Error
     */
    protected function getPlatformChannel($plat) {
        // get channel
        if (!isset($this->_configs['channels'][$plat]['className'])) {
            return new Error(PLATFORM_UNKNOWN_CHANNEL, "Unknown platform channel: ". $plat);
        }
        $className = $this->_configs['channels'][$plat]['className'];
        Yii::import($this->_login_class_path_prefix.$className);
        $channel = new $className();
        return $channel;
    }
    /**
     * receive third part platform params then go on our logic
     * @param  [type] $plat           [description]
     * @param  [type] $receive_params [description]
     * @return [type]                 [description]
     */
    public function receiveCallback($plat,$receive_params){
    	$plat = $this->getPlatformChannel($plat);
    	$userInfo = $plat->getPlatformUserInfo($receive_params);
    	return $userInfo;
    }
}