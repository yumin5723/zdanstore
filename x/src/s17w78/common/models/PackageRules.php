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
class PackageRules extends CActiveRecord
{
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
        return 'package_rules';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('package_id,probability,limit,','required'),
            array('probability', 'numerical', 'integerOnly'=>true, 'message'=>'must be int', 'max'=>100, 'min'=>0),
            array('limit', 'numerical', 'integerOnly'=>true, 'message'=>'must be int',),
            // array('recommend_image','safe'),
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
            'package' => array(self::BELONGS_TO, 'Package', 'package_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'package_id' => '礼包名称',
            'probability' => '概率',
            'limit' => '每人每天可领取次数',
            // 'upload' => '游戏图片',
            // 'upload1' => '游戏标签页图片',
            // 'name' => '游戏名',
            // 'tags' => '标签',
            // 'batch_number' => '批次名称',
            // 'source' => '来源',
            // 'category_id' => '所属分类',
            // 'desc' => '游戏介绍',
            // 'operations_guide' => '操作指南',
            // 'how_begin' => '如何开始',
            // 'target' => '游戏目标',
            // 'publish_date'=>'发布日期',
            // 'weights'=> '权重',
            // 'recommend_value'=>'顶部推荐值',
            // 'rank_value'=>'排行榜推荐值',
        );
    }
    /**
     * get code probability 
     * @param  [type] $package_id [description]
     * @return [type]             [description]
     */
    public function getProbability($package_id){
        $coderule = self::model()->findByPk($package_id);
        if(empty($coderule)){
            return 100;
        }
        return $coderule->probability;
    }
    /**
     * get code limit 
     * @param  [type] $package_id [description]
     * @return [type]             [description]
     */
    public function getLimit($package_id){
        $coderule = self::model()->findByPk($package_id);
        if(empty($coderule)){
            return null;
        }
        return $coderule->limit;
    }
}