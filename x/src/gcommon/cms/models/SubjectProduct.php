<?php

class SubjectProduct extends CmsActiveRecord
{
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'subject_product';
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'created',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * Returns the static model of the specified AR class.
     * This method is required by all child classes of CActiveRecord.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'product'=>array(self::BELONGS_TO, 'Product',
                    'product_id'),
            'subject'=>array(self::BELONGS_TO, 'Subject',
                    'subject_id'),
        );
    }
    /**
     * set attribution validator rules
     *
     *
     * @return
     */
    public function rules() {
        $rules =  array(
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }
    /**
     * [fetchAllSelectProductsByBrandId description]
     * @param  [type] $brand_id [description]
     * @return [type]           [description]
     */
    public function fetchAllSelectProductsByBrandId($brand_id,$subjectid){
        $products = Product::model()->findAllByAttributes(array('brand_id'=>$brand_id,'status'=>Product::PRODUCT_STATUS_SELL));
        $ret = array();
        foreach($products as $product){
            $ret[] = $product->id;
        }

        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.subject_id = :subject_id";
        $criteria->params = array(":subject_id"=>$subjectid);
        $criteria->addIncondition("t.product_id",$ret);
        $data = self::model()->findAll($criteria);
        return array_map(function ($a){return $a->product_id;}, $data);
    }
    /**
     * [fetchAllSelectProductsByBrandId description]
     * @param  [type] $brand_id [description]
     * @return [type]           [description]
     */
    public function fetchAllSelectProductsByTermId($term_id,$subjectid){
        $ret = Product::model()->getProdcutIdsByTermId($term_id);

        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.subject_id = :subject_id";
        $criteria->params = array(":subject_id"=>$subjectid);
        $criteria->addIncondition("t.product_id",$ret);
        $data = self::model()->findAll($criteria);
        return array_map(function ($a){return $a->product_id;}, $data);
    }
    /**
     * [updateData description]
     * @param  [type] $subjectid [description]
     * @param  [type] $products  [description]
     * @return [type]            [description]
     */
    public function updateData($subjectid,$products){
        foreach($products as $product){

        }
    }
    /**
     * save terms for product
     * @param  intval $product_id 
     * @param  array $terms 
     * @return boolean
     */
    public function saveProductTerm($product_id,$terms){
        foreach ( $terms as $term ) {
            if (!self::model()->findByPk(array("product_id"=>$product_id,"term_id"=>$term))){
                $product_term=new self;
                $product_term->product_id=$product_id;
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
    public function updateSubjectProduct($subject_id,$products){

        foreach($products as $product){
            $subjects = self::model()->findAllByAttributes(array("product_id"=>$product));
        }
        foreach($subjects as $subject){
            if($subject_id != $subject->id){
                $product = Product::model()->findByPk($subject->product_id);
                return array(false,$product->name);
            }
        }
        // get current dependence
        $current = $this->getProductsBySubjectId($subject_id);
        // calculate need to delete
        $to_del = array_diff($current, $products);
        // calculate need to insert
        $to_insert = array_diff($products, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeTerms($subject_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addTerms($subject_id,$to_insert);
        }
        return array(true,"");
    }
    /**
     * get terms of page id 
     *
     * @param $page_id:
     *
     * @return
     */
    protected function getProductsBySubjectId($subject_id) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('product_id')
                     ->from($this->tableName())
                     ->where(array('and',
                             'subject_id=:subject_id',
                         ),
                         array(
                             ':subject_id'=>$subject_id,
                         ))
                     ->queryAll();
        return array_map(function($a){return $a['product_id'];},$rows);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function addTerms($subject_id,$products) {
        if (empty($products)) {
            return true;
        }
        $sql = "INSERT INTO " . $this->tableName() . " (subject_id,subject_type,product_id) VALUES (:subject_id,:subject_type,:product_id)";
        $cmd = $this->getDbConnection()->createCommand($sql);
        if (!is_array($products)) {
            $products = array($products);
        }
        $subject = Subject::model()->findByPk($subject_id);
        $cmd->bindValue(":subject_id", $subject_id);
        $cmd->bindValue(":subject_type",$subject->type);
        foreach ($products as $id) {
            if(empty($id)){
                continue;
            }
            $cmd->bindValue(":product_id", $id);
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
    protected function removeTerms($subject_id,$products) {
        if (empty($products)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE subject_id=:subject_id ";
        $sql .= " AND product_id in ('".implode("','",$products)."')";
        if (!is_array($products)) {
            $products = array($products);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":subject_id", $subject_id);
        return $cmd->execute();
    }
        /**
     * get sale count
     * @return [type] [description]
     */
    public function getCountSales(){
        $criteria = new CDbCriteria;
        $criteria->condition = "subject_type = :subject_type";
        $criteria->params = array(":subject_type"=>Subject::SUBJECT_TYPE_DISCOUNT);
        return SubjectProduct::model()->count($criteria);
    }
    /**
     * [getSaleProducts description]
     * @param  [type] $count       [description]
     * @param  [type] $pageCurrent [description]
     * @return [type]              [description]
     */
    public function getSaleProducts($count,$pageCurrent){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->condition = "subject_type = :subject_type";
        $criteria->params = array(":subject_type"=>Subject::SUBJECT_TYPE_DISCOUNT);
        $criteria->limit = $count;
        $criteria->offset = ($pageCurrent - 1) * $count;
        return SubjectProduct::model()->with('product')->findAll($criteria);
    }
}