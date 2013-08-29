<?php
Yii::import("gcommon.components.GController");
class PageController extends GController {
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
                    'preview','activity','modify','view','gamelist','pai'
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
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $domains = $model->getCanUseDomain();
        $templetes = Yii::app()->publisher->domains;
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->attributes = $_POST['Page'];
            if(!empty($_FILES['Page']['name']['upload'])){
                $model->saveRarFile($_FILES);
                $model->status = Page::STATUS_PARSEING;
            }else{
                $model->content = $_POST['Page']['content'];
                $model->status = Page::STATUS_DRAFT;
            }
            $model->modified_id = Yii::app()->user->id;
            $model->save(false);
            if(!empty($_FILES['Page']['name']['upload'])){
                (new CmsTasks())->parsePage($model->id);
            }
            Yii::app()->user->setFlash('success', '修改成功！');
        }
        $this->render('update', array(
            'model' => $model,"domains"=>$domains
        ));
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
     * The function that do View User
     *
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
        $model_name = "Page";
        $model = GxcHelpers::loadDetailModel($model_name, $id);
        $this->render('view', array(
            "model" => $model
        ));
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
