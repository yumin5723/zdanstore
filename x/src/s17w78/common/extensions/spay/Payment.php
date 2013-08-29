<?php
class PaymentException extends CException {}
class Payment extends CApplicationComponent {

    const REQUEST_METHOD_GET = "get";

    const REQUEST_METHOD_POST = "post";

    const NOTIFY_TYPE_UNKNOW = 0;
    const NOTIFY_TYPE_BROWSER = 1;
    const NOTIFY_TYPE_SERVER  = 2;

    /**
     * for direct pay request.
     */
    const COMMUNICATE_TYPE_DIRECT_PAY_REQ = 1;

    /**
     * for direct pay return
     */
    const COMMUNICATE_TYPE_DIRECT_PAY_RETURN = 2;

    /**
     * for basic charge callback
     */
    const COMMUNICATE_TYPE_BASIC_CHARGE = 3;

    /**
     * hmac error
     */
    const ERROR_HMAC = -1;

    /**
     * incomplete data
     */
    const ERROR_INCOMPLETE = 11;

    /**
     * pay amount error
     */
    const ERROR_AMOUNT = 22;

    public $config_file = null;

    public $config_path = null;

    /**
     * for build notify url
     *
     */
    public $notifyUrlPrefix = "";

    /**
     * for build return Url
     *
     */
    public $returnUrlPrefix = "";

    /**
     *
     */
    public $db = "db";

    /**
     *  hash algo for hamc
     */
    public $hashAlgo = "sha256";

    /**
     * hmac key in request(response)
     */
    public $hmacKey = "hmac";

    protected $_configs = array();

    protected $_class_path_prefix = "spay.channels.";

    protected $_charge_class_path_prefix = "spay.chargeChn.";
    /**
     * direct pay request_keys
     */
    protected $_direct_pay_req_keys = null;

    /**
     * direct pay return keys
     */
    protected $_direct_pay_return_keys = null;
    protected $_basic_charge_callback_keys = null;

    /*
     * for hash communicate request
     */
    protected $_comm_hash = null;

    /**
     * function_description
     *
     *
     * @return
     */
    public function getVersion() {
        return "1.0.1";
    }


    public function init() {
        // Register the spay path alias
        if (Yii::getPathOfAlias("spay") === false) {
            Yii::setPathOfAlias("spay", realpath(dirname(__FILE__)));
        }
        Yii::import("spay.models.*");
        Yii::import("spay.components.*");
        if (is_null($this->config_file)) {
            throw new PaymentException('Payment config_file can not be null');
        }
        include_once(Yii::getPathOfAlias("spay")."/error.php");

        if (is_null($this->config_path)) {
            $this->config_path = dirname($this->config_file);
        }

        $this->loadConfig();

        $this->_direct_pay_req_keys = array(
            'q0_MerId',
            'q1_AppId',
            'q2_uid',
            'q3_Amount',
            'q4_Url',
            'qa_Memo',
            'qk_cardTp',
            'qk_cardNo',
            'qk_cardPwd',
            'qk_cardAmt',
        );

        $this->_direct_pay_return_keys = array(
            'r0_Method',
            'r1_Code',
            'r3_OrderNo',
            'r4_RetMsg',
        );

        $this->_basic_charge_callback_keys = array(
            'r0_Method',
            'r1_Code',
            'r3_OrderNo',
            'r4_RetMsg',
            'q1_MerId',
            'q3_Amount',
            'qa_Memo',
        );
        parent::init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getDbConnection() {
        return Yii::app()->getComponent($this->db);
    }


    /**
     * load config
     *
     *
     * @return
     */
    protected function loadConfig() {
        $this->_configs = require($this->config_file);
    }

    /**
     * function_description
     *
     * @param $order:
     *mhpay
     * @return
     */
    public function getPayMethodStringForOrderType($order_type) {
        if ($order_type == Order::ORDER_TYPE_DIRECT_CHARGE) {
            return "directCharge";
        } else {
            return "";
        }
    }


    /**
     * function_description
     *
     * @param $pay_data:
     *
     * @return Order
     */
    public function MhPay($pay_data) {
		$order_type = Order::ORDER_TYPE_MH_GOLD;

		$n_keys = array(
		    'cp_id',
		    'app_id',
		    'uid',
		    'game_account',
		);
		foreach ($n_keys as $k) {
		    if (empty($pay_data[$k])) {
                Yii::log("empty key:". $k, CLogger::LEVEL_ERROR, 'payment');
                return new Error(PAYMENT_ERROR_ON_REQUEST, "lack request data");
		    }
		}
		//split pay channel and method
		if (!isset($pay_data['pay_method'])) {
		    return new Error(PAYMENT_UNKNOW_PAY_METHOD, "Unknow pay method");
		}

		// create order
		Yii::import('spay.PayParam');
		Yii::import('spay.ChargeParam');
		$uid          = $pay_data['uid'];
		$cp_id        = $pay_data['cp_id'];
		$app_id       = $pay_data['app_id'];
		$order_from   = "cp:".$cp_id;
		$pay_method   = $pay_data['pay_method'];
		$game_account = $pay_data['game_account'];
		$amount       = $pay_data['amount'];
		$pay_param    = (new PayParam)->setMethod($pay_method);
		$charge_param = (new ChargeParam)->setGameAccount($game_account)
                                         ->setCpOrderId(@$pay_data['order_id']?:0);

		list($order, $err) = Order::model()->createNew($uid,$order_type,$order_from,$amount,$pay_param,$charge_param,$cp_id, $app_id);
		if (!is_null($err)) {
		    // error on create order
		    Yii::log("order save error", CLogger::LEVEL_ERROR, "payment");
		    if ($err->getCode() == ORDER_AMOUNT_NOT_NUMERIC) {
                return new Error(ORDER_AMOUNT_NOT_NUMERIC, "save order error.");
		    } else {
                return new Error(PAYMENT_SAVE_ORDER_ERROR, "save order error.");
		    }
		}
		return $order;
    }
    /**
     * direct pay by card
     *
     * @param $pay_data:
     *
     * @return Order
     */
    public function directPay($pay_data) {
        $order_type = Order::ORDER_TYPE_DIRECT_CHARGE;
        $n_keys = array(
            'cp_id',
            'app_id',
            'card_type',
            'card_no',
            'card_pwd',
            'game_account',
        );
        foreach ($n_keys as $k) {
            if (!isset($pay_data[$k])) {
                Yii::log("empty key:". $k, CLogger::LEVEL_ERROR, 'payment');
                return new Error(PAYMENT_ERROR_ON_REQUEST, "lack request data");
            }
        }
        // create order
        Yii::import('spay.PayParam');
        Yii::import('spay.ChargeParam');
        $uid              = $pay_data['uid'];
        $cp_id = @$pay_data['cp_id']?:0;
        $app_id = @$pay_data['app_id']?:0;
        if ($cp_id) {
            $order_from       = "CP:".$cp_id;
        } else {
            $order_from = "MAIN";
        }
        $game_account = $pay_data['game_account'];
        $amount = @$pay_data['amount'] ?: 100;
        $pay_param = (new PayParam)->setCardType($pay_data['card_type'])
                                   ->setCardNumber($pay_data['card_no'])
                                   ->setCardPassword($pay_data['card_pwd'])
                                   ->setCardAmount(@$pay_data['amount'] ?: 100);
        $charge_param = (new ChargeParam)->setGameAccount($game_account)
                                         ->setCpOrderId(@$pay_data['order_id']?:0);

        if (isset($pay_data['qa_Memo'])) {
            $charge_param->setCallbackMemo($pay_data['qa_Memo']);
        }
        list($order, $err) = Order::model()->createNew($uid,$order_type,$order_from,$amount,$pay_param,$charge_param,$cp_id, $app_id);
        if (!is_null($err)) {
            // error on create order
            Yii::log("order save error", CLogger::LEVEL_ERROR, "payment");
            if ($err->getCode() == ORDER_AMOUNT_NOT_NUMERIC) {
                return new Error(ORDER_AMOUNT_NOT_NUMERIC, "save order error.");
            } else {
                return new Error(PAYMENT_SAVE_ORDER_ERROR, "save order error.");
            }
        }
        return $order;
    }

    /**
     * function_description
     *
     * @param $pay_data:
     *
     * @return Order
     */
    public function redirectPay($pay_data) {
        $order_type = Order::ORDER_TYPE_REDIRECT;

        //split pay channel and method
        if (!isset($pay_data['pay_method'])) {
            return new Error(PAYMENT_UNKNOW_PAY_METHOD, "Unknow pay method");
        }

        // create order
        Yii::import('spay.PayParam');
        Yii::import('spay.ChargeParam');
        $uid          = $pay_data['uid'];
        $cp_id = @$pay_data['cp_id']?:0;
        $app_id = @$pay_data['app_id']?:0;
        if ($cp_id) {
            $order_from       = "CP:".$cp_id;
        } else {
            $order_from = "MAIN";
        }
        $pay_method   = $pay_data['pay_method'];
        $game_account = $pay_data['game_account'];
        $amount       = $pay_data['amount'];
        $pay_param    = (new PayParam)->setMethod($pay_method);
        $charge_param = (new ChargeParam)->setGameAccount($game_account)
                                         ->setCpOrderId(@$pay_data['order_id']?:0);
        if (isset($pay_data['qa_Memo'])) {
            $charge_param->setCallbackMemo($pay_data['qa_Memo']);
        }
        list($order, $err) = Order::model()->createNew($uid,$order_type,$order_from,$amount,$pay_param,$charge_param,$cp_id, $app_id);
        if (!is_null($err)) {
            // error on create order
            Yii::log("order save error", CLogger::LEVEL_ERROR, "payment");
            if ($err->getCode() == ORDER_AMOUNT_NOT_NUMERIC) {
                return new Error(ORDER_AMOUNT_NOT_NUMERIC, "save order error.");
            } else {
                return new Error(PAYMENT_SAVE_ORDER_ERROR, "save order error.");
            }
        }
        return $order;
    }

    /**
     * get html string for order auto redirect form
     *
     * @param $order:
     *
     * @return String
     */
    public function getAutoRedirectForm($order, $method="POST") {
    	// print_r($order);exit;
        // get post data
        $outer_data = $this->getOrderOuterData($order);
        if ($outer_data instanceof Error) {
            return $outer_data;
        }

        list($url, $data) = $outer_data;
        // build form
        return $this->buildRedirectForm($url, $data, $method);
    }


    protected function buildRedirectForm($req_url, $req_data, $method) {
        $sHtml = '<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			</head>';
        $sHtml .= "<form id='paysubmit' name='paysubmit' action='".$req_url."' method='".$method."'>";
        while (list ($key, $val) = each ($req_data)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input style='display:none;' type='submit' value='submit'></form>";

        $sHtml = $sHtml."<script>document.forms['paysubmit'].submit();</script>";

        return $sHtml;
    }

    /**
     * function_description
     *
     * @param $order:
     *
     * @return array data or Error
     */
    protected function getOrderOuterData($order) {
        //pay
        $pay_param = $order->getPayParam();
        if (empty($pay_param)) {
            return [false, new Error(ORDER_PAY_PARAM_NOT_FOUND, "Pay param not find for order:".$order->id)];
        }
        $pay_method = $pay_param->getMethod();
        list($channel, $err) = $this->getChannelByMethod($pay_method, $order);
        if (!is_null($err)) {
            return $err;
        }

        // save pay
        $pay = $order->createNewPay($channel->channel_name,$pay_method);
        if (empty($pay)) {
            return new Error(PAYMENT_PAY_CREATE_FAIL, "create order pay error");
        }

        // get request data and url
        $channel->addCustomMsg("pay_id", $pay->id);
        $req_data = $channel->getOuterData();
        /* $url = $channel->getRequestUrl(); */
        return $req_data;
    }



    /**
     * function_description
     *
     * @param $data:
     * @param $cp_id:
     *
     * @return
     */
    public function generateReturnData($data, $cp_id=null) {
        if (is_null($cp_id)) {
            return [$data, null];
        }

        list($hmac_str, $err) = $this->getHashString($data, $cp_id, self::COMMUNICATE_TYPE_DIRECT_PAY_RETURN);
        if (!is_null($err)) {
            return [$data, $err];
        }

        $data[$this->hmacKey] = $hmac_str;
        return [$data, null];
    }

    /**
     * function_description
     *
     * @param $data:
     *
     * @return boolen
     */
    public function validCpRequestData(array $data) {
        if (!isset($data['hmac']) || !isset($data['cp_id'])) {
            return false;
        }
        $cp_id = $data['cp_id'];
        $cp_key = CPUser::model()->getCpuserKeyById($cp_id);
        if (empty($cp_key)) {
            return false;
        }

        // generate hash string
        $hs = "";
        ksort($data);
        reset($data);
        foreach ($data as $key => $val) {
            if (!empty($val) && $key != "hmac") {
                $hs .= $key."=".$val."&";
            }
        }
        $hs = trim($hs, "&");
        return $data['hmac'] == hash_hmac("sha1",$hs,$cp_key);
    }



    /**
     * get communicate hash component
     *
     *
     * @return CommHash
     */
    protected function getCommunicateHash() {
        if (is_null($this->_comm_hash)) {
            Yii::import("common.components.payment.CommHash");
            $this->_comm_hash = new CommHash($this->hashAlgo);
        }
        return $this->_comm_hash;
    }

    /**
     * get the hash string
     *
     * @param $data:
     * @param $cp_key:
     * @param $communicate_type:
     *
     * @return hexdigest string, err
     */
    public function getHashString($data, $cp_id, $communicate_type) {
        $cp_key = CPUser::model()->getCpuserKeyById($cp_id);
        if (empty($cp_key)) {
            return [null, new Error(CPUSER_CAN_NOT_GET_KEY, "can not get cp key for cp_id: ". $cp_id)];
        }

        if ($communicate_type == self::COMMUNICATE_TYPE_DIRECT_PAY_REQ) {
            $keys = $this->_direct_pay_req_keys;
        } elseif ($communicate_type == self::COMMUNICATE_TYPE_DIRECT_PAY_RETURN) {
            $keys = $this->_direct_pay_return_keys;
        } elseif ($communicate_type == self::COMMUNICATE_TYPE_BASIC_CHARGE) {
            $keys = $this->_basic_charge_callback_keys;
        } else {
            return [null, new Error(UNKNOW_COMMUNICATE_TYPE, "unknow communicate type: ". $communicate_type)];
        }

        return [$this->getCommunicateHash()->hashData($data, $keys, $cp_key), null];
    }

    /**
     * get orders have order_type is direct card pay
     * and pay status is unpaid
     *
     * @param $num:
     *
     * @return array(Order)
     */
    public function getDirectOrdersNeedPay($num=10) {
        return Order::model()->getDirectOrdersNeedPay($num);
    }

    /**
     * get order need charge
     *
     * @param $num:
     *
     * @return array(Order)
     */
    public function getOrdersNeedCharge($num = 10) {
        return Order::model()->getOrdersNeedCharge($num);
    }

    /**
     * function_description
     *
     * @param $num:
     *
     * @return
     */
    public function getOrdersNeedNotifyFail($num=10) {
        return Order::model()->getOrdersNeedNotifyFail($num);
    }


    /**
     * charge for order
     *
     * @param $order:
     *
     * @return [boolean, err]
     */
    public function chargeForOrder($order) {
        //check order status
        if ($order->pay_status != Order::PAY_STATUS_SUCCESS_CALLBACK) {
            return [false, new Error(ORDER_STATUS, "Error pay status:". $order->pay_status)];
        }

        if ($order->charge_status != Order::CHARGE_STATUS_UNCHARGED) {
            return [false, new Error(ORDER_STATUS, "Error charge status:". $order->charge_status)];
        }

        // charge
        $charge_param = $order->getChargeParam();
        if (empty($charge_param)) {
            return [false, new Error(ORDER_CHARGE_PARAM_NOT_FOUND, "Charge param not find for order:".$order->id)];
        }
        if($order->app_id != 0){
            //charge for app 
            list($charge_chn, $err) = $this->getChargeChannelForApp($order->app_id);
            if (!is_null($err)) {
                return [false, $err];
            }
            $charge_chn->setOrder($order);
            list($url, $req_data, $method) = $charge_chn->getChargeRequest();
            // real request
            try {
                $ret = Yii::app()->curl->$method($url, $req_data);
            } catch (Exception  $e) {
                // save charge fail
                $order->chargeFail((string)$e);
                return [false, new Error(PAYMENT_ERROR_ON_REQUEST, "Reqest to:".$url." with error:".(string)$e)];
            }

            // check return
            if ($charge_chn->checkResponse($ret)) {
                // save charge true
                $order->chargeSuccess($ret);
                return [true, null];
            } else {
                // save charge fail
                $order->chargeFail($ret);
                return [false, new Error(PAYMENT_RETURN_BODY_NOT_VALID, "Response not valid for order:".$order->id)];
            }
        }else{
            //charge for user account
            $this->chargeForUserAccount($order);
        }
	
    }
    /**
     * charge for user 1378 account
     */
    public function chargeForUserAccount($order){
    	$account = $order->getChargeParam()->getGameAccount();
    	if(empty($account)){
    		return [false, new Error(CHARGE_USER_NOT_FOUND, "no user to charge".$account)];
    	}
    	Yii::import('common.models.UserGold');
    	Yii::import('common.models.UserGoldTxn');
    	Yii::import('common.models.UserGoldTotal');
    	if(UserGold::model()->incomeGold($account,$order->charge_amt,"-1",$order->id) == UserGoldTxn::SPEND_GOLD_SUCCESS){
    		$order->chargeSuccess("CHARGE FOR USER SUCCESS ".UserGoldTxn::SPEND_GOLD_SUCCESS);
		    return [true, null];
    	}else{
    		$order->chargeFail("CHARGE FOR USER FAIL ");
		    return [false, new Error(PAYMENT_SAVE_USERGOLD_ERROR, "save user gold error :".$order->id)];
    	}
    }
    /**
     * function_description
     *
     * @param $order:
     *
     * @return
     */
    public function notifyFailForOrder($order) {
        //check order status
        if (!in_array($order->pay_status, array(Order::PAY_STATUS_FAIL, Order::PAY_STATUS_FAIL_CALLBACK, Order::PAY_STATUS_CAN_NOT_PAY))) {
            return [false, new Error(ORDER_STATUS, "Error pay status:". $order->pay_status)];
        }

        if ($order->charge_status != Order::CHARGE_STATUS_UNCHARGED) {
            return [false, new Error(ORDER_STATUS, "Error charge status:". $order->charge_status)];
        }

        // notice
        $charge_param = $order->getChargeParam();
        if (empty($charge_param)) {
            return [false, new Error(ORDER_CHARGE_PARAM_NOT_FOUND, "Charge param not find for order:".$order->id)];
        }
        list($charge_chn, $err) = $this->getChargeChannelForApp($order->app_id);
        if (!is_null($err)) {
            return [false, $err];
        }
        $charge_chn->setOrder($order);
        list($url, $req_data, $method) = $charge_chn->getFailNotifyRequest();

        // no need to notice
        if (empty($url)) {
            $order->chargeSuccess("no request", ORDER::CHARGE_STATUS_FAIL_NOTICED);
            return [true, null];
        }

        // real request
        try {
            $ret = Yii::app()->curl->$method($url, $req_data);
        } catch (Exception  $e) {
            // save charge fail
            $order->chargeFail((string)$e);
            return [false, new Error(PAYMENT_ERROR_ON_REQUEST, "Reqest to:".$url." with error:".(string)$e)];
        }

        // check return
        if ($charge_chn->checkResponse($ret)) {
            // save charge true
            $order->chargeSuccess($ret, ORDER::CHARGE_STATUS_FAIL_NOTICED);
            return [true, null];
        } else {
            // save charge fail
            $order->chargeFail($ret);
            return [false, new Error(PAYMENT_RETURN_BODY_NOT_VALID, "Response not valid for order:".$order->id)];
        }

    }


    /**
     * function_description
     *
     * @param $app_id:
     *
     * @return [channel, err]
     */
    public function getChargeChannelForApp($app_id) {
        // temp hard code it
        //get app
        $app = App::model()->findByPk($app_id);
        if (empty($app)) {
            return [null, new Error(PAYMENT_UNKONW_APP, "Unkown app: ". $app_id)];
        }
        $charge_chn_id = $app->charge_chn;
        if (!isset($this->_configs['charges'][$charge_chn_id]['className'])) {
            return [null, new Error(PAYMENT_UNKNOW_CHARGE_CHANNEL, "Unknow charge channel:".$charge_chn_id)];
        }
        $className = $this->_configs['charges'][$charge_chn_id]['className'];
        Yii::import($this->_charge_class_path_prefix.$className);
        $channel = new $className;
        return [$channel, null];
    }

    /**
     * pay for order, use card
     *
     * @param $order:
     *
     * @return [boolean, err]
     */
    public function CardPayForOrder($order) {
        if ($order->order_type != Order::ORDER_TYPE_DIRECT_CHARGE) {
            return [false, new Error(ORDER_TYPE, "Error type:". $order->order_type)];
        }

        if ($order->pay_status != Order::PAY_STATUS_UNPAID) {
            return [false, new Error(ORDER_STATUS, "Error pay status:". $order->pay_status)];
        }
        //pay
        $pay_param = $order->getPayParam();
        if (empty($pay_param)) {
            return [false, new Error(ORDER_PAY_PARAM_NOT_FOUND, "Pay param not find for order:".$order->id)];
        }
        $pay_method = $pay_param->getMethod();

        list($channel, $err) = $this->getChannelByMethod($pay_method, $order);
        if (!is_null($err)) {
            $order->canNotPay();
            return [false, $err];
        }

        // save pay
        $pay = $order->createNewPay($channel->channel_name,$pay_method);
        if (empty($pay)) {
            return [false, new Error(PAYMENT_PAY_CREATE_FAIL, "create order pay error")];
        }

        // get request data and url
        $channel->addCustomMsg("pay_id", $pay->id);
        $outerdata = $channel->getOuterData();

        if ($outerdata instanceof Error ) {
            return [false, new Error(PAYMENT_ERROR_ON_GET_OUT_DATA, "error:".(string)$outerdata)];
        }
        list($url, $req_data) = $outerdata;
        // real request
        try {
            $ret = Yii::app()->curl->post($url, $req_data);
        } catch (Exception  $e) {
            // save pay fail
            $pay->savePayFailed((string)$e);
            return [false, new Error(PAYMENT_ERROR_ON_REQUEST, "Reqest to:".$url." with error:".(string)$e)];
        }

        // check return
        if ($channel->checkResponse($ret)) {
            // save pay true
            $pay->savePaySuccess($ret);
            return [true, null];
        } else {
            // save pay fail
            $pay->savePayFailed($ret, $channel->getReturnMsg($ret));
            return [false, new Error(PAYMENT_RETURN_BODY_NOT_VALID, "Response not valid for order:".$order->id)];
        }
    }

    /**
     * function_description
     *
     * @param $pay_method:
     *
     * @return string channel id
     */
    protected function getChannelId($pay_method) {
        if (isset($this->_configs['methods'][$pay_method]['channel'])) {
            return $this->_configs['methods'][$pay_method]['channel'];
        }
        if (isset($this->_configs['banklist'][$pay_method]['channel'])) {
            return $this->_configs['banklist'][$pay_method]['channel'];
        }
        if (isset($this->_configs['chnlist'][$pay_method]['channel'])) {
            return $this->_configs['chnlist'][$pay_method]['channel'];
        }
        if (isset($this->_configs['alipay'][$pay_method]['channel'])) {
            return $this->_configs['alipay'][$pay_method]['channel'];
        }
        if (isset($this->_configs['shenzhoufu'][$pay_method]['channel'])) {
            return $this->_configs['shenzhoufu'][$pay_method]['channel'];
        }
        return "";
    }

    /**
     * function_description
     *
     * @param $channel_id:
     *
     * @return [PayChannel, err]
     */
    public function getChannelById($channel_id, $pay_method=null, $order=null) {
        //factory pay channel
        if (!isset($this->_configs['channels'][$channel_id]['className'])) {
            return [null, new Error(PAYMENT_UNKNOW_CHANNEL, "Unknow pay channel:".$channel_id)];
        }
        $className = $this->_configs['channels'][$channel_id]['className'];
        Yii::import($this->_class_path_prefix.$this->_configs['channels'][$channel_id]["type"].".".$className);
        $channel = new $className;
        if (!empty($pay_method)) {
            $channel->setMethodId($pay_method);
        }
        if (!empty($order)) {
            $channel->setOrder($order);
        }
        return [$channel, null];
    }


    /**
     * get payment channel
     *
     * @param $pay_method:
     * @param $order:
     *
     * @return [PayChannel, err]
     */
    public function getChannelByMethod($pay_method, $order=null) {
        $channel_id = $this->getChannelId($pay_method);
        if (empty($channel_id)) {
            return [null, new Error(PAYMENT_UNKNOW_PAY_METHOD, "Unknow pay method:".$pay_method)];
        }
        return $this->getChannelById($channel_id, $pay_method, $order);
    }

    /**
     * recevie callback for channel server
     *
     * @param $channel_id:
     * @param $params:
     *
     * @return string return_msg
     */
    public function receiveCallback($channel_id, $params) {
        list($channel, $err) = $this->getChannelById($channel_id);
        if (!is_null($err)) {
            Yii::log("callback error with:".(string)$err, CLogger::LEVEL_WARNING, "payment");
            return "false";
        }
        $channel->setCallback($params);
        if (!$channel->validCallback()) {
            // the callback has not validate
            return "false";
        }
        $order = $channel->getCallbackOrder();
        if ($channel->isCallbackSuccess()) {
            // update order for success pay
            $order->OrderPaySuccess(
                $channel->getPayAmount(),
                $channel->getRealAmount(),
                $channel->getChargeAmount(),
                $channel->getCallbackPayId(),
                $params
            );
        } else {
            // update order for fail pay
            $order->OrderPayFail($channel->getCallbackPayId(), $params);
        }

        return $channel->getReturnStringForCallback();
    }

    /**
     * function_description
     *
     * @param $channel_id:
     * @param $params:
     *
     * @return [result, return_string, req_type]
     * result true or false for the request is valid and success
     * return_string to return to channal server
     * req_type check if the request is from user browser or from channal server
     */
    public function receiveNotify($channel_id, $params) {
        list($channel, $err) = $this->getChannelById($channel_id);
        if (!is_null($err)) {
            Yii::log("callback error with:".(string)$err, CLogger::LEVEL_WARNING, "payment");
            return [false, "false", self::NOTIFY_TYPE_UNKNOW];
        }
        $channel->setNotify($params);
        if (!$channel->validNotify()) {
            // the callback has not validate
            return [false, "false", $channel->getNotifyReqType()];
        }
        $order = $channel->getNotifyOrder();
        if ($channel->isNotifySuccess()) {
            // update order for success pay
            $order->OrderPaySuccess(
                $channel->getPayAmount(),
                $channel->getRealAmount(),
                $channel->getChargeAmount(),
                $channel->getNotifyPayId(),
                $params
            );
        } else {
            // update order for fail pay
            $order->OrderPayFail($channel->getNotifyPayId(), $params, $channel->getNotifyMsg());
        }

        return [true, $channel->getReturnStringForNotify(), $channel->getNotifyReqType()];
    }

    /**
     * function_description
     *
     * @param $channel_id:
     * @param $params:
     *
     * @return [result, return_string, req_type]
     * result true or false for the request is valid and success
     * return_string to return to channal server
     * req_type check if the request is from user browser or from channal server
     */
    public function DeductMhGold($order) {
		list($channel, $err) = $this->getChannelById($order->getPayParam()->getMethod());
		if (!is_null($err)) {
		    Yii::log("callback error with:".(string)$err, CLogger::LEVEL_WARNING, "payment");
		    return [false, "false", self::NOTIFY_TYPE_UNKNOW];
		}
		$channel->setOrder($order);
		if (!$channel->deduct()) {
		    // the callback has not validate
		    $order->OrderPayFail();
		}else{
			$order->OrderPaySuccess(
				$channel->getPayAmount(),
				$channel->getRealAmount(),
				$channel->getChargeAmount(),
				null,
				null,
				"deduct mh gold success"
		    );
		}
		return [true, $channel->getReturnStringForNotify(), $channel->getNotifyReqType()];
    }



    /**
     * return bank list for frontend pay
     *
     *
     * @return array
     */
    public function getBankList() {
        if (isset($this->_configs['banklist'])) {
            $ret = array();
            foreach ($this->_configs['banklist'] as $bank_key => $bank) {
                $bank['key'] = $bank_key;
                $ret[] = $bank;
            }
            return $ret;
        } else {
            return array();
        }
    }


    /**
     * get channel direct pay list
     *
     *
     * @return array
     */
    public function getChnList() {
        if (isset($this->_configs['chnlist'])) {
            foreach ($this->_configs['chnlist'] as $chn_key => $chn) {
                $chn['key'] = $chn_key;
                $ret[] = $chn;
            }
            return $ret;
        } else {
            return array();
        }
    }
    /**
     * get channel direct pay list
     *
     *
     * @return array
     */
    public function getAlipay() {
        if (isset($this->_configs['alipay'])) {
            foreach ($this->_configs['alipay'] as $chn_key => $chn) {
                $chn['key'] = $chn_key;
                $ret[] = $chn;
            }
            return $ret;
        } else {
            return array();
        }
    }
    /**
     * get charge amount price list
     *
     *
     * @return
     */
    public function getAmountList() {
        if (isset($this->_configs['amtlist'])) {
            return $this->_configs['amtlist'];
        } else {
            return array();
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getChargeChnList() {
        $ret = array();
        foreach ($this->_configs['charges'] as $key => $val) {
            $ret[$key] = $val['name'];
        }
        return $ret;
    }

    /**
     * get channel direct pay list
     *
     *
     * @return array
     */
    public function getShenzhoufu() {
        if (isset($this->_configs['shenzhoufu'])) {
            foreach ($this->_configs['shenzhoufu'] as $chn_key => $chn) {
                $chn['key'] = $chn_key;
                $ret[] = $chn;
            }
            return $ret;
        } else {
            return array();
        }
    }

}