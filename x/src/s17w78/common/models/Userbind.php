<?php

class Userbind extends UserActiveRecord
{
    const USER_IS_BIND = 1;
    const USER_NOT_BIND = 0;
    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'userbind';
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
    /*
     * check third part platform user is bind 
     */
    public function checkUserIsBind($data){
        $userbind = self::model()->findByAttributes(array("platform_uid"=>$data['platform_uid'],"platform"=>$data['platform']));
        if(empty($userbind)){
            //insert into user table
            $user = new User;
            if(($info=$user->createAccount($data)) !== false ){
                //insert into userbind table
                $model = new self;
                $model->platform_uid = $data['platform_uid'];
                $model->platform = $data['platform'];
                $model->platform_name = $data['platform_name'];
                $model->uid = $info->id;
                $model->status = self::USER_IS_BIND;
                if($model->save(false)){
                    return array(true,$info->username);                
                }
                return [false,""];
            }
            return [false,""];
        }
        $user = User::model()->findByPk($userbind->uid);
        return array(true,$user->username);
    }   
}