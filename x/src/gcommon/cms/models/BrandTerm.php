<?php

/**
 * This is the model class for table "{{object_term}}".
 *
 * The followings are the available columns in table '{{object_term}}':
 * @property string $object_id
 * @property string $term_id
 * @property integer $term_order
 */
class BrandTerm extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return ObjectTerm the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'brand_term';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

            array('brand_id, term_id', 'length', 'max'=>20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('brand_id, term_id', 'safe', 'on'=>'search'),
        );
    }

    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
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
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
           'brand' => array(self::BELONGS_TO, 'Brand','','on'=>'t.brand_id = brand.id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'brand_id' =>  t('cms','Brand'),
            'term_id' =>  t('cms','Term'),
            'data' =>  t('cms','Data'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('brand_id',$this->brand_id,true);
        $criteria->compare('term_id',$this->term_id,true);
        $criteria->compare('data',$this->data,true);


        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    /**
     * save terms for object
     * @param  intval $object_id
     * @param  array $terms
     * @return boolean
     */
    public function saveObjectTerm1($object_id,$terms){
        $ids = array_unique(array_reduce($terms, function($r, $t) {
            foreach (Oterm::model()->getAncestorsIdsByTerm($t) as $id) {
                $r[] = $id;
            }
            return $r;
         }, $terms));
        foreach ( $ids as $id ) {
            if (!ObjectTerm::model()->findByPk(array("object_id"=>$object_id,"term_id"=>$id))){
                $obj_term=new ObjectTerm();
                $obj_term->object_id=$object_id;
                $obj_term->term_id=$id;
                $obj_term->save(false);
            }
        }
    }
    /**
     * update terms for object
     * @param  intval $object_id
     * @param  array $terms
     * @return boolean
     */
    public function updateObjectTerm1($object_id,$terms){
        ObjectTerm::model()->deleteAll('object_id = :id',array(':id'=>$object_id));
        $this->saveObjectTerm($object_id,$terms);
        return true;
    }
    /**
     * get all object ids by Term id include it's child
     * @param  $term_id
     * @return array object ids
     */
    public function getObjectIdsByTermId($term_id){
        $ids = Oterm::model()->getChildTerm($term_id);
        array_push($ids, $term_id);
        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$ids);

        $object_terms = self::model()->findAll($criteria);
        $oids = array();
        foreach($object_terms as $term){
            $oids[] = $term->object_id;
        }
        $nids = array_unique($oids);
        return $nids;
    }
    /**
     * fetch all objects by termid include its child term id
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function fetchObjectsByTermid($term_id){
        $objectIds = $this->getObjectIdsByTermId($term_id);
        $criteria = new CDbCriteria;
        $criteria->order = "object_id DESC";
        $criteria->addInCondition("object_id",$objectIds);

        return new CActiveDataProvider('Object', array(
                'criteria' => $criteria,
                'pagination'=>array(
                              'pageSize'=>20,
                          ),
            ) );
    }

    /**
     * function_description
     *
     * @param $product_id:
     *
     * @return
     */
    public function getAllTermsRefObject($brand_id) {
        return array_map(function($term){return $term->term_id;},
            $this->findAllByAttributes(array('brand_id'=>intval($brand_id))));
    }
    /**
     * save terms for product
     * @param  intval $product_id 
     * @param  array $terms 
     * @return boolean
     */
    public function saveBrandTerm($brand_id,$terms){
        foreach ( $terms as $term ) {
            if (!self::model()->findByPk(array("brand_id"=>$brand_id,"term_id"=>$term))){
                $product_term=new self;
                $product_term->brand_id=$brand_id;
                $product_term->term_id=$term;
                $product_term->save(false);
            }
        }
    }
    /**
     * update terms for object
     * @param  intval $product_id 
     * @param  array $terms 
     * @return boolean
     */
    public function updateBrandTerm($brand_id,$terms){

        
        // get current dependence
        $current = $this->getTermsByBrand($brand_id);
        // calculate need to delete
        $to_del = array_diff($current, $terms);
        // calculate need to insert
        $to_insert = array_diff($terms, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeTerms($brand_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addTerms($brand_id,$to_insert);
        }
        return true;
    }
    /**
     * get terms of page id 
     *
     * @param $page_id:
     *
     * @return
     */
    protected function getTermsByBrand($brand_id) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('term_id')
                     ->from($this->tableName())
                     ->where(array('and',
                             'brand_id=:brand_id',
                         ),
                         array(
                             ':brand_id'=>$brand_id,
                         ))
                     ->queryAll();
        return array_map(function($a){return $a['term_id'];},$rows);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function addTerms($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql = "INSERT INTO " . $this->tableName() . " (brand_id,term_id) VALUES (:brand_id,:term_id)";
        $cmd = $this->getDbConnection()->createCommand($sql);
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd->bindParam(":brand_id", $model_id);
        foreach ($termids as $id) {
            if(empty($id)){
                continue;
            }
            $cmd->bindParam(":term_id", $id);
            $cmd->execute();
        }
        return true;
    }


    /**
     * function_description
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     * @param $dep_ids:
     *
     * @return
     */
    protected function removeTerms($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE brand_id=:brand_id ";
        $sql .= " AND term_id in ('".implode("','",$termids)."')";
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":brand_id", $model_id);
        return $cmd->execute();
    }
    /**
     * get ancestors ids by object id
     * @param  [type] $term_id [description]
     * @return array ids
     */
    public function getAncestorsIdsByObject($product_id){
        $criteria = new CDbCriteria;
        $criteria->condition = "object_id = :object_id";
        $criteria->params = array(":object_id"=>$object_id);
        $criteria->order = "term_id DESC";
        // $criteria->limit = 1;
        $results = ObjectTerm::model()->findAllByAttributes(array(),$criteria);
        if(empty($results)){
            return array();
        }
        $newArray = array();
        foreach($results as $result){
            $category= Oterm::model()->findByPk($result->term_id);
            $descendants=$category->ancestors()->findAll();
            $ret =  array_map(function ($a){return $a->id;}, $descendants);
            array_push($ret,$result->term_id);
            $newArray = array_merge($newArray,$ret);

        }
        $newArray = array_unique($newArray);
        // $category= Oterm::model()->findByPk($result->term_id);
        // if(empty($category)){
        //     return array();
        // }
        // $descendants=$category->ancestors()->findAll();
        // $ret =  array_map(function ($a){return $a->id;}, $descendants);
        // array_push($ret,$result->term_id);
        return $newArray;
    }
    /**
     * [getBrandTerms description]
     * @return [type] [description]
     */
    public function getBrandTerms($brand_id){
        $terms = self::model()->findAllByAttributes(array("brand_id"=>$brand_id));
        $result = array();
        foreach($terms as $key=>$term){

            $result[$key]['child'] = Oterm::model()->getChildTerm($term->term_id);
            $result[$key]['name'] = Oterm::model()->findByPk($term->term_id)->name;
            $result[$key]['id'] = Oterm::model()->findByPk($term->term_id)->id;
        }
        // print_r($result);exit;
        return $result;
    }
    /**
     * get brands by term id
     * @param  [type] $termid [description]
     * @return [type]         [description]
     */
    public function getBrandsByTermId($termid){
        $termid = intval($termid);
        $brands = self::model()->with('brand')->findAllByAttributes(array("term_id"=>$termid));
        return $brands;
    }
    /**
     * [getBrandsByTerm description]
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function getBrandsByTerm($term_id){
        $category= Oterm::model()->findByPk($term_id);
        $descendants=$category->ancestors()->findAll();
        $ret =  array_map(function ($a){return $a->id;}, $descendants);
        array_push($ret,$term_id);
        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$ret);
        $brands = self::model()->with('brand')->findAll($criteria);
        return $brands;
    }
}