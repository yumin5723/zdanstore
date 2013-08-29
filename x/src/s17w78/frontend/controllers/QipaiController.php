<?php

/**
 *
 */
class QipaiController extends CController
{
    /**
     * @return array action filters
     */
    public function filters() {
        $filters = array(
            'accessControl', // perform access control for CRUD operations
        );
        // if (isset(Yii::app()->params['needAlphaCode']) && Yii::app()->params['needAlphaCode']) {
        //     $filters[] = array('application.filters.AlphaCodeFilter + register');
        // }
        return $filters;
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('index'),
                'users' => array('*')
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionIndex()
    {
        $id = intval($_GET['id']);
        if(Yii::app()->user->id){
            return $this->redirect("/webgame/play/{$id}",true,301);
        }
        $user = new User;
        if($user->createVisitorAccount()){
            Yii::app()->eventlog->send('syscreateaccount',Yii::app()->request->userHostAddress,
                                Yii::app()->request->getUrl(),
                                $user->id,
                                Yii::app()->request->urlReferrer
                        );
            return $this->redirect("/webgame/play/{$id}",true,301);
        }
    }
}