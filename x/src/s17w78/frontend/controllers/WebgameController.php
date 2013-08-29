<?php

/**
 *
 */
class WebgameController extends CController
{
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
                'actions' => array('play','gamehtml'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index','online'),
                'users' => array('*')
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionIndex()
    {
        // all games
        $allgames = App::model()->getAppByType(0);
        // dzpk games
        $dzgames = App::model()->getAppByType(WebgameTerm::TERM_DZPK);
        // majiang games
        $mjgames = App::model()->getAppByType(WebgameTerm::TERM_MJ);
        // doudizhu games
        $ddzgames = App::model()->getAppByType(WebgameTerm::TERM_DDZ);
        // other games
        $othergames = App::model()->getAppByType(WebgameTerm::TERM_OTHER);

        $gameactive = GameActive::model()->getActive(GameActive::GAME_ACTIVE);

        $gameother = GameActive::model()->getActive(GameActive::GAME_OTHER);

        $this->render('index',array('allgames'=>$allgames,'dzpkgames'=>$dzgames,'ddzgames'=>$ddzgames,
                                    'mjgames'=>$mjgames,'othergames'=>$othergames,'gameactive'=>$gameactive,
                                    'gameother'=>$gameother));
    }

    public function actionPlay(){
        $id = $_GET['id'];
        if(Yii::app()->user->isGuest){
            return $this->render("need_login",array("id"=>$id));
        }
        $app = App::model()->findByPk($id);
        if ($app === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        if($app->status == App::GAME_IS_TEST && APP::model()->checkUserAuth(Yii::app()->user->id) == false){
            return $this->redirect("/webgame");
        }
        $url = Yii::app()->cooperation->getAppGateUrl($app->id);
        if(isset($_GET['re'])){
            return $this->redirect($url);
        }
        $style = Yii::app()->cooperation->getAppStyle($app->id);
        $relative_games = App::model()->getAppByType($app->type);
        //save user played time
        UserPlayed::model()->setUserPlayGameTime(Yii::app()->user->id,$app->id);
        // send login  game event
        Yii::app()->eventlog->send('playgame',Yii::app()->request->userHostAddress,
                    Yii::app()->request->getUrl(),
                    Yii::app()->user->id,
                    Yii::app()->request->urlReferrer
            );

        $this->render("play",array("url"=>$url,"app"=>$app,'style'=>$style,"ra_games"=>$relative_games));
    }
    /**
     * function_gamehtml
     *
     *
     * @return
     */
    public function actionGamehtml(){
        $uid=Yii::app()->user->id;
        $game = isset($_GET['game'])?$_GET['game']:'';
        $url = isset($_GET['url'])?$_GET['url']:'';
        $desk = isset($_GET['desk'])?$_GET['desk']:'';
        $host = isset($_GET['host'])?$_GET['host']:'';
        $hall = isset($_GET['hall'])?$_GET['hall']:'';
        $referrer = isset($_GET['referrer'])?$_GET['referrer']:'';
        $port = isset($_GET['port'])?$_GET['port']:'';
        $appid = isset($_GET['appid'])?$_GET['appid']:'';
        $openId = Yii::app()->openid->getUserOpenidForApp($appid,$uid);
        $url =$url."?game=".$game."&desk=".$desk."&player=".$openId."&hall=".$hall."&referrer=".$referrer."&port=".$port."&debug=off&password=test&companyPath=1378.com&host=".$host."&base=http://resource.17play8.com/szcch/hall/";
        return $this->redirect($url);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function actionOnline() {
        if (Yii::app()->user->isGuest) {
            return;
        }
        $uid = Yii::app()->user->id;
        if(!isset($_POST['app_id'])){
            return;
        }
        $app_id = intval($_POST['app_id']);

        $app = App::model()->findByPk($app_id);
        if (empty($app) || $app->status != App::GAME_IS_ONLINE ) {
            return;
        }
        UserPlayLog::model()->flushUserOnline($uid, $app_id, time());
        echo "true";
        Yii::app()->end();
    }



}