<?php
class Ques extends CActiveRecord{
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public $appeal_verify;
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'admin_qa';
    }
    
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'question_date',
                    'updateAttribute'     => 'answer_date',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * @return array validation rules for model attributes.
     *
     *
     * @return
     */
    public function rules() {
        return array(
           array('query_type','required'),
           array('content','required'),
        );
    }
    
}
?>