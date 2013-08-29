<?php
Yii::import('mainwork.redis.MRedis');

class MTaskRedis extends MRedis {
    const TYPE_ARMORY = "armory";

    public $prefix = "task:";
    /**
     * function_description
     *
     * @param $task_type:
     *
     * @return
     */
    public function getKey($task_type) {
        return $this->prefix.$task_type;
    }

    /**
     * function_description
     *
     * @param $task_type:
     * @param $task_string:
     *
     * @return
     */
    public function addTask($task_type, $task_string) {
        return $this->rpush($this->getKey($task_type), $task_string);
    }

    /**
     * function_description
     *
     * @param $task_type:
     * @param $block:
     *
     * @return
     */
    public function getTask($task_type, $block=true) {
        if ($block) {
            $ret = $this->blpop($this->getKey($task_type), 5);
        } else {
            $ret = $this->lpop($this->getKey($task_type));
        }

        if (isset($ret[1])) {
            return $ret[1];
        } else {
            return false;
        }
    }

}
