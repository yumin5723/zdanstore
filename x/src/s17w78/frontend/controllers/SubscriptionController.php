<?php
class SubscriptionController extends GController {

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
                'actions'=>array('do','addwish'),//'account','setting','message','order','address',
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('info','success'),
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('logout','account','index','setting','wishlist','message','order','address','bind','check','done','mygoods','resend','mypoints','ordershow','deletewish','trackorder'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
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
    /**
     * [actionAddwish description]
     * @return [type] [description]
     */
    public function actionAddwish(){
        $id = $_POST['id'];
        $flag = 0;
        if(Yii::app()->user->isGuest){
            $flag = 0;
        }else{
            $wish = Wishlist::model()->findByAttributes(array('product_id'=>$id,'uid'=>Yii::app()->user->id));
            if(empty($wish)){
                $model = new Wishlist;
                if($model->saveWish(Yii::app()->user->id,$id)){
                    $flag = 2;
                }
            }else{
                $flag = 1;
            }
        }
        echo json_encode($flag);
    }
}