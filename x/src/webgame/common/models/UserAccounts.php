<?php
class UserAccounts extends WebgameActiveRecord
{
    /**
     * model
     * 
     * @param $className:
     *
     * @return CPUser the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * get model table name
     * 
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'UserAccounts';
    }

    /**
     * get model rules
     * 
     *
     * @return array validation rules for model attributes
     */
    public function rules() {
        $rules =  array(

        );
        return $rules;
    }
    /**
     * get model relational rules
     * 
     *
     * @return array relational rules
     */
    public function relations() {
        return array();
    }

}