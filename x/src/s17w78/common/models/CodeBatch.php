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
class CodeBatch extends CActiveRecord
{
    public $upload;
    public $upload1;
    const BATCH_STATUS_CLOSE = 0;
    const BATCH_STATUS_OPEN = 1;
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
        return 'activecode_batch';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,related,down_url,index_url,publish_date,desc,detail,image,tag_image','required'),
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
            'tags' => '标签',
            'batch_number' => '批次名称',
            'source' => '来源',
            'category_id' => '所属分类',
            'desc' => '游戏介绍',
            'operations_guide' => '操作指南',
            'how_begin' => '如何开始',
            'target' => '游戏目标',
            'publish_date'=>'发布日期',
            'weights'=> '权重',
            'recommend_value'=>'顶部推荐值',
            'rank_value'=>'排行榜推荐值',
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
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }

    public function saveCodeBatch($package_id,$username,$fileName){
        $batch = new self;
        $batch->package_id = $package_id;
        $batch->batch_number = date('YmdHis').'-'.$username;
        $batch->status = CodeBatch::BATCH_STATUS_CLOSE;
        if ($batch->save(false)) {
            ActiveCode::model()->importCsv($fileName,$package_id,$batch->batch_number);
            return true;
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
     * function_description
     * 
     * @param $game_id:
     * @param $type:
     *
     * @return 
     */
    public function getFreeAvailBatchIds($package_id) {
        $abs = self::model()->findAllByAttributes(array(
                   'package_id'=> $package_id,
                   'status' => self::BATCH_STATUS_OPEN,
               ));
        $ret = array();
        foreach($abs as $v){
            $ret[] = $v->batch_number;
        }
        return $ret;
    }
}