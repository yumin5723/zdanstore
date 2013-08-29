<?php
class RetakepwdController extends CController {
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
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    
    public function actionIndex(){
        $model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->attributes = $_POST['Retakepwd'];

            if ($model->validate()) {
                $verify = new Retakepwd;
                $userQues = $verify->getUserQues($model->username);
                $this->render('ver_getpwd_answer',array('model'=>$model,'username'=>$model->username,'userQues'=>$userQues,'id_num'=>$model->id_num));
                return ;
            } 
        } 
        $this->render("user_getpwd",array('model'=>$model));
    }


    public function actionVerify(){
        $model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->setScenario('verifyanswer');
            $model->attributes = $_POST['Retakepwd'];
            if ($model->validate()) {
                $this->render('user_modifypassword',array('model'=>$model,'username'=>$model->username,'answer'=>$model->answer,'id_num'=>$model->id_num));
                return;
            }
            $verify = new Retakepwd;
            $userQues = $verify->getUserQues($model->username);
            $this->render('ver_getpwd_answer',array('model'=>$model,'username'=>$model->username,'userQues'=>$userQues,'id_num'=>$model->id_num));

        }else{
            $this->redirect('index');
        }
        
    }

    public function actionModifyPwd(){
        $model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->setScenario('check_password');
            $model->answer = $_POST['Retakepwd']['answer'];
            $model->attributes = $_POST['Retakepwd'];
            if ($model->validate()) {
                // echo "OK";
                $this->redirect('/Retakepwd/success');
            } else {
                $this->render('user_modifypassword',array('model'=>$model,'username'=>$model->username,'answer'=>$model->answer,'id_num'=>$model->id_num));
            }

        } else {
            $this->redirect('index');
        }
    }

    public function actionSuccess(){
        $this->render('succ');
    }    
}
