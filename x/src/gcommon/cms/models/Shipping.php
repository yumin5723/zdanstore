<?php
/**
 * This is the model class for table "click".
 *
 * The followings are the available columns in table 'game':
 */
class Shipping extends CmsActiveRecord
{
    const SHIPPING_WIGHT_UNIT = 500;
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
        return 'shipping';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
           array('country,first_weight_price,add_weight_price','required'),
           array('country','unique'),
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
        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('country',$this->country,true);
        $criteria->compare('first_weight_price',$this->first_weight_price,true);
        $criteria->compare('add_weight_price',$this->add_weight_price,true);
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
        if (!empty($attributes['country']) && $attributes['country'] != $this->country) {
            $attrs[] = 'country';
            $this->country = $attributes['country'];
        }
        if (!empty($attributes['first_weight_price']) && $attributes['first_weight_price'] != $this->first_weight_price) {
            $attrs[] = 'first_weight_price';
            $this->first_weight_price = $attributes['first_weight_price'];
        }
        if (!empty($attributes['add_weight_price']) && $attributes['add_weight_price'] != $this->add_weight_price) {
            $attrs[] = 'add_weight_price';
            $this->add_weight_price = $attributes['add_weight_price'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
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