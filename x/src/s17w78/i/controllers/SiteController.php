<?php

class SiteController extends CController {
    
    public function actionError(){
    	if (YII_DEBUG) {
        	echo Yii::app()->errorHandler->error['message'];
    	} else {
    		Yii::log(Yii::app()->errorHandler->error['message'], CLogger::LEVEL_ERROR);
    		$this->render('error');
    	}
    }
}
