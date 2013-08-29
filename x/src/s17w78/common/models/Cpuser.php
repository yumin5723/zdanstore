<?php

class Cpuser extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
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
		return 'cpuser';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('email,cpname,password','required'),
			array('cp_key','safe'),
			array('email','unique'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=&gt;label)
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>'邮箱',
			'cpname'=>'名称',
			'password'=>'密码',
		);
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
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('cpname',$this->cpname,true);
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
        if (!empty($attributes['email']) && $attributes['email'] != $this->email) {
            $attrs[] = 'email';
            $this->email = $attributes['email'];
        }
        if (!empty($attributes['cpname']) && $attributes['cpname'] != $this->cpname) {
            $attrs[] = 'cpname';
            $this->cpname = $attributes['cpname'];
        }
        if (!empty($attributes['password']) && $attributes['password'] != $this->password) {
            $attrs[] = 'password';
            $this->password = $attributes['password'];
        }
        if (!empty($attributes['cp_key']) && $attributes['cp_key'] != $this->cp_key) {
            $attrs[] = 'cp_key';
            $this->cp_key = $attributes['cp_key'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
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

   /**
     * hook before save
     *
     *
     * @return boolen
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->password = $this->hashPassword($this->password);
            // $this->created = date("Y-m-d H:i:s");
            // $this->modified = date("Y-m-d H:i:s");
        }
        return parent::beforeSave();
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
}