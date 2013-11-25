<?php
/**
 * Subject Brand Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class UserController extends GController {
    public $sidebars = array(
        array(
            'name' => '用户管理',
            'icon' => 'tasks',
            'url' => 'admin',
        ),
    );
    /**
     * @return array action filters
     */
    public function filters() {
        
        return array(
            // 'accessControl', // perform access control for CRUD operations
            
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
                    'admin',
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'allow', // all all users
                'actions' => array(
                    'error'
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
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $model = new User('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["User"]))
                    $model->attributes=$_GET["User"];  
        $this->render('admin', array(
            "model" => $model
        ));
    }
}
