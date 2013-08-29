<?php
/**
 * Backend User Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class ManagerController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '管理管理员',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '创建管理员',
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
                    'changepass',
                    'create',
                    'admin',
                    'index',
                    'view',
                    'update',
                    'delete',
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
    public function actionIndex(){
        $this->render("index");
    }
    /**
     * The function that do Change Password
     *
     */
    public function actionChangePass() {
        $model = $this->loadModel(Yii::app()->user->id);
        if (isset($_POST['Manager'])) {
            $model->setScenario("changepass");
            $model->setAttributes($_POST['Manager']);
            if ($model->validate()) {
                if ($model->updateAttrs(array(
                    "password" => $_POST['Manager']['new_password'],
                    'password_repeat' => $_POST['Manager']['password_repeat']
                ))) {
                    Yii::app()->user->setFlash("success", sprintf("密码修改成功!"));
                }
            }
        }
        $this->render("changepass", array(
            "model" => $model
        ));
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Manager::model()->findByPk((int)$id);
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
        $model = new Manager;
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'usercreate-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        // collect user input data
        if (isset($_POST['Manager'])) {
            $model->setAttributes($_POST['Manager']);
            // validate user input password
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new User Successfully!'));
                }
            }
        }
        $this->render('create', array(
            "model" => $model
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new Manager('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Manager"]))
                    $model->attributes=$_GET["Manager"];  
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
        $this->menu = array_merge($this->menu, array(
            array(
                'label' => Yii::t('cms', 'Update this user') ,
                'url' => array(
                    'update',
                    'id' => $id
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
            array(
                'label' => Yii::t('cms', 'View this user') ,
                'url' => array(
                    'view',
                    'id' => $id
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            )
        ));
        $model_name = "Manager";
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
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
        $id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
        if ($id !== 0) {
            $model = $this->loadModel($id);
            $old_pass = (string)$model->password;
            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'userupdate-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            // collect user input data
            if (isset($_POST['Manager'])) {
                if($model->validate()){
                    if ($model->updateAttrs(array(
                        "password" => $_POST['Manager']['password'],
                        'password_repeat' => $_POST['Manager']['password'],
                        'username'=>$_POST['Manager']['username'],
                    ))) {
                        Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                    }
                }
                
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('update', array(
            "model" => $model
        ));
    }
    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('Manager', $id);
    }
}
