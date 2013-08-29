<?php

abstract class AppChannelAbstract {
    public $chn_name = "";
    public $width = "";
    public $height = "";
    public $name = "";
    public $game_type = "";
    /**
     * function_description
     *
     *
     * @return
     */
    public function __construct($gate_url="",$gate_id="",$gate_key="") {

        $this->init();
        $this->width = @$this->_config['width']?:'';
        $this->height = @$this->_config['height']?:'';
        $this->name = @$this->_config['name']?:'';
        $this->game_type = @$this->_config['game_type']?:'';
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
        $this->_config = require(Yii::app()->cooperation->config_path."/app_channel_".$this->chn_name.".php");
    }

    abstract public function getGateUrl($uid, $username);

    public function getStyle($app_id = null){
        return array("width"=>$this->width,"height"=>$this->height,"name"=>$this->name,"gp"=>$this->game_type);
    }
}
