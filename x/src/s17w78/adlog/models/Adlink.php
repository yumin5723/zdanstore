<?php

class Adlink extends CActiveRecord {
    public $all_prefix = array(
	   '1378.com',
    );
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
    return "adlink";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
    return array(
	array('name,description,link,prefix', 'required',),
	array('link','url','allowEmpty'=>false, "validSchemes"=>array("http","https"),
	"defaultScheme"=>'http',
	),
	array('prefix', "prefixIndexToValue")
    );
    }

    /**
     * function_description
     *
     * @param $attribues:
     *
     * @return
     */
    public function prefixIndexToValue($attribues) {
	if (isset($this->all_prefix[$this->prefix])) {
	    $this->prefix = $this->all_prefix[$this->prefix];
	    return true;
	} else {
	    return false;
	}
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
	"name" => "链接名称",
	"description" => "链接描述",
	"link" => "指向地址",
	"prefix" => "选择域名",
    );
    }

    public function outerLink() {
	return sprintf("http://%s/?id=%d&l=%s&f=", $this->prefix,$this->id,urlencode($this->link));
    }

    public function shortLink() {
	return sprintf("http://%s/%d", $this->prefix,$this->id);
    }

}
