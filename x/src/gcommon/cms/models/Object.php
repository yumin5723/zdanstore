<?php
/**
 * This is the model class for table "{{object}}".
 *
 * The followings are the available columns in table '{{object}}':
 *
 * @property string $object_id
 * @property string $object_author
 * @property integer $object_date
 * @property integer $object_date_gmt
 * @property string $object_content
 * @property string $object_title
 * @property string $object_excerpt
 * @property integer $object_status
 * @property integer $comment_status
 * @property string $object_password
 * @property string $object_name
 * @property integer $object_modified
 * @property integer $object_modified_gmt
 * @property string $object_content_filtered
 * @property string $object_parent
 * @property string $guid
 * @property string $object_type
 * @property string $comment_count
 * @property string $object_slug
 * @property string $object_description
 * @property string $object_keywords
 * @property integer $lang
 * @property string $object_author_name
 * @property integer $total_number_meta
 * @property integer $total_number_resource
 * @property string $tags
 * @property integer $object_view
 * @property integer $like
 * @property integer $dislike
 * @property integer $rating_scores
 * @property double $rating_average
 * @property string $layout
 */

class Object extends CActiveRecord {
    const OBJECT_TYPE_GAME = "game";
    const OBJECT_TYPE_ARTICLE = "article";
    //The old Tags
    public $_oldTags;
    //This is to check the person the Object will be transferd to
    public $person;
    /**
     * object arrribute is hot
     */
    const IS_HOT = 1;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Object the static model class
     */
    public static function model( $className = __CLASS__ ) {

        return parent::model( $className );
    }
    /**
     *
     *
     * @return string the associated database table name
     */
    public function tableName() {

        return 'object';
    }
    /**
     *
     *
     * @return array validation rules for model attributes.
     */
    public function rules() {

        return self::extraRules();
    }
    /**
     *
     *
     * @return array relational rules.
     */
    public function relations() {

        return self::extraRelationships();
    }
    /**
     *
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {

        return self::extraLabel();
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {

        return self::extraSearch( $this );
    }
    public function behaviors() {

        return CMap::mergeArray( parent::behaviors() , array(
                'CTimestampBehavior' => array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'object_date_gmt',
                    'updateAttribute' => 'object_modified_gmt',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate' => true,
                ) ,
                'CmsEventBehavior' => array(
                    'class' => 'gcommon.cms.components.CmsEventBehavior',
                ),
            ) );
    }
    protected function beforeSave() {
        if ( parent::beforeSave() ) {
            if ( $this->isNewRecord ) {
                if ( $this->object_type == '' ) $this->object_type = 'object';
                self::extraBeforeSave( 'create', $this );
            } else {
                self::extraBeforeSave( 'update', $this );
            }

            return true;
        } else
            return false;
    }
    public static function extraBeforeSave( $type = 'update', $object ) {
        if (!isset(Yii::app()->user)) {
            return;
        }
        switch ( $type ) {
        case 'update':
            $object->object_modified_uid = Yii::app()->user->id;
            break;

        case 'create':
            $object->object_author = Yii::app()->user->id;
            $object->object_modified_uid = Yii::app()->user->id;
            if ( $object->guid == '' ) {
                $object->guid = uniqid();
            }
            break;
        }
    }
    protected function afterSave() {
        parent::afterSave();
        self::extraAfterSave( $this );
    }
    //After Save excucte update Tag Relationship
    public static function extraAfterSave( $object ) {
        //Check the scenairo if tags updated needed
        if ( ( $object->isNewRecord ) || ( $object->scenario = 'updateWithTags' ) ) self::UpdateTagRelationship( $object );

        return;
    }
    /**
     * Excute after Delete Object
     */
    protected function afterDelete() {
        parent::afterDelete();
        self::extraAfterDelete( $this );
        //Implements to delete The Term Relation Ship

    }
    public static function extraAfterDelete( $object ) {
        ObjectMeta::model()->deleteAll( 'meta_object_id = :obj', array(
                ':obj' => $object->object_id
            ) );
        ObjectResource::model()->deleteAll( 'object_id = :obj', array(
                ':obj' => $object->object_id
            ) );
        // Transfer::model()->deleteAll( 'object_id = :obj', array(
        //         ':obj' => $object->object_id
        //     ) );
        TagRelationships::model()->deleteAll( 'object_id = :tid', array(
                ':tid' => $object->object_id
            ) );
        ObjectTerm::model()->deleteAll( 'object_id = :tid', array(
                ':tid' => $object->object_id
            ) );
    }
    /**
     * Update Tag Relationship of the Object
     *
     * @param type    $obj
     */
    public static function UpdateTagRelationship( $obj ) {
        Tag::model()->updateFrequency( $obj->_oldTags, $obj->tags );
        //Start to DElete All the Tag Relationship
        TagRelationships::model()->deleteAll( 'object_id = :id', array(
                ':id' => $obj->object_id
            ) );
        //Start to re Insert
        $explode = explode( ',', trim( $obj->tags ) );

        foreach ( $explode as $ex ) {
            $tag = Tag::model()->find( 'slug = :s', array(
                    ':s' => Tag::model()->stripVietnamese( strtolower( $ex ) )
                ) );
            if ( $tag ) {
                $tag_relationship = new TagRelationships;
                $tag_relationship->tag_id = $tag->id;
                $tag_relationship->object_id = $obj->object_id;
                $tag_relationship->save();
            }
        }
    }
    /**
     * Get Tags of the Object
     *
     * @param type    $object_id
     * @return type
     */
    public static function getTags( $object_id ) {
        $req = Yii::app()->db->createCommand( "SELECT t.name
                                    FROM tag t, tag_relationships r, object o
                                    WHERE t.id = r.tag_id
                                    AND r.object_id = o.user_id
                                    AND o.user_id = " . $object_id );
        $tags_name = $req->queryAll();
        $result = array();
        if ( $tags_name != null ) {

            foreach ( $tags_name as $tag_name ) {
                $result[] = $tag_name['name'];
            }
        }

        return $result;
    }
    /**
     * get Related content by Tags
     *
     * @param type    $id
     * @param type    $max
     * @return CActiveDataProvider
     */
    public static function getRelatedContentByTags( $id, $max ) {
        $object = Object::loadModel( $id );
        $criteria = new CDbCriteria;
        $criteria->join = 'join tag_relationships ft on ft.object_id = t.object_id';
        $criteria->condition = 'ft.tag_id in (select tag_id from tag_relationships fr
                                                where fr.object_id = :id)
                                    AND t.object_id <> :id
                                    AND t.object_status = :status
                                    AND t.object_date <= :time
                                    AND t.object_type = :type';
        $criteria->distinct = true;
        $criteria->params = array(
            ':id' => $id,
            ':status' => ConstantDefine::OBJECT_STATUS_PUBLISHED,
            ':time' => time() ,
            'type' => $object->object_type
        );
        $criteria->order = "object_date DESC";
        //$aa = Object::model()->findAll($criteria);
        //$criteria->limit = $max;

        return new CActiveDataProvider( 'Object', array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => $max
                )
            ) );
    }
    /**
     * Normalize The Tags for the Object - Check Valid
     *
     * @param type    $attribute
     * @param type    $params
     */
    public function normalizeTags( $attribute, $params ) {
        $this->tags = Tag::array2string( array_unique( Tag::string2array( $this->tags ) ) );
    }
    /**
     * Check Tags Valid
     *
     * @param type    $attribute
     * @param type    $params
     */
    public function checkTags( $attribute, $params ) {
        $result = $this->tags;
        $regex = "/[\^\[\]\$\.\|\?\*\+\(\)\{\}\/\*\%\!\.\'\"\@\#\&\:\<\>\|\-\_\+\=\`\~\;]/";
        if ( preg_match( $regex, $result ) ) $this->addError( 'tags', Yii::t( 'Tags must contain characters only' ) );
    }
    /**
     * get content status
     *
     * @return array
     */
    public function getContentStatus() {
        $content_status = array(
            "1"=>"草稿",
            "2"=>"待发布",
            "3"=>"已发布",
        );
        return $content_status;
    }
    public static function extraSearch( $object ) {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria;
        $criteria->compare( 'object_id', $object->object_id, true );
        $criteria->compare( 'object_author', $object->object_author, true );
        $criteria->compare( 'object_date', $object->object_date );
        $criteria->compare( 'object_content', $object->object_content, true );
        $criteria->compare( 'object_title', $object->object_title, true );
        $criteria->compare( 'object_status', $object->object_status );
        $criteria->compare( 'ishot', $object->ishot );
        $criteria->compare( 'istop', $object->istop );
        $criteria->compare( 'isred', $object->isred );
        $sort = new CSort;
        $sort->attributes = array(
            'object_id',
        );
        $sort->defaultOrder = 'object_id DESC';

        return new CActiveDataProvider( $object, array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 20,
                ),
                'sort' => $sort
            ) );
    }
    public static function extraLabel() {

        return array(
            'object_id' => Yii::t( 'cms', '文章id' ) ,
            'object_author' => Yii::t( 'cms', '文章作者' ) ,
            'object_date' => Yii::t( 'cms', '发布时间' ) ,
            'object_date_gmt' => Yii::t( 'cms', 'Object Date Gmt' ) ,
            'object_content' => Yii::t( 'cms', 'Object Content' ) ,
            'object_title' => Yii::t( 'cms', 'Object Title' ) ,
            'ishot' => Yii::t( 'cms', '是否热点' ) ,
            'object_excerpt' => Yii::t( 'cms', 'Object Excerpt' ) ,
            'object_status' => Yii::t( 'cms', '状态' ) ,
            'comment_status' => Yii::t( 'cms', 'Comment Status' ) ,
            'object_password' => Yii::t( 'cms', 'Object Password' ) ,
            'object_name' => Yii::t( 'cms', '文章标题' ) ,
            'object_list_name' => Yii::t( 'cms', '列表标题' ) ,
            'object_modified' => Yii::t( 'cms', 'Object Modified' ) ,
            'object_modified_gmt' => Yii::t( 'cms', 'Object Modified Gmt' ) ,
            'object_content_filtered' => Yii::t( 'cms', 'Object Content Filtered' ) ,
            'object_parent' => Yii::t( 'cms', 'Object Parent' ) ,
            'guid' => Yii::t( 'cms', 'Guid' ) ,
            'object_type' => Yii::t( 'cms', 'Object Type' ) ,
            'comment_count' => Yii::t( 'cms', 'Comment Count' ) ,
            'object_slug' => Yii::t( 'cms', 'Object Slug' ) ,
            'object_description' => Yii::t( 'cms', 'Object Description' ) ,
            'object_keywords' => Yii::t( 'cms', 'Object Keywords' ) ,
            'lang' => Yii::t( 'cms', 'Language' ) ,
            'object_author_name' => Yii::t( 'cms', 'Object Author Name' ) ,
            'total_number_meta' => Yii::t( 'cms', 'Total Number Meta' ) ,
            'total_number_resource' => Yii::t( 'cms', 'Total Number Resource' ) ,
            'tags' => Yii::t( 'cms', 'Tags' ) ,
            'object_view' => Yii::t( 'cms', 'Object View' ) ,
            'like' => Yii::t( 'cms', 'Like' ) ,
            'dislike' => Yii::t( 'cms', 'Dislike' ) ,
            'rating_scores' => Yii::t( 'cms', 'Rating Scores' ) ,
            'rating_average' => Yii::t( 'cms', 'Rating Average' ) ,
            'layout' => Yii::t( 'cms', 'Layout' ) ,
            'person' => Yii::t( 'cms', 'Person' )
        );
    }
    public static function extraRules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.

        return array(
            array(
                'total_number_meta, total_number_resource, object_slug',
                'safe'
            ) ,
            array(
                'object_name,object_list_name',
                'required'
            ) ,
            array(
                'object_content',
                'length',
                'min' => 10
            ) ,
            array(
                'object_description,object_keywords,object_excerpt,object_title,guid',
                'safe'
            ) ,
            array(
                ' object_status, comment_status, lang, total_number_meta, total_number_resource, object_view, like, dislike, rating_scores',
                'numerical',
                'integerOnly' => true
            ) ,
            array(
                'rating_average',
                'numerical'
            ) ,
            array(
                'object_author, object_password, object_parent, object_type, comment_count',
                'length',
                'max' => 20
            ) ,
            array(
                'guid, object_keywords, object_author_name',
                'length',
                'max' => 255
            ) ,
            array(
                'layout',
                'length',
                'max' => 125
            ) ,
            array(
                'tags',
                'checkTags'
            ) ,
            // array(
            //     'tags',
            //     'normalizeTags'
            // ) ,
            array(
                'person,ishot,istop,isred,url',
                'safe'
            ) ,
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'object_id, object_author, object_date, object_content, object_title, object_status, object_name',
                'safe',
                'on' => 'search,draft,published,pending'
            ) ,
        );
    }
    /**
     * Define Relationships so that its child class can call it
     *
     * @return type
     */
    public static function extraRelationships() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.

        return array(
            'author' => array(
                self::BELONGS_TO,
                'User',
                'object_author'
            ) ,
        );
    }
    /**
     * Load Object that has been published and time is <= time()
     *
     * @param type    $id
     * @return type
     */
    public static function loadPublishedModel( $id ) {
        $model = Object::model()->findByPk( (int)$id );
        if ( $model === null ) throw new CHttpException( 404, 'The requested page does not exist.' );
        else {
            if ( ( $model->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED ) && ( $model->object_date <= time() ) ) {

                return $model;
            } else {
                throw new CHttpException( 404, 'The requested page does not exist.' );
            }
        }
    }
    /**
     * Save Meta Data of a Object Content Type
     *
     * @param type    $key
     * @param type    $value
     * @param type    $object
     * @param type    $create
     */
    public static function saveMetaValue( $key, $value, $object, $create = true ) {
        if ( $create ) {
            $object_meta = new ObjectMeta;
            $object_meta->meta_key = $key;
            $object_meta->meta_value = $value;
            $object_meta->meta_object_id = $object->object_id;
            $object_meta->save();
        } else {
            $object_meta = ObjectMeta::model()->find( 'meta_key= :key  and meta_object_id = :obj ', array(
                    ':key' => $key,
                    ':obj' => $object->object_id
                ) );
            if ( $object_meta != null ) {
                $object_meta->meta_value = $value;
                $object_meta->save();
            } else {
                $object_meta = new ObjectMeta;
                $object_meta->meta_key = $key;
                $object_meta->meta_value = $value;
                $object_meta->meta_object_id = $object->object_id;
                $object_meta->save();
            }
        }
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertObjectStatus( $value ) {
        $status = ConstantDefine::getObjectStatus();
        if ( isset( $status[$value] ) ) {

            return $status[$value];
        } else {

            return t( 'cms', 'undefined' );
        }
    }
    /**
     * Convert from value to the String of the Object url
     *
     * @param type    $value
     */
    public static function convertObjectUrl( $value ) {
        if(empty($value)){
            return "";
        }
        return $domain = Yii::app()->getModule("cms")->domain.$value;
    }
    /**
     * Convert from value to the String of the Object Comment
     *
     * @param type    $value
     */
    public static function convertObjectCommentType( $value ) {
        $types = ConstantDefine::getObjectCommentStatus();
        if ( isset( $types[$value] ) ) {

            return $types[$value];
        } else {

            return t( 'cms', 'undefined' );
        }
    }
    /**
     * Get the history workflow of the Object
     *
     * @param type    $object
     */
    public static function getTransferHistory( $model ) {
        $trans = Transfer::model()->with( 'from_user' )->findAll( array(
                'condition' => ' object_id=:obj ',
                'params' => array(
                    ':obj' => $model->object_id
                ) ,
                'order' => 'transfer_id ASC'
            ) );
        $trans_list = "<ul>";
        $trans_list.= "<li>- <b>" . $model->author->display_name . "</b> " . t( "cms", "created on" ) . " <b>" . date( 'm/d/Y H:i:s', $model->object_modified ) . "</b></li>";
        //Start to Translate all the Transition

        foreach ( $trans as $tr ) {
            if ( $tr->type == ConstantDefine::TRANS_STATUS ) {
                $temp = "<li>- <b>" . $tr->from_user->display_name . "</b> " . t( "cms", "changed status to" ) . " <b>" . self::convertObjectStatus( $tr->after_status ) . "</b> " . t( "cms", "on" ) . " <b>" . date( 'm/d/Y H:i:s', $tr->time ) . "</b></li>";
            }
            if ( $tr->type == ConstantDefine::TRANS_ROLE ) {
                $temp = "<li>- <b>" . $tr->from_user->display_name . "</b> " . t( "cms", "modified and sent to" ) . " <b>" . ucfirst( $tr->note ) . "</b> " . t( "cms", "on" ) . " <b>" . date( 'm/d/Y H:i:s', $tr->time ) . "</b></li>";
            }
            if ( $tr->type == ConstantDefine::TRANS_PERSON ) {
                $to_user = User::model()->findbyPk( $tr->to_user_id );
                $name = "";
                if ( $to_user != null ) $name = $to_user->display_name;
                $temp = "<li>- <b>" . $tr->from_user->display_name . "</b> " . t( "cms", "modified and sent to" ) . " <b>" . ucfirst( $name ) . "</b> " . t( "cms", "on" ) . " <b>" . date( 'm/d/Y H:i:s', $tr->time ) . "</b></li>";
            }
            $trans_list.= $temp;
        }
        $trans_list.= '</ul>';

        return $trans_list;
    }
    /**
     * Convert from value to the String of the Object Type
     *
     * @param type    $value
     */
    public static function convertObjectType( $value ) {
        $types = GxcHelpers::getAvailableContentType();
        if ( isset( $types[$value]['name'] ) ) {

            return $types[$value]['name'];
        } else {

            return t( 'cms', 'undefined' );
        }
    }
    /**
     * Do Search Object based on its status
     *
     * @param type    $type
     * @return CActiveDataProvider
     */
    public function doSearch( $type = 0 ) {
        $criteria = new CDbCriteria;
        $sort = new CSort;
        $sort->attributes = array(
            'object_id',
        );
        $sort->defaultOrder = 'object_id DESC';

        switch ( $type ) {
            //If looking for DRAFT Content

        case ConstantDefine::OBJECT_STATUS_DRAFT:
            $criteria->condition = 'object_status = :status and object_author = :uid';
            $criteria->params = array(
                ':status' => ConstantDefine::OBJECT_STATUS_DRAFT,
                ':uid' => Yii::app()->user->id
            );
            break;

        case ConstantDefine::OBJECT_STATUS_PUBLISHED:
            //Do nothing;
            $criteria->condition = 'object_status = :status';
            $criteria->params = array(
                ':status' => ConstantDefine::OBJECT_STATUS_PUBLISHED
            );
            break;

        case self::OBJECT_TYPE_GAME:
            //Do nothing;
            $criteria->condition = 'object_type = :type';
            $criteria->params = array(
                ':type' => self::OBJECT_TYPE_GAME
            );
            break;
        case self::OBJECT_TYPE_ARTICLE:
            //Do nothing;
            $criteria->condition = 'object_type = :type';
            $criteria->params = array(
                ':type' => self::OBJECT_TYPE_ARTICLE
            );
            break;
        }
        $criteria->compare( 'object_id', $this->object_id, true );
        $criteria->compare( 'object_author', $this->object_author, true );
        $criteria->compare( 'object_date', $this->object_date );
        $criteria->compare( 'object_content', $this->object_content, true );
        $criteria->compare( 'object_title', $this->object_title, true );
        $criteria->compare( 'object_name', $this->object_name, true );
        $criteria->addCondition("object_status!=".ConstantDefine::OBJECT_STATUS_DELETE);

        return new CActiveDataProvider( get_class( $this ) , array(
                'criteria' => $criteria,
                'sort' => $sort,
                'pagination'=>array(
                              'pageSize'=>20,
                          ),
            ) );
    }
    public static function buildLink( $obj ) {
        if ( $obj->object_id )
            return FRONT_SITE_URL . "/article?id=" . $obj->object_id . "&slug=" . $obj->object_slug;
        else
            return null;
    }
    public function getObjectLink() {
        if ( $this->object_id ) {
            $class_name = GxcHelpers::getClassOfContent( $this->object_type );
            if ( $class_name != 'Object' ) {
                Yii::import( 'common.content_type.' . $this->object_type . '.' . $class_name );
            }

            return $class_name::buildLink( $this );
        } else {

            return null;
        }
    }
    public function suggestContent( $keyword, $type = '', $limit = 20 ) {
        if ( $type == '' ) {
            $objects = $this->findAll( array(
                    'condition' => 'object_name LIKE :keyword',
                    'order' => 'object_id DESC',
                    'limit' => $limit,
                    'params' => array(
                        ':keyword' => '%' . strtr( $keyword, array(
                                '%' => '\%',
                                '_' => '\_',
                                '\\' => '\\\\'
                            ) ) . '%',
                    ) ,
                ) );
        } else {
            $objects = $this->findAll( array(
                    'condition' => 'object_type = :t and object_name LIKE :keyword',
                    'order' => 'object_name DESC',
                    'limit' => $limit,
                    'params' => array(
                        ':t' => trim( strtolower( $type ) ) ,
                        ':keyword' => '%' . strtr( $keyword, array(
                                '%' => '\%',
                                '_' => '\_',
                                '\\' => '\\\\'
                            ) ) . '%',
                    ) ,
                ) );
        }
        $names = array();

        foreach ( $objects as $object ) $names[] = str_replace( ";", "", $object->object_name ) . "|" . $object->object_id;

        return $names;
    }
    public static function Resources() {

        return array(
            'thumbnail' => array(
                'type' => 'thumbnail',
                'name' => '缩略图',
                'maxSize' => "10485760",
                'minSize' => "1",
                'max' => 1,
                'allow' => array(
                    'jpeg',
                    'jpg',
                    'gif',
                    'png'
                )
            )
        );
    }
    public static function Permissions() {

        return array(
            'Admin' => array(
                'allowedObjectStatus' => array(
                    ConstantDefine::OBJECT_STATUS_DRAFT => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PENDING => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PUBLISHED => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_HIDDEN => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedTransferto' => array(
                    'Editor' => array(
                        'condition' => ''
                    ) ,
                    'Reporter' => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedToCreateContent' => true,
                'allowedToUpdateContent' => ''
            ) ,
            'Editor' => array(
                'allowedObjectStatus' => array(
                    ConstantDefine::OBJECT_STATUS_DRAFT => array(
                        'condition' => 'return $params["new_content"]==true;'
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PENDING => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PUBLISHED => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_HIDDEN => array(
                        'condition' => 'return $params["new_content"]==false;'
                    ) ,
                ) ,
                'allowedTransferto' => array(
                    'Editor' => array(
                        'condition' => ''
                    ) ,
                    'Reporter' => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedToCreateContent' => true,
                'allowedToUpdateContent' => '
                                        return (($params["new_content"]==false)&&
                                        (($params["content_status"]==ConstantDefine::OBJECT_STATUS_PUBLISHED)
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_DRAFT)&&($params["content_author"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_to"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_type"]==ConstantDefine::TRANS_ROLE)&&(array_key_exists($params["trans_note"],Rights::getAssignedRoles(user()->id,true))))
                                        ));'
            ) ,
            'Reporter' => array(
                'allowedObjectStatus' => array(
                    ConstantDefine::OBJECT_STATUS_DRAFT => array(
                        'condition' => 'return
                                                           ($params["new_content"]==true) ;
                                                           '
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PENDING => array(
                        'condition' => 'return
                                                           ((($params["new_content"]==false)&&($params["content_status"]!=ConstantDefine::OBJECT_STATUS_PUBLISHED)&&(($params["trans_to"]==user()->id)||($params["trans_to"]==0)))||

                                                           ($params["new_content"]==true)) ;
                                                           '
                    ) ,
                    ConstantDefine::OBJECT_STATUS_HIDDEN => array(
                        'condition' => 'return
                                                          (($params["new_content"]==false)&&($params["content_status"]==ConstantDefine::OBJECT_STATUS_DRAFT)&&($params["content_author"]==user()->id)) ;
                                                          '
                    ) ,
                ) ,
                'allowedTransferto' => array(
                    'Editor' => array(
                        'condition' => ''
                    ) ,
                    'Reporter' => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedToCreateContent' => true,
                'allowedToUpdateContent' => '
                                        return (($params["new_content"]==false)&&
                                        ((($params["content_status"]==ConstantDefine::OBJECT_STATUS_DRAFT)&&($params["content_author"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_to"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_type"]==ConstantDefine::TRANS_ROLE)&&(array_key_exists($params["trans_note"],Rights::getAssignedRoles(user()->id,true))))
                                        )) ;'
            )
        );
    }
    /**
     * Increase the comment count by 1 whenever new comment was created.
     */
    public function increaseCommentCount() {
        if ( $this->comment_count != null ) $this->comment_count++;
        else $this->comment_count = 1;
        $this->save();
    }
    public function getResource() {
        return CMap::mergeArray( self::Resources(),
            array(
                'video'=>array( 'type'=>'video',
                    'name'=>'视频',
                    'maxSize'=>"10485760",
                    'minSize'=>"1",
                    'max'=>1,
                    'allow'=>array( 'flv',
                        'mp4', ) ),
                'image'=>array( 'type'=>'image',
                    'name'=>'图片',
                    'maxSize'=>"10485760",
                    'minSize'=>"1",
                    'max'=>10,
                    'allow'=>array( 'jpg',
                        'gif',
                        'png' ) ),
            )
        );
    }
    /**
     * get count resource of a object
     * @param  array $count_resource [description]
     * @return intval                 [description]
     */
    public function getCountResource($content_resources){
        $resource=array();
        $resource_upload=array();
        foreach($content_resources as $res)
        {                                                                                                            
           $resource_upload[]=GxcHelpers::getArrayResourceObjectBinding('resource_upload_'.$res['type']);
        }    
       
       $i=0;
       $count_resource=0;
       foreach($content_resources as $cres){
           $j=1;
           foreach ($resource_upload[$i] as $res_up){                                   
              $j++;
              $count_resource++;
          }
          $i++;
      }
      return array("resource_upload"=>$resource_upload,"count"=>$i);
    }

    /**
     * get all templetes
     * @return array [description]
     */
    public function getTempletes(){
        $templetes = Templete::model()->findAllByAttributes(array('type'=>0));
        $newArray = array();
        foreach($templetes as $k => $templete){
            $newArray[$k]['id'] = $templete->id;
            $newArray[$k]['name'] = $templete->name;
        }
        return $newArray;
    }
    /**
     * display the content view 
     * @param  arrar $id  object_id
     * @return string
     */
    public function display(){
        $object_templete = ObjectTemplete::model()->findByAttributes(array("object_id"=>$this->object_id));
        if(empty($object_templete)){
            return "";
        }
        $templete = Templete::model()->findByPk($object_templete->templete_id);
        if($templete){
            return Yii::app()->cmsRenderer->render($this,$templete->content,array("id"=>$this->object_id));
        }
        throw new CHttpException( 404, 'The requested page does not exist.' );
        
    }
    /**
     * get the object term level
     * @param  intaval $id object_id
     * @return array   
     */
    public function getObjectTermById($id){
        // $object_terms = ObjectTerm::model()->findAllByAttributes(array("object_id"=>$id));
        // $ret = array();
        // foreach($object_terms as $key=>$objectterm){
        //     if($objectterm->object_term->level == 1){
        //         continue;
        //     }else{
        //         $ret[$key]["term_id"] = $objectterm->term_id;
        //         $ret[$key]['term_name'] = $objectterm->object_term->name;
        //         $ret[$key]['url'] = $objectterm->object_term->url;
        //     }
        // }
        // return $ret;
        $criteria = new CDbCriteria;
        $criteria->condition = "object_id = :object_id";
        $criteria->params = array(":object_id"=>$id);
        $criteria->order = "term_id DESC";
        $criteria->limit = 1;
        $result = ObjectTerm::model()->findByAttributes(array(),$criteria);
        if(empty($result)){
            return "";
        }
        return Oterm::model()->getLevelByTermId($result->term_id);
    }
    /**
     * get the hot objects
     * @return object
     */
    public function getHotObjects($count){
        $criteria = new CDbCriteria;
        $criteria->condition = "ishot = :ishot AND object_status=:object_status";
        $criteria->params = array(":ishot"=>self::IS_HOT,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->limit = $count;
        $criteria->order = "object_id DESC";

        return Object::model()->findAll($criteria);
    }
    // /**
    //  * get all object of term id (include child id)
    //  * @param  $category_id
    //  * @return array
    //  */
    // public function getAllObjectOfTermIds($category_id,$count){
    //     $nids = ObjectTerm::model()->getObjectIdsByTermId($category_id);
    //     $criteria = new CDbCriteria;
    //     $criteria->condition = "object_status=:status";
    //     $criteria->params = array(":status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
    //     $criteria->limit = $count;
    //     $criteria->order = "object_id DESC";
    //     $criteria->addInCondition("object_id",$nids);

    //     $object = Object::model()->findAll($criteria);
    //     return $object;
    // }
    /**
     * get top content by category id
     * @param  intval $category_id content belongs to
     * @return array
     */
    public function getTopContentByTermId($category_id){
        $objects = ObjectTerm::model()->findAllByAttributes(array("term_id"=>$category_id));
        $ids = array();
        foreach($objects as $object){
            $ids[] = $object->object_id;
        }
        $oids = array_unique($ids);
        $criteria = new CDbCriteria;
        $criteria->condition = "istop=:istop AND object_status=:object_status";
        $criteria->params = array(":istop"=>ConstantDefine::OBJECT_ISTOP,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->addInCondition("object_id",$oids);

        return Object::model()->findByAttributes(array(),$criteria);
    }
    /**
     * get is red title Content By TermId 
     * @param  intval $this->categoryid 
     * @param  intval $this->count      
     * @return array                  
     */
    public function getRecommendContentByTermId($category_id,$count){
        $objects = ObjectTerm::model()->findAllByAttributes(array("term_id"=>$category_id));
        $ids = array();
        foreach($objects as $object){
            $ids[] = $object->object_id;
        }
        $oids = array_unique($ids);
        $criteria = new CDbCriteria;
        $criteria->condition = "isred=:isred AND object_status=:object_status";
        $criteria->params = array(":isred"=>ConstantDefine::OBJECT_ISRED,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->limit = $count;
        $criteria->addInCondition("object_id",$oids);

        return Object::model()->findAllByAttributes(array(),$criteria);
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        if($this->object_status == ConstantDefine::OBJECT_STATUS_DRAFT || $this->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED){
            $domain = Yii::app()->getModule("cms")->domain;
            $path = "a/".date('Y-m-d',strtotime($this->object_date_gmt))."/001".(strtotime($this->object_date_gmt)+$this->object_id).".html";
            $content = $this->display();
            $result = Yii::app()->publisher->saveDomainHtml($domain,$path,$content);
            if($result){
                $this->object_status = ConstantDefine::OBJECT_STATUS_PUBLISHED;
                $this->url = "/".$path;
                $this->save(false);
                $termCache = isset($this->term_cache) ? unserialize($this->term_cache) : array();
                $this->firePublished($termCache);
                return true;
            }else{
                Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
            }

        }
        return false;
    }
    /**
     * delete html 
     * @return boolean
     */
    public function doDelete(){
        if($this->object_status == ConstantDefine::OBJECT_STATUS_DELETE){
            $domain = Yii::app()->getModule("cms")->domain;
            $path = "a/".date('Y-m-d',strtotime($this->object_date_gmt))."/001".(strtotime($this->object_date_gmt)+$this->object_id).".html";
            $content = "对不起，您访问的页面已经被删除！";
            $result = Yii::app()->publisher->saveDomainHtml($domain,$path,$content);
            if($result){
                $this->url = "/".$path;
                $this->save(false);
                // $termCache = isset($this->term_cache) ? unserialize($this->term_cache) : array();
                // $this->firePublished($termCache);
                return true;
            }else{
                Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
            }
        }
        return false;
    }
    /**
     * get templete content by object id
     * @param   $object_id [description]
     * @return             [description]
     */
    public function getObjectTempleteById($object_id){
        $object_templete = ObjectTemplete::model()->findByAttributes(array("object_id"=>$object_id));
        $templete = Templete::model()->findByPk($object_templete->templete_id);
        if(empty($templete)){
            return false;
        }
        return $templete->content;
    }
    /**
     * get relation news list  
     * @param  intval $object_id   [description]
     * @param  intval $category_id [description]
     * @param  intval $count [description]
     * @return array
     */
    public function getRelationList($object_id,$count){
        $criteria = new CDbCriteria;
        $criteria->order = "term_id DESC";
        $category = ObjectTerm::model()->findByAttributes(array("object_id"=>$object_id),$criteria);
        if(empty($category)){
            return array();
        }
        $objects = ObjectTerm::model()->findAllByAttributes(array("term_id"=>$category->term_id));
        $ids = array();
        foreach($objects as $object){
            $ids[] = $object->object_id;
        }
        $oids = array_unique($ids);
        $key = array_search($object_id, $oids);
        unset($oids[$key]);

        $criteria = new CDbCriteria;
        $criteria->condition = "object_status=:object_status";
        $criteria->params = array(":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->limit = $count;
        $criteria->order = "object_id DESC";
        $criteria->addInCondition("object_id",$oids);

        return Object::model()->findAllByAttributes(array(),$criteria);
    }
    public function searchGame(){
        $criteria=new CDbCriteria;
        $criteria->order = "object_id DESC";
        $criteria->condition = "object_type = 'game'";

        return new CActiveDataProvider("Object", array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * get page title
     */
    public function getTitle(){
        return $this->object_title."_1378棋牌网";
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        return $this->object_keywords;
    }
    /**
     * get page description
     */
    public function getDescription(){
        return $this->object_description;
    }
    /**
     * get object id
     */
    public function getId(){
        return $this->object_id;
    }
    /**
     * get object term url for object menu display used in fhgame for now
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getTermUrl(){
        $object_term = ObjectTerm::model()->findByAttributes(array("object_id"=>$this->object_id));
        if(empty($object_term)){
            return "";
        }
        $term = Oterm::model()->findByPk($object_term->term_id);
        if(empty($term)){
            return "";
        }
        return "<a class=agray  href={$term->url}>$term->name</a>";
    }
    /**
     * get category id all descendants node id include self id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getAllChindrenIdByTermId($term_id){
        // var_dump($term_id);exit;
        $category = Oterm::model()->findByPk($term_id);
        if(empty($category)){
            return null;
        }
        $descendants=$category->descendants()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        // var_dump($allterms);exit;
        array_push($allterms, $term_id);
        // print_r(array_push($allterms, $term_id));exit;
        return $allterms;
    }
    /**
     * fetch all objects by term id 
     * the data include term's children id 
     * @return [type] [description]
     */
    public function fetchObjectsByTermId($term_id,$count,$page){
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$termIds);
        $criteria->order = "t.object_id DESC";
        $results = ObjectTerm::model()->findAll($criteria);
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->object_id;
        }
        $ids = array_unique($ids);


        $criteria = new CDbCriteria;
        $criteria->alias = "t";

        $criteria->addInCondition("object_id",$ids);
        $criteria->order = "t.object_id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->findAll($criteria);
    }
    /**
     * get objects count by term_id
     * @return [type] [description]
     */
    public function getObjectsCountByTermId($term_id){
        // $termIds = $this->getAllChindrenIdByTermId($term_id);
        // $criteria = new CDbCriteria;
        // $criteria->alias = "t";
        // $criteria->addInCondition("term_id",$termIds);
        // return ObjectTerm::model()->count($criteria);
        // 
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        $criteria = new CDbCriteria;
        $criteria->addInCondition("term_id",$termIds);
        $criteria->order = "t.object_id DESC";
        $results = ObjectTerm::model()->findAll($criteria);
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->object_id;
        }
        $ids = array_unique($ids);
        return count($ids);
    }
    //save object term cache
    public function updateObjectTermCache(){
        $current_temrs = ObjectTerm::model()->getAncestorsIdsByObject($this->object_id);
        $this->term_cache = serialize($current_temrs);
        $this->save(false);
        return true;
    }
    /**
     * get object of search
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public function getResultOfSearch($ids){
        $criteria = new CDbCriteria;
        $criteria->addInCondition("object_id",$ids);
        return self::model()->findAll($criteria);
    }
    /**
     * get object by time
     * @param  [type] $reg_date [description]
     * @param  [type] $num      [description]
     * @return [type]           [description]
     */
    public function getObjectsByTime($req_date,$offset,$num){
        $criteria = new CDbCriteria;
        $begin = $req_date ." 00:00:00";
        $end = date('Y-m-d')." 00:00:00";
        $criteria->condition = "object_date >= :begin AND object_date <= :end AND object_status = :object_status";
        $criteria->params = array(":begin"=>$begin,":end"=>$end,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->offset = $offset;
        $criteria->limit = $num;

        return self::model()->findAll($criteria);
    }
}

    
