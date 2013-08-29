<?php

require("PayChannelWebAbstract.php");
class TenPayWap extends PayChannelWebAbstract {

    protected $_init_url = "https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi";

    protected $_tenpay_wap_gateway = "https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi";

    protected $_partner;

    protected $_key;

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
	parent::init();
	$this->_partner = trim($this->_config['partner']);
	$this->_key = trim($this->_config['key']);

    }

    /**
     * function_description
     *
     *
     * @return [url, data]
     */
    public function getOuterData() {
	$token_id =  $this->getInitToken();
	if (empty($token_id)) {
	    return new Error(PAYMENT_CHANNEL_RESPONSE_ERROR, "can not get token id from ten pay api.");
	}
	return [$this->_tenpay_wap_gateway, array(
		'token_id' => $token_id,
	    )];
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function validNotify() {
	if (!isset($this->_notify_data['sign'])) {
	    return false;
	}
	$keysArr = array(
		"ver",
		"charset",
		"bank_type",
		"bank_billno", //选填
		"pay_result",
		"pay_info",     //选填
		"purchase_alias",
		"bargainor_id",
		"transaction_id",
		"sp_billno",
		"total_fee",
		"fee_type",
		"attach",
		"time_end",
	);
	$data = array();
	foreach ($keyArr as $k) {
	    if (isset($this->_notify_data[$k])) {
		$data[$k] = $this->_notify_data[$k];
	    }
	}
	return $this->_notify_data['sign'] == $this->createSign($data);
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
	if (!isset($this->_notify_data['pay_result'])) {
	    return false;
	}
	return $this->_notify_data['pay_result'] === "0";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethodIdFromNotify() {
	return "all";
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
	if (isset($this->_notify_data['attach'])) {
	    $this->setCustomMsgFromString($this->_notify_data['attach']);
	}
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyOrderId() {
	return @$this->_notify_data['sp_billno']?:null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getPayAmount() {
	return @$this->_notify_data['total_fee'] ?: null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getInitToken() {
	$req_data = array(
	    'total_fee' => $this->_order->order_amt,
	    'ver' => '2.0',
	    'bank_type' => '0',
	    "desc" => $this->_order->subject?:"充值",
	    'bargainor_id' => $this->_partner,
	    'sp_billno' => $this->_order->id,
	    'notify_url' => $this->getNotifyUrl(),
	    'callback_url' => $this->getReturnUrl(),
	    'attach' => $this->getCustomMsgOutString(),
	);
	$req_data['sign'] = $this->createSign($req_data);
	try{
	    $content = Yii::app()->curl->get($this->_init_url, $req_data);
	} catch (Exception $e) {
	    Yii::log("Error on request init token for ten pay :" .(string)$e, CLogger::LEVEL_ERROR);
	    return null;
	}
	$ret_params = $this->getParamsFromResponseContent($content);
	return @$ret_params['token_id']?:null;
    }


    /**
     * create sign
     *
     * @param $params:
     *
     * @return
     */
    protected function createSign($params) {
	$signPars = "";
	ksort($params);
	reset($params);
	foreach ($params as $k => $v) {
	    if ("" != $v && "sign" != $k) {
		$signPars .= $k . "=" . $v . "&";
	    }
	}
	$signPars .= "key=" . $this->_key;
	return md5($signPars);
    }

    /**
     * function_description
     *
     * @param $content:
     *
     * @return
     */
    protected function getParamsFromResponseContent($content) {
	$xml = simplexml_load_string($content);
	$encode = $this->getXmlEncode($content);
	$params = array();
	if($xml && $xml->children()) {
	    foreach ($xml->children() as $node){
		//有子节点
		if($node->children()) {
		    $k = $node->getName();
		    $nodeXml = $node->asXML();
		    $v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);

		} else {
		    $k = $node->getName();
		    $v = (string)$node;
		}

		if($encode!="" && $encode != "UTF-8") {
		    $k = iconv("UTF-8", $encode, $k);
		    $v = iconv("UTF-8", $encode, $v);
		}

		$params[$k] = $v;
	    }
	}
	return $params;
    }


}
