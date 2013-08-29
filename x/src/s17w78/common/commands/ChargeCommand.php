<?php
class ChargeCommand extends CConsoleCommand {
    protected $count = 0;

    protected $max = 100;
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
                list($r, $err) = Yii::app()->payment->chargeForOrder($t);
                if ($r) {
                    Yii::log("success charge for order:".$t->id, CLogger::LEVEL_INFO, "payment");
                } else {
                    Yii::log("fail charge for order:".$t->id.". with error:".$err, CLogger::LEVEL_ERROR, "payment");
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

        $tasks = $this->getTasks();
    }


    /**
     * get tasks from database
     *
     * @param $num:
     *
     * @return
     */
    public function getTasks($num = 10) {
        return Yii::app()->payment->getOrdersNeedCharge($num);
    }


}
