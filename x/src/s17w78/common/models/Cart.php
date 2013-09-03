<?php

/**
 * This is the model class for table "{{cart}}".
 *
 * The followings are the available columns in table '{{cart}}':
 * @property string $object_id
 * @property string $term_id
 * @property integer $term_order
 */
class Cart extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return ObjectTerm the static model class
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
        return 'cart';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

            // array('product_id, term_id', 'length', 'max'=>20),
            // // The following rule is used by search().
            // // Please remove those attributes that should not be searched.
            // array('product_id, term_id, data', 'safe', 'on'=>'search'),
        );
    }

    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
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
     * get cart info by uid
     * @return [type] [description]
     */
    public function getCartProductIdsByUid($uid){
        return array_map(function($product){return $product->product_id;},
            $this->findAllByAttributes(array('uid'=>intval($uid))));
    }
}