<?php
class SHttpRequest extends CHttpRequest{

	  	  	 
public function getUrl(){
 		return Yii::app()->request->hostInfo.$this->getRequestUri();
 	}
 }