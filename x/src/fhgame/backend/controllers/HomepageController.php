<?php
Yii::import("application.controllers.BlockController");
class HomepageController extends BlockController {
    const BLOCK_FOCUS_ID = 30;
    const BLOCK_MATCHACTIVE_ID = 31;
    const BLOCK_GIFT_ID = 32;
    const BLOCK_STAR_ID = 33;
    const BLOCK_LEFTGAMELIST_MOBILEGAME_ID = 26;
    const BLOCK_LEFTGAMELIST_PAI_ID = 27;
    const BLOCK_LEFTGAMELIST_GUPAI_ID = 28;
    const BLOCK_LEFTGAMELIST_QI_ID = 29;
    const BLOCK_LEFTGAMELIST_XIUXIAN_ID = 30;
    const BLOCK_AWARD_USER = 11;
    public $sidebars = array(
        array(
            'name' => '焦点图',
            'icon' => 'user',
            'url' => 'focus',
        ),
        array(
            'name' => '左侧手机游戏',
            'icon' => 'music',
            'url' => 'mobile',
        ),
        array(
            'name' => '左侧牌类游戏',
            'icon' => 'music',
            'url' => 'pai',
        ),
        array(
            'name' => '左侧骨牌类游戏',
            'icon' => 'music',
            'url' => 'gupai',
        ),
        array(
            'name' => '左侧棋类游戏',
            'icon' => 'music',
            'url' => 'qi',
        ),
        array(
            'name' => '左侧休闲游戏',
            'icon' => 'music',
            'url' => 'xiuxian',
        ),
        array(
            'name' => '比赛活动',
            'icon' => 'music',
            'url' => 'match',
        ),
        array(
            'name' => '礼品中心',
            'icon' => 'music',
            'url' => 'gift',
        ),
        array(
            'name' => '明星玩家',
            'icon' => 'music',
            'url' => 'star',
        ),
        array(
            'name' => '获奖名单',
            'icon' => 'music',
            'url' => 'award',
        ),
    );
    /**
     * @return array action filters
     */
    public function filters() {
        
        return array(
            'accessControl', // perform access control for CRUD operations
            
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        
        return array(
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'index','focus','update','mobile','pai','gupai','qi','xiuxian','match','gift','star','build','award'
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'allow', // all all users
                'actions' => array(
                    'error'
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
            array(
                'deny', // deny all users
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }
    public function actionFocus(){
        $focus = $this->loadBlock(self::BLOCK_FOCUS_ID);
        $this->render("block",array("block"=>$focus));
    }
    //moblie game list 
    public function actionMobile(){
        $gamelist = $this->loadBlock(self::BLOCK_LEFTGAMELIST_MOBILEGAME_ID);
        $this->render("block",array("block"=>$gamelist));
    }
    //pai game term list
    public function actionPai(){
        $gamelist = $this->loadBlock(self::BLOCK_LEFTGAMELIST_PAI_ID);
        $this->render("block",array("block"=>$gamelist));
    }
    //gupai game term list
    public function actionGupai(){
        $gamelist = $this->loadBlock(self::BLOCK_LEFTGAMELIST_GUPAI_ID);
        $this->render("block",array("block"=>$gamelist));
    }
    // qi game term list 
    public function actionQi(){
        $gamelist = $this->loadBlock(self::BLOCK_LEFTGAMELIST_QI_ID);
        $this->render("block",array("block"=>$gamelist));
    }
    // xiuxian game term list 
    public function actionXiuxian(){
        $gamelist = $this->loadBlock(self::BLOCK_LEFTGAMELIST_XIUXIAN_ID);
        $this->render("block",array("block"=>$gamelist));
    }
    //match block 
    public function actionMatch(){
        $match = $this->loadBlock(self::BLOCK_MATCHACTIVE_ID);
        $this->render("block",array("block"=>$match));
    }
    //gift center list 
    public function actionGift(){
        $gift = $this->loadBlock(self::BLOCK_GIFT_ID);
        $this->render("block",array("block"=>$gift));
    }
    //star list 
    public function actionStar(){
        $star = $this->loadBlock(self::BLOCK_STAR_ID);
        $this->render("block",array("block"=>$star));
    }
    //award user list
    public function actionAward(){
        $award = $this->loadBlock(self::BLOCK_AWARD_USER);
        $this->render("block",array("block"=>$award));
    }
    /**
     * load homepage custom block
     */
    public function loadBlock($id) {
        $model = Block::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
