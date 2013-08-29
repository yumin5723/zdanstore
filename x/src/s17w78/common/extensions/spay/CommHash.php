<?php

class CommHash extends CComponent {

    protected $_hashAlgo = null;

    /**
     * function_description
     *
     * @param $hash_algo:
     *
     * @return
     */
    public function __construct($hash_algo) {
	$this->_hashAlgo = $hash_algo;
    }

    /**
     * hash data array
     * for keys in $keys, use $hash_key
     *
     * @param array $data:
     * @param array $keys:
     * @param string $hash_key:
     *
     * @return string
     */
    public function hashData($data, $keys, $hash_key) {
	$old_str = "";
	foreach ($keys as $k) {
	    $old_str .= isset($data[$k]) ? $data[$k] : "";
	}

	return hash_hmac($this->_hashAlgo, $old_str, $hash_key);
    }



}