<?php
Yii::import("gcommon.cms.components.CmsActiveRecord");

class Page extends CmsActiveRecord {
    /**
     * page draft
     */
    const STATUS_PARSEING = 0;
    const STATUS_DRAFT = 1;
    const STATUS_NEED_PUBLISH = 2;
    const STATUS_PUBLISHING = 3;
    const STATUS_PUBLISHED = 4;
    const STATUS_PARSE_ERROR = 5;

    public $pagetype;

    const ERROR_ATTRIBUTE_VALUE = 3001;
    public $upload;



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
        return 'page';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('name,domain,path,title,keywords,description','required'),
            array('upload', 'file','allowEmpty'=>false,'types'=>'rar','maxSize'=>1024 * 1024 * 4,'tooLarge'=>'The file was larger than 4MB. Please upload a smaller file.'),
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
        $criteria->compare( 'path', $this->path, true );

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
     * get can use domain
     *
     * @return array
     */
    public function getCanUseDomain() {
        $domains = Yii::app()->publisher->domains;
        $ret = array();
        foreach($domains as $key=>$domain){
            $ret[$key] = $key;
        }
        return $ret;
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertPageStatus( $value ) {
        $status = self::model()->getPageStatus();
        if ( isset( $status[$value] ) ) {
            return $status[$value];
        } else {
            return Yii::t( 'cms', 'undefined' );
        }
    }

    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertLable( $value ) {
        if ($value == self::STATUS_DRAFT) {
            return "发布";
        }
        if($value == self::STATUS_PUBLISHED){
            return "重新发布";
        }
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
        $tmp_file = $files['Page']['tmp_name']['upload'];
        $real_file = $files['Page']['name']['upload'];
        $file = new UploadFile($tmp_file,$real_file,"page");
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false;
        }
        $this->status = self::STATUS_PARSEING;
        $this->rar_file = Yii::app()->params['resource_folder'].$uri;
        return true;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '页面名字',
            'upload' => '上传',
            'domain'=>"域名",
            'path'=>"路径",
            'rar_file' => '压缩包地址',
            'status' => "状态",
            'title'=>"页面标题",
            'keywords'=>"关键字",
            "description"=>"页面描述",
        );
    }
    /**
     * get page status
     *
     * @return array
     */
    public function getPageStatus() {
        $page_status = array(
            "0"=>"解析中",
            "1"=>"草稿",
            "2"=>"可用",
            "3"=>"正在发布",
            "4"=>"已经发布",
            "5"=>"解析错误",
        );
        return $page_status;
    }

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
            $this->status = self::STATUS_DRAFT;
            return $this->save(false);
        } catch (Exception $e) {
            Yii::log("Parse page error for page: ".$this->id.", with error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
            $this->content = "";
            $this->status = self::STATUS_PARSE_ERROR;
            $this->save(false);
            return true;
        }
    }
    /**
     * display the page view 
     * @param  arrar $id  page id
     * @return string
     */
    public function display(){
        return Yii::app()->cmsRenderer->render($this,$this->content,array());
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        // update block dependent
        $this->UpdateDependentBlockByHtml($this->content);
        $result = Yii::app()->publisher->saveDomainHtml($this->domain,$this->path,$this->display());
        if($result){
            $this->status = self::STATUS_PUBLISHED;
            $this->save(false);
            return true;
        }
        return false;
    }
    /**
     * get can use page type to save to table page_term
     */
    public function getPageTypes(){
        return ConstantDefine::getPageType();
    }
    /**
     * get page title
     */
    public function getTitle(){
        return $this->title;
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        return $this->keywords;
    }
    /**
     * get page description
     */
    public function getDescription(){
        return $this->description;
    }

    public function getResult($type){
        $pages = PageTerm::model()->findAllByAttributes(array('type'=>$type));
        if (empty($pages)) {
            return null;
        }
        $pages_ids = array();
        foreach ($pages as $value) {
            $pages_ids[] = $value->page_id;
        }
        $criteria = new CDbCriteria;
        $criteria->addInCondition('id',$pages_ids);
        $result = self::model()->findAll($criteria);
        return $result;
    }

    public function getSpecialPages($type){
        $result = $this->getResult($type);
        if (empty($result)) {
            return array();
        }
        $ret = array();
        foreach ($result as $key => $value) {
            $ret[$key]['name'] = $value->name;
            $ret[$key]['url'] = "/cms/special/block/page_id/".$value->id;
            $ret[$key]['id'] = $value->id;
        }
        return $ret;
    }

    public function getSubjectPages($type){
        $result = $this->getResult($type);
        if (empty($result)) {
            return array();
        }
        $ret = array();
        foreach ($result as $key => $value) {
            $ret[$key]['name'] = $value->name;
            $ret[$key]['url'] = "/cms/subject/block/page_id/".$value->id;
            $ret[$key]['id'] = $value->id;
        }
        return $ret;
    }

}