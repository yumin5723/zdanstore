<?php

class Wishlist extends CmsActiveRecord
{
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'wishlist';
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
            'product'=>array(self::BELONGS_TO, 'Product','product_id'),
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
            array('uid,product_id','required',),
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
     * [saveWish description]
     * @param  [type] $uid        [description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function saveWish($uid,$product_id){
        $model = new self;
        $model->uid = $uid;
        $model->product_id = $product_id;
        return $model->save(false);
    }
    /**
     * [getWishlistForHomepage description]
     * @return [type] [description]
     */
    public function getWishlistForHomepage($uid,$limit = 4){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $criteria->order = "t.id DESC";
        $criteria->limit = $limit;
        return self::model()->findAll($criteria);
    }
    /**
     * [getAllWishRecords description]
     * @param  [type] $uid   [description]
     * @param  [type] $count [description]
     * @param  [type] $page  [description]
     * @return [type]        [description]
     */
    public function getAllWishRecords($uid,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas;
    }
    /**
     * [getWishCountByUid description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getWishCountByUid($uid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        return self::model()->count($criteria);
    }
}