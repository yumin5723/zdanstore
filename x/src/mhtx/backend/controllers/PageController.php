<?php

class PageController extends BackendController {

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
          array('allow', // allow authenticated user to perform 'create' and 'update' actions
              'actions' => array('create', 'list','delete','push'),
              'users' => array('@'),
          ),
          array('allow', // all all users
              'actions' => array('error'),
              'users' => array('*'),
          ),
          array('deny', // deny all users
              'users' => array('*'),
          ),
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
          if (Yii::app()->request->isAjaxRequest)
              echo $error['message'];
          else
              $this->render('error', $error);
        }
    }

    /**
     * Displays the page list
     */
    public function actionList() {
        $model = new Page;
        $criteria = new CDbCriteria();
        $criteria->order = "id DESC";
        $dataProvider = new CActiveDataProvider('Page',
                      array(
                          'criteria' => $criteria,
                      )
        );
        $this->render('list',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Displays the create page
     */
    public function actionCreate() {
      $model = new Page;
      if(Yii::app()->request->isPostRequest){
          $model->setAttributes($_POST['Page']);
          if($model->validate()){
              //save rar file
              $result = $model->saveRarFile($_FILES);
              if($result != false){
                  $model->file = $result;
                  $model->admin_id = Yii::app()->user->id;
                  $model->save();
                  Yii::app()->user->setFlash('success','新建成功！');
              }
          }
      }
      $this->render('create', array('model' => $model));
    }
   /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $model = Page::model()->findByPk($id);
            $model->deleteByPk($id);

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
    /**
     * push rar file
     */
    public function actionPush($id){
        $model = $this->loadModel($id);
        $model->status = Page::STATUS_NEED_PUBLISH;
        $model->save(false);
        Yii::app()->user->setFlash("success",
                      "queue is success please wait");
        $this->redirect("/page/list");
        // $publisher = new Publisher;
        // $file = Yii::app()->params['base_file_path'].$model->file;
        // $ret = $publisher->publishEntirePage($file,$model->path);
        // if(!empty($ret)){
        //     Yii::app()->user->setFlash("success",
        //               sprintf("发布成功<strong> %s </strong>.", $model->path));
        //     $this->redirect("/page/list");
        // }
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Page::model()->findByPk((int)$id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
}