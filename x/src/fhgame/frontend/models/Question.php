<?php
class Question extends FhgameActiveRecord{
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public $appeal_verify;
    public $count = 20;
    public   $sub_pages = 6;
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
            array('user_name,question_title,question_content', 'required',),
           array('appeal_verify','required'),
           //array('appeal_verify','captcha','allowEmpty'=>!CCaptcha::checkRequirements()),
            array('appeal_verify', 'captcha', 'on'=>'login', 'allowEmpty'=> !extension_loaded('gd')),
        );
    }
    /**
     * get datas of admin_qa On ActionSearch
     *
     * return array
     */
    public function getAllcount(){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.answer_content!=''");
        return self::model()->count($criteria);
    }
    /**
     * get datas of admin_qa On ActionCreate
     *
     * return array
     */
    public function getAllques($count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.answer_content!=''");
        $criteria->order = "t.qa_id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->findAll($criteria);
    }
    /**
     * get datas of admin_qa On ActionSearch
     *
     * return array
     */
    public function getcount($content,$type){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.answer_content!=''");
        $criteria->addSearchCondition($type,$content);
        return self::model()->count($criteria);
    }

   
    /**
     * get can recharge question
     *
     * return array
     */
    public function getquestion($content,$type,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.answer_content!=''");
        $criteria->addSearchCondition($type,$content);
        $criteria->order = "t.qa_id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->findAll($criteria);
    }
    /**
     * get can all types question
     *
     * return array
     */
    public function typequestion($query_type,$content,$count,$pageCurrent){
        if($query_type=='1'){
                $datas = $this->getquestion($content,'question_title',$count,$pageCurrent);
                $nums = $this->getcount($content,'question_title');
        }elseif($query_type=='2'){
                $datas = $this->getquestion($content,'question_date',$count,$pageCurrent);
                $nums = $this->getcount($content,'question_date');  
        }elseif($query_type=='3'){
                $datas = $this->getquestion($content,'user_name',$count,$pageCurrent);
                $nums = $this->getcount($content,'user_name');  
        }
        return array($datas,$nums);
    }
}
?>