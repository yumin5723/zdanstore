<?php
/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 */
class Product extends CmsActiveRecord
{

    const PRODUCT_STATUS_SELL = 0;
    const PRODUCT_STATUS_SOLDOUT = 1;
    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
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
        return 'product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,brand_id,status,logo,quantity,shop_price,total_price,desc','required'),
            array('rank,batch_number','safe'),
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
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('brand_id',$this->brand_id,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if (!empty($attributes['brand_id']) && $attributes['brand_id'] != $this->brand_id) {
            $attrs[] = 'brand_id';
            $this->brand_id = $attributes['brand_id'];
        }
        if (!empty($attributes['rank']) && $attributes['rank'] != $this->rank) {
            $attrs[] = 'rank';
            $this->rank = $attributes['rank'];
        }
        if (!empty($attributes['status']) && $attributes['status'] != $this->status) {
            $attrs[] = 'status';
            $this->status = $attributes['status'];
        }
        if (!empty($attributes['batch_number']) && $attributes['batch_number'] != $this->batch_number) {
            $attrs[] = 'batch_number';
            $this->batch_number = $attributes['batch_number'];
        }
        if (!empty($attributes['quantity']) && $attributes['quantity'] != $this->quantity) {
            $attrs[] = 'quantity';
            $this->quantity = $attributes['quantity'];
        }
        if (!empty($attributes['total_price']) && $attributes['total_price'] != $this->total_price) {
            $attrs[] = 'total_price';
            $this->total_price = $attributes['total_price'];
        }
        if (!empty($attributes['shop_price']) && $attributes['shop_price'] != $this->shop_price) {
            $attrs[] = 'shop_price';
            $this->shop_price = $attributes['shop_price'];
        }
        if (!empty($attributes['logo']) && $attributes['logo'] != $this->logo) {
            $attrs[] = 'logo';
            $this->logo = $attributes['logo'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * get all product status
     * @return [type] [description]
     */
    public function getStatus(){
        return array(self::PRODUCT_STATUS_SELL => "正常销售",self::PRODUCT_STATUS_SOLDOUT => "售完下架");
    }
    /**
     * get all brands
     * @return [type] [description]
     */
    public function getBrands(){
        $brands = Brand::model()->findAll();
        $ret = array();
        foreach($brands as $brand){
            $ret[$brand->id] = $brand->name;
        }
        return $ret;
    }
    /**
     * get all products can buy 
     * status is self::PRODUCT_STATUS_SELL
     * @return [type] [description]
     */
    public function getAllProductsCanBuy(){
        return self::model()->findAllByAttributes(array('status'=>self::PRODUCT_STATUS_SELL));
    }

    /**
     * get product count by term_id
     * @return [type] [description]
     */
    public function getProductsCountByTermId($term_id){
        // $termIds = $this->getAllChindrenIdByTermId($term_id);
        // $criteria = new CDbCriteria;
        // $criteria->alias = "t";
        // $criteria->addInCondition("term_id",$termIds);
        // return ObjectTerm::model()->count($criteria);
        // 
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$termIds);
        $criteria->order = "t.product_id DESC";
        $results = ProductTerm::model()->findAll($criteria);
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->product_id;
        }
        $ids = array_unique($ids);
        return count($ids);
    }
    /**
     * get category id all descendants node id include self id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getAllChindrenIdByTermId($term_id){
        // var_dump($term_id);exit;
        $category = Oterm::model()->findByPk($term_id);
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
     * fetch all products by term id 
     * the data include term's children id 
     * @return [type] [description]
     */
    public function fetchProductsByTermId($term_id,$count,$page){
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$termIds);
        $criteria->order = "t.product_id DESC";
        $results = ProductTerm::model()->findAll($criteria);
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->product_id;
        }
        $ids = array_unique($ids);


        $criteria = new CDbCriteria;
        $criteria->alias = "t";

        $criteria->addInCondition("id",$ids);
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->findAll($criteria);
    }
}