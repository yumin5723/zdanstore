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
    public function addToCart($uid,$product_id,$quantity,$meta){
        if(empty($uid)){
            if(empty($this->product_list)){
                $this->product_list[] = array('id'=>$product_id,'quantity'=>$quantity,'meta'=>$meta);
                $cookie = new CHttpCookie('cart_info',serialize($this->product_list));
                $cookie->expire = time()+60*60*24*30;  //30 days
                Yii::app()->request->cookies['cart_info']=$cookie; 
            }else{
                $this->updateCart($product_id,$quantity,$meta);
            }
        }else{
            $this->updateCartForUser($uid,$product_id,$quantity,$meta);
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
    protected function updateCart($product_id,$quantity,$meta){
        $product_id = intval($product_id);
        $flag = 0;
        foreach($this->product_list as $key=>$value){
            if($value['id'] == $product_id && $value['meta'] == $meta){
                $this->product_list[$key]['quantity'] += $quantity;
                $flag = 1;
            }else{
                $ret = array('id'=>$product_id,'quantity'=>$quantity,'meta'=>$meta);
            }
        }
        if($flag == 0){
            $this->product_list[] = $ret;
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
    protected function updateCartForUser($uid,$product_id,$quantity,$meta){
        $user_cart = Cart::model()->getCartProductIdsByUid($uid);

        if(in_array($product_id, $user_cart)){
            $cart = Cart::model()->findByAttributes(array('uid'=>$uid,'product_id'=>$product_id));
            if($cart->meta == serialize($meta)){
                $cart->quantity += $quantity;
                $cart->save(false);
            }else{
                $cart = new Cart;
                $cart->uid = $uid;
                $cart->product_id = $product_id;
                $cart->quantity = $quantity;
                $cart->meta = serialize($meta);
                $cart->save(false);
            }
        }else{
            $cart = new Cart;
            $cart->uid = $uid;
            $cart->product_id = $product_id;
            $cart->quantity = $quantity;
            $cart->meta = serialize($meta);
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

        // $flag = 0;
        foreach($this->product_list as $key=>$value){
            if(in_array($value['id'], $user_cart)){
                $cart = Cart::model()->findByAttributes(array('uid'=>$uid,'product_id'=>$value['id']));
                if($cart->meta == serialize($this->product_list[$key]['meta'])){
                    $cart->quantity += $this->product_list[$key]['quantity'];
                    $cart->save(false);
                }else{
                    $cart = new Cart;
                    $cart->uid = $uid;
                    $cart->product_id = $value['id'];
                    $cart->quantity = $this->product_list[$key]['quantity'];
                    $cart->meta = serialize($this->product_list[$key]['meta']);
                    $cart->save(false);
                }
            }else{
                $cart = new Cart;
                $cart->uid = $uid;
                $cart->product_id = $value['id'];
                $cart->quantity = $this->product_list[$key]['quantity'];
                $cart->meta = serialize($this->product_list[$key]['meta']);
                $cart->save(false);
            }

        }
        //clear cart info cookie
        Yii::app()->request->cookies['cart_info'] = new CHttpCookie('cart_info', '');
        return true;
    }
}