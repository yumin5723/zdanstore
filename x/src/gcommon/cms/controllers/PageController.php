<?php

class PageController extends GController {
    public $sidebars = array(
        array(
            'name' => '页面列表',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '新建页面',
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
                    'delete',
                    'update',
                    'publish',
                    'preview',
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
        $model=new Page('search');            
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Page']))
                $model->attributes=$_GET['Page'];                       
         $this->render('admin',array('model'=>$model));
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Page;
        $page_status = $model->getPageStatus();
        $domains = $model->getCanUseDomain();
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'taxonomy-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->attributes = $_POST['Page'];
            if ($model->validate()) {
                if ($model->saveRarFile($_FILES)) {
                    $model->admin_id = Yii::app()->user->id;
                    $model->save();
                    //save page term sign page type for admin menu
                    if($_POST['Page']['pagetype'] != "none"){
                        PageTerm::model()->signPageType($model->id,$_POST['Page']['pagetype']);
                    }

                    (new CmsTasks())->parsePage($model->id);
                    Yii::app()->user->setFlash('success', '新建成功！');
                    $this->redirect("admin");
                }
            }
        }
        $this->render('create', array(
            'model' => $model,"domains"=>$domains
        ));
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $domains = $model->getCanUseDomain();
        $templetes = Yii::app()->publisher->domains;
        $model->pagetype = PageTerm::model()->getTypeByPageId($id);
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->attributes = $_POST['Page'];
            if(!empty($_FILES['Page']['name']['upload'])){
                $model->saveRarFile($_FILES);
            }else{
                $model->content = $_POST['Page']['content'];
            }
            $model->modified_id = Yii::app()->user->id;
            $model->status = Page::STATUS_DRAFT;
            $model->save(false);
            if(!empty($_FILES['Page']['name']['upload'])){
                (new CmsTasks())->parsePage($model->id);
            }
            PageTerm::model()->updatePageType($model->id,$_POST['Page']['pagetype']);
            Yii::app()->user->setFlash('success', '修改成功！');
            $this->redirect("admin");
        }
        $this->render('update', array(
            'model' => $model,"domains"=>$domains
        ));
    }
    public function actionDelete($id){
        GxcHelpers::deleteModel('Page', $id);  
    }
    /**
     * push rar file
     */
    public function actionPublish($id) {
        $page = $this->loadModel($id);
        if($page->doPublish()){
            Yii::app()->user->setFlash('success', '发布成功！');
        }else{
            Yii::app()->user->setFlash('success', '发布失败请重新尝试!');
        }
        $this->redirect("admin");
    }
    /**
     * preview page for admin user
     */
    public function actionPreview($id){
        $page = $this->loadModel($id);
        echo $page->display();
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Page::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
