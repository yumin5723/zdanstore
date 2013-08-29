<?php
require(dirname(__FILE__)."/../PayChannelAbstract.php");

abstract class PayChannelWebAbstract extends PayChannelAbstract {

    /**
     * function_description
     *
     *
     * @return
     */
    public function getReturnUrl() {
	return Yii::app()->payment->returnUrlPrefix."/".$this->channel_name;
    }


}