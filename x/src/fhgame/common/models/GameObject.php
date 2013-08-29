<?php
Yii::import("gcommon.cms.models.Object");
/**
 * This is the model class for table "game".
 *
 * The followings are the available columns in table 'game':
 * @property integer $id
 * @property string $name
 * @property string $tags
 * @property string $from
 * @property string $desc
 * @property string $operations_guide
 * @property string $how_begin
 * @property string $target
 * @property string $image
 * @property string $tag_image
 * @property string $url
 * @property string $created_uid
 * @property string $modified_uid
 * @property string $created
 * @property string $modified
 */
class GameObject extends Object
{
    const GAME_TERM_1 = "牌类游戏";
    const GAME_TERM_2 = "骨牌类游戏";
    const GAME_TERM_3 = "棋类游戏";
    const GAME_TERM_4 = "休闲游戏";
    const GAME_TERM_ROOT = 15;
    public $downurl;
    public $term;


    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return CMap::mergeArray(Object::extraRules(),
            array(
                array('term', 'required'),
                array('downurl,object_title,object_keywords,object_description', 'safe'),
            )
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        $relation =array('meta' => array(
                self::BELONGS_TO,
                'ObjectMeta',
                'object_id'
            ));
        return CMap::mergeArray($relation,Object::extraRelationships());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return CMap::mergeArray(Object::extraLabel(),
            array(
                'object_name' => '游戏名称',
                'downurl' => '下载地址',
                'term' => '游戏类型',
                'object_title'=>'标题',
                'object_keywords'=>'关键字',
                'object_description'=>'描述',
            ));
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        return Object::extraSearch($this);
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->object_type = 'game';
                Object::extraBeforeSave('create', $this);

            } else {
                Object::extraBeforeSave('update', $this);

            }
            return true;
        } else
            return false;
    }

    protected function afterSave() {
        parent::afterSave();
        if ($this->isNewRecord) {
            Object::saveMetaValue('downurl', $this->downurl, $this, true);
            Object::saveMetaValue('term', $this->term, $this, true);
        } else {
            Object::saveMetaValue('downurl', $this->downurl, $this, false);
            Object::saveMetaValue('term', $this->term, $this, false);
        }
    }


    /**
     * function_description
     *
     * @param $attributes: new atrribues key => value
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['object_title']) && $attributes['object_title'] != $this->object_title) {
            $attrs[] = 'object_title';
            $this->object_title = $attributes['object_title'];
        }
        if (!empty($attributes['object_keywords']) && $attributes['object_keywords'] != $this->object_keywords) {
            $attrs[] = 'object_keywords';
            $this->object_keywords = $attributes['object_keywords'];
        }
        if (!empty($attributes['object_description']) && $attributes['object_description'] != $this->object_description) {
            $attrs[] = 'object_description';
            $this->object_description = $attributes['object_description'];
        }
        if (!empty($attributes['downurl']) && $attributes['downurl'] != $this->downurl) {
            $attrs[] = 'downurl';
            $this->downurl= $attributes['downurl'];
        }
        if (!empty($attributes['term']) && $attributes['term'] != $this->term) {
            $attrs[] = 'term';
            $this->term= $attributes['term'];
        }
        $this->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * get game term list
     */
    public function getTerms(){
        $root = self::GAME_TERM_ROOT;
        $roots = Oterm::model()->findByAttributes(array("root"=>$root));
        $descendants = $roots->descendants()->findAll();
        $ret = array();
        foreach($descendants as $descendant){
            $ret[$descendant->id] = $descendant->name;
        }
        return $ret;
    }
    public function searchGame(){
        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->condition = "object_type = 'game'";

        return new CActiveDataProvider("Object", array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * get all game by term 
     * @return string term
     */
    public function getAllGamesOfTermIds($term){
        $criteria = new CDbCriteria;
        $criteria->order = "meta_object_id DESC";
        $criteria->condition = "meta_key = 'term' and meta_value=:gameterm";
        $criteria->params = array(":gameterm"=>$term);

        $results = ObjectMeta::model()->findAll($criteria);
        $ret = array();
        foreach($results as $key=>$result){
            $object = Object::model()->findByPk($result->meta_object_id);
            $ret[$key]['gamename'] = $object->object_name;
            $ret[$key]['downurl'] = ObjectMeta::model()->findByAttributes(array("meta_object_id"=>$result->meta_object_id),"meta_key='downurl'")->meta_value;
            $ret[$key]['introduce'] = $object->url;
        }
        return $ret;
    }
}