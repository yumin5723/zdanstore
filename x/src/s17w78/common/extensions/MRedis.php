<?php

class MRedis extends CApplicationComponent {
    protected $_predis = null;

    public $servers=array('host'=>'127.0.0.1','port'=>6379);

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getPredis() {
        if ($this->_predis == null) {
            define("PREDIS_BASE_PATH", Yii::getPathOfAlias("mainwork.vendors")."/");
            spl_autoload_register(function($class) {
                    $file = PREDIS_BASE_PATH . strtr($class, '\\', '/') . '.php';
                    if (file_exists($file)) {
                        require $file;
                        return true;
                    }
                });
            Yii::log('Opening Redis connection',CLogger::LEVEL_TRACE);
            $this->_predis = new Predis\Client($this->servers);
        }

        return $this->_predis;
    }

    /**
     * call unusual method
     *
     * @param $method:
     * @param $args:
     *
     * @return
     */
    public function __call($method, $args) {
        return call_user_func_array(array($this->getPredis(), $method), $args);
    }

}
