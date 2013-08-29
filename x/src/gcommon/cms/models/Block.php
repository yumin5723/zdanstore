<?php

Yii::import("gcommon.cms.components.widgets.CmsWidgetFactory");
Yii::import("gcommon.cms.components.CmsActiveRecord");

class Block extends CmsActiveRecord {
    protected $_widget = null;
    public $category_id;
    public $count;
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
        return 'block';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('name,type','required'),
            array('content,params','safe'),
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
                'CmsEventBehavior' => array(
                    'class' => 'gcommon.cms.components.CmsEventBehavior',
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
            'params[category_id]' => '分类ID',
            'params[count]'=> '需要的条数',
            'html'=>'最终代码',
            'content'=>'内容'
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
     * save in table when update block
     */
    public function backupBlock(){
        $model = new BlockBackup;
        $model->content = $this->content;
        $model->block_id = $this->id;
        $model->created_id = $this->modified_id;
        return $model->save(false);
    }

    /**
     * get block widget
     *
     * @return
     */
    public function getWidget() {
        if (is_null($this->_widget)) {
            // factory _widget
            if (!is_array($this->params)) {
                @$params = unserialize($this->params);
            }else{
                $params = $this->params;
            }
            if (!$params) {
                throw new CException("Can not read params from Block, ".$this->id);
            }
            // add content to params
            $params['block_content'] = $this->content;
            $params['block_id'] = $this->id;
            $this->_widget = CmsWidgetFactory::factory($this->type, $params);

        }
        return $this->_widget;
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
        return parent::beforeSave();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function afterSave() {
        $this->updateDependentCategory();

        $this->fireUpdate();
        return parent::afterSave();
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

    /**
     * get block as html
     * current only return $this->render()
     * maybe add cache for render result someday
     *
     * @return
     */
    public function updateHtml() {
        $new_html = $this->render();
        if ($new_html != $this->html) {
            Yii::log("it will update html for block_id .".$this->id, CLogger::LEVEL_INFO);
            $this->html = $new_html;
            $this->saveAttributes(array('html'));
            $this->firePublished();
        }
        return true;
    }


    /**
     * block render self, return current html content
     *
     *
     * @return
     */
    public function render() {
        return $this->getWidget()->run();
    }

    /**
     * save block
     */
    public function saveBlock($uid){
        $this->created_id = $uid;
        $this->modified_id = $uid;
        $this->save(false);
        return true;
    }
    /**
     * save block
     */
    public function updateBlock($uid){
        $this->modified_id = $uid;
        $this->save(false);
        // $this->backupBlock();
        return true;
    }

    /**
     * get objects that the model dependent.
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     *
     * @return
     */
    public function getDeps($page_type, $page_id, $dep_type) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('dep_id')
                     ->from('obj_dependence')
                     ->where(array('and',
                             'obj_type=:obj_type',
                             'dep_type=:dep_type',
                             'obj_id=:obj_id',
                         ),
                         array(
                             ':obj_type'=>$page_type,
                             ':obj_id'=>intval($page_id),
                             ':dep_type'=>$dep_type
                         ))
                     ->queryAll();
        return array_map(function($a){return $a['dep_id'];},$rows);
    }

    public function getSpecialPagesBlock($page_type, $page_id, $dep_type){
        $blocks = $this->getDeps($page_type, $page_id, $dep_type);
        $criteria = new CDbCriteria;
        $criteria->addInCondition('id',$blocks);
        return self::model()->findAll($criteria);
    }

}