<?php
Yii::import("gcommon.cms.components.ConstantDefine");
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * Backend Game Controller.
 *
 * @version 1.0
 * @GameActive backend.controllers
 *
 */

class GameactiveController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '活动列表',
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
        $model = GameActive::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $model = new GameActive;
        if (isset($_POST['GameActive'])) {
            $model->setAttributes($_POST['GameActive']);
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Game Successfully!'));
                    $this->redirect("admin");
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
        $model = new GameActive('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["GameActive"]))
                    $model->attributes=$_GET["Game"];  
        $this->render('admin', array(
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
            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'userupdate-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            // collect user input data
            if (isset($_POST['GameActive'])) {
                if ($model->updateAttrs($_POST['GameActive'])) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('create', array(
            "model" => $model,"isNew"=>false,
        ));
    }

    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('Game', $id);
    }
}
