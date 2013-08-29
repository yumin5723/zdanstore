<?php
/**
 * This is the model class for table "game".
 *
 * The followings are the available columns in table 'game':
 * @property integer $id
 * @property string $name
 * @property string $tags
 * @property string $from
 * @property string $desc
 * @property string $operations_guide
 * @property string $how_begin
 * @property string $target
 * @property string $image
 * @property string $tag_image
 * @property string $url
 * @property string $created_uid
 * @property string $modified_uid
 * @property string $created
 * @property string $modified
 */
class MobileGame extends CActiveRecord
{
    public $upload;
    public $upload1;
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
        return 'mobile_game';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,version,apply,size,category_id,publish_date,desc,lang,developers,resolution,image,tag_image,advertising,url_to_pc','required'),
            array('upload,upload1', 'file','allowEmpty'=>true),
            array('price,url_to_mobile,recommend_value,rank_value,recommend_image,top_image','safe')
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'upload' => '游戏图片',
            'upload1' => '游戏标签页图片',
            'name' => '游戏名',
            'version' => '版本',
            'apply' => '适用',
            'size' => '大小',
            'lang' => '语言',
            'developers' => '开发商',
            'resolution' => '分 辨 率',
            'advertising' => '内置广告',
            'developers' => '开发商',
            'category_id' => '所属分类',
            'rank_value'=>'排行值',
            'recommend_value'=>'推荐值',
            'desc' => '游戏介绍',
            'times' => '下载次数',
            'publish_date'=>'发布日期',
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
        $criteria->compare('category',$this->category_id,true);

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
        if (!empty($attributes['version']) && $attributes['version'] != $this->version) {
            $attrs[] = 'version';
            $this->version = $attributes['version'];
        }
        if (!empty($attributes['size']) && $attributes['size'] != $this->size) {
            $attrs[] = 'size';
            $this->size= $attributes['size'];
        }
        if (!empty($attributes['category_id']) && $attributes['category_id'] != $this->category_id) {
            $attrs[] = 'category_id';
            $this->category_id = $attributes['category_id'];
        }
        if (!empty($attributes['lang']) && $attributes['lang'] != $this->lang) {
            $attrs[] = 'lang';
            $this->lang = $attributes['lang'];
        }
        if (!empty($attributes['developers']) && $attributes['developers'] != $this->developers) {
            $attrs[] = 'developers';
            $this->developers = $attributes['developers'];
        }
        if (!empty($attributes['resolution']) && $attributes['resolution'] != $this->resolution) {
            $attrs[] = 'resolution';
            $this->resolution = $attributes['resolution'];
        }
        if (!empty($attributes['advertising']) && $attributes['advertising'] != $this->advertising) {
            $attrs[] = 'advertising';
            $this->advertising = $attributes['advertising'];
        }
        if (!empty($attributes['size']) && $attributes['size'] != $this->size) {
            $attrs[] = 'size';
            $this->size = $attributes['size'];
        }
        if (!empty($attributes['url_to_pc']) && $attributes['url_to_pc'] != $this->url_to_pc) {
            $attrs[] = 'url_to_pc';
            $this->url_to_pc = $attributes['url_to_pc'];
        }
        if (!empty($attributes['url_to_mobile']) && $attributes['url_to_mobile'] != $this->url_to_mobile) {
            $attrs[] = 'url_to_mobile';
            $this->url_to_mobile = $attributes['url_to_mobile'];
        }
        if (!empty($attributes['recommend_value']) && $attributes['recommend_value'] != $this->recommend_value) {
            $attrs[] = 'recommend_value';
            $this->recommend_value = $attributes['recommend_value'];
        }
        if (!empty($attributes['rank_value']) && $attributes['rank_value'] != $this->rank_value) {
            $attrs[] = 'rank_value';
            $this->rank_value = $attributes['rank_value'];
        }
        if (!empty($attributes['desc'])) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if (!empty($attributes['image'])) {
            $attrs[] = 'image';
            $this->image = $attributes['image'];
        }
        if (!empty($attributes['tag_image'])) {
            $attrs[] = 'tag_image';
            $this->tag_image = $attributes['tag_image'];
        }
        if (!empty($attributes['top_image'])) {
            $attrs[] = 'top_image';
            $this->top_image = $attributes['top_image'];
        }
        if (!empty($attributes['recommend_image'])) {
            $attrs[] = 'recommend_image';
            $this->recommend_image = $attributes['recommend_image'];
        }
        if (!empty($attributes['publish_date'])) {
            $attrs[] = 'publish_date';
            $this->publish_date = $attributes['publish_date'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }

    /**
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getRankgames($term_all_id){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$term_all_id);
        $criteria->order = "t.rank_value DESC";
        $criteria->limit = "10";
        return self::model()->findAll($criteria);
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
        $criteria->order = "t.id";
        $criteria->limit = '48';
        $all = self::model()->findAll($criteria);
        return $all;
    }

    /**
     * function_description
     *
     * @return
     */
    public function getRank($parent_id){
        $term_all_id = Category::model()->getChildTerm($parent_id);
        $criteria=new CDbCriteria;
        $criteria->alias = 't';
        $criteria->condition = "t.rank_value != ''";
        $criteria->addInCondition('category_id',$term_all_id);
        $criteria->order = "t.rank_value DESC";
        $criteria->limit = '10';
        $all = self::model()->findAll($criteria);
        return $all;
    }

    /**
     * function_description
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
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getRankGame($term_name){
        $categories = Category::model()->getAllSmallTerms($term_name);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$categories);
        $criteria->order = "t.rank_value DESC";
        $criteria->limit = "10";
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
     * [getIndexShowGame description]
     * @param  [type] $type [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public function getIndexShowMobileGame($type,$id){

        $game_term = Category::model()->getCategoryId($id);
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addInCondition('category_id',$game_term);
        $criteria->order = "t.id DESC";
        $criteria->limit = "28";
        return self::model()->findAll($criteria);
    }
}