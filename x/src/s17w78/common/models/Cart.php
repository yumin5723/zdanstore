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
    /**
     * get all carts info by uid
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getAllCartsInfoFromUid($uid){
        if(empty($uid) || $uid <= 0){
            return array();
        }
        $carts = self::model()->findAllByAttributes(array('uid'=>$uid));
        $ret = array();
        foreach($carts as $key=>$cart){
            $product = Product::model()->with('brand')->findByPk($cart->product_id);
            $ret[$cart->id]['cart_id'] = $cart->id;
            $ret[$cart->id]['id'] = $cart->product_id;
            $ret[$cart->id]['quantity'] = $cart->quantity;
            $ret[$cart->id]['profiles'] = unserialize($cart->meta);
            $ret[$cart->id]['productName'] = $product->name;
            $ret[$cart->id]['logo'] = $product->logo;
            $ret[$cart->id]['shop_price'] = $product->shop_price;
            $ret[$cart->id]['brand_id'] = $product->brand->id;
            $ret[$cart->id]['weight'] = $product->weight;
            $ret[$cart->id]['brand_name'] = $product->brand->name;
        }
        // $ret['total'] = $total;
        return $ret;
    }
    /**
     * delete a product from user's cart
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function deleteProductById($id,$uid){
        $cart = self::model()->findAllByAttributes(array('id'=>$id,'uid'=>$uid));
        if(empty($cart)){
            return false;
        }
        if(self::model()->deleteByPk($id)){
            return true;
        }
        return false;
    }
    /**
     * get carts total price
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getCartsTotalPrice($uid){
        $carts = self::model()->findAllByAttributes(array('uid'=>$uid));
        $price = 0;
        foreach($carts as $cart){
            $product = Product::model()->findByPk($cart->product_id);
            if(Yii::app()->shoppingcart->getNowPrice($cart->product_id) == ""){
                $price += $cart->quantity * $product->shop_price;
            }else{
                $price += $cart->quantity * Yii::app()->shoppingcart->getNowPrice($cart->product_id);
            }
        }
        return $price;
    }
}