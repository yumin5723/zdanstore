<?php
class Userverify extends CFormModel{
	public $username;
    public $password;
    public $realname;
    public $id_num;
	public $tel;

	public function rules(){
		return array(
            array('username, password,realname,id_num,tel','required'),
            array('username','check_username'),
            array('username','check_username_format'),
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
            'realname' => '真实姓名',
            'tel' => '联系方式',
        );
    }


    public function getResult(){
        // $game_username = $this->check_game_username($this->username);
        // if ($game_username !== false) {
        //     # code...
        //     $username = $this->check_getkdname($game_username);
        return $this->select_username($this->username);

        // }
    }

    public function check_username_format($username){
        $userlist = array("jh","kd","lan","管理员","版主","鸡吧","","鸡巴"," ","","@","jhkd","dxkd","网","jhlan","@kd","@sm","宽带","jhdx","电信","兰荫","jhwb","热线","大金华论坛","七天网络","凤凰网游","凤凰游戏山庄","fhgame","胡锦涛","江泽民","温家宝","杨守春","葛慧君","王挺革","政府","共产党","中共","金华电信","中国电信","电信","他妈的","操你妈","操","靠","bitch","fuck","法轮功","性爱","性交","妓","一夜情","奸","淫","系统","系統","信息","信熄","信媳","公告","公诰","提示","赌");
        if (!in_array($username, $userlist)) {
            
            $unStartStr = array("1","2","3","4","5","6","7","8","9","0","１","２","３","４","５","６","７","８","９","０","130","131","132","133","134","135","136","137","138","139","159","150","151","152","153","154","155","156","157","158","188","189","１３０","１３１","１３２","１３３","１３４","１３５","１３６","１３７","１３８","１３９","１５０","１５１","１５２","１５３","１５４","１５５","１５６","１５７","１５８","１５９","１８８","１８９");
            for($i=0;$i<count($unStartStr);$i++){
                $pos = strpos($username,$unStartStr[$i]);
                if($pos !== false){
                    $this->addError('username', '用户名首字母不正确!');
                } 
            }
        } else {
            $this->addError('username', '用户名不合法!');
        }    
    }


    public function select_username($username){
        $model = Users::model()->findByAttributes(array('user_name'=>$username));
        if (!empty($model)) {
            return true;
        } else {
            $model = new Users;
            $model->user_name = $username;
            if ($model->save(false)) {
                return true;
            }
            return true;
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
        $verify = new FhgameHelper;
        $url = Yii::app()->params['verify_username_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($this->username,"GBK","UTF-8"));
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
    public function check_game_username($username) {
        $verify = new FhgameHelper;
        $url = Yii::app()->params['get_game_name_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);
        if($verify_name != false){
            return $verify_name;
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
    public function check_getkdname($username) {
        $verify = new FhgameHelper;
        $url = Yii::app()->params['get_kd_name_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"));
        $verify_name = file_get_contents($request_url);
        if($verify_name != 'error!'){
            return iconv("GBK","UTF-8",$username);
        } else {
            return $username;
        }
    }

}