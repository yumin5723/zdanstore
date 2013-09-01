<?php

/**
 * This is the model class for table "{{product_image}}".
 *
 * The followings are the available columns in table '{{product_image}}':
 */
class ProductImage extends CActiveRecord
{
    const PRODUCT_IMAGE_TYPE_ALBUM = 0;
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
        return 'product_image';
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
    public function getAllImagesRefProduct($product_id,$type = self::PRODUCT_IMAGE_TYPE_ALBUM) {
        return array_map(function($image){return $image->image;},
            $this->findAllByAttributes(array('product_id'=>intval($product_id),'image_type'=>$type)));
    }
    /**
     * get all metas by product id
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function getAllImagesByProductId($product_id,$type = self::PRODUCT_IMAGE_TYPE_ALBUM){
        $images = self::model()->findAllByAttributes(array('product_id'=>$product_id,'image_type'=>$type));
        return $images;
    }
    /**
     * save meta for product
     * @param  intval $product_id 
     * @param  array $images 
     * @return boolean
     */
    public function saveProductImages($product_id,$images,$type = self::PRODUCT_IMAGE_TYPE_ALBUM){
        foreach ( $images as $image ) {
            $product_meta = new self;
            $product_meta->product_id = $product_id;
            $product_meta->image = $image['image'];
            $product_meta->image_type = $type;
            $product_meta->save(false);
        }
    }
    /**
     * update metas for object
     * @param  intval $product_id 
     * @param  array $images
     * @return boolean
     */
    public function updateProductImages($product_id,$images,$type = self::PRODUCT_IMAGE_TYPE_ALBUM){
        //to delete image
        self::model()->deleteAllByAttributes(array('product_id'=>$product_id,'image_type'=>$type));
        $model = new self;
        foreach($images as $image){
            $model = new self;
            $model->product_id = $product_id;
            $model->image = $image['image'];
            $model->image_type = $type;
            $model->save(false);
        }
        return true;
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
    protected function removeImages($product_id,$images) {
        if (empty($images)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE product_id=:product_id ";
        $sql .= " AND image in ('".implode("','",$images)."')";
        if (!is_array($images)) {
            $images = array($images);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":product_id", $product_id);
        return $cmd->execute();
    }
    /**
     * [removeAllImages description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function removeAllImages($product_id,$type = self::PRODUCT_IMAGE_TYPE_ALBUM){
        self::model()->deleteAllByAttributes(array('product_id'=>$product_id,'image_type'=>$type));
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