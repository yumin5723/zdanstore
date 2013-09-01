<?php

/**
 * This is the model class for table "{{product_meta}}".
 *
 * The followings are the available columns in table '{{product_meta}}':
 */
class ProductMeta extends CActiveRecord
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
        return 'product_meta';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
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
            // 'product_term'=>array(self::BELONGS_TO, 'Oterm',
            //         'term_id'),
            // 'product'=>array(self::BELONGS_TO, 'Product',
            //         'product_id'),
        );
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
    public function getAllMetaRefProduct($product_id) {
        return array_map(function($meta){return $meta->meta_key;},
            $this->findAllByAttributes(array('meta_product_id'=>intval($product_id))));
    }
    /**
     * get all metas by product id
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function getAllMetasByProductId($product_id){
        $metas = self::model()->findAllByAttributes(array('meta_product_id'=>$product_id));
        return $metas;
    }
    /**
     * save meta for product
     * @param  intval $product_id 
     * @param  array $meta 
     * @return boolean
     */
    public function saveProductMeta($product_id,$metas){
        foreach ( $metas as $meta ) {
            if (!self::model()->findByPk(array("meta_product_id"=>$product_id,"key"=>$meta['key']))){
                $product_meta = new self;
                $product_meta->meta_product_id = $product_id;
                $product_meta->meta_key = $meta['key'];
                $product_meta->meta_value = $meta['value'];
                $product_meta->save(false);
            }
        }
    }
    /**
     * update metas for object
     * @param  intval $product_id 
     * @param  array $metas 
     * @return boolean
     */
    public function updateProductMeta($product_id,$metas){
        $currents = $this->getAllMetaRefProduct($product_id);
        $ret = array();
        foreach($metas as $meta){
            $ret[] = $meta['key'];
        }
        $to_del = array_diff($currents, $ret);
        // save to db
        if (!empty($to_del)) {
            $this->removeMetas($product_id,$to_del);
        }
        $model = new self;
        foreach($metas as $meta){
            $data = self::model()->findByAttributes(array('meta_product_id'=>$product_id,'meta_key'=>$meta['key']));
            // print_r($data->meta_value);
            if(empty($data)){
                $model->meta_product_id = $product_id;
                $model->meta_key = $meta['key'];
                $model->meta_value = $meta['value'];
                $model->save(false);
            }else{
                if($data->meta_value != $meta['value']){
                    $data->meta_value = $meta['value'];
                    $data->save(false);
                }
            }
        }

    }
    /**
     * get metas of product id 
     *
     * @param $product_id:
     *
     * @return
     */
    protected function getMetasByProduct($product_id) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('meta_key')
                     ->from($this->tableName())
                     ->where(array('and',
                             'product_id=:product_id',
                         ),
                         array(
                             ':product_id'=>$product_id,
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
        $sql = "INSERT INTO " . $this->tableName() . " (product_id,term_id) VALUES (:product_id,:term_id)";
        $cmd = $this->getDbConnection()->createCommand($sql);
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd->bindParam(":product_id", $model_id);
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
    protected function removeMetas($product_id,$metas) {
        if (empty($metas)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE meta_product_id=:product_id ";
        $sql .= " AND meta_key in ('".implode("','",$metas)."')";
        if (!is_array($metas)) {
            $metas = array($metas);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":product_id", $product_id);
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

}