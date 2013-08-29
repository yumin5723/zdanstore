<?php
Yii::import("common.models.Package");
Yii::import("common.models.TaoHao");
Yii::import("common.models.ActiveCode");
Yii::import("common.models.CodeBatch");
class TaoHaoCommand extends CConsoleCommand{
	/**
	 * build can tao code in redis
	 */
	public function run($args) {
        $this->buildTaoCode();
        
    }
    public function buildTaoCode(array $packages=array()){
    	if(empty($packages)){
        	$package_ids = Package::model()->getAllOpenPackages();
        }
        foreach($package_ids as $id){
        	echo sprintf("update for package id: %d ",$id);
        	if(TaoHao::model()->updateByPackageId($id)){
        		echo "success.\n";
        	}else{
        		echo "fail.\n";
        	}
        }
        echo "all finished.\n";
    }
}