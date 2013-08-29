<?php

class UserGold extends UserActiveRecord {
    const NONE_CATEGORY_ID = -1;

    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'usergold';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
        );
    }

    /**
     * function_description
     *
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
     * get user total points
     * @param int uid
     * @param int tid
     * @return int points
     */
    public function getUserTotalGold($uid){
        return UserGoldTotal::model()->getUserGold($uid);
    }
    /**
     * user spend points
     * @param int uid
     * @param int count
     * @param int tid
     * @param string info
     * @param boolean consume
     *
     * return boolean
     */
    public function spendGold($uid,$gold,$tid,$game_id,$info = null){
        if ($gold == 0) {
            return false;
        }
        if ($gold > 0) {
            $gold = 0 - $gold;
        }
        return UserGoldTxn::model()->saveTransaction($uid,$gold,$tid,$game_id,$info,$consume=true);
    }
    /**
     * user income points
     * @param int uid
     * @param int count
     * @param int tid
     * @param string info
     *
     * return boolean
     */
    public function incomeGold($uid,$gold,$tid,$order_id,$info = null){
        return UserGoldTxn::model()->saveTransaction($uid,$gold,$tid,$order_id,$info,$cousume = false);
    }
}