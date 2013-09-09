<?php
class Subject extends CmsActiveRecord {
    protected $_widget = null;
    public $category_id;
    public $count;
    public $product_type;
    public $brand;
    public $oterm;

    const SUBJECT_TYPE_FULLCUT = 1;
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'subject';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('type,begin,end','required'),
            array('params,product','safe'),
        );
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
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
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->compare( 'id', $this->id, true );
        $criteria->compare( 'name', $this->name, true );
        $criteria->compare( 'type', $this->type, true );

        $sort = new CSort;
        $sort->attributes = array(
            'id',
        );
        $sort->defaultOrder = 'id DESC';


        return new CActiveDataProvider( $this, array(
                'criteria'=>$criteria,
                'pagination' => array(
                    'pageSize' => 20,
                ),
                'sort'=>$sort
            ) );
    }
   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '名字',
            'type'=>'类型',
            'begin'=>'活动开始时间',
            'end'=>'活动结束时间',
            'product'=>'参加活动的商品',
        );
    }
    
    /**
     * Convert from value to the String of the Block Type
     *
     * @param type    $value
     */
    public static function convertBlockType( $value ) {
        $types = ConstantDefine::getBlockType();
        if ( isset( $types[$value] ) ) {
            return $types[$value];
        } else {
            return Yii::t( 'cms', 'undefined' );
        }
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function updateDependentCategory() {
        $category_ids = $this->getWidget()->getDependentCategoryIds();
        $this->UpdateDependentCategoryByIds($category_ids);
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function beforeSave() {
        // process dependent category
        if (is_array($this->params)) {
            $this->params = serialize($this->params);
        }
        if($this->product_type == 1){
            $this->product = serialize(array('all'=> 'all'));
        }elseif($this->product_type == 2){
            $this->product = serialize(array('brand'=> $this->brand));
        }else{
            $this->product = serialize(array('oterm'=> $this->oterm));
        }
        return parent::beforeSave();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function afterFind() {
        if (!is_array($this->params)) {
            @$this->params = unserialize($this->params);
        }
    }
    public function saveSubject(){
        return $this->save();
    }
}