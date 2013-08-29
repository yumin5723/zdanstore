<?php

abstract class CmsFilter{

	public function init(){
		
	}
    abstract public function filter($value, $options=array());
}