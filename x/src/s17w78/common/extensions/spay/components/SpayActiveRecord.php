<?php

class SpayActiveRecord extends CActiveRecord {

    /**
     * function_description
     *
     *
     * @return
     */
    public function getDbConnection() {
	return Yii::app()->payment->getDbConnection();
    }


}