<?php

Yii::import("spay.channels.web.PayChannelWebAbstract");

class S99billCard extends PayChannelWebAbstract {
    public $channel_name="s99billcard";

    protected $_99billcard_gateway = "";

    protected $_merchantAcctId;

    protected $_szx_key = "";

    protected $_unicom_key = "";

    protected $_telecom_key = "";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        parent::init();

        $this->_99billcard_gateway = @$this->_config['gateway']?:$this->_99billcard_gateway;
        // $this->_merchantAcctId = @$this->_config['partner_id']?:$this->_merchantAcctId;
        $this->_szx_key = @$this->_config['szx_key'] ?: $this->_szx_key;
        $this->_unicom_key = @$this->_config['unicom_key'] ?: $this->_unicom_key;
        $this->_telecom_key = @$this->_config['telecom_key'] ?: $this->_telecom_key;
    }

    /**
     * data request to gateway
     *
     *
     * @return [url, params] or Error
     */
    public function getOuterData() {
        $params = array(
            'inputCharset' => '1', // utf8
            'pageUrl'      => $this->getUserPayRecordUrl(),
            'bgUrl'        => $this->getNotifyUrl(),
            'version'      => 'v2.0',
            'language'     => '1',
            'signType'     => '1',

            'orderId'     => $this->_order->id,
            'orderAmount' => intval($this->_order->order_amt*100),
            'orderTime'   => date("YmdHis",strtotime($this->_order->created)),
            'productName' => urlencode($this->_order->subject?:'充值'),
            'ext1'        => $this->getCustomMsgOutString(),
            /* 'bossType'    => '3', */
            'payType'     => '42',
            'fullAmountFlag' => '0',
        );
        $boss_type = $this->getBossType($this->getMethodId());
        if ($boss_type === false) {
            return new Error(PAYMENT_ERROR_ON_GET_OUT_DATA, "Can not get bossType for method: ".$this->getMethodId());
        }
        $params['bossType'] = $boss_type;
        $merchantAcctId = $this->getMerchantAccId($this->getMethodId());
        if ($merchantAcctId === false) {
            return new Error(PAYMENT_ERROR_ON_GET_OUT_DATA, "Can not get merchantAcctId for method: ".$this->getMethodId());
        }

        $params['merchantAcctId'] = $merchantAcctId;

        $params['signMsg'] = $this->requestSign($params);

        return [$this->_99billcard_gateway, $params];
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function validNotify() {
        $data = $this->_notify_data;
        $signMsg = $data['signMsg'];
        return $this->notifySign($data) == $signMsg;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function isNotifySuccess() {
        if (!$this->validNotify()) {
            return false;
        }

        return $this->_notify_data['payResult'] == '10';
    }

    /**
     * function_description
     *
     *
     * @return string method id or false
     */
    public function getMethodIdFromNotify() {
        if (!isset($this->_notify_data['receiveBossType'])) {
            return false;
        }
        switch ($this->_notify_data['receiveBossType']) {
            case "0":
                return "szx";
            case "1":
                return "unicom";
            case "3":
                return "telecom";
            default:
                return false;
        }

    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnStringForNotify() {
        return "<result>1</result><redirecturl>".$this->getUserPayRecordUrl()."</redirecturl>";
    }

    public function getUserPayRecordUrl(){
        return "http://www.1378.com/pay/list";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function setCustomMsgFromNotify() {
        if (isset($this->_notify_data['ext1'])) {
            $this->setCustomMsgFromString($this->_notify_data['ext1']);
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyOrderId() {
        return @$this->_notify_data['orderId']?:null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getPayAmount() {
        return $this->_notify_data['payAmount']? sprintf("%.2f",intval($this->_notify_data['payAmount']) * 1.0 / 100):null;
    }

    /**
     * get s99bill boss type for method
     *
     * @param $method_id:
     *
     * @return int  boss type or false
     */
    protected function getBossType($method_id) {
        switch ($method_id) {
            case "szx":
                return 0;
            case "unicom":
                return 1;
            case "telecom":
                return 3;
            default:
                return false;
        }

    }

    protected function getMerchantAccId($method_id){
        switch ($method_id) {
            case "szx":
                return $this->_config['szx_partner_id'];
            case "unicom":
                return $this->_config['unicom_partner_id'];
            case "telecom":
                return $this->_config['telecom_partner_id'];
            default:
                return false;
        }
    }

    /**
     * get request sign for 99bill gateway
     *
     * @param $params:
     *
     * @return string
     */
    protected function requestSign($params) {
        $keys = array(
            'inputCharset',
            'bgUrl',
            'pageUrl',
            'version',
            'language',
            'signType',
            'merchantAcctId',
            'orderId',
            'orderAmount',
            'payType',
            'fullAmountFlag',
            'orderTime',
            'productName',
            'ext1',
            'bossType',
        );

        $temp_str = "";
        foreach ($keys as $k) {
            if (isset($params[$k]) && $params[$k] !== "") {
                $temp_str .= $k."=".$params[$k]."&";
            }
        }
        $temp_str = trim($temp_str, "&");
        return $this->signString($temp_str,$this->getMethodId());
    }

    /**
     * get request sign for 99bill gateway
     *
     * @param $params:
     *
     * @return string
     */
    protected function notifySign($params) {
        $keys = array(
            'merchantAcctId',
            'version',
            'language',
            'payType',
            'cardNumber',
            'cardPwd',
            'orderId',
            'orderAmount',
            'dealId',
            'orderTime',
            'ext1',
            'payAmount',
            'billOrderTime',
            'payResult',
            'signType',
            'bossType',
            'receiveBossType',
            'receiverAcctId',
        );
        $temp_str = "";
        foreach ($keys as $k) {
            if (isset($params[$k]) && $params[$k] !== "") {
                $temp_str .= $k."=".$params[$k]."&";
            }
        }
        $temp_str = trim($temp_str, "&");
        return $this->signString($temp_str, $this->getMethodIdFromNotify());
    }

    /**
     * sign for string
     *
     * @param $str:
     *
     * @return
     */
    protected function signString($str, $method_id) {
        switch ($method_id) {
            case "szx":
                $key = $this->_szx_key;
                break;
            case "unicom":
                $key = $this->_unicom_key;
                break;
            case "telecom":
                $key = $this->_telecom_key;
                break;
            default:
                $key = "";
                break;
        }
        return strtoupper(md5($str."&key=".$key));
    }

    /**
     * custom msg arrty to string
     *
     *
     * @return
     */
    // protected function getCustomMsgOutString() {
    //     return urlencode(json_encode($this->_custom_msg));
    // }

    // /**
    //  * set custom msg from string
    //  *
    //  *
    //  * @return
    //  */
    // protected function setCustomMsgFromString($json) {
    //     $this->_custom_msg = (array) json_decode(urldecode($json));
    //     return $this;
    // }

    // public function setNotify($notify_params) {
    //     $this->_notify_data = $notify_params;
    //     $this->_notify_data['ext1'] = urlencode(urldecode($this->_notify_data['ext1']));
    //     return $this;
    // }



}