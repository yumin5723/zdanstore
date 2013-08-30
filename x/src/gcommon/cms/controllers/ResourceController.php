<?php
/**
 * Backend Resource Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class ResourceController extends CmsController {
    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {
        
        return array(
            'accessControl'
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules() {
        
        return array(
            array(
                'allow',
                'actions' => array(
                    'create','admin','view','update','createframe','delete'
                ) ,
                'users' => array(
                    '@'
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
    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            array(
                'label' => Yii::t('cms', '管理上传文件') ,
                'url' => array(
                    'admin'
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
            array(
                'label' => Yii::t('cms', '上传文件') ,
                'url' => array(
                    'create'
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
        );
    }
    /**
     * The function that do Create new Resource
     *
     */
    public function actionCreate() {
        $this->render('resource_create');
    }
    /**
     * The function that do Create new Resource
     *
     */
    public function actionCreateFrame() {
        $this->layout = 'clean';
        $this->render('resource_create_frame');
    }
    /**
     * The function that do Manage Resource
     *
     */
    public function actionAdmin() {
        $this->render('resource_admin');
    }
    /**
     * The function that view Resource details
     *
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
        $this->menu=array_merge($this->menu,                       
                        array(
                            array('label'=>Yii::t('cms','Update this Resource'), 'url'=>array('update','id'=>$id),'linkOptions'=>array('class'=>'button')),
                            array('label'=>Yii::t('cms','View this Resource'), 'url'=>array('view','id'=>$id),'linkOptions'=>array('class'=>'button'))
                        )
                    );
        $model_name = "Resource";
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $model = GxcHelpers::loadDetailModel($model_name, $id);
        $this->render('view', array(
            "model" => $model
        ));
    }
    /**
     * The function that update Resrouce
     *
     */
    public function actionUpdate() {
        $id=isset($_GET['id']) ? (int) ($_GET['id']) : 0 ;                
        $this->menu=array_merge($this->menu,                       
                array(
                    array('label'=>Yii::t('cms','Update this Resource'), 'url'=>array('update','id'=>$id),'linkOptions'=>array('class'=>'button')),
                    array('label'=>Yii::t('cms','View this Resource'), 'url'=>array('view','id'=>$id),'linkOptions'=>array('class'=>'button'))
                )
            );
        $this->render('resource_update',array('id'=>$id));
    }
    /**
     * The function is to Delete Menu
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('Resource', $id);
    }
}
