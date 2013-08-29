<?php

class RoleMenu extends CApplicationComponent {
    public $config_path;

    protected $_configs = array();

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
    protected function loadConfig() {
        if (empty($this->config_path)) {
            $this->config_path = Yii::getPathOfAlias("application.config")."/menu.php";
        }
        $all_configs = require($this->config_path);
        $userId = Yii::app()->user->id;
        $roles = array_keys(Yii::app()->authManager->getRoles($userId));
        foreach ($roles as $role) {
            if(isset($all_configs[$role])){
                $this->_configs = CMap::mergeArray($this->_configs, $all_configs[$role]);
            }
        }

    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function getTops() {
        return $this->_configs;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getSubs($top) {
        if (!isset($this->_configs[$top])) {
            return array();
        }
        return $this->_configs[$top]['subs'];
    }



}