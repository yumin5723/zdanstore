<?php
Yii::import("gcommon.components.GController");
class TemplateController extends GController {
    public $sidebars = array(
        array(
            'name' => '模板列表',
            'icon' => 'user',
            'url' => 'admin',
        ),
        array(
            'name' => '新建模板',
            'icon' => 'user',
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
                    'delete',
                    'update','batchtask',
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
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) echo $error['message'];
            else $this->render('error', $error);
        }
    }
    /**
     * Displays the page list
     */
    public function actionAdmin() {
        $model=new Templete('search');            
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Templete']))
                $model->attributes=$_GET['Templete'];                       
         $this->render('admin',array('model'=>$model));
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Templete;
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'taxonomy-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        // collect user input data
        if (isset($_POST['Templete'])) {
            $model->attributes = $_POST['Templete'];
            if ($model->validate()) {
                if ($model->saveRarFile($_FILES)) {
                    $model->admin_id = Yii::app()->user->id;
                    $model->save();
                    (new CmsTasks())->parseTemplete($model->id);
                    Yii::app()->user->setFlash('success', '新建成功！');
                }
            }
        }
        $this->render('create', array(
            'model' => $model
        ));
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        // collect user input data
        if (isset($_POST['Templete'])) {
            $model->attributes = $_POST['Templete'];
            $model->modified_id = Yii::app()->user->id;
            if(!empty($_FILES['Templete']['name']['upload'])){
                $model->saveRarFile($_FILES);
                $model->status = Templete::STATUS_PARSEING;
            }else{
                $model->content = $_POST['Templete']['content'];
            }
            $model->save(false);
            if(!empty($_FILES['Templete']['name']['upload'])){
                (new CmsTasks())->parseTemplete($id);
            }
            $model->firePublished();
            Yii::app()->user->setFlash('success', '修改成功！');
        }
        $this->render('update', array(
            'model' => $model
        ));
    }


    public function actionDelete($id){
        GxcHelpers::deleteModel('Templete', $id);  
    }
    /**
     * add batch publish content whose use this templete task
     */
    public function actionBatchtask($id){
        $content_ids = Templete::model()->publishAllContents($id);
        (new CmsTasks())->batchContentPublish($content_ids);
        Yii::app()->user->setFlash('success', '批量发布任务添加成功');
        $this->redirect("admin");
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Templete::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
