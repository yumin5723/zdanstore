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
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
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
            array('content','required', 'on'=>'message',),
            array('content,email','required','on'=>'addmsg'),
            array('uid','safe','on'=>'addmsg'),
            array('reply','safe','on'=>'reply'),
            array('email','email'),
            array('uid','safe'),
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
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('content',$this->content,true);
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
        if (!empty($attributes['email']) || $attributes['email'] != $this->email) {
            $attrs[] = 'email';
            $this->email = $attributes['email'];
        }
        if (!empty($attributes['content']) || $attributes['content'] != $this->content) {
            $attrs[] = 'content';
            $this->content = $attributes['content'];
        }
        if (!empty($attributes['reply']) || $attributes['reply'] != $this->reply) {
            $attrs[] = 'reply';
            $this->reply = $attributes['reply'];
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
    
    public function getAlldatas($uid=false){
        $count = 5;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $this->getCount($uid);
        $chargeRecords = $this->getAllChargeRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/message/p/",2);
        $p = $subPages->show_SubPages(2);
        return [$chargeRecords,$p];
    }
    public function getAllChargeRecords($uid=false,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        if(!empty($uid)){
            $criteria->addCondition("t.uid=$uid");
        }
        $criteria->order = "t.id DESC";
        $criteria->group = 't.id'; 
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas;
    }
    public function getCount($uid=false){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        if(!empty($uid)){
            $criteria->addCondition("t.uid=$uid");
        }
        $counts = self::model()->count($criteria);
        return $counts;
    }

}