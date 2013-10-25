<?php
/**
 * Default Controller.
 *
 * @version 1.0
 *
 */

class DefaultController extends GController {
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
        //foucus index
        $focus = Click::model()->getAdsByType(Click::AD_POSITION_INDEX_FOCUS);
        // //index right ad 
        $rightAd = Click::model()->getAdsByType(Click::AD_POSITION_INDEX_RIGHT);
        // //index down ad
        $downAd = Click::model()->getAdsByType(Click::AD_POSITION_INDEX_DOWN);
        // //recommond products
        // $products = Product::model()->getAllRecommondProducts();

        //brands in index
        $brands = Brand::model()->getBrandsForIndex();
        // $this->render('index',array('products'=>$products,'focus'=>$focus,'rightads'=>$rightads,"downAds"=>$downAd));
        $this->render("index",array('focus'=>$focus,'rights'=>$rightAd,"downAds"=>$downAd,'brands'=>$brands,'products'=>$products));
    }
}
