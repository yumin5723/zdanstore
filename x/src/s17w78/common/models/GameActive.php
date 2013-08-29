<?php

class GameActive extends CActiveRecord
{
	const GAME_ACTIVE = 1;
	const GAME_OTHER = 0;
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
		return 'gameactive';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('name,type,status,gname','required'),
            array('desc,image,time,url','safe'),
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
			'name' => "活动名称",
			'type' => "活动类型",
			'desc' => "活动描述",
			'time' => "活动时间",
            'status' => "状态",
            'url' => "链接",
			'gname' => "游戏名称",
		);
	}
	public function behaviors() {
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
        $criteria->compare('id',$this->id,true);
        $criteria->compare('type',$this->id,true);
        $criteria->compare('name',$this->id,true);
        $criteria->compare('desc',$this->id,true);
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
        if (!empty($attributes['id']) && $attributes['id'] != $this->id) {
            $attrs[] = 'id';
            $this->id = $attributes['id'];
        }
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if ($attributes['type'] != $this->type) {
            $attrs[] = 'type';
            $this->type = $attributes['type'];
        }
        if (!empty($attributes['image']) && $attributes['image'] != $this->image) {
            $attrs[] = 'image';
            $this->image = $attributes['image'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }

    public function getActive($type){
        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->condition = "t.status = :status AND t.type = :type";
        $criteria->params = array(':status'=>"0",':type'=>$type);
        $criteria->order = "t.id DESC";
        return self::model()->findAll($criteria);
    }
	/**
     * get game term list
     */
    public function getTypes(){
        return array(self::GAME_ACTIVE => "比赛",self::GAME_OTHER => "其他");
    }
}