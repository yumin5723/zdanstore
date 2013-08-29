<?php
Yii::import('spay.PayParam');
Yii::import('spay.ChargeParam');
Yii::import('spay.models.order.Pay');
Yii::import('spay.models.order.Charge');

/**
 * This is the model class for table "order".
 *
 * The followings are the available columns in table 'order':
 * @property integer $id
 * @property integer $uid
 * @property string $charge_param
 * @property string $pay_param
 * @property integer $order_type
 * @property string $order_from
 * @property string $order_amt
 * @property string $pay_amt
 * @property string $real_amt
 * @property string $charge_amt
 * @property integer $pay_status
 * @property integer $pay_id
 * @property integer $charge_status
 * @property integer $charge_id
 * @property string $created
 * @property string $modified
 */
class Order extends SpayActiveRecord
{
    // direct charge order type, always use card
    const ORDER_TYPE_DIRECT_CHARGE = 1;

    // web pay order type
    const ORDER_TYPE_REDIRECT = 2;

    // mh gold pay order type
    const ORDER_TYPE_MH_GOLD = 3;

    // pay status not paid
    const PAY_STATUS_UNPAID = 0;

    // pay status receive success from channel
    const PAY_STATUS_SUCCESS_CALLBACK = 1;

    // pay status waiting, pay have submit to PAY SERVER.
    const PAY_STATUS_WAITING = 2;

    // pay status fail
    const PAY_STATUS_FAIL = 4;

    // pay status recieved callback from channel, but paid fail
    const PAY_STATUS_FAIL_CALLBACK = 5;

    // mark order can not pay, unknow pay method etc..
    const PAY_STATUS_CAN_NOT_PAY = 6;

    // charge status not charged
    const CHARGE_STATUS_UNCHARGED = 0;

    // charge status success
    const CHARGE_STATUS_SUCCESS = 1;

    // notice pay fail
    const CHARGE_STATUS_FAIL_NOTICED = 2;

    const CHARGE_INTERVAL = 600;
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
	return 'order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
	// NOTE: you should only define rules for those attributes that
	// will receive user inputs.
	return array(
	    /*
	     * array('charge_param, pay_param, order_type, order_from, order_amt, pay_amt, real_amt, charge_amt, pay_status', 'required'),
	     * array('uid, order_type, pay_status, pay_id, charge_id', 'numerical', 'integerOnly'=>true),
	     * array('charge_param, pay_param', 'length', 'max'=>255),
	     * array('order_from, order_amt, pay_amt, real_amt, charge_amt', 'length', 'max'=>32),
	     * array('created, modified', 'safe'),
	     * // The following rule is used by search().
	     * // Please remove those attributes that should not be searched.
	     * array('id, uid, charge_param, pay_param, order_type, order_from, order_amt, pay_amt, real_amt, charge_amt, pay_status, pay_id, charge_id, created, modified', 'safe', 'on'=>'search'),
	     */
	    array('app_id','required','message'=>'请选择游戏','on'=>'check'),
	    array('cp_id','required','on'=>'check'),
	    array('order_amt','required','message'=>'请输入≥1的整数!','on'=>'check'),
	    array('order_amt','numerical', 'integerOnly'=>true, 'message'=>'请输入≥1的整数!','min'=>1,'tooSmall'=>'请输入≥1的整数!','on'=>'check'),
	    array('order_amt','checkamount','on'=>'check'),
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
    public function behaviors(){
	return array(
	    'CTimestampBehavior' => array(
		'class' => 'zii.behaviors.CTimestampBehavior',
		'createAttribute' => 'created',
		'updateAttribute' => 'modified',
	    )
	);
    }

    public function checkamount($attribute,$params){
    	$gold = UserGoldTotal::model()->findByPk(Yii::app()->user->id);
    	if(empty($gold)){
    		$this->addError('order_amt', '对不起，您的点券不足以抵扣！');
    	}elseif($this->order_amt>$gold->gold){
    		$this->addError('order_amt', '对不起，您的点券不足以抵扣！');
    	}
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
	return array(
	    'id' => '订单号',
	    'uid' => '用户ID',
	    'charge_param' => 'Charge Param(s)',
	    'pay_param' => 'Pay Param(s)',
	    'order_type' => '订单类型',
	    'order_from' => '订单来源',
	    'order_amt' => '订单金额',
	    'pay_amt' => '支付金额',
	    'real_amt' => '真实金额',
	    'charge_amt' => '充值金额',
	    'pay_status' => '支付状态',
	    'pay_id' => 'Pay',
	    'app_id' => '游戏',
	    'charge_status' => '充值状态',
	    'charge_id' => 'Charge',
	    'created' => '创建',
	    'modified' => '最后修改',
	);
    }

    /**
     * create new order
     * call by inter use
     *
     * @param uid: user id
     * @param order_type: order type
     * @param order_from: this order come from(app, user...)
     * @param order_amt: order amount
     * @param pay_param: param(s) use for pay
     * @param charge_param: param(s) use for charge
     *
     * @return new order, err
     */
    public function createNew($uid, $order_type, $order_from, $order_amt, $pay_param=null, $charge_param=null, $cp_id=null, $app_id=null) {
	$order = new self();
	$order->uid = intval($uid);

	// set order type
	$order->order_type = intval($order_type);

	// set order from
	$order->order_from = (string)$order_from;

	// set order amount
	if (!is_numeric($order_amt)) {
	    return [null, new Error(ORDER_AMOUNT_NOT_NUMERIC, "Order amount must be numeric: ". $order_amt)];
	}
	$order->order_amt = $order_amt;

	// set pay param(s)
	if (empty($pay_param)) {
	    $order->pay_param = "";
	} elseif (is_array($pay_param)) {
	    $order->pay_param = serialize($pay_param);
	} elseif ($pay_param instanceof PayParam) {
	    $order->pay_param = $pay_param->toString();
	} else {
	    $order->pay_param = (string) $pay_param;
	}

	// set charge param(s)
	if (empty($charge_param)) {
	    $order->charge_param = "";
	} elseif (is_array($charge_param)) {
	    $order->charge_param = serialize($charge_param);
	} elseif ($charge_param instanceof ChargeParam) {
	    $order->charge_param = $charge_param->toString();
	} else {
	    $order->charge_param = (string)$charge_param;
	}

	if (!empty($cp_id)) {
	    $order->cp_id = intval($cp_id);
	}

	if (!empty($app_id)) {
	    $order->app_id = intval($app_id);
	}

	// set default values
	$order->pay_amt = 0;
	$order->real_amt = 0;
	$order->charge_amt = 0;
	$order->pay_status = self::PAY_STATUS_UNPAID;
	$order->charge_status = self::CHARGE_STATUS_UNCHARGED;

	//save order
	$order->save(false);

	return [$order, null];
    }

    /**
     * function_description
     *
     *
     * @return Pay
     */
    public function createNewPay($channel_name, $pay_method) {
	$new_pay = new Pay();
	$new_pay->setAttributes(array(
		'order_id' => $this->id,
		'status' => PAY::STATUS_CREATED,
		'pay_method' => $channel_name."|".$pay_method,
		'pay_time' => new CDbExpression('NOW()'),
	    ), false);
	if(!$new_pay->save(false)) {
	    Yii::log("Error on saving order pay", CLogger::LEVEL_ERROR, 'order');
	    return null;
	}
	return $new_pay;
    }


    /**
     * get order pay param
     *
     *
     * @return PayParam
     */
    public function getPayParam() {
	if (empty($this->pay_param)) {
	    return null;
	}
	return new PayParam($this->pay_param);
    }

    /**
     * get charge param
     *
     *
     * @return
     */
    public function getChargeParam() {
	if (empty($this->charge_param)) {
	    return null;
	}
	return new ChargeParam($this->charge_param);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function canNotPay() {
	$this->saveAttributes(array('pay_status'=>self::PAY_STATUS_CAN_NOT_PAY));
    }




    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
	// Warning: Please modify the following code to remove attributes that
    // should not be searched.

    /*
	 * $criteria=new CDbCriteria;
	 *
	 * $criteria->compare('id',$this->id);
	 * $criteria->compare('uid',$this->uid);
	 * $criteria->compare('charge_param',$this->charge_param,true);
	 * $criteria->compare('pay_param',$this->pay_param,true);
	 * $criteria->compare('order_type',$this->order_type);
	 * $criteria->compare('order_from',$this->order_from,true);
	 * $criteria->compare('order_amt',$this->order_amt,true);
	 * $criteria->compare('pay_amt',$this->pay_amt,true);
	 * $criteria->compare('real_amt',$this->real_amt,true);
	 * $criteria->compare('charge_amt',$this->charge_amt,true);
	 * $criteria->compare('pay_status',$this->pay_status);
	 * $criteria->compare('pay_id',$this->pay_id);
 	 * $criteria->compare('charge_id',$this->charge_id);
	 * $criteria->compare('created',$this->created,true);
	 * $criteria->compare('modified',$this->modified,true);
	 *
	 * return new CActiveDataProvider($this, array(
	 *     'criteria'=>$criteria,
	 * ));
	 */
    }
    
    /**
     * update order for pay success
     *
     *
     * @return
     */
    public function OrderPaySuccess($pay_amount, $real_amount, $charge_amount, $pay_id=null, $pay_callback=null, $msg="") {
	//get and save pay
	$this->savePayCallback($pay_id, $pay_callback,$msg);

	// check order's pay status
	if ($this->pay_status == self::PAY_STATUS_SUCCESS_CALLBACK) {
	    Yii::log("Duplicate to set order pay success for order:".$this->id, CLogger::LEVEL_WARNING, "order");
	    return false;
	}

	// update order
	$s_attrs=array(
	    'pay_amt' => $pay_amount,
	    'real_amt' => $real_amount,
	    'charge_amt' => $charge_amount,
	    'pay_status' => self::PAY_STATUS_SUCCESS_CALLBACK,
	    'charge_status' => self::CHARGE_STATUS_UNCHARGED,
	    'next_charge_time' => new CDbExpression('NOW()'),
	);
	if (!empty($pay_id)) {
	    $s_attrs['pay_id'] = intval($pay_id);
	}
	$ret = $this->saveAttributes($s_attrs);
	if ($ret) {
	    return true;
	} else {
	    Yii::log("Error on saving order", CLogger::LEVEL_ERROR, "order");
	    return false;
	}
    }

    /**
     * set Order pay fail
     *
     * @param $pay_id:
     * @param $pay_callback:
     *
     * @return boolean
     */
    public function OrderPayFail($pay_id=null, $pay_callback=null, $msg="") {
	$this->savePayCallback($pay_id, $pay_callback, $msg);

	//check order's status
	if ($this->pay_status == self::PAY_STATUS_SUCCESS_CALLBACK || $this->pay_status == self::PAY_STATUS_FAIL_CALLBACK) {
	    Yii::log("Duplicate to set order pay callback status for order:".$this->id, CLogger::LEVEL_WARNING, "order");
	    return false;
	}
	if ($this->pay_status != self::PAY_STATUS_WAITING) {
	    Yii::log("Receive callback for order that unpaid:".$this->id, CLogger::LEVEL_WARNING, "order");
	}
	$s_attrs = array(
	    'pay_status' => self::PAY_STATUS_FAIL_CALLBACK,
	);
	if (!empty($pay_id)) {
	    $s_attrs['pay_id'] = intval($pay_id);
	}
	$ret = $this->saveAttributes($s_attrs);
	if ($ret) {
	    return true;
	} else {
	    Yii::log("Error on saving order", CLogger::LEVEL_ERROR, "order");
	    return false;
	}
    }


    /**
     * save pay callback
     *
     * @param $pay_id:
     * @param $pay_callback:
     *
     * @return pay
     */
    public function savePayCallback($pay_id, $pay_callback, $msg="") {
	$pay = $this->getPayById($pay_id);
	if (empty($pay)) {
	    $pay = new Pay();
	    $pay->order_id = $this->id;
	}
	$pay->status = Pay::STATUS_GOT_CALLBACK;
	if (is_array($pay_callback)) {
	    $pay_callback = json_encode($pay_callback);
	}
	$pay->pay_callback = $pay_callback;
	$pay->pay_msg = $msg;
	$pay->pay_callback_time = new CDbExpression('NOW()');
	if (!$pay->save(false)) {
	    Yii::log("Error on saving Pay", CLogger::LEVEL_ERROR, "order");
	}
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function chargeSuccess($ret_body, $status=self::CHARGE_STATUS_SUCCESS) {
	$charge = new Charge;
	$charge->order_id = $this->id;
	$charge->charge_time = new CDbExpression('NOW()');
	$charge->charge_return = $ret_body;
	$charge->status = Charge::STATUS_CHARGE_SUCCESS;
	if ($charge->save(false)) {
	    $charge_id = $charge->id;
	} else {
	    $charge_id = 0;
	}

	$this->saveAttributes(array(
		'charge_status' => $status,
		'charge_id' => $charge_id,
	    ));
    }

    /**
     * function_description
     *
     * @param $ret_body:
     *
     * @return
     */
    public function chargeFail($ret_body) {
	$charge = new Charge;
	$charge->order_id = $this->id;
	$charge->charge_time = new CDbExpression('NOW()');
	$charge->charge_return = $ret_body;
	$charge->status = Charge::STATUS_CHARGE_FAIL;
	$charge->save(false);
	$this->saveAttributes(array(
		'next_charge_time' => Yii::app()->dateFormatter->format('yyyy-MM-dd HH:mm:ss',
				    (is_null($this->next_charge_time) ? time() : strtotime($this->next_charge_time)) + self::CHARGE_INTERVAL)
	    ));
    }

    /**
     * get pay for this order
     *
     * @param $pay_id:
     *
     * @return Pay
     */
    public function getPayById($pay_id) {
	return Pay::model()->findByAttributes(array(
		'id' => intval($pay_id),
		'order_id' => $this->id,
	    ));
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getPay() {
	if (empty($this->pay_id)) {
	    return null;
	}
	return $this->getPayById($this->pay_id);
    }



    /**
     * get direct card order need pay
     *
     * @param $num:
     *
     * @return [Order]
     */
    public function getDirectOrdersNeedPay($num=10) {
	$criteria = new CDbCriteria;
	$criteria->condition = "pay_status = :pay_status AND order_type = :order_type";
	$criteria->order = '`id` DESC';
	$criteria->limit = $num;
	$criteria->params = array(':pay_status' => self::PAY_STATUS_UNPAID,':order_type' => self::ORDER_TYPE_DIRECT_CHARGE);
	return self::model()->findAll($criteria);
    }

    /**
     * get orders need for charge
     *
     * @param $num:
     *
     * @return
     */
    public function getOrdersNeedCharge($num=10) {
	$criteria = new CDbCriteria;
	$criteria->condition = "pay_status = :pay_status AND charge_status = :charge_status AND next_charge_time <= :time";
	$criteria->order = '`id` DESC';
	$criteria->limit = $num;
	$criteria->params = array(
	    ':pay_status' => self::PAY_STATUS_SUCCESS_CALLBACK,
	    ':charge_status' => self::CHARGE_STATUS_UNCHARGED,
	    ':time' => date('Y-m-d H:i:s', time()),
	);
	return self::model()->findAll($criteria);
    }

    /**
     * function_description
     *
     * @param $num:
     *
     * @return
     */
    public function getOrdersNeedNotifyFail($num=10) {
	$criteria = new CDbCriteria;
	$criteria->addInCondition("pay_status", array(self::PAY_STATUS_FAIL, self::PAY_STATUS_FAIL_CALLBACK, self::PAY_STATUS_CAN_NOT_PAY));
	$criteria->addCondition("charge_status = :charge_status");
	$criteria->addCondition("next_charge_time <= :time or next_charge_time IS NULL");
	$criteria->order = '`id` DESC';
	$criteria->limit = $num;
	$criteria->params += array(
	    ':charge_status' => self::CHARGE_STATUS_UNCHARGED,
	    ':time' => date('Y-m-d H:i:s', time()),
	);
	return self::model()->findAll($criteria);

    }




    /**
     * this function is get order count by date
     * @param $start datetime
     * @param $end datetime
     * return int
     */
    public function getCountByDate($start,$end){
	if($start == ""){
	   return self::model()->count();
	}
	$criteria = new CDbCriteria;
	$start = $start." 00:00:00";
	$end = $end." 23:59:59";
	$criteria->condition = "created >=:start AND created <=:end";
	$criteria->params = array(':start'=>$start,':end'=>$end);

	return self::model()->count($criteria);
    }
    /**
     * order list
     * @param int $count
     * @param int $page
     * @param $start datetime
     * @param $end datetime
     * return array
     */
    public function fetchOrdersByDate($count,$page,$start,$end){
	$criteria=new CDbCriteria;
	if($start != "" && $end != ""){
	    $start = $start." 00:00:00";
	    $end = $end." 23:59:59";
	    $criteria->condition = "created >=:start AND created <=:end";
	    $criteria->params = array(':start'=>$start,':end'=>$end);
	}
	$criteria->limit = $count;
	$criteria->offset = ($page - 1) * $count;
	$criteria->order = "t.id DESC";
	return self::model()->findAll($criteria);
    }

    /**
     * get order list for controller
     *
     * @param $params:
     *
     * @return CActiveDataProvider
     */
    public function getOrderList($params, $cp_id=null) {
	$criteria = new CDbCriteria();
	$criteria->order = "id DESC";
	$criteria->params=array();

	if (!empty($cp_id)) {
	    $criteria->addCondition("cp_id=:cp_id");
	    $criteria->params[':cp_id']=intval($cp_id);
	}

	// range
	if (isset($params['range'])) {
	    switch ($params['range']) {
		case 'yesterday':
		    $f = strtotime("midnight last day");
		    $e = $f + 86400;
		    break;
		case 'this_week':
		    $f = strtotime("midnight this week");
		    $e = time();
		    break;
		case 'last_week':
		    $f = strtotime("midnight last week");
		    $e = $f + (86400 * 7);
		    break;
		case 'this_month':
		    $f = strtotime("midnight first day of this month");
		    $e = time();
		    break;
		case 'last_month':
		    $f = strtotime("midnight first day of -1 month");
		    $e = strtotime("midnight first day of this month");
		    break;
	    }
	    if (!empty($f) && !empty($e)) {
		$criteria->addCondition("created >= :from AND created < :end ");
		$criteria->params[':from']=date("Y-m-d H:i:s", $f);
		$criteria->params[':end']=date("Y-m-d H:i:s", $e);
	    }
	}
	return new CActiveDataProvider('Order',
	    array(
		'criteria'=>$criteria,
	    )
	);
    }


    /**
     * format pay status for output
     *
     * @param $value:
     *
     * @return
     */
    public function formatPayStatus($value) {
	switch ($value) {
	    case self::PAY_STATUS_UNPAID:
		return "未支付";
	    case self::PAY_STATUS_SUCCESS_CALLBACK:
		return "成功(回调)";
	    case self::PAY_STATUS_WAITING:
		return "成功(提交)";
	    case self::PAY_STATUS_FAIL:
		return "失败(提交)";
	    case self::PAY_STATUS_FAIL_CALLBACK:
		return "失败(回调)";
	    default:
		return "未知状态";
	}
    }

    /**
     * format charge status for output
     *
     * @param $value:
     *
     * @return
     */
    public function formatChargeStatus($value) {
	switch ($value) {
	    case self::CHARGE_STATUS_UNCHARGED:
		return "未充值";
	    case self::CHARGE_STATUS_SUCCESS:
		return "已充值";
	    case self::CHARGE_STATUS_FAIL_NOTICED:
		return "失败通知";
	    default:
		return "未知状态";
	}
    }

    /**
     * format order type for output
     *
     * @param $value:
     *
     * @return
     */
    public function formatOrderType($value) {
	switch ($value) {
	    case self::ORDER_TYPE_DIRECT_CHARGE:
		return "直充";
	    default:
		return "未知类型";
	}
    }

    /**
     * function_description
     *
     * @param $value:
     *
     * @return
     */
    public function formatGameAccount($value) {
	$a = @unserialize($value);
	return @$a['game_acc'] ?: "";
    }





    /**
     * format for attribute value output
     *
     * @param $value:
     * @param $type:
     *
     * @return
     */
    public function format($value, $type) {
	$method='format'.$type;
	if(method_exists($this,$method)) {
	    return $this->$method($value);
	}

	return Yii::app()->format->format($value, $type);
    }
     /**
     * 
     * get product all datas count
     */
    public function getCount($uid){
		$criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        return  self::model()->count($criteria);
         
    }
     /**
     * 
     * get table product all datas
     */
    public function getAllChargeRecords($uid,$count,$page){
        
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->addCondition("t.uid=$uid");
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $results = self::model()->findAll($criteria);
        $ret=array();
        foreach ($results as $key => $value) {
            if($value->app_id == 0){
                $ret[$key]['charge_param']=User::model()->findByPk($value->getChargeParam()->getGameAccount())->username;
            }else{
                $appname = App::model()->getAppname($value->app_id);
                $ret[$key]['charge_param']=$appname;
            }
            $ret[$key]['id']=$value->id;
            $ret[$key]['order_amt']=$value->order_amt;
            $ret[$key]['created']=$value->created;
            $ret[$key]['pay_status']=$value->pay_status;
        }
        return $ret;
    }
    /**
     * 
     * get order all datas count
     */
    public function getCounts($type=false,$dtype){
		$criteria = new CDbCriteria;
        $criteria->alias = "t";
        if($type){
        	$condition = $this->getCondition($type,$dtype);
        	$criteria->addCondition($condition);
        }
        if($dtype==2){
        	$criteria->addCondition("charge_status=1");
        }
        return  	self::model()->count($criteria);
         
    }
     /**
     * 
     * get table order all datas
     */
    public function getAllproducts($count,$page,$type=false,$dtype){
        
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        if($type){
        	$condition = $this->getCondition($type);
        	$criteria->addCondition($condition);
        }
        if($dtype==2){
        	$criteria->addCondition("charge_status=1");
        }
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas; 
    }
    /**
     * 
     * get table order all datas condition
     */
    public function getCondition($type){
    	if($type==1){
		 	return "to_days(created)=to_days(now())";
        }elseif ($type==2){
         	return "to_days(now())-to_days(created)=1";
        }elseif ($type==3){
         	return "WEEKOFYEAR(created)=WEEKOFYEAR(NOW())";
        }elseif ($type==4) {
         	return "WEEKOFYEAR(created)=WEEKOFYEAR(DATE_SUB(now(),INTERVAL 1 week))";
        }elseif ($type==5) {
         	return "MONTH(created)=MONTH(NOW()) and year(created)=year(now())";
        }elseif ($type==6) {
         	return "MONTH(created)=MONTH(DATE_SUB(NOW(),interval 1 month))
and year(created)=year(now())";
        }elseif ($type==7) {
         	return "QUARTER(created)=QUARTER(now())";
        }elseif ($type==8) {
         	return "QUARTER(created)=QUARTER(DATE_SUB(now(),interval 1 QUARTER))";
        }elseif ($type==9) {
         	return "YEAR(created)=YEAR(NOW())";
        }elseif ($type==10) {
         	return "year(created)=year(date_sub(now(),interval 1 year))";
        }
    }
    /**
     * 
     * get order all datas count
     */
    public function doSearch($payid){
		$criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->compare('t.id',$payid);
        return  self::model()->findAll($criteria);
         
    }
    /**
     * user use mhgold to charge game
     * need deduct user gold
     * @param
     * @return boolean
     */
    public function pay(){
    	$uid = $this->uid;
    	$gold = UserGoldTotal::model()->findByPk($uid);
    	if(empty($gold)){
    		return false;
    	}
    	return $gold->gold;
    }
}
