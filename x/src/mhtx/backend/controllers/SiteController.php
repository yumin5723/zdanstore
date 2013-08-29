<?php

class SiteController extends BackendController {

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
              'actions' => array('index', 'logout'),
              'users' => array('@'),
          ),
          array('allow',
              'actions' => array('login'),
              'users' => array('?'),
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
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" .
                    "Reply-To: {$model->email}\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    "Content-type: text/plain; charset=UTF-8";

              mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
              Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
              $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
      $model = new AdminLoginForm;

      // if it is ajax validation request
      /*
       * if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
       * {
       *     echo CActiveForm::validate($model);
       *     Yii::app()->end();
       * }
       */

      // collect user input data
      if (isset($_POST['AdminLoginForm'])) {
          $model->attributes = $_POST['AdminLoginForm'];
          // validate user input and redirect to the previous page if valid
          if ($model->validate() && $model->login())
              $this->redirect(Yii::app()->user->returnUrl);
      }
      // display the login form
      $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}