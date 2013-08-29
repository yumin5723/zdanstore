<?php

/**
 * 
 */
class TodesktopController extends CController
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
        $hostInfo = Yii::app()->request->hostInfo;
        $url = Yii::app()->request->urlReferrer;
        $str = "
            [DEFAULT]
            BASEURL={$hostInfo}
            [InternetShortcut]
            URL= {$url} 
            Modified=B07A55D9386FCA01CA
            IconFile=http://www.1378.com/favicon.ico
            IconIndex=1
        ";
        // Header("Content-type: application/octet-stream");
        // header("Content-Disposition: attachment; filename=1378页游.url;");
        // echo $str;

        return Yii::app()->request->sendFile("1378页游.url",$str,"application/octet-stream");
    }
}