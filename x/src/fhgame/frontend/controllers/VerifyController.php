<?php
class VerifyController extends CController {
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
     * The function that do Create new Verify
     *
     */
    public function actionCreate() {
    	$model = new Verify;

        if(isset($_POST['Verify'])){
            $message = $model->user_login($_POST['Verify']['user_name'],$_POST['Verify']['user_pwd']);
        //echo $message;exit;
            if($message[2] == 'error'){
                echo 'error';
            }else{
                die("<script language='javascript'>alert('提交成功！');window.location.href='http://game.fhgame.com/';</script>");
            }
            $model->setAttributes($_POST['Verify']);
            if ($model->save()) {
                 echo "添加成功"; 
                 //$result = true;
            }else{ 
                echo "添加失败"; 
               // $result = false;
                //$this->redirect(Yii::app()->user->returnUrl);
            } 
       
        }
       $this->render('index',array("model" => $model));
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