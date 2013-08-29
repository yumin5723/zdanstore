<?php
Yii::import('gcommon.cms.models.Object');
class SearchController extends CController {
    const PER_PAGE = 10;
    /**
     * function_description
     *
     *
     * @return
     */
    public function actionIndex() {
        if ($q = $_GET['q']) {
            $sum = $this->getSearchCount($q);
            $searchCriteria = new stdClass();
            $pages = new CPagination($sum);
            $pages->pageSize = self::PER_PAGE;
            $searchCriteria->select = "*";
            $searchCriteria->query = $q;
            $searchCriteria->paginator = $pages;
            $searchCriteria->from = "main";
            $resArray = Yii::app()->search->searchRaw($searchCriteria);
            $sp_name = array();
            $sp_desc = array();

            // print_r($resArray);exit;
            foreach($resArray['matches'] as $values){
                $sp_name[] = $values['attrs']['object_name'];
                $sp_desc[] = $values['attrs']['object_description'];
            }
            $sp_name = Yii::app()->search->buildExcerpts($sp_name,"main",$q,array("before_match"=>"<em>","after_match"=>"</em>"));
            $sp_desc = Yii::app()->search->buildExcerpts($sp_desc,"main",$q,array("before_match"=>"<em>","after_match"=>"</em>"));

            $ret = array();
            $i = 0;
            foreach($resArray['matches'] as $key =>$value){
                    $ret[$key]['object_name'] = $sp_name[$i];
                    $ret[$key]['object_description'] = $sp_desc[$i];
                    $ret[$key]['url'] = $value['attrs']['url'];
                    $i++;
            }
            $this->render("index",array("results"=>$ret,"pages"=>$pages,"sum"=>$resArray['total'],"q"=>$q));
        }else{
            $this->render("index",array("results"=>array(),"pages"=>"","sum"=>0,"q"=>$q));
        }
    }
    public function getSearchCount($q){
        $searchCriteria = new stdClass();
        $pages = new CPagination();
        $pages->pageSize = self::PER_PAGE;
        $searchCriteria->select = "*";
        $searchCriteria->query = $q;
        $searchCriteria->paginator = $pages;
        $searchCriteria->from = "main";
        $resArray = Yii::app()->search->searchRaw($searchCriteria);

        return $resArray['total'];
    }

}