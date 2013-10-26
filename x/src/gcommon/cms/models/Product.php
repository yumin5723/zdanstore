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
    const PRODUCT_IS_NEW = 1;
    const PRODUCT_IS_NOT_NEW = 0;

    const PRODUCT_IS_RECOMMOND = 1;
    const PRODUCT_IS_NOT_RECOMMOND = 0;



    const PRODUCT_NOT_NEED_POSTAGE = 0;
    const PRODUCT_NEED_POSTAGE = 1;
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
            array('rank,batch_number,weight,give_points,points_buy,is_new,is_recommond,need_postage,special_price,special_begin,special_end,order','safe'),
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
        $criteria->compare('status',$this->status,true);
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
        if (!empty($attributes['is_new']) && $attributes['is_new'] != $this->is_new) {
            $attrs[] = 'is_new';
            $this->is_new = $attributes['is_new'];
        }
        if (!empty($attributes['is_recommond']) && $attributes['is_recommond'] != $this->is_recommond) {
            $attrs[] = 'is_recommond';
            $this->is_recommond = $attributes['is_recommond'];
        }
        if (!empty($attributes['need_postage']) && $attributes['need_postage'] != $this->need_postage) {
            $attrs[] = 'need_postage';
            $this->need_postage = $attributes['need_postage'];
        }
        if (!empty($attributes['weight']) && $attributes['weight'] != $this->weight) {
            $attrs[] = 'weight';
            $this->weight = $attributes['weight'];
        }
        if (!empty($attributes['order']) && $attributes['order'] != $this->order) {
            $attrs[] = 'order';
            $this->order = $attributes['order'];
        }
        if (!empty($attributes['give_points']) && $attributes['give_points'] != $this->give_points) {
            $attrs[] = 'give_points';
            $this->give_points = $attributes['give_points'];
        }
        if (!empty($attributes['points_buy']) && $attributes['points_buy'] != $this->points_buy) {
            $attrs[] = 'points_buy';
            $this->points_buy = $attributes['points_buy'];
        }
        if (!empty($attributes['special_price']) && $attributes['special_price'] != $this->special_price) {
            $attrs[] = 'special_price';
            $this->special_price = $attributes['special_price'];
        }
        if (!empty($attributes['special_begin']) && $attributes['special_begin'] != $this->special_begin) {
            $attrs[] = 'special_begin';
            $this->special_begin = $attributes['special_begin'];
        }
        if (!empty($attributes['special_end']) && $attributes['special_end'] != $this->special_end) {
            $attrs[] = 'special_end';
            $this->special_end = $attributes['special_end'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '商品名称',
            'desc' => '商品详情',
            'logo' => '商品首页图片',
            'status' => '商品状态',
            'is_new' => '是否新品',
            'is_recommond' => '是否推荐首页',
            'brand_id' => '所属品牌',
            'batch_number' => '商品批次',
            'quantity' => '商品库存',
            'total_price' => '市场价格',
            'shop_price' => '本站价格',
            'special_price' => '促销价',
            'special_begin' => '促销开始时间',
            'special_end' => '促销结束时间',
            'order' => '排序',
            'weight' => "商品重量",
            'give_points' => '赠送积分',
            'points_buy' => '允许积分购买',
            'need_postage' => '是否包邮',
        );
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
    /**
     * [convertProductIsNew description]
     * @param  [type] $isNew [description]
     * @return [type]        [description]
     */
    public function convertProductIsNew($isNew){
        if($isNew == 1){
            return "新品";
        }
        return "非新品";
    }
    /**
     * [convertProductIsNew description]
     * @param  [type] $isNew [description]
     * @return [type]        [description]
     */
    public function convertProductIsRecommond($isRecommond){
        if($isRecommond == 1){
            return "已推荐首页";
        }
        return "未推荐";
    }
    /**
     * get all product is new
     * @return [type] [description]
     */
    public function getIsNew(){
        return array(self::PRODUCT_IS_NOT_NEW => "非新品",self::PRODUCT_IS_NEW => "新品");
    }
    /**
     * get all product is recommond
     * @return [type] [description]
     */
    public function getIsRecommond(){
        return array(self::PRODUCT_IS_NOT_RECOMMOND => "未推荐",self::PRODUCT_NEED_POSTAGE => "推荐");
    }
     /**
     * get all product need postage
     * @return [type] [description]
     */
    public function getNeedPostage(){
        return array(self::PRODUCT_NOT_NEED_POSTAGE => "包邮",self::PRODUCT_IS_NEW => "非包邮");
    }
    /**
     * get products by brand
     */
    public function getAllProductsByBrand($brand_id)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('brand_id',$brand_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
        // return self::model()->findAllByAttributes(array('brand_id'=>$brand_id));
    }
    /**
     * get index recommond products
     * @param  integer $limit [description]
     * @return [type]         [description]
     */
    public function getAllRecommondProducts($limit = 5){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->limit = $limit;
        $criteria->condition = "is_recommond = :is_recommond";
        $criteria->params = array(":is_recommond"=>self::PRODUCT_IS_RECOMMOND);
        return self::model()->with('brand')->findAll($criteria);
    }
    /**
     * get product sum by brand id 
     * @param  [type] $brand_id [description]
     * @return [type]           [description]
     */
    public function getCountProductsByBrand($brand_id){
        $brand_id = intval($brand_id);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.brand_id = :brand_id";
        $criteria->params = array(":brand_id"=>$brand_id);
        return self::model()->count($criteria);
    }
    /**
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getProductsByBrand($brand_id,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->condition = "t.brand_id = :brand_id";
        $criteria->params = array(":brand_id"=>$brand_id);
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->with('brand')->findAll($criteria);
    }
    /**
     * fetch all objects by term id 
     * the data include term's children id 
     * @return [type] [description]
     */
    public function fetchProductsByTermIdAndBrand($term_id,$brand_id,$count,$page){
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
        $criteria->condition = "t.brand_id = :brand_id";
        $criteria->params = array(":brand_id"=>$brand_id);
        $criteria->addInCondition("t.id",$ids);
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->with('brand')->findAll($criteria);
    }
    /**
     * get objects count by term_id
     * @return [type] [description]
     */
    public function getProductsCountByTermIdAndBrand($term_id,$brand_id){
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$termIds);
        $results = ProductTerm::model()->findAll($criteria);
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->product_id;
        }
        $ids = array_unique($ids);

        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.brand_id = :brand_id";
        $criteria->params = array(":brand_id"=>$brand_id);
        $criteria->addInCondition("id",$ids);
        return self::model()->count($criteria);
    }
}