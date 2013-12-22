<?php
/**
 * Default Controller.
 *
 * @version 1.0
 *
 */

class  HelpController extends GController {
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
                'allow', // all all users
                'actions' => array(
                    'about','faq','pravitypolicy','checkorder'
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
    public function actionAbout(){
        $this->render("about");
    }
    public function actionFaq(){
        $this->render("faq");
    }
    public function actionPravitypolicy(){
        $this->render("pravitypolicy");
    }
    public function actionCheckorder(){
        if(Yii::app()->request->isPostRequest){
            if(Yii::app()->user->isGuest){
                $brands = Brand::model()->getBrandsForIndex(100);
                $this->render('checkorder',array('brands'=>$brands));
            }else{
                $this->redirect("/user/trackorder");
            }
        }
    }

}
