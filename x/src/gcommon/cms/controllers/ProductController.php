<?php
/**
 * Backend Product Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class ProductController extends GController {

    public $sidebars = array(
        array(
            'name' => '商品列表',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '添加商品',
            'icon' => 'tasks',
            'url' => 'create',
        ),
    );
    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {

        return array(
            'accessControl'
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules() {

        return array(
            array(
                'allow',
                'actions' => array(
                    'create', 'admin','update','addphoto','changenew','changerecommond'
                ) ,
                'users' => array(
                    '@'
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
     * The function that do Create new Object
     *
     */
    public function actionCreate() {
        $model = new Product;
        $node = Oterm::model()->roots()->findByPk(14);
        $descendants = $node->descendants()->findAll();

        //product profiles
        $termsProfiles = TermProfile::model()->getAllProfiles();
        if ( isset( $_POST["Product"] ) ) {
            $model->attributes=$_POST["Product"];
            if($model->validate()){
                if ( $model->save() ) {
                    //save product term
                    ProductTerm::model()->saveProductTerm($model->id,$_POST['Oterm']);
                    //save product meta
                    // if(isset($_POST['Meta'])){
                    //     ProductMeta::model()->saveProductMeta($model->id,$_POST['Meta']);
                    // }
                    if(isset($_POST['Profile'])){
                        ProductProfile::model()->saveProductProfile($model->id,$_POST['Profile']);
                    }
                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'Create new Product Successfully!' ) );
                    $this->redirect("/pp/product/admin");
                }
            }
        }
        $this->render( 'create', array( "model"=>$model,"isNew"=>true,"descendants" => $descendants,
            "node" => $node,'termprofiles'=>$termsProfiles
            ) );
    }

    
    /**
     * The function that do Update Object
     *
     */
    public function actionUpdate() {
        if(!isset($_GET['id']) || $_GET['id'] == 0){
            throw new Exception("Error Processing Request", 404);
            
        }
        $product = Product::model()->findByPk($_GET['id']);
        $node = Oterm::model()->roots()->findByPk(14);
        $descendants = $node->descendants()->findAll();
        //product terms 
        $select_terms = ProductTerm::model()->getAllTermsRefObject($_GET['id']);
        //product metas
        // $metas = ProductMeta::model()->getAllMetasByProductId($_GET['id']);
        $termsProfiles = TermProfile::model()->getAllProfiles();
        $profiles = ProductProfile::model()->getProductProfileByProduct($_GET['id']);
        // print_r($profiles);exit;
        if ( isset( $_POST["Product"] ) ) {
            $product->attributes=$_POST["Product"];
            if($product->validate()){
                if ( $product->save() ) {
                    ProductTerm::model()->updateProductTerm($product->id,$_POST['Oterm']);
                    //update product meta
                    // if(isset($_POST['Meta'])){
                    //     ProductMeta::model()->updateProductMeta($product->id,$_POST['Meta']);
                    // }
                    if(isset($_POST['Profile'])){
                        ProductProfile::model()->updateProductProfile($product->id,$_POST['Profile']);
                    }
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                    // $this->redirect("/pp/product/update/id/".$_GET['id']);
                }
            }else{
                print_r($product->getErrors());exit;
            }
        }
        $this->render( 'update',array("model"=>$product,"isNew"=>false,"descendants" => $descendants,
            "node" => $node,'select_terms'=>$select_terms,'termprofiles'=>$termsProfiles,'profiles'=>$profiles
            ) );
    }
     /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new Product('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Product"]))
                    $model->attributes=$_GET["Product"];  
        $this->render('admin', array(
            "model" => $model
        ));
    }
    /**
     * action for add product photo
     * @return [type] [description]
     */
    public function actionAddphoto(){
        $id = $_GET['id'];
        $product = Product::model()->findByPk($id);
        if(empty($product)){
            throw new Exception("the request page is not find", 404);
        }
        $images = ProductImage::model()->getAllImagesByProductId($id);
        if(Yii::app()->request->isPostRequest){
        // if(isset($_POST)){
            if(empty($_POST['Product'])){
                //delete all images
                ProductImage::model()->removeAllImages($id);
            }else{
                //save data into table product_image
                if(empty($images)){
                    ProductImage::model()->saveProductImages($id,$_POST['Product']);
                }else{
                    ProductImage::model()->updateProductImages($id,$_POST['Product']);
                }
            }
            Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'Create new ProductPhoto Successfully!' ) );
                    $this->redirect("/pp/product/addphoto/id/".$_GET['id']);
        }
        // }
        $this->render('addphoto',array('images'=>$images));

    }
    /**
     * action for change product new 
     * @return [type] [description]
     */
    public function actionChangenew(){
        $id = $_GET['id'];
        $product = Product::model()->findByPk($id);
        if(empty($product)){
            throw new Exception("Error Processing Request", 404);
        }
        if($product->is_new == Product::PRODUCT_IS_NEW){
            $product->is_new = Product::PRODUCT_IS_NOT_NEW;
        }else{
            $product->is_new = Product::PRODUCT_IS_NEW;
        }
        $product->save(false);
        $this->redirect(Yii::app()->request->urlReferrer);
    }
    /**
     * action for change product recommond 
     * @return [type] [description]
     */
    public function actionChangerecommond(){
        $id = $_GET['id'];
        $product = Product::model()->findByPk($id);
        if(empty($product)){
            throw new Exception("Error Processing Request", 404);
        }
        if($product->is_recommond == Product::PRODUCT_IS_NOT_RECOMMOND){
            $product->is_recommond = Product::PRODUCT_IS_RECOMMOND;
        }else{
            $product->is_recommond = Product::PRODUCT_IS_NOT_RECOMMOND;
        }
        $product->save(false);
        $this->redirect(Yii::app()->request->urlReferrer);
    }
}
