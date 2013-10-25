<?php
class MenuWidget extends CWidget{
	public function run(){
		$brands = Brand::model()->getBrandsForIndex();
		$mensTerms = Oterm::model()->getMensTreeMenu();
		$this->render("menu",array('brands'=>$brands,'mensterms'=>$mensTerms));
		// $this->render("menu");
	}
}