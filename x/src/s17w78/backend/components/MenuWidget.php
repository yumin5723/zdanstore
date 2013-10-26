<?php
class MenuWidget extends CWidget{
	public function run(){
		$brands = Brand::model()->getBrandsForIndex();
		$mensTerms = Oterm::model()->getMensTreeMenu();
		$womensTerms = Oterm::model()->getWomensTreeMenu();
		$hatsTerms = Oterm::model()->getHatsTreeMenu();
		$hatsBrands = Brand::model()->getHatsBrands();
		$this->render("menu",array('brands'=>$brands,'mensterms'=>$mensTerms,'womensterms'=>$womensTerms,'hatsterms'=>$hatsTerms,'hatsbrands'=>$hatsBrands));
		// $this->render("menu");
	}
}