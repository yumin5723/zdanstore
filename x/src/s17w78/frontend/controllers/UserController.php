<?php
class UserController extends GController {

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
                'actions'=>array('login','register'),//'account','setting','message','order','address',
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('info','success'),
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('logout','account','index','setting','wishlist','message','order','address','bind','check','done','mygoods','resend','mypoints'),
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
        if(!Yii::app()->user->isGuest){
            return $this->redirect('/user/');
        }
        $user = new User;
        if(Yii::app()->request->isPostRequest){
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                $user->username=$_POST['username'];
                $user->password=$_POST['password'];
                $user->rememberMe = true;
                if ($user->login()) {
                    return $this->redirect(Yii::app()->user->returnUrl);
                }else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', 'Username or password is wrong!!'));
                }
            }else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', 'Username and password cannot be blank!!'));
            }
        }
        $this->render("login",compact('user'));
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
        Yii::app()->request->cookies['bbsmember_id'] = new CHttpCookie('bbsmember_id', 0,array('domain'=>'.zdanstore.com'));
        Yii::app()->request->cookies['bbspath_hash'] = new CHttpCookie('bbspath_hash', 0,array('domain'=>'.zdanstore.com'));
        Yii::app()->request->cookies['bbssession_id'] = new CHttpCookie('bbssession_id', 0,array('domain'=>'.zdanstore.com'));
        $this->redirect("/");
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
                    $profile = new Profile;
                    $profile->uid = $user->id;
                    if($profile->save(false)){
                        $user->password = $_POST['User']['password'];
                        if($user->login()){
                           // $user->send_verify_email();
                            return $this->redirect("/user/setting");
                        }
                    }
                }
                else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', '注册失败!!'));
                }
            }
        }
        $this->render("login", compact('user'));
    }
    /*backend user account*/
    public function actionIndex(){
        $user = $this->loadModel(Yii::app()->user->id);
        $address = Address::model()->getDefaultAddress(Yii::app()->user->id);
        $myorder = Order::model()->getNewOrder(Yii::app()->user->id);
        $wishlist =  Wishlist::model()->getWishlistForHomepage(Yii::app()->user->id);
        $this->render("account",array("user"=>$user,"data"=>$address,"myOrder"=>$myorder,'wishlist'=>$wishlist));
    }
    /*backend user setting*/
    public function actionSetting(){
        $user = $this->loadModel(Yii::app()->user->id);
        $profile = Profile::model()->findByPk(Yii::app()->user->id);
        $profile->birthday = $profile->birthday == "0000-00-00 00:00:00" ? "" : date("Y-m-d",strtotime($profile->birthday));
        if (isset($_POST['Profile'])) {
            $profile->setScenario('profile');
            $profile->setAttributes($_POST['Profile']);
            if ($profile->validate()) {
                if($profile->updateInfo($_POST['Profile'])){
                    Yii::app()->user->setFlash( 'success', Yii::t( 'mii', '注册资料添加成功！' ) );
                }
            }
        }
        $this->render("account_setting",array("user"=>$user,"profile"=>$profile));
    }
    /*backend user order*/
    public function actionOrder(){
        $user = $this->loadModel(Yii::app()->user->id);
        $uid =  Yii::app()->user->id;
        $model = new Order;
        $count = 20;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = Order::model()->getCount($uid);
        $chargeRecords = $model->getAllChargeRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/order/p/",2);
        $p = $subPages->show_SubPages(2);
        $this->render("account_order",array('data'=>$chargeRecords,'pages'=>$p));
    }
    /*backend user message*/
    public function actionMessage(){
        $uid = Yii::app()->user->id;
        $model = new Message('message');
        $datas = $model->getAlldatas($uid);
        if (isset($_POST['Message'])) {
            $_POST['Message']['uid']=$uid;
            $model->setAttributes($_POST['Message']);
            if ($model->validate()) {
                if($model->save(false)){
                    
                    Yii::app()->user->setFlash('success', Yii::t('mii', 'Message提交成功!!'));
                    return $this->redirect("message");
                }
                else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', 'Message提交失败!!'));
                }
            }
        }
        $this->render("account_message",array("model"=>$model,"data"=>$datas[0],'pages'=>$datas[1]));
    }
    /*backend user address*/
    public function actionAddress(){
        $uid = Yii::app()->user->id;
        $model = new Address;
        $datas = $model->getAlldatas($uid);
        if (isset($_POST['Address'])) {
            $model->uid = Yii::app()->user->id;
            $model->setAttributes($_POST['Address']);
            if ($model->validate()) {
                if($model->save(false)){
                    
                    Yii::app()->user->setFlash('success', Yii::t('mii', 'create new address successful!!'));
                    return $this->redirect("address");
                }
                else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', '地址添加failed!!'));
                }
            }
        }
        if(isset($_GET['id'])){
            $addr = Address::model()->findByPk($_GET['id']);
            $addr->default = 1;
            $addr->setScenario("updateDefault");
            $addr->setAttributes($addr->default);
            if($addr->validate()){
                if($addr->updateDefault($addr->default)){
                    $result = Address::model()->updateAll(array("default"=>0),"id!=:id AND uid=:uid",array(":id"=>$_GET['id'],"uid"=>$uid));
                    return $this->redirect("/user/address");
                }
            }
            // $model->updateDefault($_GET['id']);
        }
        $this->render("account_address",array("model"=>$model,"data"=>$datas));
    }
    /**
     * [actionWishlist description]
     * @return [type] [description]
     */
    public function actionWishlist(){
        $uid =  Yii::app()->user->id;
        $model = new Wishlist;
        $count = 20;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = Order::model()->getWishCountByUid($uid);
        $wishRecords = $model->getAllWishRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/order/p/",2);
        $p = $subPages->show_SubPages(2);
        $this->render("account_wishlist",array('data'=>$wishRecords,'pages'=>$p));
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
    
}