<?php
/**
 * This is the model class for table "game".
 *
 * The followings are the available columns in table 'game':
 * @property integer $id
 * @property string $name
 * @property string $tags
 * @property string $from
 * @property string $desc
 * @property string $operations_guide
 * @property string $how_begin
 * @property string $target
 * @property string $image
 * @property string $tag_image
 * @property string $url
 * @property string $created_uid
 * @property string $modified_uid
 * @property string $created
 * @property string $modified
 */
class Matrixcard extends FhuserActiveRecord
{
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
        return 'uc_matrixcard';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name','required'),
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'create_time',
                    'updateAttribute'     => 'update_time',
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
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
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }

    /**
     * [unbindcard description]
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public function unbindcard($username){
        $model = self::model()->findByAttributes(array('user_name'=>$username));
        if (!empty($model)) {
            if ($model->card_value != "") {
                // return $model->delete() ? '您的账号已经解除密保卡绑定,您的账号目前没有密保卡登录保护。' : "解绑定失败！,请重试！";
                return $model->delete() ? '1' : "2";
            } 
        }
        // return "您还没有绑定密保卡,不用解除密保卡绑定";
        return "0";
    }

    /**
     * [bindcard description]
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public function getquesbindcard($username){
        $verify = new FhgameHelper;

        $result = $this->checkVip($username);
        if ($result == false) {
            return array(false,'对不起，您不是凤凰游戏山庄会员,只有红（蓝）钻石会员才能够申请密保卡服务。');
        }
        $model = self::model()->findByAttributes(array('user_name'=>$username));
        if (!empty($model)) {
            if ($model->card_value == '' || $model->card_value == null) {
                $url = Yii::app()->params['verify_member_url'];
                $request_url = $url."?username=".urlencode(mb_convert_encoding($model->username,"GBK","UTF-8"));
                $verify_result = get_headers($request_url);
                if ($verify_result[0] == "HTTP/1.1 200 OK") {
                    $result = $this->checkVip($model->user_name);
                    if ($result == false) {
                        return array(false,'对不起，您不是凤凰游戏山庄会员,只有红（蓝）钻石会员才能够申请密保卡服务。');
                    }
                } 
                return array(false,'对不起！验证会员接口出错！,请您稍后再试！');
            } 
            $retakepwd = new Retakepwd;
            return array(true,$retakepwd->getUserQues($model->user_name));
        } 
        $retakepwd = new Retakepwd;
        return array(true,$retakepwd->getUserQues($username));
    }


    /**
     * [bindcard description]
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public function bindcard($username){
        $verify = new FhgameHelper;

        $result = $this->checkVip($username);
        if ($result == false) {
            return array(false,'对不起，您不是凤凰游戏山庄会员,只有红（蓝）钻石会员才能够申请密保卡服务。');
        } else {
            $model = self::model()->findByAttributes(array('user_name'=>$username));
            if (!empty($model)) {
                if ($model->card_value != '') {
                    return array(1,'重新绑定，解除绑定');
                }
            } else {
                // print_r("expression");exit;
                $card_value = $this->insertMartrixcard($username);
                return array(2,"绑定成功");
            }
        }
    }


    public function insertMartrixcard($username){
        $model = new self;
        $model->user_name = $username;
        $model->status = 1;
        $model->card_value = $this->createMatrixcard();
        if ($model->save(false)) {
            return true;
        }
    }

    public function rebindcard($username){
        return self::model()->updateAll(array('card_value'=>$this->createMatrixcard()),'user_name = :username',array(':username'=>$username));
    }

    /**
     * create new Matrixcard
     * @return string matrixcard
     */
    public function createMatrixcard(){
        /*
        *   生成密保卡
        *   必须包含3个1位数字，其他1位2位随机，但是不能重复
        */
        $arr_1 = array(1,2,3,4,5,6,7,8,9);
        $arr_rand = array_rand($arr_1,3);
        $arr_rand = array($arr_1[$arr_rand[0]],$arr_1[$arr_rand[1]],$arr_1[$arr_rand[2]]);
        for($i = 1; $i < 100; $i ++)
        {
            $arr_10[] = $i;
        }
        $arr_2 = array_diff($arr_10,$arr_rand);
        shuffle($arr_2);
        for($i =0; $i<46; $i++)
        {
            $arr_rand_2[] = $arr_2[$i];
        }
        $arr_rand_2[] = $arr_rand[0];
        $arr_rand_2[] = $arr_rand[1];
        $arr_rand_2[] = $arr_rand[2];
        shuffle($arr_rand_2);
        $mcard = implode('|',$arr_rand_2);
        return $mcard;
    }

    /**
     * check user is vip
     * @param  string $username [description]
     * @return [type]           [description]
     */
    public function checkVip($username){
        $verify = new FhgameHelper;
        $url = Yii::app()->params['verify_member_url'];
        $request_url = $url."?username=".urlencode(mb_convert_encoding($username,"GBK","UTF-8"));
        $str = file_get_contents($request_url);
        
        if($str != 1 && $str != 2) {
            return false;
        }
        return true;
    }


    /**
     * [getMatricard description]
     * @return [type] [description]
     */
    public function getMatricard($username){
        $model = self::model()->findByAttributes(array('user_name'=>$username));
        if ($model->card_value != "") {
            return $this->createMatrixcardPic($model->card_value);
        } 
    }


    public function createMatrixcardPic($card_value){
        $width = 500;
        $hight = 666;

        $list_arr_start = array(77,180);
        $width_per = 59;
        $top_per = 59;

        $pic=imagecreatetruecolor($width,$hight); 

        $white=imagecolorallocate($pic,255,255,255);
        $black=imagecolorallocate($pic,0,0,0);
        $red=imagecolorallocate($pic,255,0,0);
        imagefill($pic,0,0,$white);

        Yii::import('common.*');
        // $picture = require(Yii::getPathOfAlias("common")."/bj.jpg");
        $simage =imagecreatefromjpeg(Yii::getPathOfAlias("common")."/bj.jpg");
        imagecopy($pic,$simage,0,0,0,0,$width,$hight); // 把背景图片 copy 到我们要输出的图片上
        // $font="Vera.ttf";
        $font=Yii::getPathOfAlias("common")."/Vera.ttf";
        // $sn = $card_no; 
        // print_r($card_value);exit;
        $list = explode('|',$card_value);
        $size = sizeof($list);
        for($i=0;$i<$size;$i++)
        {
            $left = $list_arr_start[0] + ($i%7)*$width_per;
            $top = $list_arr_start[1] + floor($i/7)*$top_per;
            if(strlen($list[$i])==3)
            {
                $left = $left;
            }
            if(strlen($list[$i])==2)
            {
                $left = $left + $width_per/3;
            }
            if(strlen($list[$i])==1)
            {
                $left = $left + $width_per/2;
            }
            imagettftext ($pic,20,0,$left,$top,$black,$font,$list[$i]);
        }
        return $pic;
        //header("Content-type: image/jpeg"); //定义输出头
        // imagejpeg($pic);  //在浏览器中输出图片
        // imagedestroy($simage); //结束图片，释放内存
        // imagedestroy($pic);
    }

}