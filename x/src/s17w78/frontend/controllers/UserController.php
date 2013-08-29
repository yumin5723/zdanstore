<?php
class UserController extends ERestController {

    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {
        return array("accessControl");
    }


    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
    return array(
        array('allow', // allow authenticated user to perform 'create' and 'update' actions
            'actions'=>array('login'),
            'users'=>array('*'),
        ),
        array('allow',
            'actions'=>array('info','success'),
            'users'=>array('*'),
        ),
        array('allow',
            'actions'=>array('logout','profile','bind','check','done','mygoods','resend','mypoints'),
            'users'=>array('@'),
        ),
        array('deny',  // deny all users
        'users'=>array('*'),
        ),
    );
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function actionInfo() {
        if (Yii::app()->user->isGuest) {
            return $this->renderJson(array(
                    'isGuest' => true,
                ));
        } else {
            $user = User::model()->with('profile')->findByPk(Yii::app()->user->id);
            $gold = UserGoldTotal::model()->findByPk(Yii::app()->user->id);
            return $this->renderJson(array(
                    'isGuest' => false,
                    'id' => Yii::app()->user->id,
                    'nickname'=>$user->nickname,
                    'avatar'=>$user->profile->avatar,
                    'gold' => empty($gold) ? 0 :$gold->gold,
                ));
        }
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function actionLogin() {
        $user = new User;
        list($valid, $msg) = Yii::app()->attemptlimit->validate();
        if (!$valid) {
            return $this->renderJson(array(
                    'success'=>false,
                    'message'=>$msg,
                    'data'=>array(
                        'needCaptcha'=>Yii::app()->attemptlimit->need_captcha(),
                        // 'captcha_url'=>Yii::app()->createUrl('captcha', array('v'=>uniqid())),
                        'captcha_url'=>"/captcha?v=".uniqid(),
                        'captcha_input_name'=>Yii::app()->attemptlimit->input_name,
                    ),
                ));
        }
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $user->username=$_POST['username'];
            $user->password=$_POST['password'];
            $user->rememberMe = true;
            if ($user->login()) {
                // send login event
                Yii::app()->eventlog->send('login',Yii::app()->request->userHostAddress,
                            Yii::app()->request->getUrl(),
                            Yii::app()->user->id,
                            Yii::app()->request->urlReferrer
                    );
                return $this->renderJson(array(
                        'success'=>true,
                        'message'=>'login success',
                        'data'=>array(
                        ),
                    ));
            } else {
                Yii::app()->attemptlimit->attempt_fail();
            }
        }
        $this->renderJson(array(
                'success'=>false,
                'message'=>'用户名密码错误',
                'data'=>array(
                    'needCaptcha'=>Yii::app()->attemptlimit->need_captcha(),
                    'captcha_url'=>Yii::app()->createUrl('captcha'),
                    'captcha_input_name'=>Yii::app()->attemptlimit->input_name,
                ),
            ));
    }
}