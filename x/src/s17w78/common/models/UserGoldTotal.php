<?php

class UserGoldTotal extends UserActiveRecord {
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
        return 'usergold_total';
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
            // 'profile'=>array(self::BELONGS_TO,'Profile','uid'),
            // 'user'=>array(self::BELONGS_TO,'User','uid'),
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
     * get user interfration
     * @param int uid
     * @return json.
     */
    public function getUserGold($uid){
        $uid = intval($uid);
        $user = self::model()->findByPk($uid);
        if(empty($user)){
            $gold = 0;
        }else{
            $gold = $user->gold;
        }
        return $gold;
    }
}