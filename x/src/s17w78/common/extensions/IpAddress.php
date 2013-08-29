<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class IpAddress extends CComponent{

    
    public function init(){
    }
    
    private function get_ip_address($ip){

        $api_url = Yii::app()->params['taobao_url'];
        $params = array('ip' => $ip);
        $ret = Yii::app()->curl->get($api_url, $params);
        $result = json_decode($ret);
        if($result->code == 1){
            return false;
        }
        $address = array(
            "ip" => $result->data->ip,
            "country" => $result->data->country,
            "country_id" => $result->data->country_id,
            "area" => $result->data->area,
            "province" => $result->data->region,
            "city" => $result->data->city,
            "isp" => $result->data->isp
        );
        return $address;
    }
    /*
     * @params $ip string into ip
     * return if country is China,so province . else country 
     */
    public function get_address($ip){
        $ret = $this->get_ip_address($ip);
        if($ret !== false){
            if($ret['country_id'] == "CN"){
                return $ret['province'];
            } else {
                return $ret['country'];
            }
        }
    }
    
    /*
     * @params $ip string into ip
     * return if country is China ,so province and city.else country 
     */
    public function get_province_city($ip){
        $ret = $this->get_ip_address($ip);
        if($ret !== false){
            if($ret['country_id'] == "CN"){
                return array($ret['province'],$ret['city']);
            } else {
                return $ret['country'];
            }
        }
    }
    
    
}
?>
