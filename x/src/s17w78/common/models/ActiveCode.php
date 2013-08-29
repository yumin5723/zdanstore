<?php
class ActiveCode extends CActiveRecord
{
    public $upload;
    public $upload1;
    const PACKAGE_STATUS_ERROR = 5001;
    const USER_ALREADY_GETCODE = 5002;
    const USER_EMAIL_NOT_AVTIVED = 5003;
    const ACTIVECODE_RES_EMPTY = 5004;
    const SAVE_CODE_FAIL = 5005;
    const USER_NOT_LOGIN = 5006;
    const USER_GETCODE_FAIL_PROBABILITY = 5007;
    const CODE_USED = 1;
    const CODE_NOT_USED = 0;
    const MAX_IP_PER_HOUR_TIME = 5;
    const USER_GETCODE_ATTEMPT_MAX_TIMES = 5;
    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
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
        return 'activecode';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,related,down_url,index_url,publish_date,desc,detail,image,tag_image','required'),
            array('upload,upload1', 'file','allowEmpty'=>true),
            array('recommend_image','safe'),
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
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'package' => array(self::BELONGS_TO, 'Package', 'package_id'),
            'profile' => array(self::BELONGS_TO, 'Profile', 'uid'),
            'user' => array(self::BELONGS_TO, 'User', 'uid'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'code' => '卡号'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    /**
     * function_description
     *
     *
     * @return
     */
    public function importCsv($fileName,$package_id,$batch_number) {
        /*
        $fileName = $_FILES[$name]['name'];
        $subfixArr = explode('.', $fileName);
        $subfix = array_pop($subfixArr);
        */
        $handle = fopen($fileName, 'r');
        
        while ($data = fgetcsv($handle, 1024, ',')) {
            $model = new self;
            $model->code = mb_convert_encoding($data[0], "UTF-8","GBK");
            $model->package_id = (int)$package_id;
            $model->batch_number = $batch_number;
            $model->save(false);
        }
        return true;
    }
    /**
     * get the send status package activecode
     * @param  [type] $uid [description]
     * @param  [type] $id  [description]
     * @return [type]      [description]
     */
    public function getActiveCode($id,$uid){
        
        return $this->fetchCode($id,$uid);
    }
    /**
     * fetch code for user
     * @param  [type] $id  [description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function fetchCode($package_id, $uid) {
        $package_id = intval($package_id);
        $ret = array();
        if(empty($uid)){
            $ret['error'] = self::USER_NOT_LOGIN;
            return $ret;
        }
        $package = Package::model()->findByPk($package_id);
        if(empty($package) || $package->status == Package::BATCH_STATUS_CLOSE){
            $ret['error'] = self::ACTIVECODE_RES_EMPTY;
            return $ret;
        }

        if(!$this->userIsAlreadyGetCode($package_id,$uid)){
            $ret['error'] = self::USER_ALREADY_GETCODE;
            return $ret;
        }
        //check get code permission
        $p = $this->validatePermission($uid, Yii::app()->getRequest()->userHostAddress, $package_id);
        if($p !== null){
            $ret['error'] = self::USER_GETCODE_FAIL_PROBABILITY;
            return $ret;
        }

        $batch_ids = CodeBatch::model()->getFreeAvailBatchIds($package_id);
        if (empty($batch_ids)) {
            $ret['error'] = self::ACTIVECODE_RES_EMPTY;
            return $ret;
        }
        $code = $this->_getOneCodeFromStacks($uid, $batch_ids);
        if (empty($code)) {
            $ret['error'] = self::ACTIVECODE_RES_EMPTY;
            return $ret;
        }
        if ($code->_useCode($uid, null)) {
            //save to redis
            // Ling::model()->save_codeRecord_to_redis($code, $uid, $game_id,$type);
            $ret['code'] = $code->code;
            $ret['activate_url'] = $code->package->activate_url;
            return $ret;
        } else {
            $ret['error'] = self::SAVE_CODE_FAIL;
            return $ret;
        }
    }
   /**
     * function_description
     *
     * @param $uid:
     * @param $stack_ids:
     *
     * @return
     */
    protected function _getOneCodeFromStacks($uid, $stack_ids) {
        $uid = intval($uid);
        if (!is_array($stack_ids)) {
            $stack_ids = array($stack_ids);
        }
        $condition = "status = ".self::CODE_NOT_USED." AND batch_number IN ('" .
                implode("','",$stack_ids)."') ";
        $params = array(':bids'=> implode("','",$stack_ids));
        $offset = $this->getDbConnection()->createCommand()
            ->select("FLOOR(RAND() * COUNT(*)) AS `offset`")
            ->from($this->tableName())
            ->where($condition)
            ->queryScalar();

        $condition = "t.status = ".self::CODE_NOT_USED." AND t.batch_number IN ('" .
                implode("','",$stack_ids)."') ";
        $criteria = new CDbCriteria();
        $criteria->condition = $condition;
        $criteria->offset = $offset;
        $criteria->limit = 1;
        return self::model()->with('package')->find($criteria);
    }
     /**
     * use this code internal
     *
     * @param $uid:
     * @param $book_id:
     *
     * @return
     */
    protected function _useCode($uid) {
        $this->uid = $uid;
        $this->ip = Yii::app()->getRequest()->userHostAddress;
        $this->status = self::CODE_USED;
        $this->used_time = date("Y-m-d H:i:s");
        return $this->save(false);
    }
    /**
     * user already get game code is or not
     */
    public function  userIsAlreadyGetCode($id,$uid){
        $criteria = new CDbCriteria;
        $criteria->condition = "package_id=:package_id AND status = :status AND uid=:uid";
        $criteria->params = array(":package_id"=>$id,":status"=>self::CODE_USED,":uid"=>$uid);
        $result = self::model()->findAll($criteria);
        if(empty($result)){
            return true;
        }
        return false;
    }
    /**
     * validate get code permission
     * @param
     * 
     * return string
     */
    public function validatePermission($uid,$ip,$package_id){
        $p = null;
        //check email validate
        // $user = User::model()->findByPk($uid);
        // if(!$user->activated){
        //     return USER_EMAIL_NOT_AVTIVED;
        // }
        //check ip
        $times = UserRepeatActionLog::model()->getIpHourFetchCodeTimes($ip);
        if ($times >= self::MAX_IP_PER_HOUR_TIME) {
            $p = self::USER_GETCODE_FAIL_PROBABILITY;
        }
                    

        UserRepeatActionLog::model()->updateIpHourFetchCodeTimes($ip);
        //check email
        // $results = Ling::model()->getUserKeywordsForCode($game_id);
        
        // list($name, $server) = explode('@', $user->email);
        // $email_url = implode(".", array_slice(explode(".",$server), -2, 2));
        // $email_config = require CONFIG_PATH."/preferredEmail.php";
        // foreach($results as $k=>$result){
        //     //check email Suffix
        //     list($name, $domin) = explode('@', $k);
        //     $url = implode(".", array_slice(explode(".",$domin), -2, 2));
        //     if($email_url == $url && !in_array($email_url, $email_config['emaillist'])){
        //         return USER_GETCODE_FAIL_PROBABILITY;
        //     }
        //     //check email similar
        //     similar_text($k,$user->email,$percent);
        //     if($percent >= 70){
        //         return USER_GETCODE_FAIL_PROBABILITY;
        //     }
        //     if($percent<70 && $percent > 50 && $result == Yii::app()->getRequest()->userAgent){
        //         return USER_GETCODE_FAIL_PROBABILITY;
        //     }
        // }
        
        // //check day attempt times
        $times = UserRepeatActionLog::model()->getUserTodayFetchCodeTimes($package_id,$uid);
        $package_limit = PackageRules::model()->getLimit($package_id);
        if($package_limit != null){
            if ($times >= $package_limit) {
                $p = self::USER_GETCODE_FAIL_PROBABILITY;
            }
            UserRepeatActionLog::model()->updateUserTodayFetchCodeTimes($package_id,$uid);
        }
        
        $r = rand(1, 100);
        if ($r > PackageRules::model()->getProbability($package_id)) {
            $p = self::USER_GETCODE_FAIL_PROBABILITY;
        }
        return $p;
    }
    /**
     * tao code 
     * @param  [type]  $package_id [description]
     * @param  integer $count      display count per time
     * @return [type]              [description]
     */
    public function taoCode($package_id,$count = 10){
        return TaoHao::model()->getAllByPackageId($package_id,$count);
    }
    /**
     * get codes that can tao
     *
     * @param $package_id
     *
     * @return
     */
    public function getAllCanTaoCodesArray($package_id) {
        $batches = CodeBatch::model()->findAllByAttributes(array(
                       'package_id' => $package_id,
                   ));
        $bids = array_map(function ($obj){
                    return $obj->batch_number;
                }, $batches);
        $rows = $this->getDbConnection()->createCommand()
            ->select('code')
            ->from($this->tableName())
            ->where(array('and',
                    'package_id=:package_id AND uid > 0 ',
                    array('in', 'batch_number', $bids),
                    'status = :status AND used_time < :used_time'
                ) , array(
                    ':package_id'   =>$package_id,
                    ':status'    =>self::CODE_USED,
                    ':used_time' =>date("Y-m-d H:i:s",strtotime("-1 hour"))
                ))
            ->queryAll();
        $ret  = array();
        foreach ($rows as $row) {
            $ret[] = $row['code'];
        }
        return $ret;
    }


    /**
     * [getCountCodes description]
     * @param  int $uid [description]
     * @return int code count
     */
    public function getCountCodes($uid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->condition = "t.status = 1 AND t.uid = {$uid}";
        return self::model()->count($criteria);
    }

    /**
     * get code pages
     * @param  int $count [description]
     * @param  int $page  [description]
     * @param  int $uid   [description]
     * @return array  page code
     */
    public function getPageCodes($count,$page,$uid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->order = "t.id DESC";
        $criteria->condition = "t.status = 1 AND t.uid = {$uid}";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        return self::model()->findAll($criteria);
    }
    /**
     * get package code get rank top 10
     * @return [type] [description]
     */
    public function getCodeRank(){
        $where = 'a.status = '.self::CODE_USED;
        $result = $this->getDbConnection()->createCommand()
                ->select("a.package_id,b.name,count(1) as sum")
                ->from('activecode as a')
                ->leftjoin("package as b",'a.package_id = b.id')
                ->where($where)
                ->group("a.package_id")
                ->order("sum DESC")
                ->limit(10)
                ->queryAll();
        return $result;
    }

}