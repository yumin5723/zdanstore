<?php

require(dirname(__FILE__)."/../PayChannelAbstract.php");

abstract class PayChannelCardAbstract extends PayChannelAbstract {

    /* valid http response */
    abstract public function checkResponse($ret_data);

}