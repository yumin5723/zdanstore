<?php
/**
 * Shoppint Controller.
 *
 * @version 1.0
 *
 */

class ShoppingController extends GController {
    /**
     * @return array action filters
     */
    public function filters() {
        
        return array(
            'accessControl', // perform access control for CRUD operations
            
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        
        return array(
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'confirm','checkout','checkaddr','complete','getdefault','getshippingprice',
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'allow', // all all users
                'actions' => array(
                    'cartinit',
                    'cart',
                    'index',
                    'delete',
                    'checkorder',
                    'cartshow',
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
            array(
                'deny', // deny all users
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    /**
     * action for add product to shoppint cart
     * @return [type] [description]
     */
    public function actionCartinit(){
        if(Yii::app()->request->isPostRequest){
            // print_r($_POST);exit;
            if(Yii::app()->user->isGuest){
                $uid = "";
            }else{
                $uid = Yii::app()->user->id;
            }
            $product_id = $_POST['id'];
            $quantity = $_POST['quantity'];
            $profiles = isset($_POST['property']) ? $_POST['property'] : "";
            Yii::app()->shoppingcart->addToCart($uid,$product_id,$quantity,$profiles);

            $result = array();
            if(Yii::app()->user->isGuest){
                //read from cookie
                $results = Yii::app()->shoppingcart->getCartInfoFromCookie();
            }else{
                //read from database my uid
                $results = Cart::model()->getAllCartsInfoFromUid(Yii::app()->user->id);
            }
            $html = $this->render('cartdialog',array('carts'=>$results),true);
            echo json_encode($html);
        }else{
            throw new Exception("this Request is not valid", 404);
        }
    }
    /**
     * action for add product to shoppint cart
     * @return [type] [description]
     */
    public function actionCartshow(){
        if(Yii::app()->request->isPostRequest){
            // print_r($_POST);exit;
            if(Yii::app()->user->isGuest){
                $uid = "";
            }else{
                $uid = Yii::app()->user->id;
            }
            $results = array();
            if(Yii::app()->user->isGuest){
                //read from cookie
                $results = Yii::app()->shoppingcart->getCartInfoFromCookie();
            }else{
                //read from database my uid
                $results = Cart::model()->getAllCartsInfoFromUid(Yii::app()->user->id);
            }
            $html = $this->render('cartdialog',array('carts'=>$results),true);
            echo json_encode($html);
        }else{
            throw new Exception("this Request is not valid", 404);
        }
    }
    /**
     * action for product list
     * @return [type] [description]
     */
    public function actionIndex(){
        // Yii::app()->shoppingcart->shareShoppintCartAfterLogin(Yii::app()->user->id);
        // // print_r(Yii::app()->user->id);exit;
        // echo "success";exit;
        // $cookie = Yii::app()->request->getCookies();
        // if(isset($cookie['cart_info'])){
        //     $aaa = unserialize($cookie['cart_info']->value);
        //     print_r($aaa);exit;
        // }

        $products = Product::model()->getAllProductsCanBuy();
        $this->render('index',array('products'=>$products));
    }
    /**
     * action for cart list 
     * @return [type] [description]
     */
    public function actionCart(){
        $result = array();
        if(Yii::app()->user->isGuest){
            //read from cookie
            $results = Yii::app()->shoppingcart->getCartInfoFromCookie();
            $total = Yii::app()->shoppingcart->getCartTotalPriceFromCookie();
        }else{
            //read from database my uid
            $results = Cart::model()->getAllCartsInfoFromUid(Yii::app()->user->id);
            $total = Cart::model()->getCartsTotalPrice(Yii::app()->user->id);
        }
        $downAd = Click::model()->getAdsByType(Click::AD_POSITION_INDEX_DOWN);
        $this->render('cart',array('carts'=>$results,'total'=>$total,'downAds'=>$downAd));
    }
    /**
     * action for delete a cart product
     * @return [type] [description]
     */
    public function actionDelete(){
        if(Yii::app()->request->isPostRequest){
            $id = $_POST['id'];
            if(Yii::app()->user->isGuest){
                if(Yii::app()->shoppingcart->deleteProductFromCart($id)){
                    $data = "success";
                    echo json_encode($data);
                }else{
                    echo "fail";
                }
            }else{
                if(Cart::model()->deleteProductById($id,Yii::app()->user->id)){
                    $data = "success";
                    echo json_encode($data);
                }else{
                    echo "fail";
                }
            }
        }else{
            throw new Exception("Error Processing Request", 404);
        }
    }
    /**
     * action for user checkout 
     * @return [type] [description]
     */
    public function actionCheckout(){
        if(Yii::app()->request->isPostRequest){
            if(empty($_POST['Product'])){
                return;
            }     
            if(isset($_POST['BillingAddress'])){
                //save billing address
                $billingAddress = new BillingAddress;
                $billingAddress->setAttributes($_POST['BillingAddress']);
                $billingAddress->uid = Yii::app()->user->id;
                if($billingAddress->validate()){
                    $billingAddress->save(false);

                    $address = new Address;
                    $address->setAttributes($_POST['Address']);
                    $address->uid = Yii::app()->user->id;
                    if($address->validate()){
                        if(isset($_POST['chose_address']) && $_POST['chose_address'] != 0){
                            $address->save(false);
                        }else{
                            $address = Address::model()->findByAttributes(array("uid"=>Yii::app()->user->id,'default'=>1));
                        }
                        $_POST['Product']['address'] = $address->id;
                        $_POST['Product']['billing_address'] = $billingAddress->id;
                        $orderid = Order::model()->createOrder($_POST['Product']);
                        //delete shopping carts
                        // Cart::model()->delete/
                        // if($order_id == Order::model()->createOrder($_POST['Product'])){
                        $this->redirect("/shopping/complete/id/".$orderid);
                    }
                    // }    
                }
                
            }
            $results = Cart::model()->getAllCartsInfoFromUid(Yii::app()->user->id);
            $total = Cart::model()->getCartsTotalPrice(Yii::app()->user->id);
            $downAd = Click::model()->getAdsByType(Click::AD_POSITION_INDEX_DOWN);
            //get user address
            // $address = Address::model()->findAllByAttributes(array("uid"=>Yii::app()->user->id));
            $this->render('address',array('products'=>$_POST['Product'],'address'=>$address,'billingaddress'=>$billingAddress,'results'=>$results,'total'=>$total,'downAds'=>$downAd));
        }
    }
    /**
     * [actionCheckaddr description]
     * @return [type] [description]
     */
    public function actionCheckaddr(){
        if(Yii::app()->request->isPostRequest){
            if(isset($_POST['Product'])){
                foreach($_POST['Product'] as $key=>$value){
                    $cart = Cart::model()->findByPk($key);
                    if($cart->quantity != $value['quantity']){
                        $cart->quantity = $value['quantity'];
                        $cart->save(false);
                    }else{
                        continue;
                    }
                }
                $results = Cart::model()->getAllCartsInfoFromUid(Yii::app()->user->id);
                $total = Cart::model()->getCartsTotalPrice(Yii::app()->user->id);
                $downAd = Click::model()->getAdsByType(Click::AD_POSITION_INDEX_DOWN);
                $this->render('address',array('results'=>$results,'total'=>$total,'billingaddress'=>new BillingAddress,'address'=>new Address,'downAds'=>$downAd));
            }
        }else{
            $this->redirect('/shopping/cart');
        }
    }
    /**
     * [actionComplete description]
     * @return [type] [description]
     */
    public function actionComplete(){
        $id = $_GET['id'];
        $order = Order::model()->findByPk($id);
        if(empty($order) || $order->uid != Yii::app()->user->id){
            throw new Exception("this request is not found", 404);
        }
        $ads = Click::model()->getAdsByType(Click::AD_POSITION_ORDER_COMPLETE,2);
        $this->render('complete',array('order'=>$order,'ads'=>$ads));
    }
    /**
     * [actionGetdefault description]
     * @return [type] [description]
     */
    public function actionGetdefault(){
        $uid = Yii::app()->user->id;
        $default = Address::model()->findByAttributes(array("uid"=>$uid,"default"=>1));
        $html = $this->render("default",array('address'=>$default),true);
        echo json_encode($html);
    }
    /**
     * [actionGetshippingprice description]
     * @return [type] [description]
     */
    public function actionGetshippingprice(){
        if(Yii::app()->request->isPostRequest){
            $uid = $_POST['uid'];
            $country = $_POST['country'];
            $result = Yii::app()->shoppingcart->getShippingPriceByUidAndCountry($uid,$country);
            echo $result;
        }
    }
}
