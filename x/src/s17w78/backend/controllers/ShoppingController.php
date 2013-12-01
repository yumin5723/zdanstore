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
                    'confirm'
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
            $ret = array_slice($results, 0,2);
            $html = $this->render('cartdialog',array('carts'=>$ret),true);
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
        }else{
            //read from database my uid
            $results = Cart::model()->getAllCartsInfoFromUid(Yii::app()->user->id);
        }
        $this->render('cart',array('carts'=>$results));
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
                    echo "success";
                }else{
                    echo "fail";
                }
            }else{
                if(Cart::model()->deleteProductById($id,Yii::app()->user->id)){
                    echo "success";
                }else{
                    echo "fail";
                }
            }
        }else{
            throw new Exception("Error Processing Request", 404);
        }
    }
}
