<?php

class BlockController extends BackendController {
     public $sidebars = array(
        array(
            'name' => '模块列表',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '新建模块',
            'icon' => 'tasks',
            'url' => 'new',
        ),
    );
    public function init(){
        $this->importCmsViews();
    }
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
                    'update','batchtask','history','viewhistory','new','build'
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
        $model=new Block('search');            
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Block']))
                $model->attributes=$_GET['Block'];                       
         $this->render('admin',array('model'=>$model));
    }
    /**
     * Displays the create page
     */
    public function actionNew() {
        $model = new Block;
        $types = ConstantDefine::getBlockType();
        if(isset($_POST['choosetype'])){
            $type = $types[$_POST['Block']['type']];
            return $this->render($type,array("block"=>$model,"type"=>$_POST['Block']['type'],"isNew"=>true));
        }
        if (isset($_POST['Block'])) {
            $model->attributes = $_POST['Block'];
            if ($model->validate()) {
                if($model->saveBlock(Yii::app()->user->id)){
                    Yii::app()->user->setFlash('success', '新建成功！');
                    $this->redirect("admin");
                }
            }
        }
        $this->render('new', array(
            'block' => $model,"types"=>$types,"isNew"=>true
        ));
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $types = ConstantDefine::getBlockType();
        // collect user input data
        if (isset($_POST['Block'])) {
            $model->attributes = $_POST['Block'];
            if($model->updateBlock(Yii::app()->user->id)){
                Yii::app()->user->setFlash('success', '修改成功！');
                // $this->redirect("/block/admin");
            }
        }
        $this->render($types[$model->type], array(
            'block' => $model,"type"=>$model->type
        ));
    }
    public function actionDelete($id){
        GxcHelpers::deleteModel('Block', $id);  
    }
    /**
     * build block html
     */
    public function actionBuild($id) {
        $block = $this->loadModel($id);
        $block->updateHtml();
        Yii::app()->user->setFlash('success', '获取内容成功');
        $this->redirect(Yii::app()->request->urlReferrer);
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Block::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * block update history
     */
    public function actionHistory($id){
        $records = BlockBackup::model()->getAllUpdateRecordsById($id);
        $this->render("history",array("records"=>$records));
    }
    /**
     * block update history
     */
    public function actionViewhistory($id){
        $backup = BlockBackup::model()->findByPk($id);
        $this->render("view",array("backup"=>$backup));
    }
}
