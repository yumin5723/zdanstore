<?php

class UserController extends ApiController {

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
          array('allow', // allow authenticated user to perform 'create' and 'update' actions
              'actions' => array('gate','charge','info'),
              'users' => array('*'),
          ),
          
          array('allow', // all all users
              'actions' => array('error'),
              'users' => array('*'),
          ),
          array('deny', // deny all users
              'users' => array('*'),
          ),
      );
    }
    /**
     * game login 
     * @return [type] [description]
     */
    public function actionGate(){
        header('P3P:CP="IDC DSP COR ADM DEVi TATi PSA PSD IVAi IVDi CONi NIS OUR IND CNT"');
        $request_params = $_GET;
        list($result, $return_string) = Yii::app()->webgame->checkUserLogin($request_params);
        if($result){
            return $this->redirect($return_string);
        }else{
            echo $return_string;
        }
    }
    /**
     * game charge
     * @return [type] [description]
     */
    public function actionCharge(){

    }
    /**
     * get user account and password for gameclient
     */
    public function actionInfo(){
        $id = Yii::app()->user->id;
        if(empty($id)){
            echo "not login user now";
        }else{
            $user = User::model()->findByPk($id);
            $data = array("username"=>$user->username,"password"=>$user->pass_str);
            echo json_encode($data);
        }
    }
}
