<?php

class UserPlayLog extends UserActiveRecord {
    /**
     * max for user online idle
     */
    const MAX_IDLE = 180;
    public $total;
    public $peoples;
    public $avg;
    public $date;

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
        return 'user_play_log';
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

    /**
     * function_description
     *
     * @param $uid:
     * @param $app_id:
     *
     * @return
     */
    public function flushUserOnline($uid, $app_id, $current_time=null) {
        if (is_null($current_time)) {
            $current_time = time();
        }
        $uid = intval($uid);
        $app_id = intval($app_id);

        // get last online record for self::MAX_IDLE
        $l = self::model()->findByAttributes(array(
                 'uid'    =>$uid,
                 'app_id' => $app_id,
             ), "end_time > :end_time", array(
                 ":end_time" => date("Y-m-d H:i:s", $current_time - self::MAX_IDLE)));

        // a new online status
        if (empty($l)) {
            $l = new self;
            $l->uid = $uid;
            $l->app_id = $app_id;
            $l->start_time = date("Y-m-d H:i:s", $current_time);
            $l->end_time = date("Y-m-d H:i:s", $current_time);
            return $l->save(false);
        }

        $addtime = $current_time - strtotime($l->end_time);
        // update end time
        $l->saveAttributes(array('end_time'=>date("Y-m-d H:i:s", $current_time)));

        // update user_play_time
        UserPlayTime::model()->updateAllTime($uid, $app_id, $addtime);

        return true;
    }
    /**
     * get play user data in some uids
     * @param  [type] $reg_date [description]
     * @param  [type] $uids     [description]
     * @return [type]           [description]
     */
    public function getPlayUsersByDate($reg_date,$uids){
        $start = $reg_date." 00:00:00";
        $end = $reg_date." 23:59:59";
        $condition = "start_time >= '{$start}' AND end_time <= '{$end}' AND TIMESTAMPDIFF(SECOND,start_time,end_time) >= 60 AND uid in('" . implode("','", $uids)."')";
        $rows = $this->getDbConnection()->createCommand()
            ->select('uid')
            ->from($this->tableName())
            ->where($condition)
            ->queryAll();
        $ret = array();
        foreach($rows as $row){
            $ret[] = $row['uid'];
        }
        return $ret = array_unique($ret);
    }

    /**
     * get for 7 days
     */
    public function getForday($day){
      $data=array();
      for($i=0;$i<$day;$i++){
        $data[]=$this->get_DayPlay($i);
      }
      return $data;
    }
    /**
     * get 7day's everyday's data
     */
    public function get_DayPlay($day){
          $criteria = new CDbCriteria;
          $criteria->select = "SUM(TIMESTAMPDIFF(SECOND,start_time,end_time)) as total,count(distinct(uid)) as peoples";
          $criteria->addCondition("TIMESTAMPDIFF(SECOND,start_time,end_time)>60");
          $criteria->addCondition("TO_DAYS(now())-TO_DAYS(start_time)<=".($day+1));
          $criteria->addCondition("TO_DAYS(now())-TO_DAYS(start_time)>".$day);
          return self::model()->find($criteria);
    }

}