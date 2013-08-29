<?php
class Feedback extends UserActiveRecord{
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public $verifyCode;
    //public $query_type;
    //public $content;
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
        return 'feedback';
    }
    
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'created',
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
            array('content', 'required','message'=>"反馈问题不可为空"),
            array('contact', 'required','message'=>"联系方式不可为空"),
            array('contact','required','message'=>"QQ/手机号/邮箱！"),
            array('verifyCode','required','message'=>"验证码不可为空"),
           //array('appeal_verify','captcha','allowEmpty'=>!CCaptcha::checkRequirements()),
            array('verifyCode', 'captcha', 'allowEmpty'=> !extension_loaded('gd')),
        );
    }
    /**
     * 
     * get table feedback all datas
     */
    public function getAllproducts($count,$page){
        $criteria = new CDbCriteria;
        $criteria->order = "created desc";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas; 
    }
    /**
     * 
     * get table feedback all datas
     */
    public function doSearch($id){
        $result = self::model()->findByPk($id);
        return $result; 
    }
}
