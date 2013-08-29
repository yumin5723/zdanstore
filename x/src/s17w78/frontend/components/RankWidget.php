<?php
class RankWidget extends CWidget{
	public $type;
	public $limit = 10;

	public function run(){
		$result = Rank::model()->getRankByType($this->type);
		if($this->limit > count($result)){
			$rank = $result;
		}else{
			$rank = array_slice($result, 0,$this->limit);
		}
		$this->render('rank',array('rank'=>$rank));
	}
}