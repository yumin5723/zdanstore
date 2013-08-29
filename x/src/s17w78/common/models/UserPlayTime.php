<?php

class UserPlayTime extends UserActiveRecord {

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
        return 'user_play_time';
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
     * @param $int_time: update seconds
     *
     * @return
     */
    public function updateAllTime($uid, $app_id, $int_time) {
        $uid = intval($uid);
        $app_id = intval($app_id);

        // get record, if not exist, create one
        $r = $this->findByAttributes(array(
                 'uid' => $uid,
                 'app_id' => $app_id,
             ));
        if (empty($r)) {
            $r = new self;
            $r->uid = $uid;
            $r->app_id = $app_id;
            $r->save(false);
        }

        // update alltime
        return $r->saveCounters(array('all_time'=> intval($int_time)));
    }


}