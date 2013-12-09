<?php
class SubscriptionController.php extends GController {

    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {
        return array("accessControl");
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('do'),//'account','setting','message','order','address',
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
            'users'=>array('*'),
            ),
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
        );
    }
    public function actionDo(){
        $flag = 1;
        if(isset($_POST['email']) && $_POST['email'] != ""){
            $email = $_POST['email'];
            $data = Subscription::model()->findByAttributes(array("email"=>$email));
            if(empty($data)){
                $model = new Subscription;
                $model->email = $email;
                $model->save(false);

                $flag = 1; 
            }else{
                $flag = 0;
            }            
        }else{
            $flag = 2;
        }
        echo json_encode($flag);
    }

}