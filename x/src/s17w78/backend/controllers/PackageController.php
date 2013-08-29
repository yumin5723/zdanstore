<?php
Yii::import("gcommon.cms.components.ConstantDefine");
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * Backend Game Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class PackageController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '礼包管理',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
        array(
            'name' => '创建礼包',
            'icon' => 'tasks',
            'url' => 'create',
        ),
        array(
            'name' => '热门礼包',
            'icon' => 'tasks',
            'url' => 'hotblock',
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
                    'batch',
                    'update',
                    'delete',
                    'add',
                    'updatestatus',
                    'admincode',
                    'updatepackagestatus',
                    'limit',
                    'hotblock',
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
        $model = Package::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Update Settings
     *
     */
    public function actionUpdateSettings() {
        $this->menu = array();
        $this->render('user_update_settings');
    }
    /**
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $model = new Package;
        $model->publish_date = date('Y-m-d H:i:s');
        if (isset($_POST['Package'])) {
            $model->setAttributes($_POST['Package']);
            if ($model->validate()) {
                $model->created_uid = Yii::app()->user->id;
                $model->modified_uid = $model->created_uid;
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Game Successfully!'));
                    $this->redirect("admin");
                }
            }
        }
        $this->render('create', array(
            "model" => $model,'isNew'=>true,
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new Package('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Package"]))
                    $model->attributes=$_GET["Game"];  
        $this->render('admin', array(
            "model" => $model
        ));
    }
    /**
     * The function that do Update User
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        if ($id !== 0) {
            $model = $this->loadModel($id);
            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'userupdate-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            // collect user input data
            if (isset($_POST['Package'])) {
                $model->modified_uid = Yii::app()->user->id;
                if ($model->updateAttrs($_POST['Package'])) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('update', array(
            "model" => $model,"isNew"=>false,
        ));
    }

    public function actionBatch(){
        $id = $_GET['id'];
        $model = $this->loadModel($id);
        $batches = CodeBatch::model()->findAllByAttributes(array('package_id'=>$id));
        $results = new CActiveDataProvider("CodeBatch", array(
            'data'=>$batches,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
        $this->render('batch',array('results'=>$results,'model'=>$model));
    }

    public function actionAdd(){
        $package_id = $_GET['id'];
        if (isset($_FILES['csv_up'])) {
                $fileName=$_FILES['csv_up']['tmp_name'];
                if(!empty($fileName)){
                    if (!($batch = CodeBatch::model()->saveCodeBatch($package_id,Yii::app()->user->name,$fileName))) {
                        throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
                    }
                    
                    $this->redirect("/package/batch/id/{$package_id}");

                }
        }
        $this->render('activecode');
    }

    public function actionUpdatepackagestatus(){
        $package_id = $_GET['package_id'];
        $package = Package::model()->findByPk($package_id);
        $package->updatestatus();
        $this->redirect("/package/admin");
    }

    public function actionUpdatestatus(){
        $batch_id = $_GET['batch_id'];
        $batch = CodeBatch::model()->findByPk($batch_id);
        $batch->updatestatus();
        $this->redirect("/package/batch/id/{$batch->package_id}");
    }

    public function actionAdmincode(){
        $batch_number = $_GET['batch_number'];
        $package = Package::model()->getPackageName($batch_number);
        $criteria = new CDbCriteria;
        $criteria->condition = "batch_number = :batch_number";
        $criteria->params = array(":batch_number"=>$batch_number);
        $results = new CActiveDataProvider("ActiveCode", array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
        $this->render('code',array('result'=>$results,'package'=>$package));
    }

    public function actionLimit(){
        $package_id = $_GET['id'];
        $model = PackageRules::model()->findByPk($package_id);
        if (empty($model)) {
            $model = new PackageRules;
        }
        if (isset($_POST["PackageRules"])) {
            $model->package_id = $package_id;
            $model->setAttributes($_POST['PackageRules']) ;
            if ($model->validate()) {
                if ($model->save()) {
                    $this->redirect('/package/admin');
                }
            }
        }
        $this->render('codelimit',array('model'=>$model));
    }

    public function actionHotblock(){
        if (isset($_POST['PackageRecommend'])) {
            foreach ($_POST['PackageRecommend'] as $key => $value) {
                $recommend = PackageRecommend::model()->findByPk($key);
                $recommend->value = $value;
                $recommend->save();
            }
            Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
        }
        $model = PackageRecommend::model()->findAll();
        $this->render('recommend', array(
            'model' => $model
        ));
    }

    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('Game', $id);
    }
}
