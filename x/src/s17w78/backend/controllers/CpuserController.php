<?php
Yii::import("gcommon.cms.components.ConstantDefine");
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * Backend App Controller.
 *
 * @version 1.0
 * @Cpuser backend.controllers
 *
 */

class CpuserController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '管理CP',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '创建CP',
            'icon' => 'tasks',
            'url' => 'create',
        ),
        array(
            'name' => '应用列表',
            'icon' => 'tasks',
            'url' => 'applist',
        ),
        array(
            'name' => '游戏类型',
            'icon' => 'tasks',
            'url' => 'termlist',
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
                    'app',
                    'add',
                    'updateapp',
                    'applist',
                    'term',
                    'updateterm',
                    'termlist'
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
        $model = Cpuser::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $model = new Cpuser;
        if (isset($_POST['Cpuser'])) {
            $model->setAttributes($_POST['Cpuser']);
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new App Successfully!'));
                    $this->redirect("admin");
                }
            }
        }
        $this->render('create', array(
            "model" => $model,'isNew'=>true,
        ));
    }

    public function actionAdmin(){
    	$model = new Cpuser('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Cpuser"]))
                    $model->attributes=$_GET["Cpuser"];  
        $this->render('admin', array(
            "model" => $model
        ));
    }

    public function actionUpdate(){
    	$id = $_GET['id'];
    	$model = $this->loadModel($id);
    	if (isset($_POST['Cpuser'])) {
            if ($model->updateAttrs($_POST['Cpuser'])) {
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
            }
        }
        $this->render('create', array('model'=>$model));
    }

    public function actionApp(){
    	$id = $_GET['id'];
        $model = $this->loadModel($id);
        $batches = App::model()->findAllByAttributes(array('cp_id'=>$id));
        $results = new CActiveDataProvider("App", array(
            'data'=>$batches,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
        $this->render('appadmin',array('results'=>$results,'model'=>$model));
    }

    public function actionAdd(){
    	$cp_id = isset($_GET['id']) ? $_GET['id'] : "";
    	$cpuser = $this->loadModel($cp_id);
    	$model = new App;
    	if (!empty($cp_id)) {
	        if (isset($_POST['App'])) {
	            $model->setAttributes($_POST['App']);
                if ($model->validate()) {
	            	$model->cp_id = $cpuser->id;
	                if ($model->save()) {
	                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new App Successfully!'));
	                    $this->redirect("/cpuser/app/id/{$cp_id}");
	                }
	            }
	        }
    	}
        $this->render('app', array("model" => $model,'cpuser'=>$cpuser));
    }

    public function actionUpdateApp(){
    	$app_id = $_GET['id'];
    	$model = App::model()->findByPk($app_id);
        if (isset($_POST['App'])) {
            if ($model->updateAttrs($_POST['App'])) {
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                $this->redirect("/cpuser/app/id/{$model->cp_id}");
            }
        }
        $this->render('app', array('model'=>$model));
    }

    public function actionApplist(){
        $model = new App('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["App"]))
                    $model->attributes=$_GET["App"];  
        $this->render('applist', array(
            "model" => $model
        ));
    }
    /**
     * create game category
     */
    public function actionTerm(){
        $model = new WebgameTerm;
        if (isset($_POST['WebgameTerm'])) {
            $model->setAttributes($_POST['WebgameTerm']);
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('webgame', 'Create new term Successfully!'));
                    $this->redirect("termlist");
                }
            }
        }
        $this->render('term', array(
            "model" => $model,'isNew'=>true,
        ));
    }
    /**
     * update webgame term
     * @return [type] [description]
     */
    public function actionUpdateterm(){
        $id = $_GET['id'];
        $model = WebgameTerm::model()->findByPk($id);
        if (isset($_POST['WebgameTerm'])) {
            if ($model->updateAttrs($_POST['WebgameTerm'])) {
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
            }
        }
        $this->render('term', array('model'=>$model));
    }
    /**
     * term list
     * @return [type] [description]
     */
    public function actionTermlist(){
        $model = new WebgameTerm('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["WebgameTerm"]))
                    $model->attributes=$_GET["WebgameTerm"];  
        $this->render('termlist', array(
            "model" => $model
        ));
    }
    
}
