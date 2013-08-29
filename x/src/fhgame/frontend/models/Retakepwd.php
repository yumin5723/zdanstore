<?php
class Retakepwd extends CFormModel{
	public $username;
	public $password;
	public $password_confirm;
	public $id_num;
    public $answer;
	public $type;

	public function rules(){
		return array(
            array('username, id_num','required'),
            array('username','check_username'),
            array('id_num','check_id_num'),
            array('answer','check_answer','on'=>'verifyanswer'),
            array('password','save_password','on'=>'check_password'),
            array('password','compare','compareAttribute'=>'password_confirm','on'=>'check_password'),
            array('password, password_confirm','required','on'=>'check_password'),
            array('id_num','required','on'=>'verifyidisnull'),
			array('type','safe'),
        );  
	}

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'username' => '用户名',
            'id_num' => '身份证号码',
            'password' => '密码',
            'answer'=>"答案",
        );
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
        $username = $this->$attribute;
        $verify = new FhgameHelper;
        $url = Yii::app()->params['verify_username_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);
        if($verify_name == '0'){
            $this->addError('username', '用户名不存在!');
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
    public function check_id_num($attribute, $params) {
        $id_num = $this->$attribute;
        $username = $this->username;
        $verify = new FhgameHelper;

        $url = Yii::app()->params['verify_id_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"))."&idcard=".urlencode(mb_convert_encoding($id_num,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);
        if($verify_name == '0'){
            $this->addError('id_num', '身份证号错误!');
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
    public function verifyidisnull($attribute, $params) {
        $id_num = $this->$attribute;
        $username = $this->username;
        $verify = new FhgameHelper;

        $url = Yii::app()->params['verify_id_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"))."&idcard=".urlencode(mb_convert_encoding($id_num,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);
        if($verify_name == '0'){
            $this->addError('id_num', '身份证号错误!');
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
    public function check_answer($attribute, $params){
        $answer = $this->$attribute;
        $verify = new FhgameHelper;

        $url = Yii::app()->params['verify_answer_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($this->username,"GBK","UTF-8"))."&answ=".urlencode(mb_convert_encoding($answer,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);
        if($verify_name != 1){
            $this->addError('answer', '答案错误!');
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
    public function save_password($attribute, $params){
        $verify = new FhgameHelper;
        $url = Yii::app()->params['modify_password_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($this->username,"GBK","UTF-8"))."&newpwd=".urlencode(mb_convert_encoding($this->password,"GBK","UTF-8"))."&idcard=".urlencode(mb_convert_encoding($this->id_num,"GBK","UTF-8"))."&answ=".urlencode(mb_convert_encoding($this->answer,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);

        if ($verify_name == '0') {
            $this->addError('password', '您输入的新密码不符合规则，或系统忙，');
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
    public function getUserQues($username){
        $verify = new FhgameHelper;
        $url = Yii::app()->params['get_ques_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"));
        $str = file_get_contents($request_url);
        return mb_convert_encoding($str,"UTF-8","GBK");
    }
}