<?php

Yii::import("gcommon.cms.components.widgets.CmsWidgetFactory");
Yii::import("gcommon.cms.components.CmsActiveRecord");

class Templete extends CmsActiveRecord {

    const STATUS_PARSEING = 0;
    const STATUS_ENABLE = 1;
    const STATUS_PARSE_ERROR = 2;
    public $upload;
    // public $type;
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
        return 'templete';
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
            array('upload', 'file','allowEmpty'=>false,'types'=>'rar','maxSize'=>1024 * 1024 * 4,'tooLarge'=>'The file was larger than 4MB. Please upload a smaller file.','safe'=>true),
            array('rar_file,content,status','safe'),
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
                'object_templete'=>array(self::BELONGS_TO, 'ObjectTemplete', 'templete_id'),
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
        $criteria->compare( 'rar_file', $this->rar_file, true );

        $sort = new CSort;
        $sort->attributes = array(
            'id',
        );
        $sort->defaultOrder = 'id DESC';


        return new CActiveDataProvider( $this, array(
                'criteria'=>$criteria,
                'sort'=>$sort
            ) );
    }
    /**
     * save new Rar file when create page
     *
     *
     * @return array(boolean result, MError err)
     * result for if new file have saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function saveRarFile($files){
        $tmp_file = $files['Templete']['tmp_name']['upload'];
        $real_file = $files['Templete']['name']['upload'];
        $file = new UploadFile($tmp_file,$real_file,"templete");
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false;
        }
        $this->rar_file = Yii::app()->params['resource_folder'].$uri;
        return true;
    }
   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '模板名字',
            'upload' => '上传',
            'rar_file' => '路径',
            'status' => "状态",
            'content' => "内容",
        );
    }
    /**
     * get templete status
     *
     * @return array
     */
    public function getPageStatus() {
        $templete_status = array(
            "0"=>"解析中",
            "1"=>"可用",
            "2"=>"解析错误",
        );
        return $templete_status;
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertTempleteStatus( $value ) {
        $status = self::model()->getPageStatus();
        if ( isset( $status[$value] ) ) {
            return $status[$value];
        } else {
            return Yii::t( 'cms', 'undefined' );
        }
    }

    /**
     * check template
     *
     *
     * @return
     */
    // protected function check() {
    //     include_once(Yii::getPathOfAlias("gcommon.lib")."/simple_html_dom.php");
    //     $html = str_get_html($this->content);
    //     $c = $html->find("[data-widget=content]");
    //     return count($c) == 1;
    // }


    /**
     * function_description
     *
     *
     * @return boolean
     */
    public function parse() {
        try {
            $files = Yii::app()->publisher->parseEntirePage($this->rar_file);
            $this->content=$files['html'];
            $this->status = self::STATUS_ENABLE;
            return $this->save(false);
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::log("Parse templete error for templete: ".$this->id.", with error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
            $this->content = "";
            $this->status = self::STATUS_PARSE_ERROR;
            $this->save(false);
            return true;
        }
    }
    /**
     * get all contents who use this templete
     * @param  intval $templete_id
     * @return array
     */
    public function getAllContentsByTempleteId($templete_id){
        $objects = ObjectTemplete::model()->findAllByAttributes(array("templete_id"=>$templete_id));
        $ret = array();
        foreach($objects as $key=>$object){
            array_push($ret,$object->object_id);
        }
        return $ret;
    }
    /**
     * [publishAllContents description]
     * @return [type] [description]
     */
    public function publishAllContents($templete_id){
        return  $this->getAllContentsByTempleteId($templete_id);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->fireNew();
        } else {
            $this->fireUpdate();
        }
        if(!empty($this->content)){
            $this->UpdateDependentBlockByHtml($this->content);
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
        $this->firePublished();
        return parent::afterSave();
    }

    /**
     * get can use page type to save to table page_term
     */
    public function getTempleteTypes(){
        return ConstantDefine::getTempleteType();
    }
}