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
                    'create', 'admin','update','addphoto'
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
        $node = Oterm::model()->roots()->findByPk(7);
        $descendants = $node->descendants()->findAll();
        if ( isset( $_POST["Product"] ) ) {
            $model->attributes=$_POST["Product"];
            if($model->validate()){
                if ( $model->save() ) {
                    //save product term
                    ProductTerm::model()->saveProductTerm($model->id,$_POST['Oterm']);
                    //save product meta
                    if(isset($_POST['Meta'])){
                        ProductMeta::model()->saveProductMeta($model->id,$_POST['Meta']);
                    }
                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'Create new Product Successfully!' ) );
                    $this->redirect("/pp/product/admin");
                }
            }
        }
        $this->render( 'create', array( "model"=>$model,"isNew"=>true,"descendants" => $descendants,
            "node" => $node
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
        $node = Oterm::model()->roots()->findByPk(7);
        $descendants = $node->descendants()->findAll();
        //product terms 
        $select_terms = ProductTerm::model()->getAllTermsRefObject($_GET['id']);
        //product metas
        $metas = ProductMeta::model()->getAllMetasByProductId($_GET['id']);
        if ( isset( $_POST["Product"] ) ) {
            $product->attributes=$_POST["Product"];
            if($product->validate()){
                if ( $product->save() ) {
                    ProductTerm::model()->updateProductTerm($product->id,$_POST['Oterm']);
                    //update product meta
                    if(isset($_POST['Meta'])){
                        ProductMeta::model()->updateProductMeta($product->id,$_POST['Meta']);
                    }

                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'Create new Product Successfully!' ) );
                    $this->redirect("/pp/product/update/id/".$_GET['id']);
                }
            }
        }
        $this->render( 'update',array("model"=>$product,"isNew"=>false,"descendants" => $descendants,
            "node" => $node,'select_terms'=>$select_terms,'metas'=>$metas,
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
}
