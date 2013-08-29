<?php
class PayController extends CController {
    /**
     *
     *
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
     *
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array( 'allow',
                'actions' => array('return','success','checkuser','chargeRate','checkuser','success','card'),
                'users' => array( '*' )
            ),
            array( 'allow',
                'actions' => array('index','help','list','mh','pwd'),
                'users' => array( '@' )
            ),
            array( 'deny', // deny all users
                'users' => array( '*' ),
            ),
        );
    }
    public function actionIndex() {
        $uid = Yii::app()->user->id;
        $type = isset($_GET['type'])?$_GET['type']:'';
        $gid = isset($_GET['gid']) ? $_GET['gid'] : "";
        $game = "";
        if(isset($_GET['gid'])){
            // $game = App::model()->findByPk($gid);
            // if(empty($game)){
            //     throw new CHttpException(404, 'The requested page does not exist.');
            // }
            $this->redirect("/pay/mh/gid/".$_GET['gid']);
        }
        $payMent = Yii::app()->payment;
        if ( Yii::app()->request->isPostRequest ) {
            $pay_data = $_POST['Order'];
            $pay_data['uid'] = $uid;
            $pay_data['cp_id'] = isset($_POST['cp_id']) ? $_POST['cp_id']:"";
            $pay_data['app_id'] = isset($_POST['app_id']) ? $_POST['app_id'] : "";
            $pay_data['game_account'] = User::model()->getIdByAccount($_REQUEST['user_id']);
            $order = Yii::app()->payment->redirectPay( $pay_data );
            if ( $order instanceof Order ) {
                $order = Order::model()->findByPk($order->id);
                $html = Yii::app()->payment->getAutoRedirectForm( $order, "GET" );
                if ( $html instanceof Error ) {
                    Yii::log( $html, CLogger::LEVEL_ERROR, "payment" );
                    throw new CHttpException( 500, "internal error" );
                } else {
                    echo $html;
                    Yii::app()->end();
                }
            }
        }
        $games = App::model()->findAll();
        $this->render( 'direct', array('payment'=>$payMent,'game'=>$games,'type'=>$type,"app"=>$game) );
    }
    /**
     * use mh gold to charge game
     * @return [type] [description]
     */
    public function actionMh(){
        $uid = Yii::app()->user->id;
        $payMent = Yii::app()->payment;
        $gid = isset($_GET['gid']) ? $_GET['gid'] : "";
        $game = "";
        if(isset($_GET['gid'])){
            $game = App::model()->findByPk($gid);
            if(empty($game)){
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }else{
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $model = new Order('check');
        if ( Yii::app()->request->isPostRequest ) {
            $model->attributes = $_POST['Order'];
            if ($model->validate()){
                $pay_data = $_POST['Order'];
                $pay_data['uid'] = $uid;
                $pay_data['cp_id'] = $_POST['cp_id'];
                $pay_data['app_id'] = $_POST['Order']['app_id'];
                $pay_data['game_account'] = User::model()->getIdByAccount($_REQUEST['user_id']);
                $pay_data['amount'] = $_POST['Order']['order_amt'];
                $order = Yii::app()->payment->mhpay( $pay_data );
                if ( $order instanceof Order ) {
                    list($result, $return_string, $req_type) = Yii::app()->payment->deductMhGold($order);
                    if($result){
                        // echo "success";
                        return $this->render("mhsuccess",array('gid'=>$gid));
                    }else{
                        echo "fail";
                    }
                }
            }
        }
        $games = App::model()->findAll();
        $data = explode(',', $game->charge_amount);
        $this->render( 'mhpay', array('game'=>$games,'model'=>$model,'app'=>$game,'getcharge'=>$data ));
    }    
    /**
     * function_description
     *
     *
     * @return
     */
    public function actionReturn() {
        $channel_name = $_GET['channel_name'];
        $notify_params = $_GET;
        unset($notify_params['channel_name']);
        list($result, $return_string, $req_type) = Yii::app()->payment->receiveNotify($channel_name, $notify_params);
        if ($result) {
            if ($req_type == Payment::NOTIFY_TYPE_SERVER) {
                echo $return_string;
                return null;
            } else {
                $order = Order::model()->findByPk($notify_params['orderId']);
                if(Yii::app()->user->id){
                    return $this->redirect("list");
                }
                return $this->render("success",array("order"=>$order));
            }
        }else{
            echo "支付失败";
        }

    }
    /**
     * function_paylist
     *
     *
     * @return
     */
    public function actionList() {
        Yii::app()->payment;
        $uid =  Yii::app()->user->id;
        $model = new Order;
        $count = 20;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = Order::model()->getCount($uid);
        $chargeRecords = $model->getAllChargeRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/pay/list/p/",2);
        $p = $subPages->show_SubPages(2);
        $this->render("list",array('datas'=>$chargeRecords,'pages'=>$p));
    }
    /*
     *
     *  username complete
     */
    public function actionCheckuser(){
        if(isset($_GET['username'])){
            $user = User::model()->findByAttributes(array(
                    'username'=>$_GET['username']));
            if($user === null){
                echo '0';
            }else{
                echo '1';
            }
        }
    }
    /**
     * compare user password
     * @return [type] [description]
     */
    public function actionPwd(){
        $user_id = Yii::app()->user->id;
        $model= new User;
        if(isset($_GET['pwd']) && isset($_GET['username'])){
            $pwd = $model->hashPw($_GET['username'],$_GET['pwd']);
            $user = $model->findByAttributes(array(
                    'id'=>$user_id,"password"=>$pwd));
            if($user === null){
                echo '0';
            }else{
                echo '1';
            }
        }
    }
    /**
     * [actionSuccess description]
     * @return [type] [description]
     */
    public function actionSuccess(){
        return $this->render("success");
    }
    /**
     * pay help
     * @return [type] [description]
     */
     public function actionHelp(){
        $this->render("help");
    }
    /**
     * get game charge rate
     * @return [type] [description]
     */
    public function actionChargeRate(){
        $app_id = intval($_POST['app_id']);
        list($charge_chn, $err) = Yii::app()->payment->getChargeChannelForApp($app_id);
        if (!is_null($err)) {
            return [false, $err];
        }
        $rate = $charge_chn->getChargeRate($app_id);
        echo json_encode($rate);
    }

    public function actionCard(){
        $uid = Yii::app()->user->id;
        if (Yii::app()->request->isPostRequest) {
            $pay_data = $_POST['Order'];
            $pay_data['uid'] = $uid;
            $pay_data['cp_id'] = "";
            $pay_data['app_id'] = "";
            $pay_data['game_account'] = $uid;
            $order = Yii::app()->payment->directPay( $pay_data );
            if ( $order instanceof Order ) {
                $order = Order::model()->findByPk($order->id);
                $this->render('cardret');
            } else {
                $this->render('fail');
            }
        }
    }
}