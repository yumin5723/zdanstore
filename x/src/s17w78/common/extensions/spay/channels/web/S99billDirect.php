<?php

require_once("PayChannelWebAbstract.php");

class S99billDirect extends PayChannelWebAbstract {

    protected $_99bill_gateway = "https://www.99bill.com/gateway/recvMerchantInfoAction.htm";

    protected $_merchantAcctId;

    protected $_priv_key;

    protected $_priv_key_passphrase = "";

    protected $_pub_key;

    public $channel_name="99bill";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        parent::init();
        $this->_merchantAcctId = @$this->_config['partner_id']?:'1001213884201';
        $this->_priv_key = @$this->_config['private_key_file']?:dirname(__FILE__)."/99bill.pcarduser.pem";
        $this->_pub_key = @$this->_config['public_key_file']?:dirname(__FILE__)."/99bill.cert.rsa.cer";
        $this->_priv_key_passphrase = @$this->_config['private_key_pass']?:"";
    }

    /**
     * function_description
     *
     *
     * @return [url, data]
     */
    public function getOuterData() {
        $params = array(
            'inputCharset' => '1', // utf8
            'pageUrl'      =>$this->getReturnUrl(),
            'bgUrl'        => $this->getNotifyUrl(),
            'version'      => 'v2.0',
            'language'     => '1',
            'signType'     => '4',

            'merchantAcctId' => $this->_merchantAcctId,

            'orderId'     => $this->_order->id,
            'orderAmount' => intval($this->_order->order_amt*100),
            'orderTime'   => date("YmdHis",strtotime($this->_order->created)),
            'productName' => $this->_order->subject?:'充值',
            'ext1'        => $this->getCustomMsgOutString(),
            'payType'     => '00',
            'redoFlag'    => '1',
        );
        if ($this->getMethodId() != '99bill') {
            $params['payType'] = 10;
            $params['bankId'] = $this->getMethodId();
        }
        $params['signMsg'] = $this->requestSign($params);
        return [$this->_99bill_gateway, $params];
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function validNotify() {
        $data = $this->_notify_data;
        $signMsg = base64_decode($data['signMsg']);
        return !!$this->sslVerify($this->getNotifyBodyString($data), $signMsg);
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
     * @return
     */
    public function getMethodIdFromNotify() {
        return @$this->_notify_data['bankId']?:'99bill';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnStringForNotify() {
        return "<result>1</result><redirecturl>".$this->getReturnUrl()."</redirecturl>";
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
     * calculate request sign
     *
     * @param $request_data:
     *
     * @return
     */
    protected function requestSign($request_data) {
        $keys = array(
            'inputCharset',
            'pageUrl',
            'bgUrl',
            'version',
            'language',
            'signType',

            'merchantAcctId',

            'orderId',
            'orderAmount',
            'orderTime',
            'productName',
            'ext1',
            'ext2',
            'payType',
            'bankId',
            'redoFlag',
        );
        $temp_str = "";
        foreach ($keys as $k) {
            if (!empty($request_data[$k])) {
                $temp_str .= $k."=".$request_data[$k]."&";
            }
        }
        $temp_str = trim($temp_str, "&");
        return $this->signString($temp_str);
    }

    /**
     * function_description
     *
     * @param $data:
     *
     * @return
     */
    protected function getNotifyBodyString($data) {
        $keys = array(
            'merchantAcctId',
            'version',
            'language',
            'signType',
            'payType',
            'bankId',
            'orderId',
            'orderTime',
            'orderAmount',
            'bindCard',
            'bindMobile',
            'dealId',
            'bankDealId',
            'dealTime',
            'payAmount',
            'fee',
            'ext1',
            'ext2',
            'payResult',
            'errCode',
        );
        $temp_str = "";
        foreach ($keys as $k) {
            if (!empty($data[$k])) {
                $temp_str .= $k."=".$data[$k]."&";
            }
        }
        return trim($temp_str, '&');
    }


    /**
     * sign for string
     *
     * @param $str:
     *
     * @return
     */
    protected function signString($str) {
        $key = @file_get_contents($this->_priv_key);
        if ($key === false) {
            throw new PaymentException("can not read private key file: ".$this->_priv_key);
        }

        $pkeyid = openssl_get_privatekey($key,$this->_priv_key_passphrase);

        openssl_sign($str, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1);

        openssl_free_key($pkeyid);
        return base64_encode($signMsg);
    }

    /**
     * function_description
     *
     * @param $resp_str:
     * @param $signMsg:
     *
     * @return
     */
    protected function sslVerify($str, $signMsg) {
        $key = @file_get_contents($this->_pub_key);
        if ($key === false) {
            throw new PaymentException("can not read public key file: ".$this->_pub_key);
        }

        $pubkeyid = openssl_get_publickey($key);
        return openssl_verify($str, $signMsg, $pubkeyid);

    }




}