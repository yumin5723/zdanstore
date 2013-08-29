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
class Package extends CActiveRecord
{
    const BATCH_STATUS_OPEN = 0;
    const BATCH_STATUS_CLOSE = 1;
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
        return 'package';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,related,down_url,index_url,activate_url,publish_date,desc,detail,image,tag_image','required'),
            array('upload,upload1', 'file','allowEmpty'=>true),
            array('recommend_image','safe'),
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
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'upload' => '游戏图片',
            'tag_image' => '游戏图adsfasdf片',
            'upload1' => '游戏标签页图片',
            'name' => '礼包名称',
            'status' => '状态',
            'created' => '创建时间',
            'modified' => '修改时间',
            'related' => '运营平台',
            'down_url' => '下载链接',
            'index_url' => '官网首页',
            'activate_url' => '激活地址',
            'category_id' => '所属分类',
            'desc' => '游戏介绍',
            'detail' => '游戏详情',
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
        $criteria->compare('name',$this->name,false);

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
        if (!empty($attributes['related']) && $attributes['related'] != $this->related) {
            $attrs[] = 'related';
            $this->related = $attributes['related'];
        }
        if (!empty($attributes['down_url']) && $attributes['down_url'] != $this->down_url) {
            $attrs[] = 'down_url';
            $this->down_url= $attributes['down_url'];
        }
        if (!empty($attributes['index_url']) && $attributes['index_url'] != $this->index_url) {
            $attrs[] = 'index_url';
            $this->index_url = $attributes['index_url'];
        }
        if (!empty($attributes['activate_url']) && $attributes['activate_url'] != $this->activate_url) {
            $attrs[] = 'activate_url';
            $this->activate_url = $attributes['activate_url'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if (!empty($attributes['detail']) && $attributes['detail'] != $this->detail) {
            $attrs[] = 'detail';
            $this->detail = $attributes['detail'];
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

    public function updatestatus(){
        if ($this->status == self::BATCH_STATUS_CLOSE) {
            $this->status = self::BATCH_STATUS_OPEN;
            # code...
        } else {
            $this->status = self::BATCH_STATUS_CLOSE;
        }
        $this->save(false);
    }

    /**
     * get package info
     * @param  [type] $batch_number [description]
     * @return [type]               [description]
     */
    public function getPackageName($batch_number){
        $package_id = CodeBatch::model()->findByAttributes(array('batch_number'=>$batch_number))->package_id;
        return self::model()->findByPk($package_id);
    }

    /**
     * function_description
     *
     * @param $term_all_id:
     *
     * @return
     */
    public function getPagePackages($count,$page,$search){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->condition = "t.status = 0";
        if ($search != "") {
            $criteria->addSearchCondition('t.name',$search);
        }
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
    public function getCountPages($search){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.status = 0";
        if ($search != "") {
            $criteria->addSearchCondition('t.name',$search);
        }
        return self::model()->count($criteria);
    }
    public function getAllOpenPackages(){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->condition = "t.status = ".self::BATCH_STATUS_OPEN;
        $results = self::model()->findAll($criteria);
        if(empty($results)){
            return array();
        }
        $ret = array();
        foreach($results as $result){
            $ret[] = $result->id;
        }
        return $ret;
    }

    public function getRecommend($arr){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.status = 0";
        $criteria->limit = 4;
        $criteria->addInCondition('id',$arr);
        return self::model()->findAll($criteria);
    }

    public function getRecommendPackage(){
        $recommend = PackageRecommend::model()->findAll();
        $ids = "";
        foreach ($recommend as $value) {
            $ids .= $value->value.",";            
        }
        return $this->getRecommend(explode(",", trim($ids,",")));
    }

}