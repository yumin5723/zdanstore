<?php

class CPUser extends CActiveRecord {

    public $password_repeat;
    public $cp_key;
    /**
     * model
     * 
     * @param $className:
     *
     * @return CPUser the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * get model table name
     * 
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'cpuser';
    }

    /**
     * get model rules
     * 
     *
     * @return array validation rules for model attributes
     */
    public function rules() {
        $rules =  array(
            array('email,cpname,webaddress', 'required', 'on'=>'addcp'),
            array('email', 'length', 'max' => 40),
            array('email', 'email'),
            array('email', 'unique', 'on'=>'addcp'),
            array('cp_key', 'generateCpkey','on'=>'addcp'),
//            array('password', 'compare', 'compareAttribute'=>'password_repeat', 'on'=>'addcp'),
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }
    /**
     * get behaviors
     *
     *
     * @return
     */
    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class'               => 'zii.behaviors.CTimestampBehavior',
                'createAttribute'     => 'created',
                'updateAttribute'     => 'modified',
                'timestampExpression' => 'date("Y-m-d H:i:s")',
                'setUpdateOnCreate'   => true,
            ),
        );
    }
    /**
     * hook before save
     *
     *
     * @return boolen
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->cp_key = $this->generateCpkey(40);
            $this->password = $this->hashPassword($this->password);
        }
        return parent::beforeSave();
    }
    /**
     * hash password
     *
     *
     * @param username
     * @param password not hash yet
     *
     * @return
     */
    public function hashPassword($password) {
        $mode = 'sha1';
        // hash the text //
        $textHash = hash($mode, $password);
        // set where salt will appear in hash //
        $saltStart = strlen($password);
        $saltHash = hash($mode, $this->email);
        // add salt into text hash at pass length position and hash it //
        if($saltStart > 0 && $saltStart < strlen($saltHash)) {
            $textHashStart = substr($textHash,0,$saltStart);
            $textHashEnd = substr($textHash,$saltStart,strlen($saltHash));
            $outHash = hash($mode, $textHashEnd.$saltHash.$textHashStart);
        } elseif($saltStart > (strlen($saltHash)-1)) {
            $outHash = hash($mode, $textHash.$saltHash);
        } else {
            $outHash = hash($mode, $saltHash.$textHash);
        }
        return $outHash;
    }
    public function generateCpkey($length=20) {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;

        while ($i < $length) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
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
        if (!empty($attributes['email']) && $attributes['email'] != $this->email) {
            $attrs[] = 'email';
            $this->email = $attributes['email'];
        }
        if (!empty($attributes['cpname']) && $attributes['cpname'] != $this->cpname) {
            $attrs[] = 'cpname';
            $this->cpname = $attributes['cpname'];
        }
        if (!empty($attributes['webaddress']) && $attributes['webaddress'] != $this->webaddress) {
            $attrs[] = 'webaddress';
            $this->webaddress = $attributes['webaddress'];
        }
        if (!empty($attributes['password'])) {
            $attrs[] = 'password';
            $this->password = $attributes['password'];
            $this->password_repeat = $attributes['password_repeat'];
        }
        if ($this->validate($attrs)) {
            if (in_array('password', $attrs)) {
                $this->password = $this->hashPassword($this->password);
            }

            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * get model relational rules
     * 
     *
     * @return array relational rules
     */
    public function relations() {
        return array();
    }

    /**
     * get attribute labels
     * 
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'email' => '邮箱',
            'cpname' => '名称',
            'password' => '密码',
            'password_repeat' => '重复密码',
        );
    }
    /**
     * fetch act
     */
    public function fetchCpusers($count,$page){
        $criteria=new CDbCriteria;
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $criteria->order = "t.id DESC";
        return self::model()->findAll($criteria);
    }
   /**
     * get cpuser key by user id
     *
     * @param int $cpuser_id: cpuser's id
     *
     * @return str
     */
    public function getCpuserKeyById($cpuser_id){
        $id = intval($cpuser_id);
        $result = self::model()->findByPk($id);
        
        if(empty($result)){
            return "";
        }
        return $result->cp_key;
        
    }
    /**
     * get app by cp user's id
     *
     * @param int $cpuser_id: cpuser's id
     *
     * @return array
     */
     public function fetchAppsByCpuserId($cpuser_id){
         $criteria = new CDbCriteria;
         $criteria->order = 'id DESC';
         $results = App::model()->findAllByAttributes(array("cp_id"=>$cpuser_id),$criteria);
         return $results;
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

        $criteria->compare('id',$this->id);
        $criteria->compare('email',$this->email);
        $criteria->compare('cpname',$this->cpname,true);
        $criteria->compare('webaddress',$this->webaddress,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
