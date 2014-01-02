<?php
/**
 * This is the model class for table "click".
 *
 * The followings are the available columns in table 'game':
 */
class Click extends CActiveRecord
{
    public $upload;
    const AD_POSITION_INDEX_FOCUS = 1;
    const AD_POSITION_INDEX_RIGHT = 2;
    const AD_POSITION_INDEX_DOWN = 3;
    const AD_POSITION_BRAND_BANNER = 4;
    const AD_POSITION_MENU_MENS = 5;
    const AD_POSITION_MENU_WOMENS = 6;
    const AD_POSITION_MENU_HATS = 7;
    const AD_POSITION_MANS_FOCUS = 8;
    const AD_POSITION_MANS_RIGHT = 9;
    const AD_POSITION_MANS_FOOTER = 10;
    const AD_POSITION_WOMANS_FOCUS = 11;
    const AD_POSITION_WOMANS_FOOTER = 12;
    const AD_POSITION_HATS_FOCUS = 13;
    const AD_POSITION_HATS_FOOTER = 14;
    const AD_POSITION_SALE_FOCUS = 15;
    const AD_POSITION_MANS_TERM_BANNER = 16;
    const AD_POSITION_ORDER_COMPLETE = 17;
    const AD_POSITION_WOMENS_TERM_BANNER = 18;
    const AD_POSITION_HATS_TERM_BANNER = 19;
    const AD_POSITION_MANS_FOOTER_BIG = 20;
    const AD_POSITION_WOMANS_FOOTER_BIG = 21;
    const AD_POSITION_HATS_FOOTER_BIG = 22;
    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
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
        return 'click';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,image,url,type','required'),
            array('upload', 'file','allowEmpty'=>true),
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
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('url',$this->created,true);
        $criteria->compare('created',$this->created,true);

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
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['url']) && $attributes['url'] != $this->url) {
            $attrs[] = 'url';
            $this->url = $attributes['url'];
        }
        if (!empty($attributes['type']) && $attributes['type'] != $this->type) {
            $attrs[] = 'type';
            $this->type = $attributes['type'];
        }
        if (!empty($attributes['image'])) {
            $attrs[] = 'image';
            $this->image = $attributes['image'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * get all ad types 
     * status is self::PRODUCT_STATUS_SELL
     * @return [type] [description]
     */
    public function getTypes(){
        return array(self::AD_POSITION_INDEX_FOCUS => "首页焦点图广告",self::AD_POSITION_INDEX_RIGHT => "首页焦点图右侧"
            ,self::AD_POSITION_INDEX_DOWN=>"首页尾部广告",self::AD_POSITION_BRAND_BANNER=>"品牌页banner",
            self::AD_POSITION_MENU_MENS=>"导航条MENS广告",self::AD_POSITION_MENU_WOMENS=>"导航条WOMENS广告",
            self::AD_POSITION_MENU_HATS=>'导航条HATS广告',self::AD_POSITION_MANS_FOCUS=>'男装首页焦点图',
            self::AD_POSITION_MANS_RIGHT=>'男装首页右侧广告',self::AD_POSITION_MANS_FOOTER=>"男装首页尾部广告小图",
            self::AD_POSITION_WOMANS_FOCUS=>'女装首页焦点图',self::AD_POSITION_WOMANS_FOOTER=>'女装首页尾部广告小图',
            self::AD_POSITION_HATS_FOCUS=>'hats首页焦点图',self::AD_POSITION_HATS_FOOTER=>'hats首页尾部广告小图',
            self::AD_POSITION_SALE_FOCUS=>'SALE页焦点图',self::AD_POSITION_MANS_TERM_BANNER=>'男装分类页横幅广告',
            self::AD_POSITION_ORDER_COMPLETE=>'订单成功页广告',self::AD_POSITION_WOMENS_TERM_BANNER=>'女装分类页横幅广告',
            self::AD_POSITION_HATS_TERM_BANNER=>'hats分类页横幅广告',self::AD_POSITION_MANS_FOOTER_BIG=>'男装首页尾部广告大图',
            self::AD_POSITION_WOMANS_FOOTER_BIG=>'女装首页尾部广告大图',self::AD_POSITION_HATS_FOOTER_BIG=>'hats首页尾部广告大图'
            );
    }
    /**
     * [convertProductIsNew description]
     * @param  [type] $isNew [description]
     * @return [type]        [description]
     */
    static public function convertAdTypes($type){
        if($type == self::AD_POSITION_INDEX_FOCUS){
            return "首页焦点图广告";
        }elseif($type == self::AD_POSITION_INDEX_RIGHT){
            return "首页焦点图右侧";
        }elseif($type == self::AD_POSITION_INDEX_DOWN){
            return "首页尾部广告";
        }elseif($type == self::AD_POSITION_MENU_MENS){
            return "导航条MENS广告";
        }elseif($type == self::AD_POSITION_MENU_WOMENS){
            return "导航条WOMENS广告";
        }elseif($type == self::AD_POSITION_MENU_HATS){
            return "导航条HATS广告";
        }elseif($type == self::AD_POSITION_MANS_FOCUS){
            return "男装首页焦点图";
        }elseif($type == self::AD_POSITION_MANS_RIGHT){
            return "男装首页右侧广告";
        }elseif($type == self::AD_POSITION_MANS_FOOTER){
            return "男装首页尾部广告小图";
        }elseif($type == self::AD_POSITION_WOMANS_FOCUS){
            return "女装首页焦点图";
        }elseif($type == self::AD_POSITION_WOMANS_FOOTER){
            return "女装首页尾部广告小图";
        }elseif($type == self::AD_POSITION_HATS_FOCUS){
            return "hats首页焦点图";
        }elseif($type == self::AD_POSITION_HATS_FOOTER){
            return "hats首页尾部广告小图";
        }elseif($type == self::AD_POSITION_SALE_FOCUS){
            return "SALE页焦点图";
        }elseif($type == self::AD_POSITION_MANS_TERM_BANNER){
            return "男装分类页横幅广告";
        }elseif($type == self::AD_POSITION_ORDER_COMPLETE){
            return "订单成功页广告";
        }elseif($type == self::AD_POSITION_WOMENS_TERM_BANNER){
            return "女装分类页横幅广告";
        }elseif($type == self::AD_POSITION_HATS_TERM_BANNER){
            return "hats分类页横幅广告";
        }elseif($type == self::AD_POSITION_WOMANS_FOOTER_BIG){
            return "女装首页尾部广告大图";
        }elseif($type == self::AD_POSITION_MANS_FOOTER_BIG){
            return "男装首页尾部广告大图";
        }elseif($type == self::AD_POSITION_HATS_FOOTER_BIG){
            return "hats首页尾部广告大图";
        }
        else{
            return "品牌页banner";
        }
    }
    /**
     * [getAdsByType description]
     * @param  [type] $type  [description]
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
    public function getAdsByType($type,$limit = 2){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->limit = $limit;
        $criteria->condition = "type = :type";
        $criteria->params = array(":type"=>$type);
        return self::model()->findAll($criteria);
    }
}