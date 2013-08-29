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
 * @property string $created_uid
 * @property string $modified_uid
 * @property string $created
 * @property string $modified
 */
class GameRules extends CActiveRecord
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
        return 'game_rules';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id,area_id,type_id','required'),
            array('desc','safe'),
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
        return array(
            );
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
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
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('source',$this->source,true);
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
        if (!empty($attributes['type_id']) && $attributes['type_id'] != $this->type_id) {
            $attrs[] = 'type_id';
            $this->type_id = $attributes['type_id'];
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
     * [saveRelated description]
     * @param  [type] $data       [description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function saveRules($data,$product_id){

        foreach ($data as $value) {
            if (isset($value['type_id']) && !empty($value['type_id'])) {
                $model = new GameRules;
                $model->product_id = $product_id;
                $model->area_id = $value['area_id'];
                $model->type_id = serialize($value['type_id']);
                $model->desc = $value['desc'];
                $model->save();
            }
        }
        return true;
    }
    /**
     * [getGameRules description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getGameRules($id,$type_id){

        Yii::import("backend.config.rules");
        $rules = require(Yii::getPathOfAlias("backend")."/config/rules.php");
        $newArray = array();
        foreach($rules[$type_id] as $k => $gid){
            $results = self::model()->findAllByAttributes(array("product_id"=>$id,"area_id"=>$k));
            foreach($results as $key=>$result){
                $newArray[$k]['id'] = $result->id;
                $newArray[$k]['product_id'] = $result->product_id;
                $newArray[$k]['area_id'] = $result->area_id;
                $newArray[$k]['type_id'] = unserialize($result->type_id);
                $newArray[$k]['desc'] = $result->desc;
            }
        }
        return $newArray;
    }

    /**
     * [updateRules description]
     * @param  [type] $data       [description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function updateRules($data,$product_id){

        foreach ($data as $single_text_area) {

            if (!empty($single_text_area['id'])) {
                $model = self::model()->findByPk($single_text_area['id']);
                $single_text_area['type_id'] = serialize($single_text_area['type_id']);
                $model->updateAttrs($single_text_area);
            } else {
                if (isset($single_text_area['type_id']) && !empty($single_text_area['type_id'])) {
                    $model = new GameRules;
                    $model->product_id = $product_id;
                    $model->area_id = $single_text_area['area_id'];
                    $model->type_id = serialize($single_text_area['type_id']);
                    $model->desc = $single_text_area['desc'];
                    $model->save();
                }
            }
        }
        return true;

    }

}