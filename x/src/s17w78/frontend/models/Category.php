<?php

class Category extends CActiveRecord {
    const ERROR_ATTRIBUTE_VALUE = 3001;

    const ERROR_NOT_ALLOW_ATTRIBUTE = 3002;

    const ERROR_NOTHING_TO_MODIFY = 3003;

    const ERROR_UNKNOW = 3004;

    const ERROR_NOT_FOUND = 3005;

    const ERROR_SOME_NOT_FOUND = 3006;
    
    const PRODUCT_CATEGORY = 43;
    
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
        return 'category';
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
           array('description','safe'),
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
     *  add "|-" for view category
     * 
     */
    static public function str_tree($level,$name){
        $nav = '|';
        $result = str_repeat("-", $level*pow(2, 3));
        
        return $nav.$result.$name."(".($level-1)."级分类)";
    }


    /**
     * get category name all descendants node id
     * 
     * @param $term_name
     * @return array()
     */
    public function getAllTermId($term_id){
        $taxonomy_id = GameTaxonomy::model()->find()->taxonomy_id;
        $categories = GameTerm::model()->findAll('taxonomy_id = :id',array(':id'=>$taxonomy_id));
        $newArray = array();
        foreach($categories as $key => $ca){
            $newArray[$key]['term_id'] = $ca->term_id;
            $newArray[$key]['parent'] = $ca->parent;
            $newArray[$key]['name'] = $ca->name;
            $newArray[$key]['description'] = $ca->description;
            $newArray[$key]['slug'] = $ca->slug;
            $newArray[$key]['taxonomy_id'] = $ca->taxonomy_id;
        }
        $tree = new Tree($newArray);
        $arr = $tree->leaf($term_id);
        $this->insertDB($arr);
    }

    private function insertDB($array){
        foreach ($array as $key => $value) {
            $root = self::model()->findByPk($value['parent']+1);
            $cate = new Category;
            $cate->id = $value['term_id']+1;
            $cate->root = $value['taxonomy_id'];
            $cate->name = $value['name'];
            $cate->short_name = $value['slug'];
            $cate->description = $value['description'];
            $cate->admin_id = Yii::app()->user->id;
            if($cate->appendTo($root)){
                if (isset($value['child'])) {
                    $this->insertDB($value['child']);
                }
            }
        }
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
        //print_r($descendants);exit;
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
        $category=Category::model()->findByPk($term_id);
        $descendants=$category->descendants()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        return $allterms;
    }

    /**
     * [getProductLevelTwo description]
     * @param  [type] $category_id [description]
     * @return [type]              [description]
     */
    public function getProductLevelTwo($category_id){

        $category = Category::model()->findByPk($category_id);
        if(empty($category)){
            return null;
        }
        $type_id = $category->parent()->findByAttributes(array('level'=>2));
        return $type_id != NULL ? $type_id->id : $category_id;
    }

    /**
     * get category id all descendants node id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getCategoryId($term_id){
        // var_dump($term_id);exit;
        $category=Category::model()->findByPk($term_id);
        if(empty($category)){
            return null;
        }
        $descendants=$category->descendants()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        // var_dump($allterms);exit;
        array_push($allterms, $term_id);
        // print_r(array_push($allterms, $term_id));exit;
        return $allterms;
    }
     /**
     * get category name all children node (id,name)
     * 
     * @param $term_name
     * @return array()
     */
    public function getProductcate(){

        $category=Category::model()->findByPk(self::PRODUCT_CATEGORY);
        $descendants=$category->children()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[$value->id] = $value->name;
        }
        return $allterms;
    }
}