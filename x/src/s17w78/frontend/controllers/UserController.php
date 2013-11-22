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
                'actions'=>array('login','register','message','recover','recoverpass'),//'account','setting','message','order','address',
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('info','success'),
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('logout','account','index','setting','wishlist','order','address','bind','check','done','mygoods','resend','mypoints','ordershow','deletewish','trackorder','changepw'),
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
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $user->email=$_POST['email'];
                $user->password=$_POST['password'];
                $user->rememberMe = true;
                if ($user->login()) {
                    Yii::app()->shoppingcart->shareShoppintCartAfterLogin(Yii::app()->user->id);
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
                    Yii::app()->user->setFlash('error', Yii::t('mii', 'Registration failed!!'));
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
        $profile = User::model()->findByPk(Yii::app()->user->id);
        $profile->birthday = $profile->birthday == "0000-00-00 00:00:00" ? "" : date("Y-m-d",strtotime($profile->birthday));
        if (isset($_POST['User'])) {
            $profile->setScenario('setting');
            $profile->setAttributes($_POST['User']);
            if ($profile->validate()) {
                if($profile->updateInfo($_POST['User'])){
                    Yii::app()->user->setFlash( 'success', Yii::t( 'mii', 'Registration Information added successfully!!' ) );
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
    /**
     * [actionOrdershow description]
     * @return [type] [description]
     */
    public function actionOrdershow(){
        $id = $_GET['id'];
        $uid = Yii::app()->user->id;
        $order = Order::model()->findByAttributes(array('id'=>$id,'uid'=>$uid));
        if(empty($order)){
            return array();
        }
        $products = OrderProduct::model()->findAllByAttributes(array('order_id'=>$id));
        $this->render('ordershow',array('order'=>$order,'products'=>$products));
    }
    /*backend user message*/
    public function actionMessage(){
        $uid = Yii::app()->user->id;
        $datas =array();
        $model = new Message('message');
        $datas = $model->getAlldatas($uid);
        if (isset($_POST['Message'])) {
            $_POST['Message']['uid']=$uid;
            $model->setAttributes($_POST['Message']);
            if ($model->validate()) {
                if($model->save(false)){
                    
                    Yii::app()->user->setFlash('success', Yii::t('mii', 'Message Sent ！'));
                    return $this->redirect("message");
                }
                else{
                    Yii::app()->user->setFlash('error', Yii::t('mii', 'Message fail!!'));
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
                    Yii::app()->user->setFlash('error', Yii::t('mii', 'Failed to add address!!'));
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
        $count = 1;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = Wishlist::model()->getWishCountByUid($uid);
        $wishRecords = $model->getAllWishRecords($uid,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/user/wishlist/p/",2);
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
    /**
     * [actionDeltewish description]
     * @return [type] [description]
     */
    public function actionDeletewish(){
        $id = $_POST['id'];
        if(Wishlist::model()->deleteByPk($id)){
            echo json_encode("success");
        }
    }
    public function getProductProfile($id){
        $product = OrderProduct::model()->findByPk($id);
        if(empty($product)){
            return "";
        }
        if(empty($product->product_meta)){
            return "";
        }
        $meta = unserialize($product->product_meta);
        $str = "";
        foreach($meta as $k=>$v){
            $str .= $k.":".$v."<span>|</span>";
        }
        return $str;
    }
    /**
     * [actionTrackorder description]
     * @return [type] [description]
     */
    public function actionTrackorder(){
        $this->render('trackorder');
    }
    public function getName($uid){
        $result = User::model()->findByPk($uid);
        return $result->username;
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
                    // $found->send_recover_mail();
                    return $this->render('recover_sent', array('user'=>$found));
                } else {
                    Yii::app()->user->setFlash('error',array(
                                'main'=>Yii::t('mii','邮箱地址不存在！'),
                            ));
                }
            }
        }
        
        $this->render('login', compact('user'));
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
                return $this->render('recover_pass', array('user' => $user, 'result' => 2, 'message'=>'Reset Password success!'));
            }
        }
        unset($user->password);
        return $this->render('recover_pass', array('user'=>$user,'result' => 1));
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
                 Yii::app()->user->setFlash( 'success', Yii::t( 'mii', 'Congratulations on your successful password change！' ) );
            }
        }
        return $this->render('changepw', array('user' => $user));
    }
}