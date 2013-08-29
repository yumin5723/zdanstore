<?php

class UserPlayed extends UserActiveRecord
{
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'userplayed';
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
        );
        return $rules;
    }
    /**
     * save user play game time
     * @param int uid    login user id
     * @param int app_id  game_id 
     * @return boolean
     */
    public function setUserPlayGameTime($uid,$app_id){
        $record = self::model()->findByAttributes(array('uid'=>$uid,'app_id'=>$app_id));
        if(empty($record)){
            $record = new self;
            $record->uid = $uid;
            $record->app_id = $app_id;
        }
        $record->last_play_time = date('Y-m-d H:i:s',time());
        if(!$record->save(false)){
            return false;
        }
        return true;        
    }
    /**
     * get user played games recently
     * @param  int  $uid   user's id
     * @param  integer $number need get number
     * @return array
     */
    public function getUserPlayedGamesRecently($uid,$number = 3){
        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->condition = "t.uid = :uid";
        $criteria->params = array(':uid'=>$uid);
        $criteria->order = "t.last_play_time DESC";
        $criteria->limit = $number;
        return self::model()->findAll($criteria);
    }
}