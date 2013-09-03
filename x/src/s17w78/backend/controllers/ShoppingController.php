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
            if(Yii::app()->user->isGuest){
                $uid = "";
            }else{
                $uid = Yii::app()->user->id;
            }
            $product_id = $_POST['product'];
            $quantity = $_POST['quantity'];

            Yii::app()->shoppingcart->addToCart($uid,$product_id,$quantity);
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
}
