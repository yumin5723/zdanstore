<?php

class DirectCardPayCommand extends CConsoleCommand {
    protected $count = 0;

    protected $max = 1000;

    public function init() {
	Yii::getLogger()->autoFlush = 1;
	Yii::getLogger()->autoDump = true;
	parent::init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function run($args) {
	while (true) {
	    $tasks = $this->getTasks();
	    foreach ($tasks as $t) {
		$this->count++;
		list($r, $err) = Yii::app()->payment->CardPayForOrder($t);
		if ($r) {
		    Yii::log("success pay for order:".$t->id, CLogger::LEVEL_INFO, "payment");
		} else {
		    Yii::log("fail pay for order:".$t->id.". with error:".$err, CLogger::LEVEL_ERROR, "payment");
		}
	    }
	    if ($this->count >= $this->max) {
		break;
	    }
	    if (empty($tasks)) {
		Yii::app()->db->setActive(false);
		sleep(5);
	    }
	}
    }

    /**
     * get tasks from database
     *
     *
     * @return
     */
    protected function getTasks($num = 10) {
	return Yii::app()->payment->getDirectOrdersNeedPay($num);
    }



}
