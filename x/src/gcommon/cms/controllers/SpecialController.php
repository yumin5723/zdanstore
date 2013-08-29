<?php

class SpecialController extends GController {
    public $sidebars = array(
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
                    'block',
                    'update',
                    'build',
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

    public function init(){
        $pages = Page::model()->getSpecialPages('special');
        $this->sidebars = $pages;
    }
    /**
     * Displays the page list
     */
    public function actionIndex() {
        $this->render('index');
    }

    public function actionBlock(){
        $page_id = $_GET['page_id'];
        $blocks = Block::model()->getSpecialPagesBlock("page", $page_id, "block");
        $results = new CActiveDataProvider("Block", array(
            'data'=>$blocks,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
        $this->render('block',array('blocks'=>$results,'page_id'=>$page_id));
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
            }
        }
        $this->render($types[$model->type], array(
            'block' => $model,"type"=>$model->type
        ));
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
}
