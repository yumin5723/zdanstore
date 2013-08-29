<?php

/**
 * This is the model class for table "{{object_templete}}".
 *
 * The followings are the available columns in table '{{object_templete}}':
 * @property string $object_id
 * @property string $templete_id
 */
class ObjectTemplete extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return ObjectTerm the static model class
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
        return 'object_templete';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            // 'object_term'=>array(self::BELONGS_TO, 'Term',
      //               'term_id'),
      //        'object'=>array(self::BELONGS_TO, 'Object',
      //               'object_id'),
                'templete'=>array(self::BELONGS_TO, 'Templete',
                     'object_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'object_id' =>  t('cms','Object'),
            'term_id' =>  t('cms','Term'),
            'data' =>  t('cms','Data'),
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

        $criteria->compare('object_id',$this->object_id,true);
        $criteria->compare('templete',$this->templete_id,true);
        $criteria->compare('data',$this->data,true);


        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    /**
     * save object templete relation
     */
    public function saveObjectTemplete($object_id,$templete){
        $result = self::model()->findByAttributes(array("object_id"=>$object_id));
        if(empty($result)){
            $model = new self;
            $model->object_id = $object_id;
            $model->templete_id = $templete;
            $model->save(false);
        }else{
            if($result->templete_id != $templete){
                $result->templete_id = $templete;
                $result->save(false);
            }
        }

    }

    /**
     * function_description
     *
     * @param $id:
     *
     * @return
     */
    public function getAllObjectsIdByTemplateId($id) {
        return array_map(function($t){return $t->object_id;},
            $this->findAllByAttributes(array('templete_id'=>intval($id))));
    }


}