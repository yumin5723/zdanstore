<?php

abstract class PlatformChannelAbstract {
    public $chn_name = "";
    /**
     * function_description
     *
     *
     * @return
     */
    public function __construct() {
        $this->init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        $this->loadConfig();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function loadConfig() {
        $this->_config = require(Yii::app()->platform->config_path."/platform_channel_".$this->chn_name.".php");
    }

    abstract public function getLoginUrl();

    abstract public function getPlatformUserInfo($receive_params);

    public function getCallbackUrl(){
        return "http://i.1378.com/user/bind?plat=".$this->chn_name;
    }
}