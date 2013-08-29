<?php
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * 
 */
class TestuserController extends BackendController{
	public $sidebars = array(
        array(
            'name' => '白名单列表',
            'icon' => 'tasks',
            'url' => 'index',
        ),
        array(
            'name' => '添加用户',
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
                    'index',
                    'create',
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
    public function actionIndex() {
        $model = new TestUser('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["TestUser"]))
                    $model->attributes=$_GET["TestUser"];  
        $this->render('admin', array(
            "model" => $model
        ));
    }

    /**
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $model = new TestUser;
        if (isset($_POST['TestUser'])) {
            $model->setAttributes($_POST['TestUser']);
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new App Successfully!'));
                    $this->redirect("index");
                }
            }
        }
        $this->render('create', array(
            "model" => $model,'isNew'=>true,
        ));
    }

    public function actionUpdate(){
    	$id = $_GET['id'];
    	$model = $this->loadModel($id);
    	if (isset($_POST['TestUser'])) {
            if ($model->updateAttrs($_POST['TestUser'])) {
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
            }
        }
        $this->render('create', array('model'=>$model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = TestUser::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }

    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('TestUser', $id);
    }
}