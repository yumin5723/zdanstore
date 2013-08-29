<?php
require('PayChannelCardAbstract.php');
class ShenZhouFu extends PayChannelCardAbstract {

    public $channel_name = 'shenzhoufu';

    protected $_merId;

    protected $_reqURL;

    protected $_privateKey;

    protected $_merUserName = "";
    protected $_merUserMail = "";

    protected $_des;

    protected $_req_keys = array(
		'version',
		'merId',
		'payMoney',
		'orderId',
		'returnUrl',
		'cardInfo',
		'privateField',
		'verifyType',
		'privateKey',
		'md5String',

		'merUserName',
		'merUserMail',
    );

    protected $_callback_keys = array(
		'version',
    	'merId',
    	'payMoney',
    	'cardMoney',
    	'orderId',
    	'payResult',
    	'privateField',
    	'payDetails',
        // 'errcode'
    );

    protected $_return_msg = array(
        '101'     => 'md5 验证失败',
        '102'     => '订单号重复',
        '103'     => '恶意用户',
        '104'     => '序列号,密码简单验证失败或之前曾提交过的卡密已验证失败',
        '105'     => '密码正在处理中',
        '106'     => '系统繁忙,暂停提交',
        '107'     => '多次充值时卡内余额不足',
        '109'     => 'des 解密失败',
        '201'     => '证书验证失败',
        '501'     => '插入数据库失败',
        '502'     => '插入数据库失败',
        '200'     => '请求成功,神州付收单(非订单状态为成功)',
        '902'     => '商户参数不全',
        '903'     => '商户 ID 不存在',
        '904'     => '商户没有激活',
        '905'     => '商户没有使用该接口的权限',
        '906'     => '商户没有设置 密钥(privateKey)',
        '907'     => '商户没有设置 DES 密钥',
        '908'     => '该笔订单已经处理完成(订单状态已经为确定的状态: 成功 或者 失败)',
        '910'     => '服务器返回地址,不符合规范',
        '911'     => '订单号,不符合规范',
        '912'     => '非法订单',
        '913'     => '该地方卡暂时不支持',
        '914'     => '金额非法',
        '915'     => '卡面额非法',
        '916'     => '商户不支持该充值卡',
        '917'     => '参数格式不正确',
        '0'     => '网络连接失败',
    );

    protected $_status_msg = array(
        '200' => '充值卡验证成功',
        '201' => '您输入的充值卡密码错误或充值卡余额不足',
        '202' => '您输入的充值卡已被使用',
        '203' => '您输入的充值卡密码非法',
        '204' => '您输入的卡号或密码错误次数过多',
        '205' => '卡号密码正则不匹配或者被禁止',
        '206' => '本卡之前被提交过,本次订单失败,不再继续处理',
        '207' => '暂不支持该充值卡',
        '208' => '您输入的充值卡卡号错误',
        '209' => '您输入的充值卡未激活(生成卡)',
        '210' => '您输入的充值卡已经作废(能查到有该卡,但是没卡的信息)',
        '211' => '您输入的充值卡已过期',
        '212' => '您选择的卡面额不正确',
        '213' => '该卡为特殊本地业务卡,系统不支持',
        '214' => '该卡为增值业务卡,系统不支持',
        '215' => '新生卡',
        '216' => '系统维护',
        '217' => '接口维护',
        '218' => '运营商系统维护',
        '219' => '系统忙,请稍后再试',
        '220' => '未知错误',
        '221' => '本卡之前被处理完毕,本次订单失败,不再继续处理',
    );


    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
		parent::init();
		$this->_merId = @$this->_config['merId'] ?:null;
		$this->_privateKey = @$this->_config['privateKey'] ?:"123456" ;
        $this->_reqUrl = @$this->_config['reqUrl'] ?:"http://pay3.shenzhoufu.com/interface/version3/serverconnszx/entry-noxml.aspx";
        $this->_des = @$this->_config['des'] ?: "fNCrhSynUm4=";
        $this->_version = "3";
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


		$this->_req_data['version'] = $this->_version;
		$this->_req_data['merId'] = $this->_merId;
		$this->_req_data['payMoney'] = $this->_order->order_amt*100;
		$this->_req_data['orderId'] = date("Ymd")."-".$this->_merId."-".$this->_order->id;
		$this->_req_data['returnUrl'] = $this->getNotifyUrl();
		$this->_req_data['cardInfo'] = $this->DesDesCardInfo($pay_param->getCardAmount(),$pay_param->getCardNumber(),$pay_param->getCardPassword(),$this->_des);
		$this->_req_data['privateField'] = $this->getCustomMsgOutString();
		$this->_req_data['verifyType'] = "1";
		$this->_req_data['md5String'] = $this->getReqMd5String($this->_req_data);

		$this->_req_data['merUserName'] = $this->_merUserName;
		$this->_req_data['merUserMail'] = $this->_merUserMail;
		$this->_req_data['cardTypeCombine'] = $this->getCardType($this->_method_id);

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
        if ($ret_data == "200") {
            return true;
        } 
        return false;
		// $ret_data = iconv("GB2312", 'UTF-8', $ret_data);
		// $attrs = explode("\n", $ret_data);
		// $data = array();
		// foreach ($attrs as $ts) {
		//     $tr = explode('=', trim($ts));
		//     if (count($tr) == 2) {
		// 	list($k, $v) = $tr;
		// 	$data[$k] = $v;
		//     }
		// }

		// // validate
		// if (!isset($data['md5String'])) {
		//     return false;
		// }
		// if ($data['md5String'] != $this->getReturnMd5String($data)) {
		//     return false;
		// }
		// if (isset($data['payResult']) && $data['payResult'] == '1') {
		//     return true;
		// } else {
		//     return false;
		// }
    }

    /**
     * get s99bill boss type for method
     *
     * @param $method_id:
     *
     * @return int  boss type or false
     */
    protected function getCardType($method_id) {
        switch ($method_id) {
            case "szx":
                return 0;
            case "unicom":
                return 1;
            case "telecom":
                return 2;
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
    protected function getReqMd5String($data) {
	   return $this->md5Data($this->_req_keys, $data);
    }

    /**
     * [md5Data description]
     * @param  [type] $keys [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function md5Data($keys,$data){
    	$old = "";
    	foreach ($keys as $k) {
    		$old .= isset($data[$k]) ? $data[$k] : "";
    	}
    	$old .= $this->_privateKey;
    	return md5($old);
    }

    // /**
    //  * function_description
    //  *
    //  *
    //  * @return
    //  */
    // public function getReturnMd5String($data) {
	   // return $this->md5Data($this->_return_keys, $data);
    // }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getCallbackMd5String($data) {
	   return $this->md5Data($this->_callback_keys, $data);
    }

    /**
     * valid callback
     *
     *
     * @return
     */
    public function validNotify() {
    	$data = $this->_notify_data;
    	if (!isset($data['md5String'])) {
    	    return false;
    	}
    	return $this->getCallbackMd5String($data) == $data['md5String'];
    }

    /**
     * check if pay has success
     *
     *
     * @return boolean
     */
    public function isNotifySuccess() {
	   return $this->validNotify() && isset($this->_notify_data['payResult']) && $this->_notify_data['payResult'] == '1';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethodIdFromNotify() {
        $order = $this->getNotifyOrder();
        $pay_param = unserialize($order->pay_param);
        return isset($pay_param['card_type']) ? $pay_param['card_type'] : null;
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

    /*
     * get order id from callback params
     *
     *
     * @return
     */
    public function getNotifyOrderId() {
	   return isset($this->_notify_data['orderId']) ? ltrim(strrchr($this->_notify_data['orderId'], "-"),"-") : null;
    }

    /**
     * get pay amount from callback params
     *
     *
     * @return
     */
    public function getPayAmount() {
        return $this->_notify_data['payMoney']? sprintf("%.2f",intval($this->_notify_data['payMoney']) * 1.0 / 100):null;

    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function setCustomMsgFromNotify() {
		if (isset($this->_notify_data['privateField'])) {
		    $this->setCustomMsgFromString($this->_notify_data['privateField']);
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
    	if (isset($this->_return_msg[$ret_data])) {
    	    return $this->_return_msg[$ret_data];
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
    	if (empty($this->_notify_data['errcode'])) {
    	    return "";
    	}
    	return @$this->_status_msg[$this->_notify_data['errcode']] ?: "";
    }


    public function DesDesCardInfo($cardmoney,$cardnum,$cardpwd,$deskey){

        $str=$cardmoney."@".$cardnum."@".$cardpwd;	
         $size = mcrypt_get_block_size('des', 'ecb');
         $input = $this->pkcs5_pad($str, $size);

         $td = mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式  
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);  
        $ks = mcrypt_enc_get_key_size($td);  
        $key=base64_decode($deskey);
        mcrypt_generic_init($td, $key, $iv); //初始处理  
        //加密  
        $encrypted_data = mcrypt_generic($td, $input);  

        //结束处理  
        mcrypt_generic_deinit($td);  
        mcrypt_module_close($td);
        /////作base64的编??        
        $encode = base64_encode($encrypted_data);
        return $encode;
    }

    function pkcs5_pad($text, $blocksize){    	
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);	
	}

}