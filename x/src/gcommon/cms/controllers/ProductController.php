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
                    'create', 'admin','update'
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
                    ProductMeta::model()->saveProductMeta($model->id,$_POST['Meta']);
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
                    ProductMeta::model()->updateProductMeta($product->id,$_POST['Meta']);

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
     * The function that list object update history
     *
     */
    public function actionHistory() {
        $model = new ObjectBak('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["ObjectBak"]))
                    $model->attributes=$_GET["ObjectBak"];  
        $this->render('history', array(
            "model" => $model
        ));
    }
     /**
     * The function that do View content bak
     *
     */
    public function actionBakview() {
        $model_name = "ObjectBak";
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $model = GxcHelpers::loadDetailModel($model_name, $id);
        $this->menu = array(
            array(
                'label' => Yii::t( 'cms', '重新应用' ) ,
                'url' => array(
                    Yii::app()->createUrl("/cms/object/update", array("id"=>$model->object_id,"history_id"=>$id)),
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
            array(
            ) ,
        );
        
        $this->render('bakview', array(
            "model" => $model
        ));
    }
    /**
     * This function change content status as pending
     *
     */
    public function actionChange($id){
        $content = Object::model()->findByPk($id);
        if(!empty($content)){
            $content->object_status = ConstantDefine::OBJECT_STATUS_PENDING;
            $content->save();
            $this->redirect('/object/admin',array("type"=>0));
        }
    }
    /**
     * This function sugget Person that the current user can send content to
     *
     */
    public function actionSuggestPeople() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'suggest_people'
            ) );
    }
    /**
     * This function sugget Person that the current user can send content to
     *
     */
    public function actionCheckTransferRights() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'check_transfer_rights'
            ) );
    }
    /**
     * This function sugget Tags for Object
     *
     */
    public function actionSuggestTags() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'suggest_tags'
            ) );
    }
    /**
     * The function is to Delete a Content
     *
     */
    public function actionDelete( $id ) {
       $model = new Object;
        $result = $model->updateByPk((int)$id,array('object_status'=>ConstantDefine::OBJECT_STATUS_DELETE));
        if($result !== false){
            $object = $model->findByPk($id);
            $updateTemplate = $object->doDelete();
            $this->redirect("/cms/object/admin/type/0");
        }
    }
    /**
     * The function is to Delete a Content backup
     *
     */
    public function actionBakdelete( $id ) {
        GxcHelpers::deleteModel( 'ObjectBak', $id );
    }
    /**
     * preview object
     */
    public function actionPreview($id){
        $object = Object::model()->findByPk($id);
        echo $object->display();
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $object = Object::model()->findByPk($id);
        $result = $object->doPublish();
        if($result !== false){
            Yii::app()->user->setFlash( 'success', Yii::t( 'cms', '发布成功!' ) );
            $this->redirect("/cms/object/admin/type/0");
        }
    }
    /**
     * the action is get object by category id
     */
    public function actionFind($id){
        $model = new Object;
        $model->unsetAttributes(); 
        $result = ObjectTerm::model()->fetchObjectsByTermid($id);
        $term = Term::model()->findByPk($id);
        $this->render( 'find', array(
            'model'=>$model,
            'result'=>$result,
            'term'=>$term,
            ) );
    }
     
}
