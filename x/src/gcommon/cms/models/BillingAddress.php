<?php

class BillingAddress extends CmsActiveRecord
{
    public $uid;
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'billing_address';
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'created',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * Returns the static model of the specified AR class.
     * This method is required by all child classes of CActiveRecord.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * set attribution validator rules
     *
     *
     * @return
     */
    public function rules() {
        $rules =  array(
            array('firstname,lastname,address,phone,city,country,zipcode','required'),
            //array('email','email'),
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }

    /**
     * function_descriptionaddress
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['name']) || $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['phone']) || $attributes['phone'] != $this->phone) {
            $attrs[] = 'phone';
            $this->phone = $attributes['phone'];
        }
        if (!empty($attributes['address']) || $attributes['address'] != $this->address) {
            $attrs[] = 'address';
            $this->address = $attributes['address'];
        }
        if (!empty($attributes['country']) || $attributes['country'] != $this->country) {
            $attrs[] = 'country';
            $this->country = $attributes['country'];
        }
        if (!empty($attributes['city']) || $attributes['city'] != $this->city) {
            $attrs[] = 'city';
            $this->city = $attributes['city'];
        }
        if (!empty($attributes['zipcode']) || $attributes['zipcode'] != $this->zipcode) {
            $attrs[] = 'zipcode';
            $this->zipcode = $attributes['zipcode'];
        }
        if (!empty($attributes['uid']) || $attributes['uid'] != $this->uid) {
            $attrs[] = 'uid';
            $this->uid = $attributes['uid'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * updata profile info if profile is emtpy create one
     * @return [type] [description]
     */
    public function updateInfo($data){
        $this->updateAttrs($data);
        return true;
    }
    
    public function getAlldatas($uid){
        $count = 3;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $this->getCount($uid);
        $chargeRecords = $this->getAllChargeRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/address/p/",2);
        $p = $subPages->show_SubPages(2);
        return [$chargeRecords,$p];
    }
    public function getAllChargeRecords($uid,$count,$page){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $criteria->order = "t.id DESC";
        $criteria->group = 't.id'; 
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas;
    }
    public function getCount($uid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $counts = self::model()->count($criteria);
        return $counts;
    }
    public function updateDefault($attributes){
        $attrs = array();
        if (!empty($attributes['default']) || $attributes['default'] != $this->default) {
            $attrs[] = 'default';
            $this->default = 1;
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    public function getDefaultAddress($uid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $criteria->addCondition("t.default=1");
        return self::model()->findAll($criteria);
    }
    /**
     * create address for user
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createAddress($data){
        $model = new self;
        $model->uid = Yii::app()->user->id;
        $model->name = $data['name'];
        $model->address = $data['address'];
        $model->zipcode = $data['zipcode'];
        $model->country = $data['country'];
        $model->sava(false);
        return $model->id;
    }
    /**
     * get all country
     * status is self::PRODUCT_STATUS_SELL
     * @return [type] [description]
     */
    public function getCu(){
        return array(
            "Argentina"=>"Argentina",   
            "Australia"=>"Australia",
            "Austria"=>"Austria",
            "Barbados"=>"Barbados",
            "Belgium"=>"Belgium",
            "Bolivia"=>"Bolivia",
            "Brazil"=>"Brazil",
            "Brunei Darussalam"=>"Brunei Darussalam",
            "Canada"=>"Canada",
            "Cayman Islands"=>"Cayman Islands",
            "Chile"=>"Chile",
            "Colombia"=>"Colombia",
            "Cook Islands"=>"Cook Islands",
            "Costa Rica"=>"Costa Rica",
            "Croatia"=>"Croatia",
            "Cyprus"=>"Cyprus",
            "Czech Republic"=>"Czech Republic",
            "Denmark"=>"Denmark",
            "Dominican Republic"=>"Dominican Republic",
            "Ecuador"=>"Ecuador",
            "Egypt"=>"Egypt",
            "Estonia"=>"Estonia",
            "Fiji"=>"Fiji",
            "Finland"=>"Finland",
            "France"=>"France",
            "Germany"=>"Germany",
            "Greece"=>"Greece",
            "Greenland "=>"Greenland",
            "Guam "=>"Guam",
            "Hungary"=>"Hungary",
            "Iceland"=>"Iceland",
            "India"=>"India",
            "Indonesia"=>"Indonesia",
            "Ireland"=>"Ireland",
            "Israel"=>"Israel",
            "Italy"=>"Italy",
            "Jamaica"=>"Jamaica",
            "Japan"=>"Japan",
            "South Korea"=>"South Korea",
            "Latvia"=>"Latvia",
            "Lithuania"=>"Lithuania",
            "Luxembourg"=>"Luxembourg",
            "Malawi"=>"Malawi",
            "Malaysia"=>"Malaysia",
            "Mexico"=>"Mexico",
            "Monaco"=>"Monaco",
            "Netherlands"=>"Netherlands",
            "Netherlands Antilles"=>"Netherlands Antilles",
            "New Caledonia"=>"New Caledonia",
            "New Zealand"=>"New Zealand",
            "Norway"=>"Norway",
            "Oman"=>"Oman",
            "Panama"=>"Panama",
            "Peru"=>"Peru",
            "Philippines"=>"Philippines",
            "Poland"=>"Poland",
            "Portugal"=>"Portugal",
            "Puerto Rico"=>"Puerto Rico",
            "Republic of Kuwait"=>"Republic of Kuwait",
            "Russian Federation"=>"Russian Federation",
            "Saudi Arabia"=>"Saudi Arabia",
            "Slovakia "=>"Slovakia",
            "Slovenia"=>"Slovenia",
            "Singapore"=>"Singapore",
            "South Africa"=>"South Africa",
            "Spain"=>"Spain",
            "Sweden"=>"Sweden",
            "Switzerland"=>"Switzerland",
            "Taiwan"=>"Taiwan",
            "Thailand"=>"Thailand",
            "Trinidad and Tobago"=>"Trinidad and Tobago",
            "Turkey"=>"Turkey",
            "United Arab Emirates"=>"United Arab Emirates",
            "United Kingdom"=>"United Kingdom",
            "Uruguay"=>"Uruguay",
            "Venezuela"=>"Venezuela",
            "Kazakhstan"=>"Kazakhstan",
            );
    }
}