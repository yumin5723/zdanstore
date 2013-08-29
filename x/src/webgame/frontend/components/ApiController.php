<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class ApiController extends CController
{
    //key 
    protected $_key;
    /*
    public function init() {
        parent::init();
        $this->_key = "hmac";
        $this->requestValidte();
    }
    /**
     * validate request 
     *
     *
     * @return
     
    public function requestValidte(){
        throw new CHttpException(400,'Invalid request'); 
    }
    */
}