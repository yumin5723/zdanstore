<?php
class QuestionController extends CController {
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
     * The function that do Create new Question
     *
     */
    public function actionCreate() {
        $model = new Question;
        $count = $model->count;
        $sub_pages = $model->sub_pages;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $model->getAllcount();      
        if(isset($_POST['Question'])){
            $model->attributes = $_POST['Question'];
            if ($model->validate()) {
                $valid = $this->createAction('captcha')->getVerifyCode()  ;
                if($valid==$_POST['Question']['appeal_verify']){
                    $model->setAttributes($_POST['Question']);
                    if ($model->save()) {
                        Yii::app()->user->setFlash( 'success', Yii::t( 'cms', '在线提问成功！' ) );
                    }else{
                        Yii::app()->user->setFlash( 'warning', Yii::t( 'cms', '在线提问失败，请重新添加信息！' ) );
                    }
                }else{  Yii::app()->user->setFlash( 'warning', Yii::t( 'cms', '验证码不正确！' ) ); }
            }
        }
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/question/create/p/",2);
        $datas = $model->getAllques($count,$pageCurrent);
        $query_type = !empty($_GET['query_type']) ? $_GET['query_type'] : "";
        $content = !empty($_GET['content']) ? $_GET['content'] : "";
        if(!empty($_GET["content"])){
            $array = $model->typequestion($query_type,$_GET["content"],$count,$pageCurrent);
            $datas =$array[0];
            $nums =$array[1];
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/question/create?query_type=$query_type&content=$content&p=",2);
        }
        $p = $subPages->show_SubPages(2);
        $this->render('create',array("model" => $model,"datas"=>$datas,'type'=>$query_type,'content'=>$content,'pager'=>$p));
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