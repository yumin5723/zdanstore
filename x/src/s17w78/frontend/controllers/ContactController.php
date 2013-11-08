<?php

class ContactController extends GController {

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
                    'index',
                    'list',
                    'view',
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
     * action for default home page
     * @return [type] [description]
     */
    public function actionIndex(){
        $model = new Message("addmsg");
        $email = '';
        $uid = Yii::app()->user->id;
        if(!empty($uid)){
            $email = User::model()->findByPk($uid);
        }
        if (isset($_POST['Message'])) {
            $model->setAttributes($_POST['Message']);
            if ($model->validate()) {
                if ($model->save(false)) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new User Successfully!'));
                    return $this->render("index",array("model"=>$model,"uid"=>$uid,"data"=>$email));
                }
            }else{
                Yii::app()->user->setFlash('failed', Yii::t('cms', 'Create new User failed!'));
            }
        }
        $this->render("index",array("model"=>$model,"uid"=>$uid,"data"=>$email));
    }
}
