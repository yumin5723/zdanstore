<?php
/**
 * RedisConnection.php ---
 *
 */

class RedisConnection extends CComponent {

    /**
     * master server config
     * array (host,port,timeout)
     */
    public $masterServer;

    public $slaveServer = array();

    protected $_redisMaster=null;
    protected $_redisSlave=null;

    /**
     * function_description
     *
     * @param $connString:
     *
     * @return
     */
    public function __construct($masterServer=array(),$slaveServer=array()) {
        $this->masterServer = $masterServer;
        $this->slaveServer = $slaveServer;
    }
    public function init(){
        
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function Open($serverConfig) {
        try{
            Yii::trace("Opening Redis connection", 'mainwork.redis.MRedisConnection');
            $redis = $this->createRedisInstance($serverConfig);
            $this->initConnection($redis);
            return $redis;
        } catch (RedisException $e) {
            if (YII_DEBUG) {
                throw new RedisException(Yii::t('mii','RedisConnection failed to open the Redis connection: {error}', array('{error}'=>$e->getMessage())),(int)$e->getCode());
            } else {
                Yii::log($e->getMessage(),CLogger::LEVEL_ERROR,'exception.RedisException');
                throw new RedisException(Yii::t('mii','RedisConnection failed to open the Redis connection.'), (int)$e->getCode());
            }
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function close() {
        Yii::trace('Closing Redis connection','mainwork.redis.RedisConnection');
        $this->_redisMaster=null;
        $this->_redisSlave=null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function createRedisInstance($serverConfig) {
        $host = isset($serverConfig['host']) ? $serverConfig['host'] : '127.0.0.1';
        $port = isset($serverConfig['port']) ? $serverConfig['port'] : 6379;
        $timeout = isset($serverConfig['timeout']) ? $serverConfig['timeout'] : 5;
        $redis = new Redis();
        $redis->connect($host,$port,$timeout);
        return $redis;
    }

    /**
     * function_description
     *
     * @param $redis:
     *
     * @return
     */
    public function initConnection($redis) {
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getRedisInstance($useSlave=false) {
        if ($useSlave && count($this->slaveServer)) {
            if(is_null($this->_redisSlave)) {
                $randIndex = array_rand($this->slaveServer);
                $this->_redisSlave=$this->open($this->slaveServer[$randIndex]);
            }
            return $this->_redisSlave;
        } else {
            if (is_null($this->_redisMaster)) {
                $this->_redisMaster=$this->open($this->masterServer);
            }
            return $this->_redisMaster;
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getWriteIns() {
        return $this->getRedisInstance(false);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReadIns() {
        return $this->getRedisInstance(true);
    }

}