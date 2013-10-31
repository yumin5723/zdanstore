<?php

class Message extends CmsActiveRecord
{
  public $uid;
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'message';
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'date',
                    'updateAttribute'     => 'date',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * Returns the static model of the specified AR class.
     * This method is required by all child classes of CActiveRecord.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * set attribution validator rules
     *
     *
     * @return
     */
    public function rules() {
        $rules =  array(
            array('content,uid','required', 'on'=>'message',),
            //array('email','email'),
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }

     /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'content' => "Message",
        );
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
       /* if (!empty($attributes['email']) || $attributes['email'] != $this->email) {
            $attrs[] = 'email';
            $this->email = $attributes['email'];
        }*/
        if (!empty($attributes['content']) || $attributes['content'] != $this->content) {
            $attrs[] = 'content';
            $this->content = $attributes['content'];
        }
        if (!empty($attributes['uid']) || $attributes['uid'] != $this->uid) {
            $attrs[] = 'uid';
            $this->uid = $attributes['uid'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * updata profile info if profile is emtpy create one
     * @return [type] [description]
     */
    public function updateInfo($data){
        $this->updateAttrs($data);
        return true;
    }
    
    public function getAlldatas($uid){
        $count = 3;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $this->getCount($uid);
        $chargeRecords = $this->getAllChargeRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/order/p/",2);
        $p = $subPages->show_SubPages(2);
        return [$chargeRecords,$p];
    }
    public function getAllChargeRecords($uid,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $criteria->order = "t.id DESC";
        $criteria->group = 't.id'; 
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas;
    }
    public function getCount($uid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $counts = self::model()->count($criteria);
        return $counts;
    }
}