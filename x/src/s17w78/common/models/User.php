<?php
Yii::import('common.components.UserActiveRecord');
class User extends UserActiveRecord
{
    public $password_repeat;
    public $rememberMe;

    public $verifyCode;
    public $reset_password_code;
    protected $reset_password_tablename = 'user_reset_pass';

    public $old_password;
    public $new_password;
    public $p_password;
    public $confirm_password;
    public $nickname;
    public $old_email;
    public $new_email;
    private $_identity;
    const IS_ADMIN_INSERT = 1;

    const IS_NOT_REG = 1;
    const IS_REG = 0;

    const TODAY_REGISTER_USER = 1;
    const YESTODAY_REGISTER_USER = 2;
    const ALL_REGISTER_USER = 3;

    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'user';
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
            array('password,password_repeat,username,nickname', 'required', 'on'=>'register',),
            array('password,old_password,new_password,p_password,username', 'length','min'=>'6','max'=>'16','tooLong'=>'长度限定为6-16的字符串！','tooShort'=>'长度限定为6-16的字符串！', 'on'=>'register'),
            array('nickname', 'length','min'=>'2','max'=>'16','tooLong'=>'长度限定为2-16的字符串！','tooShort'=>'长度限定为2-16的字符串！', 'on'=>'register'),
            array('username', 'unique', 'on'=>'register'),
            array('username','match','pattern'=>'/^[a-zA-Z0-9]+$/','message'=>'必须为字母、数字！', 'on'=>'register',),
            array('password_repeat', 'compare', 'compareAttribute'=>'password', 'on'=>'register'),
            array('verifyCode','required', 'on'=>'register'),
            array('username, password', 'required', 'on' => 'login'),
            array('password', 'authenticatePass', 'on' => 'login'),
            array('email', 'required', 'on'=>'recover'),
            array('old_email,p_password,new_email', 'required', 'on'=>'changeemail'),
            array('new_email,old_email', 'email'),
            array('p_password','check_pass','on'=>'changeemail'),
            array('old_email', 'check_changeemail', 'on'=>'changeemail'),
            array('new_email','check_email','on'=>'changeemail'),
            array('email', 'check_recoveremail', 'on'=>'recover'),
            
            array('password, reset_password_code','required','on'=>'resetPass'),
            array('old_password,new_password,confirm_password', 'required','on'=>'changePass'),
            array('old_password','check_password','on'=>'changePass'),
            array('new_password','compare','compareAttribute'=>'confirm_password', 'on'=>'changePass'),
            array('password','compare','compareAttribute'=>'confirm_password', 'on'=>'resetPass'),
            array('reset_password_code', 'resetPassword','on'=>'resetPass'),
            array('username', 'length', 'min'=>6, 'max'=>15, 'on'=>'update,setusername'),
            array('username', 'required','on'=>'setusername'),
            array('username', 'check_username','on'=>'setusername'),
            array('username', 'check_AccountName','on'=>'register'),
            array('nickname','check_nickname','on'=>'profile,register'),
            array('nickname','required','on'=>'profile'),
            array('email','email','on'=>'profile'),
            array('email','check_safeemail','on'=>'profile'),
            //array('verifyCode','captcha','allowEmpty'=>YII_DEBUG,'on'=>"recover"),
            //array('verifyCode','captcha','allowEmpty'=>YII_DEBUG,'on'=>"changeemail"),
            //array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements(),'on'=>'register'),
            //array('verifyCode', 'captcha', 'allowEmpty'=> !extension_loaded('gd') ,'on'=>'login'),
            array('verifyCode', 'captcha', 'allowEmpty'=> !extension_loaded('gd') ,'on'=>'register,recover'),

        );
        
        if (Yii::app()->attemptlimit->need_captcha()) {
             $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=> !extension_loaded('gd') ,'on'=>'login');
        }
         
        return $rules;
    }
    /*public function safeAttributes() {
        return array(
            //parent::safeAttributes(),
            'login'=>'email, password, rememberMe',
        );
    }*/

    /**
     * hash password
     *
     *
     * @param username
     * @param password not hash yet
     *
     * @return
     */
    public function hashPassword($password) {
        $mode = 'sha1';
        // hash the text //
        $textHash = hash($mode, $password);
        // set where salt will appear in hash //
        $saltStart = strlen($password);
        $saltHash = hash($mode, $this->username);
        // add salt into text hash at pass length position and hash it //
        if($saltStart > 0 && $saltStart < strlen($saltHash)) {
            $textHashStart = substr($textHash,0,$saltStart);
            $textHashEnd = substr($textHash,$saltStart,strlen($saltHash));
            $outHash = hash($mode, $textHashEnd.$saltHash.$textHashStart);
        } elseif($saltStart > (strlen($saltHash)-1)) {
            $outHash = hash($mode, $textHash.$saltHash);
        } else {
            $outHash = hash($mode, $saltHash.$textHash);
        }
        return $outHash;
    }
    
    /**
     * hook before save
     *
     *
     * @return boolen
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->password = $this->hashPassword($this->password);
        }
        return parent::beforeSave();
    }

    /**
     * Authenticates the password.
     * This is the 'authenticatePass' validator as declared in rules().
     */
    public function authenticatePass($attribute,$params) {

        if (!$this->hasErrors()) { // we only want to authenticate when no input errors
            Yii::import('common.components.UserIdentity');
            $identity = new UserIdentity($this->username, $this->password);
            $identity->authenticate();
            switch ($identity->errorCode) {
                case UserIdentity::ERROR_NONE:
                    $duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
                    Yii::app()->user->login($identity, $duration);
                    break;

                case UserIdentity::ERROR_USERNAME_INVALID:
                    $this->addError('username','用户名或密码不正确!!!');

                    break;
                default: // UserIdentity::ERROR_PASSWORD_INVALID
                    $this->addError('password','用户名或密码不正确!!!');
                    break;
            }
        }
    }
    /**
     * set user login
     */
    public function login(){
        Yii::import('common.components.UserIdentity');
        if($this->_identity===null)
        { 
            $this->_identity=new UserIdentity($this->username,$this->password);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
        {
            $duration=$this->rememberMe ? 60*60*24*365 : 17400; //7 days or default to 20 minutes
            Yii::app()->user->login($this->_identity,$duration);
            //write cookie for name avatar display
            $user = self::model()->with('profile')->findByPk(Yii::app()->user->id);
            $value = 'avatar='.Yii::app()->params["avatar_url"].$user->profile->small_avatar.":nickname=".$user->nickname;
            Yii::app()->request->cookies['user_info'] = new CHttpCookie('user_info', $value);

            return true;
        }
        else
            return false;
    }
    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public function active_user($code) {
        if ($this->activationCode != $code) {
            return false;
        }

        $this->activated = true;
        if ($this->save(false)) {
            //login user
            $identity = new UserIdentity($this->email, $this->password);
            $identity->authenticate();
            Yii::app()->user->login($identity, 0);
            return true;
        } else {
            return false;
        }
    }

    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_password($attribute, $params) {
        $pass = $this->$attribute;
        $user = self::model()->findByAttributes(array(
                'username'=>$this->username));
        if($this->hashPassword($pass) != $user->password){
            $this->addError('old_password', '旧密码错误！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_ppassword($attribute, $params) {
        $pass = $this->$attribute;
        $user = self::model()->findByAttributes(array(
                'password'=>$this->hashPassword($pass)));
        if(!$user){
            $this->addError('old_password', '旧密码错误！');
        }
    }
     /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_pass($attribute, $params) {
        $this->password = $this->hashPassword($this->p_password);
        $user = self::model()->findByAttributes(array(
                'password'=>$this->password));
        if($user === null){
            $this->addError('p_password', '密码错误！');
        }
    }
    /**
     * hash password
     *
     *
     * @param username
     * @param password not hash yet
     *
     * @return
     */
    public function hashPw($username,$password) {
        $mode = 'sha1';
        // hash the text //
        $textHash = hash($mode, $password);
        // set where salt will appear in hash //
        $saltStart = strlen($password);
        $saltHash = hash($mode, $username);
        // add salt into text hash at pass length position and hash it //
        if($saltStart > 0 && $saltStart < strlen($saltHash)) {
            $textHashStart = substr($textHash,0,$saltStart);
            $textHashEnd = substr($textHash,$saltStart,strlen($saltHash));
            $outHash = hash($mode, $textHashEnd.$saltHash.$textHashStart);
        } elseif($saltStart > (strlen($saltHash)-1)) {
            $outHash = hash($mode, $textHash.$saltHash);
        } else {
            $outHash = hash($mode, $saltHash.$textHash);
        }
        return $outHash;
    }
     /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_recoveremail($attribute, $params) {
        $pass = $this->$attribute;
        $user = self::model()->findByAttributes(array(
                'email'=>$this->email));
        if($user === null){
            $this->addError('email', '邮箱地址不存在！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_changeemail($attribute, $params) {
        $this->old_email = $this->$attribute;
        $user = self::model()->findByAttributes(array(
                'email'=>$this->old_email,'id'=>$this->id));
        if($user === null){
            $this->addError('old_email', '邮箱地址不存在！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_email($attribute, $params) {
        $pass = $this->$attribute;
        $user = self::model()->findByAttributes(array(
                'email'=>$this->new_email));
        if($user !== null){
            $this->addError('new_email', '此邮箱已被占用！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_safeemail($attribute, $params) {
        $email = $this->email;
        //print_r($email);exit;
        $criteria=new CDbCriteria;
        $criteria->condition = 'email=:email AND id !=:id';
        $criteria->params =array(':email'=>$email,':id'=>  Yii::app()->user->id);
        $user = self::model()->find($criteria);
        if($user && $user->email != ""){
            $this->addError('email', '来晚一步，此邮箱已被占用！！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_username($attribute, $params) {
        $username = $this->username;
        $criteria=new CDbCriteria;
        $criteria->condition = 'username=:username AND id !=:id';
        $criteria->params =array(':username'=>$username,':id'=>  Yii::app()->user->id);
        $user = self::model()->find($criteria);
        
        if($user){
            $this->addError('username', '来晚一步，昵称已被其他用户抢先用了！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_nickname($attribute, $params) {
        $nickname = $this->nickname;
        $criteria=new CDbCriteria;
        $user_id =Yii::app()->user->id;
        if($user_id){
            $criteria->condition = 'nickname=:nickname AND id !=:id';
            $criteria->params =array(':nickname'=>$nickname,':id'=>  $user_id);
        }else{
            $criteria->condition = 'nickname=:nickname';
            $criteria->params =array(':nickname'=>$nickname);
        }
        $user = self::model()->find($criteria);
        
        if($user){
            $this->addError('nickname', '来晚一步，昵称已被占用！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_registerNickname($attribute, $params) {
        $nickname = $this->nickname;
        $criteria=new CDbCriteria;
        $criteria->condition = 'nickname=:nickname';
        $criteria->params =array(':nickname'=>$nickname,':id'=>  Yii::app()->user->id);
        $user = self::model()->find($criteria);
        
        if($user){
            $this->addError('nickname', '来晚一步，昵称已被其他用户抢先用了！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_AccountName($attribute, $params) {
        $username = $this->username;
        $criteria=new CDbCriteria;
        $criteria->condition = 'username=:username';
        $criteria->params =array(':username'=>$username);
        $user = self::model()->find($criteria);
        
        $tmpuser = self::model()->findByAttributes(array('username'=>$username));
        if(!empty($user) || !empty($tmpuser)){
            $this->addError('username', '用户名已被使用！');
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function use_new_password() {
        if (!empty($this->new_password)) {
            $this->password = $this->hashPassword($this->new_password);
            $this->save();
        }
        return true;
    }
    public function set_username() {
        if (!empty($this->username)) {
            $this->save();
        }
        return true;
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function use_new_email() {

        if (!empty($this->new_email)) {
            $this->email = $this->new_email;
            $this->password = $this->hashPassword($this->p_password);
            $this->save(false);
        }
        return true;
    }

    public function generatePassword($length=20) {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;

        while ($i < $length) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function generateResetPasswordCode() {
        $code = $this->generatePassword(16);
        $this->saveResetPasswordCode($code);
        $this->reset_password_code = $code;
        return $code;
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public function saveResetPasswordCode($code) {
        $code = trim($code);
        $sql = "INSERT INTO {$this->reset_password_tablename} (`uid`,`code`,`created`) VALUES (:uid, :code, :created)";
        $command = $this->getDbConnection()->createCommand($sql);
        $command->bindValue(':uid', $this->id, PDO::PARAM_INT);
        $command->bindValue(':code', $code, PDO::PARAM_STR);
        $command->bindValue(':created', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        return $command->execute();
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public static function findUserByResetPasswordCode($code) {
        $tableName = self::model()->reset_password_tablename;
        $sql = "SELECT `uid` FROM {$tableName} WHERE `code`=:code AND `created`>:created";
        $command = self::model()->getDbConnection()->createCommand($sql);
        $command->bindValue(':code', $code, PDO::PARAM_STR);
        $command->bindValue(':created', date("Y-m-d H:i:s",strtotime('-1 day')), PDO::PARAM_STR);
        $result = $command->queryRow();
        if (isset($result['uid'])) {
            return self::model()->findByPk(intval($result['uid']));
        } else {
            return null;
        }
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public static function deleteResetPasswordCode($code) {
        $tableName = self::model()->reset_password_tablename;
        $sql = "DELETE FROM {$tableName} WHERE `code`=:code";
        $command = self::model()->getDbConnection()->createCommand($sql);
        $command->bindValue(':code', $code, PDO::PARAM_STR);
        return !!$command->execute();
    }

    /**
     * reset password check
     *
     *
     * @return
     */
    public function resetPassword($attributes, $params) {
        if (!$this->hasErrors()) {
            //use reset password code for user
            if (self::deleteResetPasswordCode($this->reset_password_code)) {
                $this->password = $this->hashPassword($this->password);
            } else {
                $this->addError('reset_password_code', 'can not find this reset code');
            }
        }

    }


    /**
     * get if user email has confirmed
     *
     *
     * @return
     */
    public function getActivated() {
        return $this->email_confirmed==null;
    }

    /**
     * set user activeated status
     *
     * @param val
     *
     * @return
     */
    public function setActivated($val) {
        if ($val)
            $this->email_confirmed = null;
        else {
            $this->email_confirmed = $this->generatePassword();
        }
    }

    public function getActivationCode() {
        return ($this->email_confirmed != null) ? $this->email_confirmed : false;
    }

    /**
     * get user link
     *
     *
     * @return
     */
    public function getUserlink() {
        $url = Yii::app()->createUrl('/user/view',array('username'=>$this->username));
        return "<a href='{$url}'>{$this->username}</a>";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getResetPasswordLink() {
        return Yii::app()->createAbsoluteUrl('/user/recoverpass',
            array('code'=>$this->generateResetPasswordCode()));
    }



    protected function afterSave() {
        parent::afterSave();
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'profile' => array(self::BELONGS_TO,'Profile','id'),
            'userplaytime' => array(self::STAT,'UserPlayTime','uid', 
                    'select' => "SUM(all_time)",
                ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'username' => "用户名",
            'nickname' => "昵称",
            'old_password' => "旧密码",
            'new_password' => "新密码",
            'confirm_password' => "确认新密码",
            'password' => "密码",
            'p_password' => "密码",
            'password_repeat' => "确认密码",
            'old_email' => "旧邮箱",
            'new_email' => "新邮箱",
            'email' => "安全邮箱： ",
            'verifyCode'=>'验证码',
        );
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function send_verify_email() {
        $to_send = $this->email;
        $subject = Yii::t("mii","请完成1378通行证邮箱注册");
        $content = Yii::app()->getViewRenderer()
            ->renderPartial('user/active', array('user'=>$this));
        return Yii::app()->sendMail->send($to_send,$subject,$content,'html');
    }

    /**
     * function_description
     *
     * @param $email:
     *
     * @return
     */
    public function resend_verify_mail($email) {
        $email = trim($email);
        $user = self::model()->findByAttributes(array(
                    'email'=>$email));
        if (empty($user) || $user->getActivated()) {
            return false;
        }

        return $user->send_verify_email();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function send_recover_mail() {
        $to_send = $this->email;
        $subject = Yii::t('mii', '1378密码找回');
        $content = Yii::app()->getViewRenderer()->renderPartial('user/recoveremail', array('user'=>$this));
        return Yii::app()->sendMail->send($to_send, $subject,$content,'html');
    }

    /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    public function getUserByName($username,$uid) {
        $criteria=new CDbCriteria;
        $criteria->condition = "username LIKE :username";
        $username = '%'.$username.'%';
        $criteria->params =array(':username'=>$username);
        $results = self::model()->findAll($criteria);
        foreach($results as $k=>$result){
            if($uid == $result->id){
                unset($results[$k]);
            }
        }
        return $results;
    }
    /**
     * get email in user
     * 
     * @param Array
     * @return Array
     */
    public function getUsernameByUid($codedata){
        $ret = array();
        foreach($codedata as $value){
            $name= "";
            $user = self::model()->findByPk($value['uid']);
            if(!empty ($user)){
                $name = $user->email;
            }else{
                $t_user = self::model()->findByPk($value['uid']);
                if(!empty($t_user)){
                    $name = $user->AccountName;
                }else{
                    $name = "";
                }
                
            }
            $ret[] = $name; 
        }
        return $ret;
    }
    /**
     * my played games
     */
    public function fetchMyPlayedGames($uid){
        $payMent = Payment::getInstance();
        $games = $payMent->getAllGames();
        $gameInfo = require CONFIG_PATH."/payment/game_charge.php";
        $ret = array();
        foreach($games as $game){
            $gameCheck = GameCharge::getInstance();
            $result = $gameCheck->getUserPlayed($game['id'],$uid);
            if($result !==null){
                $ret[$game['id']] = $gameInfo['games'][$game['id']];
                $ret[$game['id']]["result"] = $result;
            }
        }
        return $ret;
    }
    /**
     * get recommend game in user page 
     */
    public function getCanRecommendToUser($my_games){
        $gameInfo = require CONFIG_PATH."/payment/game_charge.php";
        foreach($my_games as $k=>$v){
            unset ($gameInfo['games'][$k]);
        }
        return $gameInfo['games'];
    }
    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['nickname']) || $attributes['nickname'] != $this->nickname) {
            $attrs[] = 'nickname';
            $this->nickname = $attributes['nickname'];
        }
        if (!empty($attributes['email']) || $attributes['email'] != $this->email) {
            $attrs[] = 'email';
            $this->email = $attributes['email'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * get user id 
     * @param  [type] $account [description]
     * @return [type]          [description]
     */
    public function getIdByAccount($account){
        $user = self::model()->findByAttributes(array("username"=>$account));
        if(empty($user)){
            return "";
        }
        return $user->id;
    }
    /**
     * create account from third part platform
     * @return [type] [description]
     */
    public function setUserBind($data){
        list($result, $return) = Userbind::model()->checkUserIsBind($data);
        if(!$result){
            return false;
        }else{
           $identity = new PlatformUserIdentity($return, "");
           $identity->authenticate();
           Yii::app()->user->login($identity);
           return true;
        }
    }
    /**
     * create a data for third part platform user
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createAccount($data){
        $user = new self;
        $nickname = $data['nickname'];
        //$result = self::model()->findByAttributes(array('nickname'=>$nickname));
        $result = $this->getNickname($nickname);
        if(!empty($result)){
            $num = $this->getUniqidName($result);
            if($num == false){
                $nickname = $nickname."(1)";
            }else{
                $nickname = $nickname."($num)";
            }
        }
        // print_r($result);exit;
        $user->username = $data['platform_uid'].rand(100,999);
        $user->nickname = $nickname;
        if($user->save(false)){
            $profile = new Profile;
            $profile->uid = $user->id;
            $profile->small_avatar = $data['avatar'];
            $profile->gender = $data['gender'];
            $profile->save(false);
            return $user;
        }
        return false;
    }
    /**
     * get user nickname
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getNickname($param){
        $criteria = new CDbCriteria;
        $criteria->addCondition("nickname like '$param%'");
        return self::model()->findAll($criteria);
    }
    /**
     * check user nickname unique
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getUniqidName($result){
        $array = array();
        foreach ($result as $value) {
            preg_match("/\((\d)\)/", $value->nickname, $match);
            if (!empty($match)) {
                $array[] = $match["1"];
            } 
        }
        if(!empty($array)){
            $max = max($array);
            return $max+1;
        }else{
            return false;
        }
    }
    /**
     * create a sys account for visitor
     * nickname 
     * @return [type] [description]
     */
    public function createVisitorAccount(){
        srand((double)microtime()*1000000);
        $rand_number = rand();
        $this->username = "visitor".$rand_number;
        $nickname = GHelper::getRandomNickName();
        $this->nickname = "牌客".".".$nickname;
        $this->password = rand(100,9999999);
        $this->is_reg = self::IS_NOT_REG;
        if($this->save(false)){
            //save profile
            $profile = new Profile;
            $profile->uid = $this->id;
            $profile->save(false);
            //set user login
            $identity = new PlatformUserIdentity($this->username, "");
            $identity->authenticate();
            Yii::app()->user->login($identity,60*60*24*365);
            return true;            
        }
    }
}