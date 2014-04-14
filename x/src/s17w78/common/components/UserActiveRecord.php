<?php

class UserActiveRecord extends CActiveRecord {

    public static function getDb(){
    	return Yii::$app->getComponent('userDb');
    }

}