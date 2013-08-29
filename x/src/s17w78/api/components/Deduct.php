<?php
/**
 * the third part send request to api,then 
 *  verify request data and deduct user monery 
 *   send callback to third part url
 */
class Deduct {
	protected $_config = "";

    /**
     * function_description
     *
     *
     * @return
     */
    public function __construct() {
		$this->init();
    }
	/**
	 * init get need load data
	 * @return [type] [description]
	 */
	public function init(){
		$this->load_config();
	}
	/**
	 * load config
	 */
	public function load_config(){
		$this->_config = require_with_local(Yii::getPathOfAlias("config")."/partner.php"); 
	}
	/**
	 * verify request data
	 * params data
	 */
	protected function verifyData($data){
		if (!isset($data['hmac']) || !isset($data['merchantId'])) {
	    	return false;
		}
		$merchant_id = $data['merchantId'];
		$key = $this->getMerchantKeyById($merchant_id);
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
	 * deduct user gold
	 * @return boolean
	 */
	public function deductUserGold($data){
		if($this->verifyData($data)){

		}
	}
	/**
	 * get merchant key by id
	 * @param  [type] $merchant_id [description]
	 * @return 
	 */
	protected function getMerchantKeyById($merchant_id){

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
            'orderId',
            'merchantId',
            'orderAmount',
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
}