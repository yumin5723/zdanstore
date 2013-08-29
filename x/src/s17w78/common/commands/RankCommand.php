<?php
Yii::import("common.models.Package");
Yii::import("common.models.TaoHao");
Yii::import("common.models.ActiveCode");
Yii::import("common.models.CodeBatch");
Yii::import("common.models.Rank");
class RankCommand extends CConsoleCommand{
	/**
	 * build can tao code in redis
	 */
	public function run($args) {
        $this->buildRankCode();
        
    }
    public function buildRankCode(array $packages=array()){
    	$result = ActiveCode::model()->getCodeRank();
        $model = new Rank;
        $model->type = Rank::CODE_GET_RANK;
        $model->value= serialize($result);
        $model->created = date('Y-m-d H:i:s');
        $model->modified = date('Y-m-d H:i:s');
        if($model->save(false)){
            echo "code rank is created successfull.\n";
        }
    }   
}