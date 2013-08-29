<?php

class UserGoldTxn extends UserActiveRecord {

    const SPEND_GOLD_SUCCESS = 1;
    const SPEND_GOLD_FAIL = 2;
    const SYS_FIXED_UID = 999;

    const NOT_ENOUGH_TO_SPEND = 0;

    const CONSUME_GOLD_LIMIT = 3;
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
        return 'usergold_txn';
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
     * save intergration log
     * @param intval uid
     * @param intval gold
     * @param intval goods id
     * @param string info
     * @param boolean consume
     * 
     * return boolean
     */
    public function saveTransaction($uid,$gold,$tid,$goods_id,$info,$consume = true){
        $uid = intval($uid);
        $tid = intval($tid);
        $gold  = intval($gold);
        
        $userGold = UserGold::model()->findByAttributes(array("uid"=>$uid,"tid"=>$tid));
        $userGoldTotal = UserGoldTotal::model()->findByPk($uid);
        //user's gold is not enough
        if($consume == true){
            if(empty($userGold) || $userGold->gold + $gold < 0){
                return  self::NOT_ENOUGH_TO_SPEND;
            }
            if($gold > 0 ){
                return self::CONSUME_GOLD_LIMIT;
            }
        }
        
        $model = new self;
        $model->uid = $uid;
        $model->tid = $tid;
        $model->approver_uid = self::SYS_FIXED_UID;
        $model->gold = $gold;
        $model->entity_id = $goods_id;
        $model->description = $info;
        $model->expirydate = null;

        if($model->save()){
            $update = $this->updateUserGold($userGold,$uid,$tid,$gold);
            $update_total = $this->updateUserGoldTotal($userGoldTotal,$uid,$gold, $consume);
            if($update && $update_total){
                return self::SPEND_GOLD_SUCCESS;
            }
        }else{
            return self::SPEND_GOLD_FAIL;
        }
    }
    /**
     * @param object usergold
     * @param int gold
     * return boolean
     */
    protected function updateUserGold($userGold,$uid,$tid,$gold){
    	if(empty($userGold)){
    	    $model = new UserGold;
    	    $model->uid = $uid;
    	    $model->tid = $tid;
    	    $model->gold = $gold;
    	    $model->max_gold = $gold;
    	    $model->save();	
            return true;
    	}
        $update = $this->getDbConnection()->createCommand()
                    ->update('usergold', 
                            array(
                            'gold' =>new CDbExpression("gold + {$gold}"),
                            ),
                            "uid=".$uid." AND tid=".$tid
                    );
        $user = UserGold::model()->findByAttributes(array("uid"=>$uid,"tid"=>$tid));
        if($user->gold > $user->max_gold){
            $user->max_gold = $user->gold;
            $user->save();
        }
        return true;
    }
    /**
     * update user gold and max_gold
     * @param object usergold
     * @param int gold
     * 
     * return boolean
     */
    protected function updateUserGoldTotal($userGold,$uid,$gold,$consume = true){
        if(empty($userGold)){
	       $model = new UserGoldTotal;
	       $model->uid = $uid;
	       $model->gold = $gold;
	       $model->max_gold = $gold;
	       $model->save();
            return true;
	    }
        
        if($consume){
            $update = $this->getDbConnection()->createCommand()
                    ->update('usergold_total', 
                            array(
                            'gold' =>new CDbExpression("gold + {$gold}"),
                            ),
                            "uid=".$uid." AND gold=".$userGold->gold
                    );
        }else{
            $update = $this->getDbConnection()->createCommand()
                    ->update('usergold_total', 
                            array(
                            'gold' =>new CDbExpression("gold + {$gold}"),
                            ),
                            "uid=".$uid
                    );
        }
        $user = UserGoldTotal::model()->findByPk($uid);
        if($user->gold > $user->max_gold){
            $user->max_gold = $user->gold;
            $user->save();
        }
        return true;
    }
}
