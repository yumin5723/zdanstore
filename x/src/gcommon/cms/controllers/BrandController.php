<?php
/**
 * Backend Brand Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class BrandController extends GController {
    public $sidebars = array(
        array(
            'name' => '品牌列表',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '创建品牌',
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
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $model = new Brand;
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'usercreate-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        //get term for brand chose
        $terms = Oterm::model()->getOtermLevelTwo();
        // collect user input data
        if (isset($_POST['Brand'])) {
            $model->setAttributes($_POST['Brand']);
            // validate user input password
            if ($model->validate()) {
                if ($model->save()) {
                    BrandTerm::model()->saveBrandTerm($model->id,$_POST['Oterm']);
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Brand Successfully!'));
                }
            }
        }
        $this->render('create', array(
            "model" => $model,'isNew'=>true,"terms"=>$terms
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
                    BrandTerm::model()->updateBrandTerm($model->id,$_POST['Oterm']);
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        //get term for brand chose
        $terms = Oterm::model()->getOtermLevelTwo();
        $select_terms = BrandTerm::model()->getAllTermsRefObject($_GET['id']);
        $this->render('update', array(
            "model" => $model,"isNew"=>false,'select_terms'=>$select_terms,'terms'=>$terms
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
