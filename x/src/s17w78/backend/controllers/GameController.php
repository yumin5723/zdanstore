<?php
Yii::import("gcommon.cms.components.ConstantDefine");
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * Backend Game Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class GameController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '小游戏管理',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '创建小游戏',
            'icon' => 'tasks',
            'url' => 'create',
        ),
        array(
            'name' => '手机游戏管理',
            'icon' => 'tasks',
            'url' => '/mobilegame/admin',
        ),
        array(
            'name' => '创建手机游戏',
            'icon' => 'tasks',
            'url' => '/mobilegame/create',
        ),
        array(
            'name' => '分类管理',
            'icon' => 'tasks',
            'url' => '/category/show?root=1',
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
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Game::model()->findByPk((int)$id);
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
        $model = new Game;
        $weights = ConstantDefine::getGameWeights();
        
        $roots = Category::model()->findByAttributes(array("root"=>1,"name"=>"小游戏"));
        $descendants = $roots->descendants()->findAll();

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'usercreate-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        $model->publish_date = date('Y-m-d H:i:s');
        // collect user input data
        if (isset($_POST['Game'])) {
            $model->setAttributes($_POST['Game']);
            // validate user input password
            if ($model->validate()) {
                $model->created_uid = Yii::app()->user->id;
                $model->modified_uid = $model->created_uid;
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Game Successfully!'));
                }
            }
        }
        $this->render('create', array(
            "model" => $model,"weights"=>$weights,'isNew'=>true,
            // 'category'=>$category_html,
            "descendants" => $descendants
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new Game('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Game"]))
                    $model->attributes=$_GET["Game"];  
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
        $weights = ConstantDefine::getGameWeights();
        $roots = Category::model()->findByAttributes(array("root"=>1,"name"=>"小游戏"));
        $descendants = $roots->descendants()->findAll();
        if ($id !== 0) {
            $model = $this->loadModel($id);
            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'userupdate-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            // collect user input data
            if (isset($_POST['Game'])) {
                $model->modified_uid = Yii::app()->user->id;
                if ($model->updateAttrs($_POST['Game'])) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('update', array(
            "model" => $model,"weights"=>$weights,"isNew"=>false,
            // 'category'=>$html,
            "descendants" => $descendants
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
