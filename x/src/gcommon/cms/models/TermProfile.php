<?php
/**
 * This is the model class for table "click".
 *
 * The followings are the available columns in table 'game':
 */
class TermProfile extends CmsActiveRecord
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
        return 'term_profile';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('term_id,name,value','safe'),
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
        $criteria->compare('created',$this->created,true);

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
        if (!empty($attributes['value']) && $attributes['value'] != $this->value) {
            $attrs[] = 'value';
            $this->value = $attributes['value'];
        }
        if (!empty($attributes['term_id']) && $attributes['term_id'] != $this->term_id) {
            $attrs[] = 'term_id';
            $this->term_id = $attributes['term_id'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * save meta for product
     * @param  intval $term_id 
     * @param  array $meta 
     * @return boolean
     */
    public function saveTermProfile($term_id,$metas){
        foreach ( $metas as $meta ) {
            // if (!self::model()->findByPk(array("term_id"=>$term_id,"name"=>$meta['name']))){
                $product_meta = new self;
                $product_meta->term_id = $term_id;
                $product_meta->name = $meta['name'];
                $product_meta->value = $meta['value'];
                $product_meta->save(false);
            // }
        }
        return true;
    }
    /**
     * update metas for object
     * @param  intval $product_id 
     * @param  array $metas 
     * @return boolean
     */
    public function updateTermProfile($term_id,$metas){
        $currents = $this->getAllMetaRefTerm($term_id);
        $ret = array();
        foreach($metas as $meta){
            $ret[] = $meta['name'];
        }
        $to_del = array_diff($currents, $ret);
        // save to db
        if (!empty($to_del)) {
            $this->removeMetas($term_id,$to_del);
        }
        $model = new self;
        foreach($metas as $meta){
            $data = self::model()->findByAttributes(array('term_id'=>$term_id,'name'=>$meta['name']));
            // print_r($data->meta_value);
            if(empty($data)){
                $model->term_id = $term_id;
                $model->name = $meta['name'];
                $model->value = $meta['value'];
                $model->save(false);
            }else{
                if($data->value != $meta['value']){
                    $data->value = $meta['value'];
                    $data->save(false);
                }
            }
        }
        return true;

    }
    /**
     * function_description
     *
     * @param $product_id:
     *
     * @return
     */
    public function getAllMetaRefTerm($term_id) {
        return array_map(function($meta){return $meta->name;},
            $this->findAllByAttributes(array('term_id'=>intval($term_id))));
    }
    /**
     * get all metas by product id
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function getAllMetasByProductId($product_id){
        $metas = self::model()->findAllByAttributes(array('meta_product_id'=>$product_id));
        return $metas;
    }
    /**
     * function_description
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     * @param $dep_ids:
     *
     * @return
     */
    protected function removeMetas($term_id,$metas) {
        if (empty($metas)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE term_id=:term_id ";
        $sql .= " AND name in ('".implode("','",$metas)."')";
        if (!is_array($metas)) {
            $metas = array($metas);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":term_id", $term_id);
        return $cmd->execute();
    }
    /**
     * get profile by term id
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function getProfileByTerm($term_id){
        $term_id = intval($term_id);
        $profiles = self::model()->findAllByAttributes(array('term_id'=>$term_id));
        return $profiles;
    }
}