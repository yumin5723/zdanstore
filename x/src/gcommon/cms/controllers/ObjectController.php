<?php
/**
 * Backend Object Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class ObjectController extends CmsController {
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
                'label' => Yii::t( 'cms', '文章管理' ) ,
                'url' => array(
                    'admin'
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
            array(
                'label' => Yii::t( 'cms', '创建文章' ) ,
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
        $content_status = $model->getContentStatus();
        $content_resources = $model->getResource();
        $templetes = $model->getTempletes();
        // $terms = Taxonomy::model()->getTerms($type);
        $roots=Oterm::model()->roots()->findAll();
        $selected_terms = array();
        if ( isset( $_POST["Object"] ) ) {
            $model->attributes=$_POST["Object"];
            $model->object_type = $type;
            if($model->validate()){
                //Convert the date time publish to timestamp
                $model->object_date = $_POST["Object"]["object_date"];
                $model->object_modified = $model->object_date;
                $model->object_date_gmt=date("Y-m-d H:i:s");
                $model->object_modified_gmt = $model->object_date_gmt;
                // Get the Terms that the User Choose
                //
                $post_terms=isset( $_POST['terms'] ) ? $_POST['terms'] : array();
                //get count resource
                $resource_result = $model->getCountResource($content_resources); 
                
                $model->total_number_resource = $resource_result['count'];     
                //object status can not be published when create object
                $model->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
                if ( $model->save() ) {
                    $obj_tem = new ObjectTemplete;
                    $obj_tem->object_id = $model->attributes['object_id'];
                    $obj_tem->templete_id = isset($_POST['Templete']['id']) ? $_POST['Templete']['id'] : "0";
                    $obj_tem->save();

                    ObjectTerm::model()->saveObjectTerm($model->object_id,$_POST['Oterm']);

                    //save resource
                    ObjectResource::model()->saveResourceForObject($content_resources,$resource_result['resource_upload'],$model->object_id);
                    
                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'Create new Content Successfully!' ) );
                    $this->redirect("/cms/object/admin/type/0");
                } else {
                    $model->object_date=date( 'Y-m-d H:i:s', $model->object_date );
                }
            }
        }
        $this->render( 'create', array( "model"=>$model, "content_status"=>$content_status, "type"=>$type, "content_resources"=>$content_resources ,
             'templetes'=>$templetes,"isNew"=>true,"templete"=>"",'roots'=>$roots
            ) );
    }

    
    /**
     * The function that do Update Object
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        $this->menu = array_merge( $this->menu, array(
                array(
                    'label' => Yii::t( 'cms', 'Update this content' ) ,
                    'url' => array(
                        'update',
                        'id' => $id
                    ) ,
                    'linkOptions' => array(
                        'class' => 'button'
                    )
                ) ,
            ) );
        $model=  GxcHelpers::loadDetailModel('Object', $id);
        if(isset($_GET['history_id'])){
            $history = ObjectBak::model()->findByPk($_GET['history_id']);
            $model->object_content = $history->object_content;
        }
        $type = $model->object_type;
        $content_resources = Object::model()->getResource();
        $guid = $model->guid;
        $model->isNewRecord = false;
        $content_status = $model->getContentStatus();

        $templetes = $model->getTempletes();
        $templete = ObjectTemplete::model()->findByAttributes(array('object_id'=>$id));
        $roots=Oterm::model()->roots()->findAll();
        if ( isset( $_POST["Object"] ) ) {
            //backup object
            if($_POST['Object']['object_content'] != $model->object_content){
                ObjectBak::model()->backupContent($model);
            }

            $model->attributes=$_POST["Object"];
            $model->object_type = $type;
            if($model->validate()){
                

                //Convert the date time publish to timestamp
                $model->object_date = $_POST["Object"]['object_date'];
                // Get the Terms that the User Choose

                //handle resource
                $resource_result = $model->getCountResource($content_resources); 
                $model->total_number_resource = $resource_result['count']; 

                //after update content,the status will be changed as draft
                //before update object save nearest published term cache
                if($model->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED){
                    $model->updateObjectTermCache();
                }
                //object status can not be published when create object
                $model->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
                if ( $model->save() ) {
                    if(!empty($templete)){
                        $templete->templete_id = isset($_POST['Templete']['id']) ? $_POST['Templete']['id'] : "0";
                        $templete->save(false);
                    }
                    Yii::app()->user->setFlash( 'success', Yii::t( 'cms', 'modify Content Successfully!' ) );
                    // if($model->updateObjectTermCache()){
                        ObjectTerm::model()->updateObjectTerm($model->object_id,$_POST['Oterm']);
                    // }
                    //save resource
                    ObjectResource::model()->deleteAll('object_id = :id',array(':id'=>$model->object_id));
                    ObjectResource::model()->saveResourceForObject($content_resources,$resource_result['resource_upload'],$model->object_id);
                    $this->redirect("/cms/object/admin/type/0");
                } else {
                    $model->object_date=date( 'Y-m-d H:i:s', $model->object_date );
                }
            }
        }
        $this->render( 'create',array("model"=>$model,"content_status"=>$content_status,"type"=>$type,'content_resources'=>$content_resources,
            'templete'=>$templete,'templetes'=>$templetes,"isNew"=>false,"roots"=>$roots
            ) );
    }
    /**
     * The function that do View User
     *
     */
    public function actionView() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        $this->menu = array_merge( $this->menu, array(
                array(
                    'label' => Yii::t( 'cms', 'Update this content' ) ,
                    'url' => array(
                        'update',
                        'id' => $id
                    ) ,
                    'linkOptions' => array(
                        'class' => 'button'
                    )
                ) ,
                array(
                    'label' => Yii::t( 'cms', 'View this content' ) ,
                    'url' => array(
                        'view',
                        'id' => $id
                    ) ,
                    'linkOptions' => array(
                        'class' => 'button'
                    )
                )
            ) );
        $this->render( 'object_view' );
    }
    /**
     * The function that do Manage Object
     *
     */
    public function actionAdmin() {
        $result=null;
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        switch ($type){                    
            case ConstantDefine::OBJECT_STATUS_DRAFT :
                $model=new Object('draft');                                                          
                break;
            case ConstantDefine::OBJECT_STATUS_PUBLISHED :
                $model=new Object('published');                      
                break;
            default :
                $model=new Object('search');                      
                break;
        }
        $result=$model->doSearch($type);
        
        $model->unsetAttributes(); 
        if(isset($_GET['Object'])) {
            $model->attributes=$_GET['Object'];
        }                       
        $result=$model->doSearch($type);
        $this->render( 'admin', array(
            'model'=>$model,
            'result'=>$result,
            ) );
    }
    /**
     * The function that list object update history
     *
     */
    public function actionHistory() {
        $model = new ObjectBak('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["ObjectBak"]))
                    $model->attributes=$_GET["ObjectBak"];  
        $this->render('history', array(
            "model" => $model
        ));
    }
     /**
     * The function that do View content bak
     *
     */
    public function actionBakview() {
        $model_name = "ObjectBak";
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $model = GxcHelpers::loadDetailModel($model_name, $id);
        $this->menu = array(
            array(
                'label' => Yii::t( 'cms', '重新应用' ) ,
                'url' => array(
                    Yii::app()->createUrl("/cms/object/update", array("id"=>$model->object_id,"history_id"=>$id)),
                ) ,
                'linkOptions' => array(
                    'class' => 'button'
                )
            ) ,
            array(
            ) ,
        );
        
        $this->render('bakview', array(
            "model" => $model
        ));
    }
    /**
     * This function change content status as pending
     *
     */
    public function actionChange($id){
        $content = Object::model()->findByPk($id);
        if(!empty($content)){
            $content->object_status = ConstantDefine::OBJECT_STATUS_PENDING;
            $content->save();
            $this->redirect('/object/admin',array("type"=>0));
        }
    }
    /**
     * This function sugget Person that the current user can send content to
     *
     */
    public function actionSuggestPeople() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'suggest_people'
            ) );
    }
    /**
     * This function sugget Person that the current user can send content to
     *
     */
    public function actionCheckTransferRights() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'check_transfer_rights'
            ) );
    }
    /**
     * This function sugget Tags for Object
     *
     */
    public function actionSuggestTags() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'suggest_tags'
            ) );
    }
    /**
     * The function is to Delete a Content
     *
     */
    public function actionDelete( $id ) {
       $model = new Object;
        $result = $model->updateByPk((int)$id,array('object_status'=>ConstantDefine::OBJECT_STATUS_DELETE));
        if($result !== false){
            $object = $model->findByPk($id);
            $updateTemplate = $object->doDelete();
            $this->redirect("/cms/object/admin/type/0");
        }
    }
    /**
     * The function is to Delete a Content backup
     *
     */
    public function actionBakdelete( $id ) {
        GxcHelpers::deleteModel( 'ObjectBak', $id );
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
            $this->redirect("/cms/object/admin/type/0");
        }
    }
    /**
     * the action is get object by category id
     */
    public function actionFind($id){
        $model = new Object;
        $model->unsetAttributes(); 
        $result = ObjectTerm::model()->fetchObjectsByTermid($id);
        $term = Term::model()->findByPk($id);
        $this->render( 'find', array(
            'model'=>$model,
            'result'=>$result,
            'term'=>$term,
            ) );
    }
     
}
