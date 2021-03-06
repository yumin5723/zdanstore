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
            $result = $this->checkIsHaveProductInCart($uid,$product_id,$meta);
            if($result[0] == true){
                $cart = Cart::model()->findByPk($result[1]);
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
     * [checkIsHaveProductInCart description]
     * @param  [type] $uid        [description]
     * @param  [type] $product_id [description]
     * @param  [type] $meta       [description]
     * @return [type]             [description]
     */
    public function checkIsHaveProductInCart($uid,$product_id,$meta){
        $carts = Cart::model()->findAllByAttributes(array('uid'=>$uid,'product_id'=>$product_id));
        $have = array(false);
        foreach($carts as $cart){
            if($cart->meta == serialize($meta)){
                $have = array(true,$cart->id);
            }else{
                continue;
            }
        }
        return $have;
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
    /**
     * get cart info from cookie 
     * if user is not login list the cart info from cookie
     * @return [type] [description]
     */
    public function getCartInfoFromCookie(){
        if(empty($this->product_list)){
            return array();
        }
        foreach($this->product_list as $key=>$value){
            $product = Product::model()->with('brand')->findByPk($value['id']);
            if(!empty($product)){
                $this->product_list[$key]['productName'] = $product->name;
                $this->product_list[$key]['cart_id'] = $key;
                $this->product_list[$key]['logo'] = $product->logo;
                $this->product_list[$key]['shop_price'] = $product->shop_price;
                $this->product_list[$key]['profiles'] = $value['meta'];
                $this->product_list[$key]['brand_id'] = $product->brand->id;
                $this->product_list[$key]['brand_name'] = $product->brand->name;
                $this->product_list[$key]['id'] = $product->id;
                $this->product_list[$key]['quantity'] = $value['quantity'];
            }
        }
        return $this->product_list;
    }
    /**
     * delete a product from cart cookie
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteProductFromCart($id){
        if(empty($this->product_list)){
            return false;
        }
        unset($this->product_list[$id]);
        $cookie = new CHttpCookie('cart_info',serialize($this->product_list));
        $cookie->expire = time()+60*60*24*30;  //30 days
        Yii::app()->request->cookies['cart_info']=$cookie; 
        return true;
    }
    /**
     * [getCartTotalPriceFromCookie description]
     * @return [type] [description]
     */
    public function getCartTotalPriceFromCookie(){
        if(empty($this->product_list)){
            return 0;
        }
        $total = 0;
        foreach($this->product_list as $key=>$value){
            $product = Product::model()->findByPk($value['id']);
            $nowprice = $this->getNowPrice($value['id']);
            if($nowprice == ""){
                $price = $product->shop_price;
            }else{
                $price = $nowprice;
            }
            $total += $value['quantity'] * $price;
        }
        return $total;
    }
    /**
     * [getNowPrice description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function getNowPrice($product_id){
        $product = Product::model()->findByPk($product_id);
        $subject = SubjectProduct::model()->with('subject')->findByAttributes(array('product_id'=>$product_id));
        if(empty($subject) || $subject->subject->status == Subject::SUBJECT_STATUS_CLOSED){
            return "";
        }
        $discount = $subject->subject->value/10;
        // sprintf(”%01.3f”,1)
        return sprintf("%01.2f",$product->shop_price*$discount);
    }
    /**
     * [getShippingPriceByUidAndCountry description]
     * @param  [type] $uid     [description]
     * @param  [type] $country [description]
     * @return [type]          [description]
     */
    public function getShippingPriceByUidAndCountry($uid,$country){
        $uid = intval($uid);
        $products = Cart::model()->getAllCartsInfoFromUid($uid);
        $total_weight = 0;
        foreach($products as $product){
            $total_weight += $product['weight'] * $product['quantity'];
        }
        $unit = Shipping::SHIPPING_WIGHT_UNIT;
        $country_price = Shipping::model()->findByAttributes(array('country'=>$country));
        if(empty($country_price)){
            return "-1";
        }
        $shipping_price = 0;
        if($total_weight <= $unit){
            return $country_price->first_weight_price;
        }
        if($total_weight > $unit){
            $discrepancy = $total_weight - $unit;
            return ceil($discrepancy/$unit) * $country_price->add_weight_price + $country_price->first_weight_price;
        }
    }
}