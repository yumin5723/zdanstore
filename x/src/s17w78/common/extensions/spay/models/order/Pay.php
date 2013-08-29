<?php

class Pay extends SpayActiveRecord {
    /* pay just created */
    const STATUS_CREATED = 0;

    /* pay get response true */
    const STATUS_PAY_SUCCESS = 1;

    /* pay fail */
    const STATUS_PAY_FAIL = 2;

    /* pay get callback */
    const STATUS_GOT_CALLBACK = 3;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
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
	return 'pay';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
	// NOTE: you should only define rules for those attributes that
	// will receive user inputs.
	return array(
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
     * get order this pay belong to
     *
     *
     * @return
     */
    public function getOrder() {
	return Order::model()->findByPk($this->order_id);
    }


    /**
     * save pay success response and status
     *
     * @param $return_body:
     *
     * @return
     */
    public function savePaySuccess($return_body, $msg="") {
	// log pay
	$this->savePay($return_body, self::STATUS_PAY_SUCCESS, $msg);

	// update order
	// callback may have received
	$criteria = new CDbCriteria;
	$criteria->addNotInCondition('pay_status',array(
		Order::PAY_STATUS_WAITING,
		Order::PAY_STATUS_SUCCESS_CALLBACK,
	    ));
	Order::model()->updateByPk($this->getOrder()->id,
	    array(
		'pay_status' => Order::PAY_STATUS_WAITING,
		'pay_id' => $this->id,
	    ),
	    $criteria
	);
	return true;
    }

    /**
     * save pay failed and status
     *
     * @param $return_body:
     *
     * @return
     */
    public function savePayFailed($return_body, $msg="") {
	// log pay
	$this->savePay($return_body, self::STATUS_PAY_FAIL, $msg);

	// update order
	// callback may have received
	$criteria = new CDbCriteria;
	$criteria->addNotInCondition('pay_status',array(
		Order::PAY_STATUS_SUCCESS_CALLBACK,
	    ));
	Order::model()->updateByPk($this->getOrder()->id,
	    array(
		'pay_status' => Order::PAY_STATUS_FAIL,
		'pay_id' => $this->id,
	    ),
	    $criteria
	);
	return true;
    }

    /**
     * function_description
     *
     * @param $return_body:
     * @param $status:
     *
     * @return
     */
    protected function savePay($return_body, $status, $msg="") {
	$this->saveAttributes(array(
	    'pay_time' => new CDbExpression('NOW()'),
	    'pay_return' => (string)$return_body,
	    "pay_msg" => $msg,
	    ));
	// update pay status
	// callback may have received
	self::model()->updateByPk($this->id,array(
		'status' => $status,
	    ),
	    "status <> :status_callback",
	    array(
		':status_callback'=>self::STATUS_GOT_CALLBACK,
	    )
	);
    }

    public function getChannelName() {
	list($channel, $method) = explode("|", $this->pay_method);
	return $channel;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getMethodName() {
	list($channel, $method) = explode("|", $this->pay_method);
	return $method;
    }


}