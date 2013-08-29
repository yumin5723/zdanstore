<?php
class AppealController extends CController {
/**
     * @return array action filters
     */
    public function filters() {
        return array('accessControl');
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
                    'create',
                    'captcha'
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    /**
     * The function that do Create new Appeal
     *
     */
    public function actionCreate() { 
        $model = new Appeal;
        if(isset($_POST['Appeal'])){
            $valid = $this->createAction('captcha')->getVerifyCode()  ;
            if($valid==$_POST['Appeal']['appeal_verify']){
                $model->setAttributes($_POST['Appeal']);
                if ($model->save()) {
                    $this->redirect("/appeal/success/type/1");
                }else{ 
                    $this->redirect("/appeal/success/type/2");
                }
            }else{
                $this->redirect("/appeal/success/type/3");
            }
        }
       $this->render('index',array("model" => $model));
    }
    public function actionSuccess(){
        $type = $_GET['type'];
        $this->render('success',array("type"=>$type));
    }
/**
     * Confirmation Code
     *
     */
    public function actions()
    { 
            return array( 
                    // captcha action renders the CAPTCHA image displayed on the contact page
                    'captcha'=>array(
                            'class'=>'CCaptchaAction',
                            'backColor'=>0xFFFFFF, 
                            'maxLength'=>'4',       // 最多生成几个字符
                             'minLength'=>'2',       // 最少生成几个字符
                             // 'fixedVerifyCode' => substr(md5(time()),0,4), 
                             'testLimit'=>1,
                           'height'=>'40'
                    ), 
            ); 
    }

}
?>