<?php

class Profile extends UserActiveRecord
{
  public $uid;
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'profile';
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
            array('gender','required', 'on'=>'profile',),
            array('birthday,avatar,small_avatar,realname,idnumber,address,phone,qq','safe'),
            // array('idnumber','match','pattern'=>'/(^\d{6}((0[48]|[2468][048]|[13579][26])0229|\d\d(0[13578]|10|12)(3[01]|[12]\d|0[1-9])|(0[469]|11)(30|[12]\d|0[1-9])|(02)(2[0-8]|1\d|0[1-9]))\d{3}$)|(^\d{6}((2000|(19|21)(0[48]|[2468][048]|[13579][26]))0229|(((20|19)\d\d)|2100)(0[13578]|10|12)(3[01]|[12]\d|0[1-9])|(0[469]|11)(30|[12]\d|0[1-9])|(02)(2[0-8]|1\d|0[1-9]))\d{3}[\dX]$)/','on'=>'profile'),
            array('avatar','file','allowEmpty'=>true,'types'=>'jpg, gif, png','maxSize'=>1024*1024*5,'tooLarge'=>'文件需小于5MB.',
                    'wrongType'=>'文件类型(T):Image(*.jpg,*.gif,*.png)','on'=>'upavatar'),
            array('phone','match','pattern'=>'/^0{0,1}(13[0-9]|15[3-9]|15[0-2]|18[0-9])[0-9]{8}$/'),
            array('qq','match','pattern'=>'/^[1-9]*[1-9][0-9]*$/')
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
            'avatar' => "头像",
            'small_avatar' => "小头像",
            'birthday' => "生日",
            'address' => "地址",
            'game_stage' => "game_stage",
            'realname' => "真实姓名",
            'idnumber' => "身份证号",
            'phone' => "电话号码",
            'qq' => "QQ",
            'edu' => "教育程度",
            'gender' => "性别",
        );
    }

   /* public static function factory($uid){
        $this->uid = $uid;
        $info = self::model()->findByPk($uid);
        return $info;
    }
    */
    public function getAvatarUri(){
            return $this->avatar;
    }
    public function update_avatar($file_path){
        $this->avatar = $file_path;
        $this->updateByPk($this->uid,array('avatar' =>$this->avatar));
    }
    public function getSmallAvatarUri(){
            return $this->small_avatar;
    }
    public function update_smallavatar($file_path){
        $this->small_avatar = $file_path;
        $this->updateByPk($this->uid,array('small_avatar' =>$this->small_avatar));
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
        if (!empty($attributes['birthday']) || $attributes['birthday'] != $this->birthday) {
            $attrs[] = 'birthday';
            $this->birthday = $attributes['birthday'];
        }
        if (!empty($attributes['address']) || $attributes['address'] != $this->address) {
            $attrs[] = 'address';
            $this->address = $attributes['address'];
        }
        if (!empty($attributes['realname']) || $attributes['realname'] != $this->realname) {
            $attrs[] = 'realname';
            $this->realname = $attributes['realname'];
        }
        if (!empty($attributes['idnumber']) || $attributes['idnumber'] != $this->idnumber) {
            $attrs[] = 'idnumber';
            $this->idnumber = $attributes['idnumber'];
        }
        if (!empty($attributes['phone']) || $attributes['phone'] != $this->phone) {
            $attrs[] = 'phone';
            $this->phone = $attributes['phone'];
        }
        if (!empty($attributes['qq']) || $attributes['qq'] != $this->qq) {
            $attrs[] = 'qq';
            $this->qq = $attributes['qq'];
        }
        if (!empty($attributes['gender']) || $attributes['gender'] != $this->gender) {
            $attrs[] = 'gender';
            $this->gender = $attributes['gender'];
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
    public function getIsnull($id){
        $criteria = new CDbCriteria;
        $criteria->addCondition("uid=$id");
        $criteria->addCondition("birthday!='' and address!='' and realname!='' and idnumber!='' and phone!='' and qq!=''");
        
        return self::model()->find($criteria);
    }
}