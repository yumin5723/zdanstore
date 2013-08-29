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
class Brand extends CmsActiveRecord
{
    public $upload;
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
        return 'brand';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,desc','required'),
            array('upload', 'file','allowEmpty'=>true),
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
            // 'gameterm' => array(self::BELONGS_TO, 'Category', 'category_id'),
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
        if (!empty($attributes['source']) && $attributes['source'] != $this->source) {
            $attrs[] = 'source';
            $this->source = $attributes['source'];
        }
        if (!empty($attributes['tags']) && $attributes['tags'] != $this->tags) {
            $attrs[] = 'tags';
            $this->tags= $attributes['tags'];
        }
        if (!empty($attributes['category_id']) && $attributes['category_id'] != $this->category_id) {
            $attrs[] = 'category_id';
            $this->category_id = $attributes['category_id'];
        }
        if (!empty($attributes['operations_guide']) && $attributes['operations_guide'] != $this->operations_guide) {
            $attrs[] = 'operations_guide';
            $this->operations_guide = $attributes['operations_guide'];
        }
        if (!empty($attributes['how_begin']) && $attributes['how_begin'] != $this->how_begin) {
            $attrs[] = 'how_begin';
            $this->how_begin = $attributes['how_begin'];
        }
        if (!empty($attributes['target']) && $attributes['target'] != $this->target) {
            $attrs[] = 'target';
            $this->target = $attributes['target'];
        }
        if (!empty($attributes['desc'])) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if (!empty($attributes['url'])) {
            $attrs[] = 'url';
            $this->url = $attributes['url'];
        }
        if (!empty($attributes['image'])) {
            $attrs[] = 'image';
            $this->image = $attributes['image'];
        }
        if (!empty($attributes['tag_image'])) {
            $attrs[] = 'tag_image';
            $this->tag_image = $attributes['tag_image'];
        }
        if (!empty($attributes['recommend_image'])) {
            $attrs[] = 'recommend_image';
            $this->recommend_image = $attributes['recommend_image'];
        }
        if (!empty($attributes['top_image'])) {
            $attrs[] = 'top_image';
            $this->top_image = $attributes['top_image'];
        }
        if (!empty($attributes['publish_date'])) {
            $attrs[] = 'publish_date';
            $this->publish_date = $attributes['publish_date'];
        }
        if (!empty($attributes['recommend_value'])) {
            $attrs[] = 'recommend_value';
            $this->recommend_value = $attributes['recommend_value'];
        }
        if (!empty($attributes['rank_value'])) {
            $attrs[] = 'rank_value';
            $this->rank_value = $attributes['rank_value'];
        }
        if (!empty($attributes['weights'])) {
            $attrs[] = 'weights';
            $this->weights = $attributes['weights'];
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