<?php
class Rank extends CActiveRecord
{
    const CODE_GET_RANK = 1;
    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'rank';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            
        );
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
    }
    /**
     * get rank result by type 
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function getRankByType($type){
        $criteria = new CDbCriteria;
        $criteria->condition = "type=:type";
        $criteria->params = array(":type"=>$type);
        $criteria->order = "id DESC";
        $result = self::model()->find($criteria);
        if(empty($result)){
            return array();
        }
        return unserialize($result->value);
    }
}