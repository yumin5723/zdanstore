<?php

class ProductStock extends CmsActiveRecord
{
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'product_stock';
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
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }
    public function updateProductStock($product_id,$profiles){
        self::model()->deleteAllByAttributes(array('product_id'=>$product_id));
        foreach($profiles as $key=>$profile){
            foreach($profile as $v){
                $model = new self;
                $model->product_id = $product_id;
                $model->color = $key;
                $model->size = $v;
                $model->save(false);
            }            
        }
        return true;
    }
}