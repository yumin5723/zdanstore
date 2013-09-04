<?php
/**
* shoppint cart class
* @author hackerone
*/
class ShoppingCart extends CApplicationComponent{

    public $product_list = array();
    //$product_list = array(
    //      "1" => array(
    //          'quantity' => '2',
    //      ),
    //      '3' => array(
    //          'quantity' => '3',
    //      ),
    //
    //);

    public function init(){
        //read from cookie
        $cookie = Yii::app()->request->getCookies();
        if(isset($cookie['cart_info'])){
            $this->product_list = unserialize($cookie['cart_info']->value);
        }

        parent::init();
    }
    /**
     * add product into shopping cart 
     * if user is login the info saved into mysql
     * if user is not login the info save into cookie
     * @param intval $uid        user's id  if user not login null
     * @param intval $product_id product id
     * @param intval $quantity   product number
     */
    public function addToCart($uid,$product_id,$quantity){
        if(empty($uid)){
            if(empty($this->product_list)){
                $this->product_list[$product_id] = array('quantity'=>$quantity);
                $cookie = new CHttpCookie('cart_info',serialize($this->product_list));
                $cookie->expire = time()+60*60*24*30;  //30 days
                Yii::app()->request->cookies['cart_info']=$cookie; 
            }else{
                $this->updateCart($product_id,$quantity);
            }
        }else{
            $this->updateCartForUser($uid,$product_id,$quantity);
        }
    }
    /**
     * update shoppint cart 
     * add a new cart 
     * update old product auantity
     * @param  intval $product_id [description]
     * @param  intval $quantity   [description]
     * @return [type]             [description]
     */
    protected function updateCart($product_id,$quantity){
        $product_id = intval($product_id);
        if(array_key_exists($product_id, $this->product_list)){
            $this->product_list[$product_id]['quantity'] += $quantity;
        }else{
            $this->product_list[$product_id]['quantity'] = $quantity;
        }
        $cookie = new CHttpCookie('cart_info',serialize($this->product_list));
        $cookie->expire = time()+60*60*24*30;  //30 days
        Yii::app()->request->cookies['cart_info']=$cookie; 
    }
    /**
     * update for user shopping cart
     * this cart is saved into database
     * @param  [type] $uid        [description]
     * @param  [type] $product_id [description]
     * @param  [type] $quantity   [description]
     * @return [type]             [description]
     */
    protected function updateCartForUser($uid,$product_id,$quantity){
        $user_cart = Cart::model()->getCartProductIdsByUid($uid);

        if(in_array($product_id, $user_cart)){
            $cart = Cart::model()->findByAttributes(array('uid'=>$uid,'product_id'=>$product_id));
            $cart->quantity += $quantity;
            $cart->save(false);
        }else{
            $cart = new Cart;
            $cart->uid = $uid;
            $cart->product_id = $product_id;
            $cart->quantity = $quantity;
            $cart->save(false);
        }
    }
    /**
     * after user login need read cookie get shoppint cart
     * then save cookie shoppint cart info into database
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function shareShoppintCartAfterLogin($uid){
        $user_cart = Cart::model()->getCartProductIdsByUid($uid);
        $cookie_cart = array_keys($this->product_list);
        $to_update = array_intersect($user_cart,$cookie_cart);
        if(!empty($to_update)){
            foreach($to_update as $v){
                $cart = Cart::model()->findByAttributes(array('uid'=>$uid,'product_id'=>$v));
                $cart->quantity += $this->product_list[$v]['quantity'];
                $cart->save(false);
            }
        }
        $to_add = array_diff($cookie_cart,$user_cart);
        if(!empty($to_add)){
            foreach($to_add as $v){
                $cart = new Cart;
                $cart->uid = $uid;
                $cart->product_id = $v;
                $cart->quantity = $this->product_list[$v]['quantity'];
                $cart->save(false);
            }
        }

        //clear cart info cookie
        Yii::app()->request->cookies['cart_info'] = new CHttpCookie('cart_info', '');
        return true;
    }
}