<?php
/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property integer $id
 * @property string $name
 * @property string $tags
 * @property string $desc
 * @property string $image
 * @property string $tag_image
 * @property string $url
 * @property string $created_uid
 * @property string $modified_uid
 * @property string $created
 * @property string $modified
 */
class Product extends CActiveRecord
{
    public $upload;
    public $upload1;
    public $count = 20;
    public $sub_pages = 6;
    public $cid=44;
    public $duration = 300;
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
            array('name,name_pinyin,category_id,publish_date,desc','required'),
            array('upload,upload1', 'file','allowEmpty'=>true),
            array('index_image,image,tag_image,down_url','safe'),
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
            'gameterm' => array(self::BELONGS_TO, 'Category', 'category_id'),
            'gamerules' => array(self::BELONGS_TO, 'GameRules', 'product_id'),
            'g_related' => array(self::HAS_ONE, 'GameRelated', 'product_id','condition'=>'g_related.group_id=20'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($type_id)
    {
        // print_r($ids);
        // print_r($type_id);exit;
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $type_id = Category::model()->getProductLevelTwo($type_id);
        $ids = Category::model()->getCategoryId($type_id);
        // var_dump($ids);exit;

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->addInCondition('category_id', $ids);
        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('name_pinyin',$this->name_pinyin,true);
        $criteria->compare('category_id',$this->category_id,true);
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
        if (!empty($attributes['name_pinyin']) && $attributes['name_pinyin'] != $this->name_pinyin) {
            $attrs[] = 'name_pinyin';
            $this->name_pinyin = $attributes['name_pinyin'];
        }
        if (!empty($attributes['down_url']) && $attributes['down_url'] != $this->down_url) {
            $attrs[] = 'down_url';
            $this->down_url= $attributes['down_url'];
        }
        if (!empty($attributes['category_id']) && $attributes['category_id'] != $this->category_id) {
            $attrs[] = 'category_id';
            $this->category_id = $attributes['category_id'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if (!empty($attributes['publish_date']) && $attributes['publish_date'] != $this->publish_date) {
            $attrs[] = 'publish_date';
            $this->publish_date = $attributes['publish_date'];
        }
        if (!empty($attributes['index_image']) && $attributes['index_image'] != $this->index_image) {
            $attrs[] = 'index_image';
            $this->index_image = $attributes['index_image'];
        }
        if (!empty($attributes['image']) && $attributes['image'] != $this->image) {
            $attrs[] = 'image';
            $this->image = $attributes['image'];
        }
        if (!empty($attributes['tag_image']) && $attributes['tag_image'] != $this->tag_image) {
            $attrs[] = 'tag_image';
            $this->tag_image = $attributes['tag_image'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }

    /**
     * [ImageUrl description]
     * @param [type] $image [description]
     */
    public function ImageUrl($image){
        return substr($image, 20);
    }
    /**
     * get category update url
     */
    static public function getUpdateUrl($id,$type_id){
        return "/product/update/id/".$id."/type_id/".$type_id;
    }

    /**
     * [getAllProduct description]
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    // public function getAllProduct($ids){
    //     $criteria = new Criteria;
    //     $criteria->addIn
    // }
    public function getSmallproduct($category_id){
        $cache_id='getSmallproduct'.$category_id;
        $smallproduct=Yii::app()->cache->get($cache_id);   
        if($smallproduct===false)
        {
            $criteria = new CDbCriteria;
            $criteria->alias = "t";
            $criteria->with='g_related';
            $criteria->select='t.id,t.name';
            $criteria->addCondition("t.category_id=$category_id");
            $criteria->group = 't.id'; 
            $criteria->limit = 30;
            $smallproduct = self::model()->findAll($criteria);
            Yii::app()->cache->set($cache_id,$smallproduct,$this->duration);
         }
        return $smallproduct; 
    }
    /**
     * get datas of left product rank
     * 
     */
    public function getBillproduct($category_id){
        $cache_id='getBillproduct'.$category_id;
        $billproduct=Yii::app()->cache->get($cache_id);                     
        if($billproduct===false)
        {
            $criteria = new CDbCriteria;
            $criteria->alias = "t";
            $criteria->with='g_related';
            $criteria->addCondition("t.category_id=$category_id");
            $criteria->order = "t.id DESC";
            $criteria->group = 't.id';
            $criteria->limit = 10;
            $billproduct = self::model()->findAll($criteria);
            Yii::app()->cache->set($cache_id,$billproduct,$this->duration);
         }
        return $billproduct; 
        
    }
    /**
     * 
     * get table product all datas
     */
    public function getAllproduct($type=false,$category_id=false,$pname=false,$page){
        $cache_id='getAllproduct'.$type.$category_id.$pname.$page;
        $datas=Yii::app()->cache->get($cache_id); 
        //print_r($datas);exit;
        if($datas===false)
        {
            $criteria = new CDbCriteria;
            $criteria->alias = "t";
            $criteria->with='g_related';

            $criteria->addCondition("t.name_pinyin like '$type%'");
            if($category_id){
                $criteria->addCondition("t.category_id=$category_id");
            }
            if($pname){
                $criteria->addSearchCondition('t.name',$pname);
            }
            $criteria->order = "t.id DESC";
            $criteria->group = 't.id'; 
            $criteria->limit = $this->count;
            $criteria->offset = ($page - 1) * $this->count;
            $datas =self::model()->findAll($criteria);
            Yii::app()->cache->set($cache_id,$datas,$this->duration);
         }
        return $datas; 
    }
    /**
     * 
     * get product all datas count
     */
    public function getCount($type=false,$category_id=false,$pname){
        $cache_id='getCount'.$type.$category_id.$pname;
        $counts=Yii::app()->cache->get($cache_id);                     
        if($counts===false)
        {
            $criteria = new CDbCriteria;
            $criteria->alias = "t";
            $criteria->with='g_related';
            if($type){
                $criteria->addCondition("t.name_pinyin like '$type%'");
            }
            if($category_id){
                $criteria->addCondition("t.category_id=$category_id");
            }
            if($pname){
                $criteria->addSearchCondition('t.name',$pname);
            }
            $counts = self::model()->count($criteria);
            Yii::app()->cache->set($cache_id,$counts,$this->duration);
         }
        return $counts; 
    }
    /**
     * get datas of related product
     * 
     */
    public function getPlayRelatedGame($category_id){
        $cache_id='getPlayRelatedGame'.$category_id;
        $relatedgame=Yii::app()->cache->get($cache_id);                     
        if($relatedgame===false)
        {
            $category=Category::model()->findByPk($category_id);
            $parent=$category->parent()->find()->id;
            $term_all_id = Category::model()->getChildTerm($parent);
            $criteria = new CDbCriteria;
            $criteria->alias = "t";
            $criteria->addInCondition('category_id',$term_all_id);
            $criteria->order = "t.id DESC";
            $criteria->limit = "6";
            $relatedgame = self::model()->findAll($criteria);
            Yii::app()->cache->set($cache_id,$relatedgame,$this->duration);
        }
        return $relatedgame; 
    }
    /**
     * get datas of hots product
     * 
     */
    public function getHotgame($category_id){
        $cache_id='getHotgame'.$category_id;
        $layouts=Yii::app()->cache->get($cache_id);                     
        if($layouts===false)
        {
            $criteria = new CDbCriteria;
            $criteria->alias = "t";
            $criteria->with='g_related';
            $criteria->addCondition("t.category_id=$category_id");
            $criteria->order = "t.id DESC";
            $criteria->group = 't.id'; 
            $criteria->limit = 4;
            $layouts= self::model()->findAll($criteria);
            Yii::app()->cache->set($cache_id,$layouts,$this->duration);
        }
                                         
        return $layouts; 
    }
}