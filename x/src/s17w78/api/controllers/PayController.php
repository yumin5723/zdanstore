<?php

class PayController extends Controller {

    /**
     * function_description
     *
     *
     * @return
     */
    public function actionNotify() {
        $channel_name = $_REQUEST['channel_name'];
        $notify_params = $_REQUEST;
        unset($notify_params['channel_name']);
        list($result, $return_string, $req_type) = Yii::app()->payment->receiveNotify($channel_name, $notify_params);
        echo $return_string;
    }
}