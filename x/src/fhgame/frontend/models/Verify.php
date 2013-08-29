<?php
class Verify extends FhgameActiveRecord{
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
     * @return string the associated database table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'users';
    }
public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'make_time',
                    'updateAttribute'     => 'lastmodifytime',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * @return array validation rules for model attributes.
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('user_name,user_pwd,real_name,ID_num', 'required',),
          // array('ID_NUM,pwd_answer,reg_date,reg_area,last_login,user_tel,other_proof','safe'),
        );
    }
    public function user_check_from_wsdl($user_name){
        $unRegStr = "jh,kd,lan,管理员,版主,鸡吧,,鸡巴, ,　,@,jhkd,dxkd,网,jhlan,@kd,@sm,宽带,jhdx,电信,兰荫,jhwb,热线,大金华论坛,七天网络,凤凰网游,凤凰游戏山庄,fhgame,胡锦涛,江泽民,温家宝,杨守春,葛慧君,王挺革,政府,共产党,中共,金华电信,中国电信,电信,他妈的,操你妈,操,靠,bitch,fuck,法轮功,性爱,性交,妓,一夜情,奸,淫,系统,系統,信息,信熄,信媳,公告,公诰,提示,赌";
        $arr = explode(",",$unRegStr);
        for($i=0;$i<sizeof($arr);$i++){
            if(strpos($user_name,$arr[$i]) === false){
                //echo 888;
            }
            else{
                return 'error';
            }
        }

        $arr = array();
        $unStartStr = "1,2,3,4,5,6,7,8,9,0,１,２,３,４,５,６,７,８,９,０,130,131,132,133,134,135,136,137,138,139,159,150,151,152,153,154,155,156,157,158,188,189,１３０,１３１,１３２,１３３,１３４,１３５,１３６,１３７,１３８,１３９,１５０,１５１,１５２,１５３,１５４,１５５,１５６,１５７,１５８,１５９,１８８,１８９";
        $arr = explode(",",$unStartStr);
        for($i=0;$i<sizeof($arr);$i++){
            $pos = strpos($user_name,$arr[$i]);
            if($pos === false){

            }
            else{
                if($pos == '0'){
                    return 'error';
                }
            }
        }

        $str = file_get_contents("http://www.jingame.com/dbweb/user/checkname.asp?username=".urlencode(mb_convert_encoding($user_name,"GBK","UTF-8")));
        if($str == '0'){
            return 'success';
        }
        else{
            return array('2'=>'error');
        }
    }
    function __user_check_exist($user_name){
        $query = $model->find('user_name=:user_name', array(':user_name' => $user_name));
        if ($query){
            return true;
        }
        else{
            return false;
        }
    }
    public function user_login($user_name,$user_pwd){
        $arr = $this->user_check_from_wsdl($user_name);
        if ( $arr[2] == 'error'){
            $str = $this->user_login_from_wsdl($user_name,$user_pwd);
            var_dump($str);exit;
            if($str != false){
                $string = file_get_contents('http://www.jingame.com/dbweb/user/getkdname.asp?username='.$str);
                if($string != 'error!'){
                    $user_name = iconv("GBK","UTF-8",$string);
                }
                else{
                    $user_name = $str;
                }
                if($this->__user_check_exist($user_name)){
                    $query = $model->find('user_name=:user_name', array(':user_name' => $user_name));
                    if ($query){  
                        $user_id = $query->user_id;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    $data = array(
                    'user_name' => $user_name,
                    'user_pwd' => ""
                    );
                    $model->setAttributes($data);
                    $model->save();
                    $user_id = Yii::app()->db->getLastInsertID();
                }
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_id'] =  $user_id;
                return 'success';
            }
            else{
                return 'error';
            }

        }
        else{
            echo 999;exit;
            return 'error';
        }
    }
    function user_login_from_wsdl($user_name,$user_pwd){
        $str = file_get_contents("http://www.jingame.com/dbweb/user/checkuser.asp?username=" . urlencode(mb_convert_encoding($user_name,"GBK","UTF-8")) . "&pwd=".urlencode(mb_convert_encoding(md5($user_pwd),"GBK","UTF-8")));
        echo $str;exit;
        if($str == '0'){
            return false;
        }
        else{
            return $user_name;
        }
    }
    
}
?>