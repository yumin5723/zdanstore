<?php

class Subject extends CmsActiveRecord
{
    const SUBJECT_TYPE_DISCOUNT = 1;
    const SUBJECT_STATUS_OPEN = 0;
    const SUBJECT_STATUS_CLOSED = 1;
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'subject';
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'created',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('url',$this->url,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * Returns the static model of the specified AR class.
     * This method is required by all child classes of CActiveRecord.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
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
     * set attribution validator rules
     *
     *
     * @return
     */
    public function rules() {
        $rules =  array(
            array('name,type,value,status,url','required',),
            //array('email','email'),
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }
        /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['type']) && $attributes['type'] != $this->type) {
            $attrs[] = 'type';
            $this->type = $attributes['type'];
        }
        if (!empty($attributes['value']) && $attributes['value'] != $this->value) {
            $attrs[] = 'value';
            $this->value = $attributes['value'];
        }
        if (!empty($attributes['status']) && $attributes['status'] != $this->status) {
            $attrs[] = 'status';
            $this->status = $attributes['status'];
        }
        if (!empty($attributes['url']) && $attributes['url'] != $this->url) {
            $attrs[] = 'url';
            $this->url = $attributes['url'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * [convertProductIsNew description]
     * @param  [type] $isNew [description]
     * @return [type]        [description]
     */
    static public function convertSubjectTypes($type){
        if($type == self::SUBJECT_TYPE_DISCOUNT){
            return "折扣活动";
        }
    }
    /**
     * get all ad types 
     * status is self::PRODUCT_STATUS_SELL
     * @return [type] [description]
     */
    public function getTypes(){
        return array(
            self::SUBJECT_TYPE_DISCOUNT=>'折扣活动'
            );
    }
    /**
     * get all brands
     * @return [type] [description]
     */
    public function getStatus(){
        return array(
            self::SUBJECT_STATUS_OPEN=>'活动开放',
            self::SUBJECT_STATUS_CLOSED=>'活动关闭',
            );
    }
    /**
     * [getLastestSale description]
     * @return [type] [description]
     */
    public function getLastestSale($limit = 5){

        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.status = :status AND t.type = :type";
        $criteria->params = array("status"=>self::SUBJECT_STATUS_OPEN,":type"=>self::SUBJECT_TYPE_DISCOUNT);
        $criteria->order = "t.id DESC";
        return self::model()->findAll($criteria);
    }
}