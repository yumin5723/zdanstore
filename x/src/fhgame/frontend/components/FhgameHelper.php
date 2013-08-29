<?php
class FhgameHelper {

	public function buildUrl($url,$data = array()){
		$parsed = parse_url($url);
        isset($parsed['query'])?parse_str($parsed['query'],$parsed['query']):$parsed['query']=array();
        $params = isset($parsed['query'])?array_merge($parsed['query'], $data):$data;
        $parsed['query'] = ($params)?'?'.http_build_query($params):'';
        $server = isset($parsed['port'])?$parsed['host'].":".$parsed['port']:$parsed['host'];
        return $parsed['scheme'].'://'.$server.$parsed['path'].$parsed['query'];
	}

	public function requestApi($url,$params){
		$url = $this->buildUrl($url,$params);
		return file_get_contents($url);
	}

	public function verifymemberApi($url,$params){
		$url = $this->buildUrl($url,$params);
		return get_headers($url);
	}

	
}
