<?php

class PageCommand extends CConsoleCommand {
    /**
     * function_description
     *
     *
     * @return
     */
    public function run($args) {
        $tasks = $this->getTasks();
        foreach ($tasks as $t) {
            $publisher = new MhPublisher;
            $file = Yii::app()->params['base_file_path'].$t->file;
            $ret = $publisher->publishEntirePage($file,$t->path,$t->domain);
            $t->status = Page::STATUS_PUBLISHED;
            $t->save();
        }
        if (empty($tasks)) {
            echo "all done";
        }
    }


    /**
     * get tasks from database
     *
     * @param $num:
     *
     * @return
     */
    public function getTasks() {
        $tasks = Page::model()->findAllByAttributes(array("status"=>Page::STATUS_NEED_PUBLISH));
        return $tasks;
    }


}