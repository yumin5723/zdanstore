<?php
class MenuWidget extends CWidget{
	public function run(){
		$brands = Brand::model()->getBrandsForIndex();
		$this->render("menu",array('brands'=>$brands));
		// $this->render("menu");
	}
}