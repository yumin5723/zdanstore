<?php

class Adlog extends CActiveRecord {
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
	return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
	return "adlog";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
	return array(
	);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function relations() {
	return array();
    }

    /**
     * get attribute labels
     *
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'click_time'=>"访问时间",
	    'ip' => 'IP地址',
	    'refer' => '访问来源',
	    'l_from' => '自定义标识',
	);
    }

}
