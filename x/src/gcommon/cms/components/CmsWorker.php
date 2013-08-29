<?php

class CmsWorker extends CConsoleCommand {

    public $host = "127.0.0.1";

    public $port = "5672";

    public $user = "guest";

    public $password = "guest";

    protected $_amqp = null;

    protected $_channel = null;

    protected $_queue_name = null;

    public $default_exchange="cms_event";

    protected $_listen_events = array();

    protected $_current_event = null;

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        Yii::getLogger()->autoFlush = 1;
        Yii::getLogger()->autoDump = true;
        Yii::app()->autoloader->getAutoloader()->addNamespace('PhpAmqpLib',
            Yii::getPathOfAlias("gcommon.lib.PhpAmqpLib")
        );
        $this->open();
        $this->_channel->exchange_declare($this->default_exchange, 'topic');
        $this->_channel->basic_qos(0, 1, null);

        $this->initQueue();
        $this->bindEvents();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function initQueue() {
        // set queue
        $queue = $this->_channel->queue_declare("",false,false,true);
        $this->_queue_name = $queue[0];
        Yii::log("Declare new queue: ". $this->_queue_name, CLogger::LEVEL_INFO);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function bindEvents() {
        if (empty($this->_listen_events)) {
            throw new CException("Does not listen any events");
        }

        foreach ($this->_listen_events as $e) {
            $this->_channel->queue_bind($this->_queue_name,$this->default_exchange,$e);
            Yii::log("Listen on event: ". $e, CLogger::LEVEL_INFO);
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function registerWorker() {
        $this->_channel->basic_consume($this->_queue_name, "", false,false,false,false,
            function($msg) {
                // call worker
                $this->processMsg($msg->body);
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        );

        // for process shutdown
        $ch = $this->_channel;
        declare(ticks = 1);
        pcntl_signal(SIGTERM, function(){
                //shutdown
                echo "shutdown...";
                exit(0);
            });

        while (count($ch->callbacks)) {
            $ch->wait();
        }
    }


    /**
     * function_description
     *
     * @param $msg:
     *
     * @return
     */
    protected function processMsg($msg) {
        // json decode
        $this->_current_event = json_decode($msg,true);
        $this->work();
        Yii::app()->db->setActive(false);
    }


    /**
     * function_description
     *
     *
     * @return
     */
    protected function open() {
        $this->_amqp = new \PhpAmqpLib\Connection\AMQPConnection(
            $this->host, $this->port,$this->user,$this->password
        );
        $this->_channel = $this->_amqp->channel();
        if (!$this->_channel) {
            throw new CException("Cannot create channel.");
        }
        Yii::log("Connected to ". $this->host . ":".$this->port, 'info');
    }

    /**
     * function_description
     *
     * @param $args:
     *
     * @return
     */
    public function run($args) {
        $this->registerWorker();
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function work() {
        throw new CException("Subclass must override this work() method.");
    }


}