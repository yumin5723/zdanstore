<?php

/**
 * cms objects type definition
 */

class CmsTypeBehavior extends CActiveRecordBehavior {

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

}