<?php
class User extends CActiveRecord
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
            array('password,old_password,new_password,p_password,username,nickname', 'length','max'=>'16', 'on'=>'register',),
            array('username', 'unique', 'on'=>'register'),
            array('username','match','pattern'=>'/^[a-zA-Z0-9]+$/', 'on'=>'register',),
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
     * check user is exists
     * @param  string puid (platform uid @ platform_id)
     * @param  strint nickname
     * @param  int platform
     */
    public function checkUser($puid,$nickname,$platform){
        $user = self::model()->findByAttributes(array("puid"=>$puid));
        $nickuser = self::model()->findByAttributes(array("nickname"=>$nickname));
        if(empty($user)){
            //create a user
            $model = new self;
            //create a username for insert into sqlserver UserAccounts
            $username = $this->generateNewUsername();
            $model->username = $username;
            $model->puid = $puid;
            if(empty($nickuser)){
                $model->nickname = $nickname;
            }else{
                $model->nickname = $nickname."(".rand(1,9).")";
            }
            $model->platform = $platform;
            $model->pass_str = GHelper::generateRandomString(13);
            if($model->save(false)){
                return $model;
            }
            return false;
        }else{
            if($user->nickname != $nickname){
                if(empty($nickuser)){
                    $user->nickname = $nickname;
                }else{
                    $user->nickname = $nickname."(".rand(1,9).")";
                }
                $user->save(false);
            }
        }
        return $user;
    }
    /**
     * return a unique username 
     *
     *
     * @return
     */
    public function generateNewUsername() {
        while (true) {
            $username = GHelper::generateRandomString(16);
            if (self::model()->findByAttributes(array('username'=>$username)) == null) {
                return $username;
            }
        }

    }
}