<?php

class Oterm extends CmsActiveRecord {
    const ERROR_ATTRIBUTE_VALUE = 3001;

    const ERROR_NOT_ALLOW_ATTRIBUTE = 3002;

    const ERROR_NOTHING_TO_MODIFY = 3003;

    const ERROR_UNKNOW = 3004;

    const ERROR_NOT_FOUND = 3005;

    const ERROR_SOME_NOT_FOUND = 3006;

    public $parent_id;

    const LIST_PAGE_DISPLAY_COUNT = 30;
    const STATUS_PUBLISHED = 1;
    
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'oterm';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('name,short_name', 'required',),
           array('description,url,template_id','safe'),
        );
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'nestedSetBehavior'=>array(
                  'class'=>'gcommon.extensions.NestedSetBehavior',
                  'leftAttribute'=>'left_id',
                  'rightAttribute'=>'right_id',
                  'levelAttribute'=>'level',
              ),
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * create and save new category
     *
     *
     * @return array(boolean result, MError err)
     * result for if new category have created and saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function setCategory($category_id,$uid){
        $root = self::model()->findByPk($category_id);
        $model = new self;
        $model->name = $this->name;
        $model->short_name = $this->short_name;
        $model->description = $this->description;
        $model->admin_id = $uid;
        if($model->appendTo($root)){
            return array(true, null);
        }
    }
    /**
     * update and save new category
     *
     *
     * @return array(boolean result, MError err)
     * result for if new category have created and saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function updateCategory($data){
        if($data['template_id'] != 0){
            $data['url'] = "/list/".$this->id."_1.html";
        }
        return $this->saveAttributes($data);

    }
    /**
     *  add "|-" for view category
     * 
     */
    static public function str_tree($level,$name){
        $nav = '|';
        $result = str_repeat("-", $level*2);
        
        return $nav.$result.$name."(".($level-1)."级分类)";
    }
    /**
     * [getAllDescendantsByRoot description]
     * @param  [type] $root [description]
     * @return [type]       [description]
     */
    public function getAllDescendantsByRoot($root){
        $category = self::model()->findByPk($root);
        $descendants = $category->descendants()->findAll();
        $ret = array();
        foreach($descendants as $key=>$desc){
            $ret[$key]['id'] = $desc->id;
            $ret[$key]['name'] = $desc->name;
            $ret[$key]['level'] = $desc->level;
        }
        return $ret;
    }

    /**
     * get category name all descendants node id
     * 
     * @param $term_name
     * @return array()
     */
    public function getAllSmallTerms($term_name){
        $category = Category::model()->findByAttributes(array('level'=>2,'name'=>$term_name));
        $category=Category::model()->findByPk($category->id);
        $descendants=$category->descendants()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        return $allterms;
    }

    /**
     * get category name all children node (id,name)
     * 
     * @param $term_name
     * @return array()
     */
    public function getSecTermId($term_name){
        $category = Category::model()->findByAttributes(array('level'=>2,'name'=>$term_name));
        $category=Category::model()->findByPk($category->id);
        $descendants=$category->children()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[$value->id] = $value->name;
        }
        return $allterms;
    }

    /**
     * get category id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getTermId($term_name){
        $category = self::model()->findByAttributes(array('level'=>2,'name'=>$term_name));
        return $category->id;
    }

    /**
     * get category id all descendants node id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getChildTerm($term_id){
        $category=self::model()->findByPk($term_id);
        $descendants=$category->descendants()->findAll();
        $allterms = array();
        foreach ($descendants as $key=>$value) {
            $allterms[$key]['id'] = $value->id;
            $allterms[$key]['name'] = $value->name;
            $allterms[$key]['level'] = $value->level;
        }
        return $allterms;
    }
    /**
     * get level 
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function getLevelByTermId($term_id){
        $category= self::model()->findByPk($term_id);
        if(empty($category)){
            return array();
        }
        $descendants=$category->ancestors()->findAll();
        $ret = array();
        foreach($descendants as $key =>$desc){
            if($desc->level == 1){
                unset($desc);
            }else{
                $ret[$key]['term_name'] = $desc['name'];
                $ret[$key]['url'] = $desc['url'];
                $ret[$key]['id'] = $desc['id'];
            }
            
        }
        $self = array("term_name"=>$category->name,"url"=>$category->url,"id"=>$category->id);
        array_push($ret, $self);
        return $ret;
    }
    /**
     * get ancestors ids by term id
     * @param  [type] $term_id [description]
     * @return array ids
     */
    public function getAncestorsIdsByTerm($term_id){
        $category=Oterm::model()->findByPk($term_id);
        $descendants=$category->ancestors()->findAll();
        return array_map(function ($a){return $a->id;}, $descendants);
    }

    /**
     * [updateNode description]
     * @return [type] [description]
     */
    public function updateNode($parent,$newNode,$uid){
        if ($parent->id != $newNode) {
            $new = Oterm::model()->findByPk($newNode);
            $this->moveAsFirst($new);
        }
        return true;
    }
    /**
     * get template for category list
     * @return [type] [description]
     */
    public function getTemplates(){
        $templetes = Templete::model()->findAllByAttributes(array('type'=>1));
        $newArray = array();
        $newArray[0] = "暂不添加";
        foreach($templetes as $k => $templete){
            $newArray[$templete->id] = $templete->name;
        }
        return $newArray;
    }
    /**
     * display the content view 
     * @param  arrar $id  object_id
     * @return string
     */
    public function display($page){
        $template = Templete::model()->findByPk($this->template_id);
        if(empty($template)){
            return "";
        }
        return Yii::app()->cmsRenderer->render($this,$template->content,array("page"=>$page,"categoryid"=>$this->id));
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        $domain = Yii::app()->getModule("cms")->domain;
        $count = Object::model()->getObjectsCountByTermId($this->id);
        $offset = self::LIST_PAGE_DISPLAY_COUNT;
        $page = ceil($count/$offset);
        //only build 100 pages
        if($page > 100){
            $page = 100;
        }
        for($i=1;$i<=$page;$i++){
            $content = $this->display($i);
            $path = "list/".$this->id."_".$i.".html";
            $result = Yii::app()->publisher->saveDomainHtml($domain,$path,$content);
            if($result){
                // $this->firePublished();
                // return true;
            }else{
                Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
            }
        }
        $this->saveAttributes(array("status"=>self::STATUS_PUBLISHED));
        return true;
    }
   /**
     * function_description
     *
     * @param $id:
     *
     * @return
     */
    public function getAllTermIdsByTemplateId($id) {
        return array_map(function($t){return $t->id;},
            $this->findAllByAttributes(array('template_id'=>intval($id))));
    }

    /**
     * get page title
     */
    public function getTitle(){
        return $this->name."新闻中心_".$this->name."最新棋牌资讯_1378棋牌网";
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        return $this->name."新闻中心,最新".$this->name."信息,".$this->name."最新棋牌资讯,最新".$this->name."棋牌报道,1378棋牌网";
    }
    /**
     * get page description
     */
    public function getDescription(){
        return "1378棋牌网(www.1378.com)".$this->name."新闻中心栏目为您提供最新的".$this->name."信息,最新的".$this->name."棋牌新闻报道,让您可以及时了解".$this->name."最新棋牌资讯。";
    }
    /**
     * get level two term for brand chose
     * @return [type] [description]
     */
    public function getOtermLevelTwo(){
        $terms = self::model()->findAllByAttributes(array("level"=>"2"));
        return $terms;
    }
}