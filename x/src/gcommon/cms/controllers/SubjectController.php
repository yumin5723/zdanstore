<?php
/**
 * Subject Brand Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class SubjectController extends GController {
    public $sidebars = array(
        array(
            'name' => '满减活动',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '创建活动',
            'icon' => 'tasks',
            'url' => 'create',
        ),
    );
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
                    'create',
                    'admin',
                    'update',
                    'delete',
                    'product',
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'allow', // all all users
                'actions' => array(
                    'error'
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
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Brand::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Update Settings
     *
     */
    public function actionUpdateSettings() {
        $this->menu = array();
        $this->render('user_update_settings');
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Subject;
        $types = ConstantDefine::getSubjectType();
        if(isset($_POST['choosetype'])){
            $subject = $model->findByAttributes(array('type'=>$_POST['choosetype']));
            if(!empty($subject)){
                 Yii::app()->user->setFlash('error', '已经存在此类活动');
                 return $this->render('new', array(
                    'subject' => $model,"types"=>$types,"isNew"=>true
                ));
            }
            $type = $types[$_POST['Subject']['type']];
            $all_brands = Brand::model()->findAll();
            $node = Oterm::model()->roots()->findByPk(7);
            $descendants = $node->descendants()->findAll();
            return $this->render($type,array("subject"=>$model,"type"=>$_POST['Subject']['type'],'brands'=>$all_brands,"isNew"=>true,"descendants" => $descendants));
        }
        if (isset($_POST['Subject'])) {
            $model->attributes = $_POST['Subject'];
            $model->product_type = isset($_POST['product_type']) ? $_POST['product_type '] : "";
            $model->brand = isset($_POST['brand']) ? $_POST['brand'] : "";
            $model->oterm = isset($_POST['oterm']) ? $_POST['oterm'] : "";
            if ($model->validate()) {
                if($model->saveSubject()){
                    Yii::app()->user->setFlash('success', '新建成功！');
                    // $this->redirect("admin");
                }
            }
        }
        $this->render('new', array(
            'subject' => $model,"types"=>$types,"isNew"=>true
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new Brand('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Brand"]))
                    $model->attributes=$_GET["Brand"];  
        $this->render('admin', array(
            "model" => $model
        ));
    }
    /**
     * The function that do View User
     *
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
        $model_name = "Game";
        $model = GxcHelpers::loadDetailModel($model_name, $id);
        $this->render('view', array(
            "model" => $model
        ));
    }
    /**
     * The function that do Update User
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        if ($id !== 0) {
            $model = $this->loadModel($id);
            // collect user input data
            if (isset($_POST['Brand'])) {
                if ($model->updateAttrs($_POST['Brand'])) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('update', array(
            "model" => $model,"isNew"=>false,
        ));
    }
    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('Brand', $id);
    }
    /**
     * action for view product belong to brand
     * @return [type] [description]
     */
    public function actionProduct(){
        $id = $_GET['id'];
        $brand = Brand::model()->findByPk($id);
        if(empty($brand)){
            throw new Exception("Error Processing Request", 404);
        }
        $products = Product::model()->getAllProductsByBrand($id);
        $this->render('product',array('model'=> new Product,'id'=>$id));
    }
}
