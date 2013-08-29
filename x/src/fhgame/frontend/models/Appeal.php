<?php
class Appeal extends FhgameActiveRecord{
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
        return 'user_appeal';
    }
public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'make_time',
                    'updateAttribute'     => 'lastmodifytime',
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
            array('user_name', 'required',),
           array('ID_NUM,pwd_answer,reg_date,reg_area,yinzi_count,last_login,user_tel,other_proof','safe'),
           array('appeal_verify','required'),
           //array('appeal_verify','captcha','allowEmpty'=>!CCaptcha::checkRequirements()),
            array('appeal_verify', 'captcha', 'on'=>'login', 'allowEmpty'=> !extension_loaded('gd')),
        );
    }
    
}
?>