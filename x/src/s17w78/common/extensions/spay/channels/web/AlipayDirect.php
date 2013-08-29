<?php

require_once("PayChannelWebAbstract.php");
class AlipayDirect extends PayChannelWebAbstract {
    public $channel_name = 'alipaydirect';

    /**
     * HTTPS形式消息验证地址
     */
    protected $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    /**
     * HTTP形式消息验证地址
     */
    protected $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';

    protected $_partner;

    protected $_key;

    protected $_seller_email;

    protected $_alipay_gateway_new = "https://mapi.alipay.com/gateway.do?";

    protected $_fixed_req_data = array(
	"service" => "create_direct_pay_by_user",
	"payment_type"=>"1",
	"_input_charset"=>"utf-8",
    );

    protected $_response_cache = array();

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
	$this->_seller_email = trim($this->_config['email']);
    }

    /**
     * data post to server
     *
     *
     * @return [url, data] or err
     */
    public function getOuterData() {
	$para_temp = array(
	    "partner" => $this->_partner,
	    "notify_url" => $this->getNotifyUrl(),
	    "return_url" => $this->getReturnUrl(),
	    "seller_email" => $this->_seller_email,
	    "out_trade_no" => $this->_order->id,
	    "subject" => $this->_order->subject?:"充值",
	    "extra_common_param" => $this->getCustomMsgOutString(),
	    "total_fee" => $this->_order->order_amt,
	);
    $para_temp += $this->_fixed_req_data;
    $data = $this->buildRequestPara($para_temp);
	return [$this->_alipay_gateway_new, $data];
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function validNotify() {
	$data = $this->_notify_data;
	$isSign = $this->getSignVerify($data, $data['sign']);
	$responseTxt = 'true';
	if (!empty($data['notify_id'])) {
	    $responseTxt = $this->getResponse($data['notify_id']);
	}
	if (preg_match("/true$/i",$responseTxt) && $isSign) {
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
    public function isNotifySuccess() {
	if (!$this->validNotify()) {
	    return false;
	}
	$trade_status = $this->_notify_data['trade_status'];
	if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
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


    public function setCustomMsgFromNotify() {
	if (isset($this->_notify_data['extra_common_param'])) {
	    $this->setCustomMsgFromString($this->_notify_data['extra_common_param']);
	}
    }


    public function getNotifyOrderId() {
	return isset($this->_notify_data['out_trade_no']) ? $this->_notify_data['out_trade_no']:null;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getPayAmount() {
	return isset($this->_notify_data['total_fee'])?$this->_notify_data['total_fee']:null;
    }


    /* ===============================================================================
       from alipay_core.function.php
       ===============================================================================
    */

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
	    $arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);

	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected function createLinkstringUrlencode($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
	    $arg.=$key."=".urlencode($val)."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);

	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
    }
    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    protected function paraFilter($para) {
	$para_filter = array();
	while (list ($key, $val) = each ($para)) {
	    if($key == "sign" || $key == "sign_type" || $val == "")continue;
	    else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
    }
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    protected function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
    }
    /**
     * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
     * 注意：服务器需要开通fopen配置
     * @param $word 要写入日志里的文本内容 默认值：空值
     */
    protected function logResult($word='') {
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
    }

    /**
     * 远程获取数据，POST模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * @param $para 请求的数据
     * @param $input_charset 编码格式。默认值：空值
     * return 远程输出的数据
     */
    protected function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

	if (trim($input_charset) != '') {
	    $url = $url."_input_charset=".$input_charset;
	}
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl,CURLOPT_POST,true); // post传输数据
	curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
	$responseText = curl_exec($curl);
	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	curl_close($curl);

	return $responseText;
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    protected function getHttpResponseGET($url,$cacert_url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
	$responseText = curl_exec($curl);

	/* var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容 */
	curl_close($curl);

	return $responseText;
    }

    /**
     * 实现多种字符编码方式
     * @param $input 需要编码的字符串
     * @param $_output_charset 输出的编码格式
     * @param $_input_charset 输入的编码格式
     * return 编码后的字符串
     */
    protected function charsetEncode($input,$_output_charset ,$_input_charset) {
	$output = "";
	if(!isset($_output_charset) )$_output_charset  = $_input_charset;
	if($_input_charset == $_output_charset || $input ==null ) {
	    $output = $input;
	} elseif (function_exists("mb_convert_encoding")) {
	    $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
	} elseif(function_exists("iconv")) {
	    $output = iconv($_input_charset,$_output_charset,$input);
	} else die("sorry, you have no libs support for charset change.");
	return $output;
    }
    /**
     * 实现多种字符解码方式
     * @param $input 需要解码的字符串
     * @param $_output_charset 输出的解码格式
     * @param $_input_charset 输入的解码格式
     * return 解码后的字符串
     */
    protected function charsetDecode($input,$_input_charset ,$_output_charset) {
	$output = "";
	if(!isset($_input_charset) )$_input_charset  = $_input_charset ;
	if($_input_charset == $_output_charset || $input ==null ) {
	    $output = $input;
	} elseif (function_exists("mb_convert_encoding")) {
	    $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
	} elseif(function_exists("iconv")) {
	    $output = iconv($_input_charset,$_output_charset,$input);
	} else die("sorry, you have no libs support for charset changes.");
	return $output;
    }

    /* ===============================================================================
       from alipay_md5.function.php
       ===============================================================================
    */
    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    protected function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
    }

    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    protected function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr . $key;
	$mysgin = md5($prestr);
	if($mysgin == $sign) {
	    return true;
	}
	else {
	    return false;
	}
    }

    /* ===============================================================================
       from alipay_submit.class.php
       ===============================================================================
    */
    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    protected function buildRequestMysign($para_sort) {
	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = $this->createLinkstring($para_sort);

	$mysign = $this->md5Sign($prestr, $this->_key);

	return $mysign;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    protected function buildRequestPara($para_temp) {
	//除去待签名参数数组中的空值和签名参数
	$para_filter = $this->paraFilter($para_temp);

	//对待签名参数数组排序
	$para_sort = $this->argSort($para_filter);

	//生成签名结果
	$mysign = $this->buildRequestMysign($para_sort);

	//签名结果与签名方式加入请求提交参数组中
	$para_sort['sign'] = $mysign;
	$para_sort['sign_type'] = "MD5";

	return $para_sort;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组字符串
     */
    protected function buildRequestParaToString($para_temp) {
	//待请求参数数组
	$para = $this->buildRequestPara($para_temp);

	//把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
	$request_data = createLinkstringUrlencode($para);

	return $request_data;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
    /*
     * function buildRequestForm($para_temp, $method, $button_name) {
     *     //待请求参数数组
     *     $para = $this->buildRequestPara($para_temp);
     *
     *     $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->alipay_gateway_new."_input_charset=".trim(strtolower($this->alipay_config['input_charset']))."' method='".$method."'>";
     *     while (list ($key, $val) = each ($para)) {
     *         $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
     *     }
     *
     *     //submit按钮控件请不要含有name属性
     *     $sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
     *
     *     $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
     *
     *     return $sHtml;
     * }
     */

    /**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果
     * @param $para_temp 请求参数数组
     * @return 支付宝处理结果
     */
    protected function buildRequestHttp($para_temp) {
	$sResult = '';

	//待请求参数数组字符串
	$request_data = $this->buildRequestPara($para_temp);

	//远程获取数据
	$sResult = getHttpResponsePOST($this->alipay_gateway_new, $this->alipay_config['cacert'],$request_data,trim(strtolower($this->alipay_config['input_charset'])));

	return $sResult;
    }

    /**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果，带文件上传功能
     * @param $para_temp 请求参数数组
     * @param $file_para_name 文件类型的参数名
     * @param $file_name 文件完整绝对路径
     * @return 支付宝返回处理结果
     */
    protected function buildRequestHttpInFile($para_temp, $file_para_name, $file_name) {

	//待请求参数数组
	$para = $this->buildRequestPara($para_temp);
	$para[$file_para_name] = "@".$file_name;

	//远程获取数据
	$sResult = getHttpResponsePOST($this->alipay_gateway_new, $this->alipay_config['cacert'],$para,trim(strtolower($this->alipay_config['input_charset'])));

	return $sResult;
    }

    /**
     * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
     * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
     * return 时间戳字符串
     */
    protected function query_timestamp() {
	$url = $this->alipay_gateway_new."service=query_timestamp&partner=".trim(strtolower($this->alipay_config['partner']));
	$encrypt_key = "";

	$doc = new DOMDocument();
	$doc->load($url);
	$itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
	$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;

	return $encrypt_key;
    }

    /* ===============================================================================
       from alipay_notify.class.php
       ===============================================================================
    */
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
    protected function verifyNotify(){
	if(empty($_POST)) {//判断POST来的数组是否为空
	    return false;
	}
	else {
	    //生成签名结果
	    $isSign = $this->getSignVerify($_POST, $_POST["sign"]);
	    //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
	    $responseTxt = 'true';
	    if (! empty($_POST["notify_id"])) {$responseTxt = $this->getResponse($_POST["notify_id"]);}

	    //写日志记录
	    //if ($isSign) {
	    //	$isSignStr = 'true';
	    //}
	    //else {
	    //	$isSignStr = 'false';
	    //}
	    //$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
	    //$log_text = $log_text.createLinkString($_POST);
	    //logResult($log_text);

	    //验证
	    //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
	    //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
	    if (preg_match("/true$/i",$responseTxt) && $isSign) {
		return true;
	    } else {
		return false;
	    }
	}
    }

    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
    protected function verifyReturn(){
	if(empty($_GET)) {//判断POST来的数组是否为空
	    return false;
	}
	else {
	    //生成签名结果
	    $isSign = $this->getSignVerify($_GET, $_GET["sign"]);
	    //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
	    $responseTxt = 'true';
	    if (! empty($_GET["notify_id"])) {$responseTxt = $this->getResponse($_GET["notify_id"]);}

	    //写日志记录
	    //if ($isSign) {
	    //	$isSignStr = 'true';
	    //}
	    //else {
	    //	$isSignStr = 'false';
	    //}
	    //$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
	    //$log_text = $log_text.createLinkString($_GET);
	    //logResult($log_text);

	    //验证
	    //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
	    //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
	    if (preg_match("/true$/i",$responseTxt) && $isSign) {
		return true;
	    } else {
		return false;
	    }
	}
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVerify($para_temp, $sign) {
	//除去待签名参数数组中的空值和签名参数
	$para_filter = $this->paraFilter($para_temp);

	//对待签名参数数组排序
	$para_sort = $this->argSort($para_filter);

	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = $this->createLinkstring($para_sort);

	$isSgin = $this->md5Verify($prestr, $sign, $this->_key);

	return $isSgin;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse($notify_id) {
	if (isset($this->_response_cache[$notify_id])) {
	    return $this->_response_cache[$notify_id];
	}
	$transport = "https";
	$partner = $this->_partner;
	$veryfy_url = '';
	if($transport == 'https') {
	    $veryfy_url = $this->https_verify_url;
	}
	else {
	    $veryfy_url = $this->http_verify_url;
	}
	$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
	$responseTxt = $this->getHttpResponseGET($veryfy_url, $this->getCacert());

	/* cache response */
	$this->_response_cache[$notify_id] = $responseTxt;

	return $responseTxt;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getCacert() {
	return dirname(__FILE__)."/alipay_cacert.pem";
    }


}
