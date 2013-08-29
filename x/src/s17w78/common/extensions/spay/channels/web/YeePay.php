<?php

require("PayChannelWebAbstract.php");
class YeePay extends PayChannelWebAbstract {
    public $channel_name = 'yeepay';

    protected $_merId;

    protected $_key;

    protected $_yeepay_gateway;

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	parent::init();
	$this->_merId = trim($this->_config['merId']);
	$this->_key = trim($this->_config['key']);
	$this->_yeepay_gateway = trim($this->_config['gateway']);
    }

    function getOuterData() {
	$params = array(
	    'p0_Cmd'          => 'Buy',
	    'p1_MerId'        => $this->_merId,
	    'p2_Order'        => $this->_order->id,
	    'p3_Amt'          => $this->_order->order_amt,
	    'p4_Cur'          => 'CNY',
	    'p5_Pid'          => $this->_order->subject?:"charge",
	    'p6_Pcat'         => '',
	    'p7_Pdesc'        => '',
	    'p8_Url'          => $this->getReturnUrl(),
	    'p9_SAF'          => '0',
	    'pa_MP'           => $this->getCustomMsgOutString(),
	    'pd_FrpId'        => $this->getMethodId(),
	    'pr_NeedResponse' =>'1',
	);
	$params['hmac'] = $this->getReqHmacString($params);
	return [$this->_yeepay_gateway, $params];
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function validNotify() {
	$data = $this->_notify_data;
	if (!isset($data['hmac'])) {
	    return false;
	}
	return $this->getCallbackHmacString($data) == $data['hmac'];
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
	return true;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethodIdFromNotify() {
	return isset($this->_notify_data['rb_BankId'])?$this->_notify_data['rb_BankId']:null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnStringForNotify() {
	if ($this->validNotify()) {
	    return "success";
	} else {
	    return "fail";
	}
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function setCustomMsgFromNotify() {
	if (isset($this->_notify_data['r8_MP'])) {
	    $this->setCustomMsgFromString($this->_notify_data['r8_MP']);
	}
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyOrderId() {
	return isset($this->_notify_data['r6_Order'])?$this->_notify_data['r6_Order']:null;
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function getPayAmount() {
	return isset($this->_notify_data['r3_Amt'])?$this->_notify_data['r3_Amt']:null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyReqType() {
	if (!isset($this->_notify_data['r9_BType'])) {
	    return Payment::NOTIFY_TYPE_UNKNOW;
	} elseif ($this->_notify_data["r9_BType"] == "1") {
	    return Payment::NOTIFY_TYPE_BROWSER;
	} else {
	    return Payment::NOTIFY_TYPE_SERVER;
	}
    }




    /**
     * function_description
     *
     * @param $data:
     *
     * @return
     */
    protected function getCallbackHmacString($data) {
	$temp_str = $this->_merId;
	$keys = array(
	    'r0_Cmd',
	    'r1_Code',
	    'r2_TrxId',
	    'r3_Amt',
	    'r4_Cur',
	    'r5_Pid',
	    'r6_Order',
	    'p7_Uid',
	    'r8_MP',
	    'r9_BType',
	);
	foreach ($keys as $k) {
	    if (isset($data[$k])) {
		$temp_str .= ($data[$k]);
	    }
	}

	return $this->HmacMd5($temp_str);
    }



    /**
     * get hmac string for request
     *
     * @param $data:
     *
     * @return
     */
    protected function getReqHmacString($data) {
	$temp_str = "";
	$keys = array(
	    'p0_Cmd',
	    'p1_MerId',
	    'p2_Order',
	    'p3_Amt',
	    'p4_Cur',
	    'p5_Pid',
	    'p6_Pcat',
	    'p7_Pdesc',
	    'p8_Url',
	    'p9_SAF',
	    'pa_MP',
	    'pd_FrpId',
	    'pr_NeedResponse'
	);
	foreach ($keys as $k) {
	    if (isset($data[$k])) {
		$temp_str .= ($data[$k]);
	    }
	}
	return $this->HmacMd5($temp_str);
    }


    /**
     * hmac hash data by key
     *
     * @param data
     * @param key
     *
     * @return
     */
    protected function HmacMd5($data)
    {
	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing(NOTE: Hacked means written)
	$key = $this->_key;

	//需要配置环境支持iconv，否则中文参数不能正常处理
	/* $key = iconv("GB2312","UTF-8",$key); */
	/* $data = iconv("GB2312","UTF-8",$data); */

	$b = 64; // byte length for md5
	if (strlen($key) > $b) {
	    $key = pack("H*",md5($key));
	}
	$key = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad ;
	$k_opad = $key ^ $opad;

	return md5($k_opad . pack("H*",md5($k_ipad . $data)));
    }

}