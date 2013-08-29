<?php
/**
 * Backend Object Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class NewsController extends CmsController {
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
                    'create', 'admin', 'view', 'update','delete','change','history','bakdelete','bakview','preview','publish','find'
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
    public function __construct( $id, $module = null ) {
        parent::__construct( $id, $module );
        $this->menu = array(
            array(
                'label' => Yii::t( 'cms', '新闻管理' ) ,
                'url' => array(
                    'admin'
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
            array(
                'label' => Yii::t( 'cms', '创建新闻' ) ,
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
     * The function that do Create new Object
     *
     */
    public function actionCreate() {
        $model = new Object;
        $model->object_date = date( "Y-m-d H:i:s" );
        $type = isset( $_GET['type'] ) ? $_GET['type'] : "article";
        $templetes = $model->getTempletes();
        $roots=Oterm::model()->roots()->findAll();
        if ( isset( $_POST["Object"] ) ) {
            $model->attributes=$_POST["Object"];
            $model->object_type = $type;
            if($model->validate()){
                //Convert the date time publish to timestamp
                $model->object_date = $_POST["Object"]["object_date"];
                $model->object_modified = $model->object_date;
                $model->object_date_gmt=date("Y-m-d H:i:s");
                $model->object_modified_gmt = $model->object_date_gmt;
                //object status can not be published when create object
                $model->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
                if ( $model->save() ) {
                    ObjectTemplete::model()->saveObjectTemplete($model->attributes['object_id'],$_POST['Templete']['id']);
                    if(!empty($_POST['Oterm'])){
                        $fhmodel = new FhObjectTerm;
                        $fhmodel->saveObjectTerm($model->object_id,$_POST['Oterm']);
                    }
                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'Create new Content Successfully!' ) );
                    $this->redirect("admin");
                }
            }
        }
        $this->render( 'create', array( "model"=>$model, "type"=>$type, 
             'templetes'=>$templetes,"isNew"=>true,"templete"=>"",'roots'=>$roots
            ) );
    }
    /**
     * The function that do Update Object
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        $model=  GxcHelpers::loadDetailModel('Object', $id);
        $type = $model->object_type;
        $guid = $model->guid;
        $model->isNewRecord = false;
        $templetes = $model->getTempletes();
        $templete = ObjectTemplete::model()->findByAttributes(array('object_id'=>$id));
        $roots=Oterm::model()->roots()->findAll();
        if ( isset( $_POST["Object"] ) ) {
            $model->attributes=$_POST["Object"];
            $model->object_type = $type;
            if($model->validate()){
                $model->object_date = $_POST["Object"]['object_date'];
                $model->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
                if ( $model->save() ) {
                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'modify Content Successfully!' ) );
                    ObjectTemplete::model()->saveObjectTemplete($model->attributes['object_id'],$_POST['Templete']['id']);
                    if(!empty($_POST['Oterm'])){
                        $fhmodel = new FhObjectTerm;
                        $fhmodel->updateObjectTerm($model->object_id,$_POST['Oterm']);
                    }
                    //save resource
                    $this->redirect("/news/admin");
                }
            }
        }
        $this->render( 'create',array("model"=>$model,"type"=>$type,
            'templete'=>$templete,'templetes'=>$templetes,"isNew"=>false,"roots"=>$roots
            ) );
    }
    /**
     * The function that do Manage Object
     *
     */
    public function actionAdmin() {
        $result=null;
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $model = new Object;
        $model->unsetAttributes(); 
        if(isset($_GET['Object'])) {
            $model->attributes=$_GET['Object'];
        }                       
        $result=$model->doSearch("article");
        $this->render( 'admin', array(
            'model'=>$model,
            'result'=>$result,
            ) );
    }
    /**
     * preview object
     */
    public function actionPreview($id){
        $object = Object::model()->findByPk($id);
        echo $object->display();
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $object = Object::model()->findByPk($id);
        $result = $object->doPublish();
        if($result !== false){
            Yii::app()->user->setFlash( 'success', Yii::t( 'cms', '发布成功!' ) );
            $this->redirect("/news/admin");
        }
    }
}
