<?php
class UserController extends Controller {
    /**
     * function_description
     *
     *
     * @return
     */
    public function actionInfobysid() {
        $sid = $_GET['session_id'];
        if (empty($sid)) {
            exit(0);
        }

        $rs = Yii::app()->readSession;
        $sess = $rs->getSessionById($sid);
        $id_key = $rs->stateKeyPrefix."__id";
        if (!isset($sess[$id_key])) {
            echo json_encode(array('error'=>'can not find session'));
            exit(0);
        }

        $user = User::model()->findByPk($sess[$id_key]);
        if ($user) {
            $ret = array(
                'id'=>$user->id,
                'username' => $user->username,
                'email'    => $user->email,
                'nickname' => $user->nickname,
                'regtime'  => strtotime($user->created),
            );
            echo json_encode($ret);
            exit(0);
        }
        echo json_encode(array('error'=>'can not find user'));
    }


}
