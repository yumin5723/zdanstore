<?php

class Category extends CActiveRecord {
    const ERROR_ATTRIBUTE_VALUE = 3001;

    const ERROR_NOT_ALLOW_ATTRIBUTE = 3002;

    const ERROR_NOTHING_TO_MODIFY = 3003;

    const ERROR_UNKNOW = 3004;

    const ERROR_NOT_FOUND = 3005;

    const ERROR_SOME_NOT_FOUND = 3006;
    
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'category';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('name', 'required',),
//            array('name','unique'),
        );
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'nestedSetBehavior'=>array(
                  'class'=>'common.extensions.NestedSetBehavior',
                  'leftAttribute'=>'left_id',
                  'rightAttribute'=>'right_id',
                  'levelAttribute'=>'level',
              ),
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
     * create and save new category
     *
     *
     * @return array(boolean result, MError err)
     * result for if new category have created and saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function setCategory($category_id){
        $root = self::model()->findByPk($category_id);
        $model = new self;
        $model->name = $this->name;
        $model->admin_id = Yii::app()->user->id;
        if($model->appendTo($category_id)){
            return array(true, null);
        }
    }
}