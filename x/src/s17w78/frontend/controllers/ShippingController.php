<?php
/**
 * Default Controller.
 *
 * @version 1.0
 *
 */

class  ShippingController extends GController {
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
                    'insurance','shippingtime','returns','options'
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
    public function actionInsurance(){
        $this->render("insurance");
    }
    public function actionShippingtime(){
        $this->render("shippingtime");
    }
    public function actionReturns(){
        $this->render("returns");
    }
    public function actionOptions(){
        $this->render("options");
    }

}
