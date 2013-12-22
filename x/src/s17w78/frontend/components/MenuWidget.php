<?php
class MenuWidget extends CWidget{
	public function run(){
		$brands = Brand::model()->getBrandsForIndex(18);
		$mensTerms = Oterm::model()->getMensTreeMenu();
		$womensTerms = Oterm::model()->getWomensTreeMenu();
		$hatsTerms = Oterm::model()->getHatsTreeMenu();
		$hatsBrands = Brand::model()->getHatsBrands();
		$newarrivals = Newarrivals::model()->findAll();
		$mensad = Click::model()->getAdsByType(Click::AD_POSITION_MENU_MENS,1);
		$womenad = Click::model()->getAdsByType(Click::AD_POSITION_MENU_WOMENS);
		$hatsad = Click::model()->getAdsByType(Click::AD_POSITION_MENU_HATS,1);
		$this->render("menu",array('brands'=>$brands,'mensterms'=>$mensTerms,'womensterms'=>$womensTerms,
			'hatsterms'=>$hatsTerms,'hatsbrands'=>$hatsBrands,'newarrivals'=>$newarrivals,
			'mensad'=>$mensad,"womensad"=>$womenad,'hatsad'=>$hatsad,
			));
		// $this->render("menu");
	}
}