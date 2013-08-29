<?php

/**
 * All Model attach this behavior can have additinal methods for publish events
 * to amqp server.
 * fireNew: object just created.
 * fireUpdate: object has some attribute modified.
 * fireDelete: object remove from database
 * firePublished: object just published for public user access.
 * fireTouch: a touch event means use touch object to force publish/update
 */
class CmsEventBehavior extends CActiveRecordBehavior {

    /**
     * function_description
     *
     *
     * @return
     */
    public function getObjType() {
        if (is_a($this->getOwner(), "Block")) {
            return "block";
        }

        if (is_a($this->getOwner(), "Page")) {
            return "page";
        }
        if (is_a($this->getOwner(), "Templete")) {
            return "template";
        }

        if (is_a($this->getOwner(), "Object")) {
            return "object";
        }

        return null;
    }

    /**
     * function_description
     *
     * @param $action:
     * @param $info:
     * @param $parent_event_id:
     *
     * @return
     */
    protected function basicPublish($action,$info="",$parent_event_id=0) {
        $object_id = $this->getOwner()->id;
        $object_type = $this->getObjType();
        // from
        if (Yii::app()->hasComponent("user")) {
            $from = Yii::app()->user->id;
        } else {
            $from = 0;
        }

        Yii::app()->cmsEvent->publishEvent($object_id,$object_type,$action,$from,$info,$parent_event_id);
    }


    /**
     * publish new event
     *
     *
     * @return
     */
    public function fireNew($info="",$parent_event_id=0) {
        return $this->basicPublish('new',$info,$parent_event_id);
    }

    /**
     * publish update event
     *
     *
     * @return
     */
    public function fireUpdate($info="",$parent_event_id=0) {
        return $this->basicPublish('update',$info,$parent_event_id);
    }

    /**
     * publish delete event
     *
     *
     * @return
     */
    public function fireDelete($info="",$parent_event_id=0) {
        return $this->basicPublish('delete',$info,$parent_event_id);

    }

    /**
     * publish object published event
     *
     *
     * @return
     */
    public function firePublished($info="",$parent_event_id=0) {
        return $this->basicPublish('published',$info,$parent_event_id);

    }

    /**
     * publish touch event
     *
     *
     * @return
     */
    public function fireTouch($info="",$parent_event_id=0) {
        return $this->basicPublish('touch',$info,$parent_event_id);

    }


}
