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
                'actions' => array( 'index','return','success' ),
                'users' => array( '*' )
            ),
            array( 'deny', // deny all users
                'users' => array( '*' ),
            ),
        );
    }
    public function actionIndex() {
        // $order = new Order;
        $payMent = Yii::app()->payment;
        if ( Yii::app()->request->isPostRequest ) {
            $pay_data = $_POST['Order'];
            $pay_data['cp_id'] = $_POST['cp_id'];
            $pay_data['app_id'] = $_POST['app_id'];
            $pay_data['game_account'] = $_REQUEST['user_id'];
            $order = Yii::app()->payment->redirectPay( $pay_data );
            if ( $order instanceof Order ) {
                $html = Yii::app()->payment->getAutoRedirectForm( $order, "GET" );
                if ( $html instanceof Error ) {
                    Yii::log( $html, CLogger::LEVEL_ERROR, "payment" );
                    throw new CHttpException( 500, "internal error" );
                } else {
                    echo $html;
                    exit( 0 );
                }
            }
        }
        $this->render( 'direct', array('payment'=>$payMent,
            ) );
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
                return $this->render("success");
            }
        }

    }
    /*
     *
     *  order complete
     */
    public function actionSuccess(){
        return $this->render("success");
    }
}
