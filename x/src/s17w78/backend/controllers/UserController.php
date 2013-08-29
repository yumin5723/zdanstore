<?php
class UserController extends BackendController {

    public $sidebars = array(
        array(
            'name' => '当日注册用户',
            'icon' => 'tasks',
            'url' => '/user/register/1',
        ),
        array(
            'name' => '游戏时长',
            'icon' => 'tasks',
            'url' => '/user/playtime/1',

        ),
    );

    public $side_nav = 'index';

    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
      return array(
          array('allow', // allow authenticated user to perform 'create' and 'update' actions
	      'actions' => array('index', 'create', "view",'user','register','playtime'),
              'users' => array('@'),
          ),
          array('allow', // all all users
              'actions' => array('error'),
              'users' => array('*'),
          ),
          array('deny', // deny all users
              'users' => array('*'),
          ),
      );
    }

    // public function actionPlaytime($id){
    //     return $this->render('playtime',array(
    //         'id' => $id,
    //         'model' => UserPlayLog::model(),
    //         'dataProvider' => $this->get_playtime($id),
    //     ));
    // }

    // public function get_playtime($id){
    //     $criteria = new CDbCriteria;
    //     $criteria->select = "SUM(TIMESTAMPDIFF(SECOND,start_time,end_time)) AS total";

    //     $start = date("Y-m-d",strtotime("-{$id} days"))." 00:00:00";

    //     $end = date("Y-m-d",strtotime("-1 days"))." 23:59:59";
    //     $criteria->addCondition("start_time >=".'"'.$start.'"');
    //     $criteria->addCondition("end_time <=".'"'.$end.'"');
    //     return new CActiveDataProvider('UserPlayLog', array(
    //         'criteria'=>$criteria,
    //         'pagination'=>false,
    //     ));
    // }

    public function actionPlaytime($id){
       $datas = UserPlayLog::model()->getForday($id);
       $data=array();
       foreach ($datas as $key => $value) {
          if(isset($value->total)){
           $value->avg=round(($value->total/$value->peoples)/60,2);
           $value->total = round($value->total/3600,2);
          }
          $day = $key+1;
          $value->date = date("Y-m-d",strtotime("-$day day"));
          $data[]=$value;
       }
        return $this->render('playtime',array(
            'id' => $id,
            'model' => UserPlayLog::model(),
            'dataProvider' => $data,
        ));
    }







    public function actionRegister($id){
        $type = isset($_GET['type']) ? $_GET['type'] : 3;
        return $this->render("register",array(
            'id' => $id,
            'type' => $type,
            'model' => User::model(),
            'dataProvider'=>$this->get_register($id,$type),
        ));
    }

    protected function get_register($id,$type){
        $criteria = new CDbCriteria;
        if ($type != 2) {
            $criteria->join = "LEFT JOIN user_play_time upt ON t.id = upt.uid";
        }
        $criteria->order = "t.id DESC";
        if ($id == User::TODAY_REGISTER_USER) {
            $date = date("Y-m-d",time())." 00:00:00";
            $criteria->addCondition("created >=".'"'.$date.'"');
        } elseif($id == User::YESTODAY_REGISTER_USER) {
            $date = date("Y-m-d",time())." 00:00:00";
            $yestoday = date("Y-m-d", strtotime("-1 day"))." 00:00:00";
            $criteria->addCondition("created >=".'"'.$yestoday.'"');
            $criteria->addCondition("created <=".'"'.$date.'"');
        } 
        if ($type != 2) {
            $criteria->addCondition('upt.all_time > 0');
        }
        $criteria->with=array(
          'userplaytime'=>array(
            ),
        );
        return new CActiveDataProvider('User', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));
    }

}
