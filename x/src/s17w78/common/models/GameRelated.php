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
class GameRelated extends CActiveRecord
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
        return 'game_related';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id,group_id,name,url','safe'),
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
            'g_related' => array(self::HAS_MANY, 'Product', 'product_id'),
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
        if ( !empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if ( !empty($attributes['url']) && $attributes['url'] != $this->url) {
            $attrs[] = 'url';
            $this->url = $attributes['url'];
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
    public function saveRelated($data,$product_id){
        foreach ($data as $k => $da) {
            foreach ($da as $key=>$value) {
                
                if (!empty($value['name']) && !empty($value['url'])) {

                    $model = new GameRelated;
                    $model->product_id = $product_id;
                    $model->group_id = $value["group_id"];
                    $model->name = $value['name'];
                    $model->url = $value['url'];
                    $model->save();
                }
            }
        }
        return true;
    }

    /**
     * [getGameRules description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getGameRelated($id){
        Yii::import("backend.config.rules");
        $related = require(Yii::getPathOfAlias("backend")."/config/related.php");
        $newArray = array();
        foreach($related[1] as $k => $gid){
            $results = self::model()->findAllByAttributes(array("product_id"=>$id,"group_id"=>$k));
            foreach($results as $key=>$result){
                $newArray[$k][$key]['id'] = $result->id;
                $newArray[$k][$key]['product_id'] = $result->product_id;
                $newArray[$k][$key]['group_id'] = $result->group_id;
                $newArray[$k][$key]['name'] = $result->name;
                $newArray[$k][$key]['url'] = $result->url;
            }
        }
        return $newArray;
    }


    /**
     * [updateRelated description]
     * @param  [type] $data       [description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function updateRelated($data,$product_id){
        foreach ($data as $k => $da) {
            foreach ($da as $key=>$value) {
                if (!empty($value['related_id'])) {
                    if (empty($value['name']) || empty($value['url'])) {
                        GameRelated::model()->deleteByPk($value['related_id']);
                    } else {
                        $model = GameRelated::model()->findByPk($value['related_id']);
                        $model->updateAttrs($value);
                    }
                } else {
                    if (!empty($value['name']) && !empty($value['url'])) {
                        $model = new GameRelated;
                        $model->product_id = $product_id;
                        $model->group_id = $value["group_id"];
                        $model->name = $value['name'];
                        $model->url = $value['url'];
                        $model->save();
                    }
                }
            }
        }
        return true;
    }
}