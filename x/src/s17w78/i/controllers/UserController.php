<?php

class UserController extends CController {

    public $registered = false;
    public $invite_code;
    public $caseSensitive=false;
    /**
     * @return array action filters
     */
    public function filters() {
        $filters = array(
            'accessControl', // perform access control for CRUD operations
        );
        // if (isset(Yii::app()->params['needAlphaCode']) && Yii::app()->params['needAlphaCode']) {
        //     $filters[] = array('application.filters.AlphaCodeFilter + register');
        // }
        return $filters;
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('captcha','login','emailverify','recoverpass','feedback','logout','register','visitor'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('recover','connect','bind'),
                'users' => array('?'),
            ),
            array('allow',
                'actions' => array('changepw','changeemail','profile','setusername','index','avatar','upavatar','upload','resize','code','help','rereg'),
                'users' => array('@')
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * class-based actions
     *
     *
     * @return
     */
    public function actions() {
        return array(
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xEBF4FB,
                'maxLength'=>'4',       // 最多生成几个字符
                'minLength'=>'2',       // 最少生成几个字符
                // 'fixedVerifyCode' => substr(md5(time()),0,4), 
                'testLimit'=>"6",
                'height'=>'40'
        ),
        );
    }
    /**
     * user login
     * return errorcode(success or failure)
     */
    public function actionLogin(){
        if (!Yii::app()->user->isGuest) {
            $this->redirect('index');
        }
        $model = new User;
        list($valid, $msg) = Yii::app()->attemptlimit->validate();
        // collect user input data
        if (isset($_POST['User'])) {
            $model->setScenario('login');
            $model->attributes = $_POST['User'];
            //set auto rember me
            $model->rememberMe = true;
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                Yii::app()->session['lastlogintime']=date("Y年m月d日 H时i分s秒");
                // send login event
                Yii::app()->eventlog->send('login',Yii::app()->request->userHostAddress,
                            Yii::app()->request->getUrl(),
                            Yii::app()->user->id,
                            Yii::app()->request->urlReferrer
                    );
                $this->redirect(Yii::app()->user->returnUrl);
            } else {
                Yii::app()->attemptlimit->attempt_fail();
            }
        }
        if(Yii::app()->request->urlReferrer != "" && Yii::app()->user->returnUrl =="/"){
            Yii::app()->user->returnUrl = Yii::app()->request->urlReferrer;
        }
        // print_r(Yii::app()->request->urlReferrer);exit;
        // display the login form
        $this->render('login', array(
            'model' => $model,'valid'=>$valid
        ));
    }
    /**
     * user logout
     * return errorcode(success or failure)
     */
    public function actionLogout() {
        if(Yii::app()->user->isGuest){
            return $this->redirect(Yii::app()->user->loginUrl);
        }
        Yii::app()->user->logout();
        //unset bbs session
        Yii::app()->request->cookies['bbsmember_id'] = new CHttpCookie('bbsmember_id', 0,array('domain'=>'.1378.com'));
        Yii::app()->request->cookies['bbspath_hash'] = new CHttpCookie('bbspath_hash', 0,array('domain'=>'.1378.com'));
        Yii::app()->request->cookies['bbssession_id'] = new CHttpCookie('bbssession_id', 0,array('domain'=>'.1378.com'));
        $this->redirect(Yii::app()->user->loginUrl);
    }
    /**
     * user center index
     * return view
     */
    public function actionIndex() {
        $logintime = Yii::app()->session['lastlogintime'];
        $user = $this->loadModel(Yii::app()->user->id);
        $null = Profile::model()->getIsnull($user->id);
        $null = isset($null)?$null:'';
        $profile = Profile::model()->findByPk($user->id);
        $recommend = Package::model()->getRecommendPackage();
        // print_r($recommend);exit;
        if($profile){
            $profile->small_avatar = Yii::app()->params['avatar_url'].$profile->small_avatar;
        }
        $this->render("user_center",array('user' => $user,'logintime'=>$logintime,'pnull'=>$null,'profile'=>$profile,'recommend'=>$recommend));
    }
    /**
     * user register
     *
     *
     * @return
     */
    public function actionRegister() {
        if(!Yii::app()->user->isGuest){
            return $this->redirect('index');
        }
        $user = new User('register');
        if(isset($_POST['ajax']) && $_POST['ajax']==='create-form')
        {
            echo CActiveForm::validate($user);
            Yii::app()->end();
        }
        if (isset($_POST['User'])) {
            $user->setAttributes($_POST['User']);
            $user->rememberMe = true;
            if ($user->validate()) {
                if($user->save(false)){
                    // send register event
                    Yii::app()->eventlog->send('register',Yii::app()->request->userHostAddress,
                                Yii::app()->request->getUrl(),
                                $user->id,
                                Yii::app()->request->urlReferrer
                        );
                    $profile = new Profile;
                    $profile->uid = $user->id;
                    if($profile->save(false)){
                        $user->password = $_POST['User']['password'];
                        if($user->login()){
                           // $user->send_verify_email();
                            return $this->render('user_success', array('username' => $user->username,'isresend'=>false));
                        }
                    }
                }
                else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', '注册失败!!'));
                }
             }
        }
        $this->render('register', compact('user'));
    }
    /**
     * User Feedback 
     */
    public function actionFeedback(){
        $model = new Feedback;
        if(isset($_POST['ajax']) && $_POST['ajax']==='feedback-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if(isset($_POST['Feedback'])){
            $model->setAttributes($_POST['Feedback']);
            if($model->validate()){
                if($model->save(false)){
                    Yii::app()->user->setFlash( 'success', Yii::t( 'mii', '反馈问题提交成功，请耐心等待客服的回复，谢谢！' ) );
                }
            }
        }
        $this->render('feedback',array('user'=>$model));
    }
    /*
     *user upload avatar
     */
    public function actionAvatar(){
        $user = $this->loadModel(Yii::app()->user->id);
        $profile = Profile::model()->findByPk(Yii::app()->user->id);
        if($profile){
            $profile->small_avatar = Yii::app()->params['avatar_url'].$profile->small_avatar;
        }
        $this->render('avatar',array('profile'=>$profile,'user'=>$user));
    }
    /*
     *user upload avatar
     */
    public function actionUpAvatar(){
        $user = $this->loadModel(Yii::app()->user->id);
        $model = new Profile('upavatar');
        $profile = $model->findByPk(Yii::app()->user->id);
        if($profile){
            $profile->avatar = Yii::app()->params['avatar_url'].$profile->avatar;
        }else{
            $profile =$model;
        }
        if(!empty($_FILES)){
            if($model->validate()){
                $uid = Yii::app()->user->id;
                $tmp_file = $_FILES['Profile']['tmp_name']['avatar'];
                $avatar = new UserAvatar($tmp_file, $uid);
                $url = $avatar->get_file_uri();
                return $this->redirect('/user/upavatar');
            }else{
                $error = $model->getErrors();
                Yii::app()->user->setFlash( 'success', Yii::t( 'mii', $error['avatar'][0]) );
            }
        }
        $this->render('upavatar',array('profile'=>$profile,'user'=>$user));
    }

    /**
     * user change password
     *
     *
     * @return
     */
    public function actionChangepw() {
        $user = $this->loadModel(Yii::app()->user->id);
        if (!empty($_POST['User'])) {
            $user->setScenario('changePass');
            $user->setAttributes($_POST['User']);
            if ($user->validate()) {
                $user->use_new_password();
                 Yii::app()->user->setFlash( 'success', Yii::t( 'mii', '恭喜您，密码修改成功！' ) );
            }
        }
        return $this->render('changepw', array('user' => $user));
    }
    public function actionSetusername() {
        $user = $this->loadModel(Yii::app()->user->id);
        if (!empty($_POST['User'])) {
            $user->setScenario('setusername');
            $user->setAttributes($_POST['User']);
            if ($user->validate()) {
                $user->set_username();
                 Yii::app()->user->setFlash( 'success', Yii::t( 'mii', '恭喜您，昵称修改成功！' ) );
            }
        }
        return $this->render('setusername',  compact('user'));
    }
    /**
     * user change email
     *
     *
     * @return
     */
    public function actionChangeemail() {
        $user = $this->loadModel(Yii::app()->user->id);
        if (!empty($_POST['User'])) {
            $user->setScenario('changeemail');
            $user->setAttributes($_POST['User']);
            if ($user->validate()) {
                $user->use_new_email();
                 Yii::app()->user->setFlash( 'success', Yii::t( 'mii', '恭喜您，邮箱修改成功！' ) );
            }
        }
        return $this->render('changeemail', array('user' => $user));
    }
     /**
     * resend add profile
     *
     *
     * @return
     */
    public function actionProfile(){
        $user = $this->loadModel(Yii::app()->user->id);
        $profile = Profile::model()->findByPk(Yii::app()->user->id);
        $profile->birthday = $profile->birthday == "0000-00-00 00:00:00" ? "" : date("Y-m-d",strtotime($profile->birthday));
        if (isset($_POST['Profile']) && isset($_POST['User'])) {
            $user->setScenario('profile');
            $user->setAttributes($_POST['User']);
            $profile->setScenario('profile');
            $profile->setAttributes($_POST['Profile']);
            if ($profile->validate() && $user->validate()) {
                if($user->updateAttrs($_POST['User'])){
                    if($profile->updateInfo($_POST['Profile'])){
                        Yii::app()->user->setFlash( 'success', Yii::t( 'mii', '注册资料添加成功！' ) );
                    }
                }
            }
        }
        return $this->render('profile', array('profile' => $profile,'user'=>$user));
    }

    /**
     * resend verify email
     *
     *
     * @return
     */
    public function actionResendverifyemail() {
        if(isset($_GET['email'])){
            if(User::model()->resend_verify_mail($_GET['email'])){
                return $this->render('email_verify',array('email'=>$_GET['email'],'isresend'=>true));
            }
        }
    }

    /**
     * verify email
     *
     *
     * @return
     */
    public function actionVerify() {
        if (!isset($_GET['id'], $_GET['code'])) {
            throw new CHttpException(404, 'Invalid request');
        }

        if ($user = User::model()->findByPk($_GET['id'])) {
            if ($user->activated) {
                return $this->render('actived_already');
            }
            if ($user->active_user(trim($_GET['code']))) {
                $this->render('success',array('email'=>$user->email));
            } else {
                return $this->render('active_fail');
            }
        } else {
            throw new CHttpException(404, 'Invalid user');
        }
    }
    /**
     * recover user
     *
     *
     * @return
     */
    public function actionRecover() {
        $user = new User;
        if (isset($_POST['User'])) {
            $user->setScenario('recover');
            $user->setAttributes($_POST['User']);
            if ($user->validate()) {
                $found = User::model()->findByAttributes(array('email' => $user->email));

                if ($found !== null) {
                    $link = $found->getResetPasswordLink();
                    $found->send_recover_mail();
                    return $this->render('recover_sent', array('user'=>$found));
                } else {
                    Yii::app()->user->setFlash('error',array(
                                'main'=>Yii::t('mii','邮箱地址不存在！'),
                            ));
                }
            }
        }
        
        $this->render('recover', compact('user'));
    }
    
    /**
     * function_description
     *
     *
     * @return
     */
    public function actionRecoverpass() {
        $code = $_GET['code'];
        if (!$user = User::findUserByResetPasswordCode($code)) {
            $message = "无效的地址！";
            return $this->render('recover_pass', array('user' => false, 'result' => 0, 'message' => $message));
        }
        if (isset($_POST['User']['password'])) {
            $user->setScenario('resetPass');
            $user->password = $_POST['User']['password'];
            $user->confirm_password = $_POST['User']['confirm_password'];
            $user->reset_password_code = $code;
            if ($user->validate()) {
                $user->save(false);
                return $this->render('recover_pass', array('user' => $user, 'result' => 2, 'message'=>'重置密码成功'));
            }
        }
        unset($user->password);
        return $this->render('recover_pass', array('user'=>$user,'result' => 1));
    }


    /**
     * verify email 
     * @return type 
     */
    public function actionEmailverify(){
        if(isset($_GET['email'])){
            $this->render('email_verify',array('email'=>$_GET['email'],'isresend'=>false));
        }
    }
    public function redirectUrl(){
        if(Yii::app()->user->returnUrl == "/index.php"){
            return constant("MAIN_DOMIN");
        }else{
            return Yii::app()->user->returnUrl;  
        }
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = User::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    /**
     * upload avatar
     *
     *
     * @return
     */
    public function actionUpload() {
        $uid = Yii::app()->user->id;
        $model = new Profile('upload');
        if($model->validate()){
            echo 33;exit;
        }else{
            print_r($model->getErrors());exit;
        }
        $tmp_file = $_FILES['Profile']['tmp_name']['avatar'];
        $avatar = new UserAvatar($tmp_file, $uid);
        $url = $avatar->get_file_uri();
       // print_r($avatar);exit;
        if($url == 'sizefalse'){
            return $this->redirect(array('/user/upavatar','error'=>1));
        }elseif($url == 'typefalse'){
            return $this->redirect(array('/user/upavatar','error'=>2));
        }else{
            return $this->redirect('/user/upavatar');

        }
        
        
    }
    /**
     * upload avatar
     *
     *
     * @return
     */
    public function actionResize() {
        $uid = Yii::app()->user->id;
        $tmp_file = $_POST['bigImage'];
        $avatar = new AvatarResize($tmp_file, $uid,$_POST);
        $url = CJSON::encode($avatar->get_file_uri());
        return $this->redirect('/user/avatar');
    }
    /**
     * upload avatar
     *
     *
     * @return
     */
    public function actionUploadphoto() {
        $uid = Yii::app()->user->id;
        $tmp_file = $_FILES['photo']['tmp_name'];
        $avatar = new UserPhoto($tmp_file, $uid);
        $url = CJSON::encode(Global_Func::user_image_url($avatar->get_file_uri()));
        $thumburl = CJSON::encode(Global_Func::user_image_url($avatar->get_thumb_uri()));
        $file_path = CJSON::encode($avatar->get_file_uri());
        $thumb_path = CJSON::encode($avatar->get_thumb_uri());
        $name = CJSON::encode($_FILES['photo']['name']);
        
        echo <<<EOF
<html>
      <body>
        <script>
            document.domain = "1378test.com";window.parent.avatar_upload_success({$url},{$thumburl},{$file_path},{$thumb_path},{$name});</script>
       </body>
     </html>
EOF;
    }

    public function actionCode(){
        $count = 6;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = ActiveCode::model()->getCountCodes(Yii::app()->user->id);
        $result = ActiveCode::model()->getPageCodes($count,$pageCurrent,Yii::app()->user->id);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/code?p=",2);
        $p = $subPages->show_SubPages(2);
        
        return $this->render('user_gift',array('results'=>$result,'pager'=>$p));
    }
    /**
     * user login to third part platform 
     * @return [type] [description]
     */
    public function actionConnect(){
        $plat = $_REQUEST['plat'];
        if(!isset($plat) || empty($plat)){
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        // print_r($plat);
        $url  = Yii::app()->platform->getPlatformLoginUrl($plat);
        return $this->redirect($url);
    }
    /**
     * third part platform callback
     */
    public function actionBind(){
        $plat= $_GET['plat'];
        $receive_params = $_GET;
        unset($receive_params['plat']);
        list($result, $return_string, $data) = Yii::app()->platform->receiveCallback($plat, $receive_params);
        if($result){
            $ret = User::model()->setUserBind($data);
            if($ret){
                return $this->redirect(Yii::app()->user->returnUrl);
            }else{
                echo "create account fail";
            }
        }
        echo "get user info fail";
    }
    /**
     * help page 
     * @return [type] [description]
     */
    public function actionHelp(){
        return $this->render("help");
    }
    /**
     * visitor login sys create a account
     */
    public function actionVisitor(){
        if(Yii::app()->user->id){
            return $this->redirect(Yii::app()->user->returnUrl);
        }
        Yii::app()->user->returnUrl = Yii::app()->request->urlReferrer;
        $user = new User;
        if($user->createVisitorAccount()){
            Yii::app()->eventlog->send('syscreateaccount',Yii::app()->request->userHostAddress,
                                Yii::app()->request->getUrl(),
                                $user->id,
                                Yii::app()->request->urlReferrer
                        );
            return $this->redirect(Yii::app()->user->returnUrl);
        }
    }
    /**
     * complete reg for visitor
     */
    public function actionRereg(){
        $user = User::model()->findByPk(Yii::app()->user->id);
        if($user->is_reg == User::IS_REG){
            return $this->redirect("profile");
        }
        if(isset($_POST['ajax']) && $_POST['ajax']==='create-form')
        {
            echo CActiveForm::validate($user);
            Yii::app()->end();
        }
        if (isset($_POST['User'])) {
            $user->setScenario('register');
            $user->setAttributes($_POST['User']);
            if ($user->validate()) {
                $user->password = $user->hashPassword($_POST['User']['password']);
                $user->is_reg = User::IS_REG;
                if($user->save(false)){
                    // Yii::app()->user->setFlash('success', Yii::t('mii', '您已经成功完成注册!!'));
                    $this->redirect('profile');
                }
                else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', '注册失败!!'));
                }
             }
        }
        $this->render("rereg",array("user"=>$user));

    }
}