<?php
/**
 * This is the model class for table "deliverynote".
 *
 * The followings are the available columns in table 'deliverynote':
 */
class DeliveryNote extends CmsActiveRecord
{
    const DELIVERY_ALL_PRODUCT = 0;
    const DELIVERY_PART_PRODUCT = 1;
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
        return 'deliverynote';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id,express_number,delivery_time,status,uid','required'),
            array('admin_uid','safe'),
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
            'admin'=>array(self::BELONGS_TO, 'Manager',
                    'admin_uid'),
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
        $criteria->compare('order_id',$this->order_id,true);
        $criteria->compare('express_number',$this->express_number,true);
        $criteria->compare('uid',$this->uid,true);
        $criteria->compare('status',$this->status,true);
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
        if (!empty($attributes['order_id']) && $attributes['order_id'] != $this->order_id) {
            $attrs[] = 'order_id';
            $this->order_id = $attributes['order_id'];
        }
        if (!empty($attributes['express_number']) && $attributes['express_number'] != $this->express_number) {
            $attrs[] = 'express_number';
            $this->express_number = $attributes['express_number'];
        }
        if (!empty($attributes['uid']) && $attributes['uid'] != $this->uid) {
            $attrs[] = 'uid';
            $this->uid = $attributes['uid'];
        }
        if (!empty($attributes['delivery_time']) && $attributes['delivery_time'] != $this->delivery_time) {
            $attrs[] = 'delivery_time';
            $this->delivery_time = $attributes['delivery_time'];
        }
        if (!empty($attributes['status']) && $attributes['status'] != $this->status) {
            $attrs[] = 'status';
            $this->status = $attributes['status'];
        }
        $this->admin_uid = Yii::app()->user->id;
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
     /**
     * get all delivery status
     * @return [type] [description]
     */
    public function getDeliveryStatus(){
        return array(self::DELIVERY_ALL_PRODUCT => "全部发货",self::DELIVERY_PART_PRODUCT => "部分发货");
    }
}