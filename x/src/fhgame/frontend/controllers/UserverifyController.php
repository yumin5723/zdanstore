<?php
class UserverifyController extends CController {
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
                    'index',
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    
    public function actionIndex(){
        $model = new Userverify;
        if (isset($_POST['Userverify'])) {
            $model->attributes = $_POST['Userverify'];
            if ($model->validate()) {
                $result = $model->getResult();
                // print_r($result);exit;
                if ($result ===  true) {
                    $this->redirect('/userverify/success/type/1');
                } 
            } 
        } 
        $this->render("user_shimingrenzheng",array('model'=>$model));
    }

    public function actionSuccess(){
        $type = $_GET['type'];
        $this->render('succ',array('type'=>$type));
    }
}
