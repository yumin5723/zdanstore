<?php

require_once(Yii::getPathOfAlias('common.extensions.spay.channels')."/PayChannelAbstract.php");

class Mhpay extends PayChannelAbstract {

    public $channel_name="mhpay";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        parent::init();
    }

    /**
     * function_description
     *
     *
     * @return [url, data]
     */
    public function getOuterData() {
        
    }
    /**
     * deduct user gold by order
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public function deduct(){
        $uid = $this->_order->uid;
        $gold = UserGoldTotal::model()->findByPk($uid);
        if(empty($gold)|| $gold->gold < $this->_order->order_amt){
            return false;
        }
        $amount = intval(0 - $this->_order->order_amt);
        if(UserGold::model()->spendGold($uid,$amount,"-1",$this->_order->id,"deduct gold for order id: ".$this->_order->id) == UserGoldTxn::SPEND_GOLD_SUCCESS){
            return true;
        }
        return false;
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function validNotify() {
        
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function isNotifySuccess() {
    
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethodIdFromNotify() {
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnStringForNotify() {
        return "success";
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function setCustomMsgFromNotify() {
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNotifyOrderId() {
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getPayAmount() {
         return $this->_order->order_amt;
    }
    public function getRealAmount() {
        $method_id = $this->channel_name;
        if (empty($method_id)) {
            return false;
        }
        if (!isset($this->_config['methods'][$method_id]['price']['real'])) {
            return $this->getPayAmount();
        } else {
            return floatval($this->getPayAmount()) * floatval($this->_config['methods'][$method_id]['price']['real']);
        }
    }
    public function getChargeAmount() {
        $method_id = $this->channel_name;
        if (empty($method_id)) {
            return false;
        }
        if (!isset($this->_config['methods'][$method_id]['price']['charge'])) {
            return $this->getPayAmount();
        } else {
            return floatval($this->getPayAmount()) * floatval($this->_config['methods'][$method_id]['price']['charge']);
        }

    }






}