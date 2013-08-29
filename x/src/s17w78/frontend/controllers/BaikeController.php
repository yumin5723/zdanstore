<?php
class BaikeController extends CController {
    /**
     * @return array action filters
     */
    public function filters() {
        
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        
        return array(
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'index',
                    'list',
                    'detail',
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
     /**
     * product index
     * 
     * 
     */
    public function actionIndex(){
        $model = new Product;
        $term_small = Category::model()->getProductcate();
        $hots = $model->getHotgame($model->cid);
        $small_product = $model->getSmallproduct($model->cid);

        $type = isset($_GET['type'])?$_GET['type']:'';
        $cid = isset($_GET['cid'])?$_GET['cid']:'';
        $pname = isset($_GET['pname'])?$_GET['pname']:'';
        $count = $model->count;
        $sub_pages = $model->sub_pages;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $model->getCount($type,$cid,$pname);
        $all_product = $model->getAllproduct($type,$cid,$pname,$pageCurrent);
        if($cid){$url_cid = "/cid/$cid";}else{$url_cid='';}
        if($type){$url_type = "/type/$type";}else{$url_type='';}
        if($pname){$url_pname = "/pname/$pname";}else{$url_pname='';}
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/baike/index$url_type$url_cid$url_pname/p/",2);
        $p = $subPages->show_SubPages(2);
        $billboard = $model->getBillproduct($model->cid);
        $this->render('index',array('term_small'=>$term_small,'small_product'=>$small_product,'hots'=>$hots,'all_product'=>$all_product,'billboard'=>$billboard,'pages'=>$p,'pname'=>$pname,'type'=>$type,'cid'=>$cid));
    }
    /**
     * product detail
     * 
     * 
     */
    public function actionDetail(){
        Yii::import("backend.config.rules");
        $rules = require(Yii::getPathOfAlias("backend")."/config/rules.php");
        $model = new Product;
        $small_product = $model->getSmallproduct($model->cid);
        $billboard = $model->getBillproduct($model->cid);

        if(isset($_GET['id'])){
            $cache_id = "product".$_GET['id'];
            $datas = Yii::app()->cache->get($cache_id);
            if($datas===false){
                $datas = $model->findByPk($_GET['id']);
                if(empty($datas)){
                    return $this->redirect("/baike");
                }
                Yii::app()->cache->set($cache_id,$datas,$model->duration);
            }
            $hots = $model->getHotgame($model->cid);
            $related = GameRelated::model()->getGameRelated($_GET['id']);
            //$relatedgame = Product::model()->getPlayRelatedGame($model->cid);
            $game_rules = GameRules::model()->getGameRules($_GET['id'],$model->cid);
            if(!isset($related[10]))  $related[10]=array();   
            if(!isset($related[20]))  $related[20]=array();   
            $page_title = $datas->name."_". $datas->name."怎么玩_".$datas->name."怎么打_".$datas->name."玩法_".$datas->name."规则-棋牌百科_1378棋牌网";
            $keywords = $datas->name.','.$datas->name."怎么玩,".$datas->name."游戏怎么打,".$datas->name."玩法,".$datas->name ."规则,棋牌百科,1378棋牌网";
            $description = "1378棋牌网(www.1378.com)".$datas->name."百科为您介绍".$datas->name."的规则和玩法,教您学会怎么玩".$datas->name.",提高您对".$datas->name."打法的了解。";
            return $this->render('detail',array('small_product'=>$small_product,'model'=>$datas,'hots'=>$hots,'billboard'=>$billboard,'rules'=>$rules[44],'related'=>$related[20],'gamerelated'=>$related[10],"game_rules" => $game_rules,'p_title'=>$page_title,'p_k'=>$keywords,'p_d'=>$description));
        } 
        $this->redirect("/baike");
    }
}