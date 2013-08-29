<?php
class RecommendWidget extends CWidget{
	public function run(){
		// public function getRightRecommend(){
		$bigValue = PackageRecommend::model()->findByPk("1")->value;
		$smallValue = PackageRecommend::model()->findByPk("2")->value;
		$bigArr = explode(",", $bigValue);
		$smallArr = explode(",", $smallValue);
		$smallRecommend = Package::model()->getRecommend($smallArr);
		$bigRecommend = Package::model()->getRecommend($bigArr);
		// return array($bigRecommend,$smallRecommend);
		$this->render("recommend",array('bigRecommend'=>$bigRecommend,'smallRecommend'=>$smallRecommend));
	}
}