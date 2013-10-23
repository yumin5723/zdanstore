<?php
/**
 * BrandController Controller.
 *
 * @version 1.0
 *
 */

class BrandController extends GController {
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
                    'index'
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
     * action for brand index
     * @return [type] [description]
     */
    public function actionIndex(){
        $brand = Brand::model()->findByPk($_GET['id']);
        if(empty($brand)){
            throw new Exception("this page is not found", 404);
        }
        $terms = BrandTerm::model()->getBrandTerms($_GET['id']);
        $this->render("index",array("terms"=>$terms));
    }
}
