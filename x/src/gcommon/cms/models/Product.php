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
            array('name,brand_id,status,quantity,shop_price,total_price,desc','required'),
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
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getallgames($term_all_id){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$term_all_id);
        $criteria->order = "t.id DESC";
        $criteria->limit = "28";
        return self::model()->findAll($criteria);
    }

    /**
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getlistgames($term_all_id,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$term_all_id);
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->findAll($criteria);
    }

    /**
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getCountGames($term_all_id){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$term_all_id);
        return self::model()->count($criteria);
    }

    /**
     * function_description
     *
     * @return
     */
    public function getRecommend(){
        $criteria=new CDbCriteria;
        $criteria->alias = 't';
        $criteria->condition = "t.recommend_value != ''";
        $criteria->order = "t.recommend_value DESC";
        $criteria->limit = '44';
        $all = self::model()->findAll($criteria);
        return $all;
    }
    /**
     * function_description
     *
     * @return
     */
    public function getRecommend2(){
        $criteria=new CDbCriteria;
        $criteria->alias = 't';
        $criteria->condition = "t.recommend_value != ''";
        $criteria->order = "t.recommend_value DESC";
        $criteria->limit = '22';
        $criteria->offset = '23';
        $all = self::model()->findAll($criteria);
        return $all;
    }

    /**
     * function_description
     *
     * @return
     */
    public function getRank(){
        $criteria=new CDbCriteria;
        $criteria->alias = 't';
        $criteria->condition = "t.rank_value != ''";
        $criteria->order = "t.rank_value DESC";
        $criteria->limit = '10';
        $all = self::model()->findAll($criteria);
        return $all;
    }


    /**
     * function_description category
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getAllGame($term_name){

        $categories = Category::model()->getAllSmallTerms($term_name);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$categories);
        $criteria->order = "t.id DESC";
        $criteria->limit = "28";
        return self::model()->findAll($criteria);
    }

    /**
     * function_description game detail related category
     *
     * @return
     */
    public function getDetailRelatedGame($category_id){

        $category=Category::model()->findByPk($category_id);
        $parent=$category->parent()->find()->id;
        $term_all_id = Category::model()->getChildTerm($parent);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$term_all_id);
        $criteria->order = "t.id DESC";
        $criteria->limit = "8";
        return self::model()->findAll($criteria);
    }

    /**
     * function_description game play related category
     *
     * @return
     */
    public function getPlayRelatedGame($category_id){
        $category=Category::model()->findByPk($category_id);
        $parent=$category->parent()->find()->id;
        $term_all_id = Category::model()->getChildTerm($parent);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$term_all_id);
        $criteria->order = "t.id DESC";
        $criteria->limit = "12";
        return self::model()->findAll($criteria);
    }

    /**
     * [getIndexShowGame description]
     * @param  [type] $type [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public function getIndexShowGame($type,$id){

        $game_term = Category::model()->getCategoryId($id);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$game_term);
        $criteria->order = "t.id DESC";
        $criteria->limit = "28";
        return self::model()->findAll($criteria);
    }
}