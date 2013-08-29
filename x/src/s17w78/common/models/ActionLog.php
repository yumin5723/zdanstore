<?php

class ActionLog extends ActionLogActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()	{
        return 'action_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
        );
    }

    /**
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
     * save event 
     * @param  string $action       user touch action
     * @param  string $ip           user client ip address
     * @param  strint $url          current url
     * @param  int  $uid            user id ,if user is guest null
     * @param  strint $referrer     url referrer
     * @param  current_time         if current time is null the time is time
     * @return boolean
     */
    public function saveActionLog($action,$ip,$url,$uid = null,$referrer = null,$current_time = null) {
        if(!is_null($uid)){
            $uid = intval($uid);
        }
        // create a new record
        $actionlog = new self;
        $actionlog->action = $action;
        $actionlog->uid = $uid;
        $actionlog->ip = $ip;
        $actionlog->url = $url;
        if(isset($_COOKIE['u_f'])){
            $actionlog->u_from = $_COOKIE['u_f'];
        }
        if(isset($_COOKIE['u_id'])){
            $actionlog->u_id = $_COOKIE['u_id'];
        }
        if(!is_null($referrer)){
            $actionlog->referrer = $referrer;
        }
        return $actionlog->save(false);
    }
    /**
     * get reg user by date and channel
     * @param  [type] $channel  [description]
     * @param  [type] $reg_date [description]
     * @param  [type] $offset   [description]
     * @param  [type] $count    [description]
     * @return [type]           [description]
     */
    public function getRegUsersByDate($channel,$reg_date,$offset,$count){
        $criteria = new CDbCriteria();
        $begin = $reg_date." 00:00:00";
        $end = $reg_date." 23:59:59"; 
        $criteria->condition = "created >= :begin AND created <= :end AND action='syscreateaccount' AND u_from = :channel";
        $criteria->params = array(':begin'=>$begin,':end'=>$end,':channel'=>$channel);
        $criteria->offset = $offset;
        $criteria->limit = $count;
        return self::model()->findAll($criteria);
    }
}