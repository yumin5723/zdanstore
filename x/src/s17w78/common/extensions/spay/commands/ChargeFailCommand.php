<?php

class ChargeFailCommand extends CConsoleCommand {
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
		list($r, $err) = Yii::app()->payment->notifyFailForOrder($t);
		if ($r) {
		    Yii::log("success send fail notice for order:".$t->id, CLogger::LEVEL_INFO, "payment");
		} else {
		    Yii::log("error send fail notice for order:".$t->id.". with error:".$err, CLogger::LEVEL_ERROR, "payment");
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
     * @param $num:
     *
     * @return
     */
    public function getTasks($num = 10) {
	return Yii::app()->payment->getOrdersNeedNotifyFail($num);
    }


}