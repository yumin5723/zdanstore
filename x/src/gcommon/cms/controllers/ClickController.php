<?php
/**
 * click Brand Controller.
 * 
 * ad position
 * @version 1.0
 * @package backend.controllers
 *
 */

class ClickController extends GController {
    public $sidebars = array(
        array(
            'name' => '广告列表',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '添加广告',
            'icon' => 'tasks',
            'url' => 'create',
        ),
        array(
            'name' => '导航条新品管理',
            'icon' => 'tasks',
            'url' => 'new',
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
                    'new','newarrivals','addnew','updatenew','deletenew'
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
        $model = Click::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $model = new Click;
        // collect user input data
        if (isset($_POST['Click'])) {
            $model->setAttributes($_POST['Click']);
            // validate user input password
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Click Successfully!'));
                    $this->redirect('admin');
                }
            }
        }
        $this->render('create', array(
            "model" => $model,'isNew'=>true,
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new Click('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Click"]))
                    $model->attributes=$_GET["Click"];  
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
            if (isset($_POST['Click'])) {
                if ($model->updateAttrs($_POST['Click'])) {
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
        GxcHelpers::deleteModel('Click', $id);
    }
    /**
     * action for new arrivals create
     * @return [type] [description]
     */
    public function actionNew(){
        
        $model = new Newarrivals('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Newarrivals"]))
                    $model->attributes=$_GET["Newarrivals"];  
        $this->render('newlist', array(
            "model" => $model
        ));
    }
    /**
     * 
     */
    public function actionAddnew(){
        $model = new Newarrivals;
        $Newarrivals = $model->findAll();
        if(isset($_POST['Newarrivals'])){
            if($model->validate()){
                if($model->saveNewarrivals($_POST['Newarrivals'])){
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new arrivals Successfully!'));
                }
            }
        }
        $this->render('new',array('model'=>$model,'isNew'=>true));
    }
    public function actionUpdatenew(){
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        if ($id !== 0) {
            $model = Newarrivals::model()->findByPk($id);
            // collect user input data
            if (isset($_POST['Newarrivals'])) {
                if ($model->updateAttrs($_POST['Newarrivals'])) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('updatenew', array(
            "model" => $model
        ));
    }
    /**
     * The function is to Delete a User
     *
     */
    public function actionDeletenew($id) {
        GxcHelpers::deleteModel('Newarrivals', $id);
    }
    /**
     * [actionNewarrivals description]
     * @return [type] [description]
     */
    public function actionNewarrivals(){
        $Newarrivals = Newarrivals::model()->findAll();
        $model = new Newarrivals;
        if(isset($_POST['Newarrivals'])){
            if($model->updateNewarrivals($_POST['Newarrivals'])){
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Update new Newarrivals Successfully!'));
            }
        }
        $this->render('newarrivals',array('newarrivals'=>$Newarrivals,'isNew'=>false));
    }
}
