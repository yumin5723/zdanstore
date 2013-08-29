<?php
class WebmapCommand extends CConsoleCommand {
    protected $offset = 0;

    protected $max = 100;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run($args) {
        $data = array();
        while (true) {
            $objects = $this->getLastSevenDaysObjects($this->offset);
            foreach ($objects as $o) {
                $data[$o->id]['name'] = $o->object_list_name;
                $data[$o->id]['url'] = $o->url;
            }
            if (empty($objects)) {
                Yii::app()->db->setActive(false);
                break;
            }
            $this->offset += $this->max;
        }
        print_r($data);
    }

    /**
     * get tasks from database
     *
     * @param $num:
     *
     * @return
     */
    public function getLastSevenDaysObjects($offset,$num = 100) {
        return Object::model()->getObjectsByTime(date('Y-m-d',strtotime("-700 day")),$offset,$num);
    }
}
