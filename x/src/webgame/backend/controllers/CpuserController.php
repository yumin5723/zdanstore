<?php
class CpuserController extends WController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('create','view','list','update','appview','createapp', 'appadmin'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public $sidebars = array(
        array(
            'name' => '管理',
            'icon' => 'tasks',
            'url' => 'list',
        ),
        array(
            'name' => '创建',
            'icon' => 'tasks',
            'url' => 'create',
        ),
    );
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
                'cpuser'=>$this->loadModel($id),
        ));
    }
    /**
     * Create a cp user
     */
    public function actionCreate(){
        $model=new CPUser($scenario='addcp');

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['CPUser']))
        {
              $model->setAttributes($_POST['CPUser']);
              if($model->validate()){
                  if($model->save())
                      Yii::app()->user->setFlash("success",
                          sprintf("成功添加新的合作伙伴<strong> %s </strong>.", $model->cpname));
                      $this->redirect(array('list'));
                  }
        }

        $this->render('addcp',array(
                'model'=>$model,
            ));
    }
    /**
     * cp user list
     */
    public function actionList(){
        $model = new CPUser('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["CPUser"]))
                    $model->attributes=$_GET["CPUser"];  
        $this->render('list', array(
            "model" => $model
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['CPUser']))
        {
            if($model->updateAttrs($_POST['CPUser']))
                $this->redirect(array('/cpuser/list'));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
     * app list
     */
    public function actionAppview($id){
        $this->render('appview',array(
            'model'=>$this->loadAppModel($id),
        ));
    }

    /**
     * create app
     */
    public function actionCreateapp(){
        $cpuser_id = intval($_GET['id']);
        $cpuser = CPUser::model()->findByPk($cpuser_id);

        $model=new App;
        $model->cp_id = $cpuser_id;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['App']))
        {
              $model->setAttributes($_POST['App']);
              if($model->validate()){
                  if($model->save())
                      Yii::app()->user->setFlash("success",
                          sprintf("成功添加新的应用<strong> %s </strong>.", $model->app_name));
                      $this->redirect(array('appview','id'=>$model->id));
                  }
              }
        $this->render('createapp',array(
                'model'=>$model,'cpuser'=>$cpuser,
            ));
    }
    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdateapp($id)
    {
        $model=App::model()->findByPk($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['App']))
        {
            if($model->updateAttrs($_POST['App']))
                $this->redirect(array('/cpuser/app','id'=>$model->cp_id));
        }

        $this->render('updateapp',array(
            'model'=>$model,
        ));
    }
    /**
     * delete app
     */
    public function actionDeleteapp(){
        App::model()->deleteByPk($_REQUEST['id']);
        $this->redirect(array('/cpuser/app','id'=>$_REQUEST['cp']));
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function actionAppadmin() {
        $model=new App('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['App']))
            $model->attributes=$_GET['App'];

        $this->render('appadmin',array(
                'model'=>$model,
            ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=CPUser::model()->findByPk((int)$id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    public function loadAppModel($id)
    {
        $model=App::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='manager-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}