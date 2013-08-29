<?php
require('PayChannelCardAbstract.php');
class YeeCardPay extends PayChannelCardAbstract {

    public $channel_name = 'yeecardpay';

    protected $_merId;

    protected $_merKey;

    protected $_reqURL;

    protected $_req_keys = array(
	#加入业务类型
	'p0_Cmd',
	#加入商家ID
	'p1_MerId',
	#加入商户订单号
	'p2_Order',
	#加入支付卡面额
	'p3_Amt',
	#加入是否较验订单金额
	'p4_verifyAmt',
	#加入产品名称
	'p5_Pid',
	#加入产品类型
	'p6_Pcat',
	#加入产品描述
	'p7_Pdesc',
	#加入商户接收交易结果通知的地址
	'p8_Url',
	#加入临时信息
	'pa_MP',
	#加入卡面额组
	'pa7_cardAmt',
	#加入卡号组
	'pa8_cardNo',
	#加入卡密组
	'pa9_cardPwd',
	#加入支付通道编码
	'pd_FrpId',
	#加入应答机制
	'pr_NeedResponse',
	#用户唯一标识
	'pz_userId',
	#用户的注册时间
	'pz1_userRegTime',
    );

    protected $_return_keys = array(
	'r0_Cmd',
	'r1_Code',
	'r6_Order',
	'rq_ReturnMsg',
    );

    protected $_callback_keys = array(
	'r0_Cmd',
	'r1_Code',
	'p1_MerId',
	'p2_Order',
	'p3_Amt',
	'p4_FrpId',
	'p5_CardNo',
	'p6_confirmAmount',
	'p7_realAmount',
	'p8_cardStatus',
	'p9_MP',
	'pb_BalanceAmt',
	'pc_BalanceAct',
    );
    protected $_req_data = array(
	'p0_Cmd' => 'ChargeCardDirect',
	'p4_verifyAmt' => 'false',
	'pr_NeedResponse' => '1',
    );

    protected $_return_msg = array(
	'-1'   =>'签名较验失败或未知错误',
	'2'    => '卡密成功处理过或者提交卡号过于频繁',
	'5'    => '卡数量过多，目前最多支持10张卡',
	'11'   => '订单号重复',
	'66'   => '支付金额有误',
	'95'   => '支付方式未开通',
	'112'  => '业务状态不可用，未开通此类卡业务',
	'8001' =>'卡面额组填写错误',
	'8002' =>'卡号密码为空或者数量不相等（使用组合支付时）',
    );

    protected $_status_msg = array(
	'0'     => '销卡成功，订单成功',
	'1'     => '销卡成功，订单失败',
	'7'     => '卡号卡密或卡面额不符合规则',
	'1002'  => '本张卡密您提交过于频繁，请您稍后再试',
	'1003'  => '不支持的卡类型（比如电信地方卡）',
	'1004'  => '密码错误或充值卡无效',
	'1006'  => '充值卡无效',
	'1007'  => '卡内余额不足',
	'1008'  => '余额卡过期（有效期1个月）',
	'1010'  => '此卡正在处理中',
	'10000' => '未知错误',
	'2005'  => '此卡已使用',
	'2006'  => '卡密在系统处理中',
	'2007'  => '该卡为假卡',
	'2008'  => '该卡种正在维护',
	'2009'  => '浙江省移动维护',
	'2010'  => '江苏省移动维护',
	'2011'  => '福建省移动维护',
	'2012'  => '辽宁省移动维护',
	'2013'  => '该卡已被锁定',
	'2014'  => '系统繁忙，请稍后再试',
	'3001'  => '卡不存在',
	'3002'  => '卡已使用过',
	'3003'  => '卡已作废',
	'3004'  => '卡已冻结',
	'3005'  => '卡未激活',
	'3006'  => '密码不正确',
	'3007'  => '卡正在处理中',
	'3101'  => '系统错误',
	'3102'  => '卡已过期',
    );


    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	parent::init();
	$this->_merId = $this->_config['merId'];
	$this->_merKey = $this->_config['merchantKey'];
	$this->_reqUrl = $this->_config['reqUrl'];
    }

    /**
     * get request url
     *
     *
     * @return
     */
    public function getRequestUrl() {
	return $this->_reqUrl;
    }


    /**
     * generate request data
     *
     *
     * @return [boolean, err]
     */
    protected function generateReqData() {
	if (!isset($this->_order)) {
	    return [false, new Error(PAYMENT_NEED_ORDER)];
	}
	$pay_param = $this->_order->getPayParam();
	if (empty($pay_param)) {
	    return [false, new Error(PAYMENT_NEED_PAY_PARAM)];
	}


	$this->_req_data['p1_MerId'] = $this->_merId;
	$this->_req_data['p2_Order'] = $this->_order->id;
	$this->_req_data['p3_Amt'] = $this->_order->order_amt;
	$this->_req_data['p8_Url'] = $this->getNotifyUrl();
	$this->_req_data['pa_MP'] = $this->getCustomMsgOutString();
	$this->_req_data['pa7_cardAmt'] = $pay_param->getCardAmount();
	$this->_req_data['pa8_cardNo'] = $pay_param->getCardNumber();
	$this->_req_data['pa9_cardPwd'] = $pay_param->getCardPassword();
	$this->_req_data['pd_FrpId'] = $this->_method_id;
	$this->_req_data['hmac'] = $this->getReqHmacString($this->_req_data);
	return [true, null];
    }


    /**
     * get data for post
     *
     *
     * @return array(request_url, data)
     */
    public function getOuterData() {
	list($ret, $err) = $this->generateReqData();
	if (!$ret) {
	    return $err;
	}
	return [$this->getRequestUrl(), $this->_req_data];
    }

    /**
     * validate http response
     *
     * @param $ret_data:
     *
     * @return boolean
     */
    public function checkResponse($ret_data) {
	$ret_data = iconv("GB2312", 'UTF-8', $ret_data);
	$attrs = explode("\n", $ret_data);
	$data = array();
	foreach ($attrs as $ts) {
	    $tr = explode('=', trim($ts));
	    if (count($tr) == 2) {
		list($k, $v) = $tr;
		$data[$k] = $v;
	    }
	}

	// validate
	if (!isset($data['hmac'])) {
	    return false;
	}
	if ($data['hmac'] != $this->getReturnHmacString($data)) {
	    return false;
	}
	if (isset($data['r1_Code']) && $data['r1_Code'] == '1') {
	    return true;
	} else {
	    return false;
	}
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getReqHmacString($data) {
	return $this->hashData($this->_req_keys, $data);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnHmacString($data) {
	return $this->hashData($this->_return_keys, $data);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getCallbackHmacString($data) {
	return $this->hashData($this->_callback_keys, $data);
    }



    protected function hashData($keys, $data) {
	$old = "";
	foreach ($keys as $k) {
	    $old .= isset($data[$k]) ? $data[$k] : "";
	}
	return hash_hmac("md5", $old, $this->_merKey);
    }

    /**
     * valid callback
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
     * check if pay has success
     *
     *
     * @return boolean
     */
    public function isNotifySuccess() {
	return $this->validNotify() && isset($this->_notify_data['r1_Code']) && $this->_notify_data['r1_Code'] == '1';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethodIdFromNotify() {
	return isset($this->_notify_data['p4_FrpId'])?$this->_notify_data['p4_FrpId']:null;
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnStringForNotify() {
	return $this->validNotify() ? "success" : "false";
    }

    /**
     * get order id from callback params
     *
     *
     * @return
     */
    public function getNotifyOrderId() {
	return isset($this->_notify_data['p2_Order']) ? $this->_notify_data['p2_Order'] : null;
    }

    /**
     * get pay amount from callback params
     *
     *
     * @return
     */
    public function getPayAmount() {
	return isset($this->_notify_data['p3_Amt'])?floatval($this->_notify_data['p3_Amt']): null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function setCustomMsgFromNotify() {
	if (isset($this->_notify_data['p9_MP'])) {
	    $this->setCustomMsgFromString($this->_notify_data['p9_MP']);
	    return $this;
	}
    }

    /**
     * function_description
     *
     * @param $ret_body:
     *
     * @return
     */
    public function getReturnMsg($ret_data) {
	$ret_data = iconv("GB2312", 'UTF-8', $ret_data);
	$attrs = explode("\n", $ret_data);
	$data = array();
	foreach ($attrs as $ts) {
	    $tr = explode('=', trim($ts));
	    if (count($tr) == 2) {
		list($k, $v) = $tr;
		$data[$k] = $v;
	    }
	}
	if (isset($data['rq_ReturnMsg'])) {
	    return $data['rq_ReturnMsg'];
	}
	if (isset($this->_return_msg[$data['r1_Code']])) {
	    return $this->_return_msg[$data['r1_Code']];
	}
	return "";
    }

    /**
     * function_description
     *
     * @param $notify_body:
     *
     * @return
     */
    public function getNotifyMsg() {
	if (empty($this->_notify_data['p8_cardStatus'])) {
	    return "";
	}
	return @$this->_status_msg[$this->_notify_data['p8_cardStatus']] ?: "";
    }

}