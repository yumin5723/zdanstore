<?php
class GameController extends CController {
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
                    'index',
                    'list',
                    'detail',
                    'play',
                    'android',
                    'androiddetail',
                    'apple',
                    'appledetail'
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    public function actionIndex() {
        $game = new Game;

        $all_small_game = Game::model()->getAllGame('小游戏');
        $term_small = Category::model()->getSecTermId('小游戏');
        $small_recommend = Game::model()->getRecommend();
        // $small_recommend2 = Game::model()->getRecommend2();
        $small_rank = Game::model()->getRank();
        $small_id = Category::model()->findByAttributes(array('name'=>'小游戏','root'=>1))->id;

        $all_android_game = MobileGame::model()->getAllGame('安卓游戏');
        $term_android = Category::model()->getSecTermId('安卓游戏');
        $android_rank = MobileGame::model()->getRankGame('安卓游戏');
        $android_id = Category::model()->findByAttributes(array('name'=>'安卓游戏','root'=>1))->id;


        $all_apple_game = MobileGame::model()->getAllGame('苹果游戏');
        $term_apple = Category::model()->getSecTermId('苹果游戏');
        $apple_rank = MobileGame::model()->getRankGame('苹果游戏');
        $apple_id = Category::model()->findByAttributes(array('name'=>'苹果游戏','root'=>1))->id;

        $this->render('game',array('term_small'=>$term_small,'term_android'=>$term_android,
                                    'term_apple'=>$term_apple,'all_small_game'=>$all_small_game,
                                    'all_android_game'=>$all_android_game,'all_apple_game'=>$all_apple_game,
                                    'small_recommend'=>$small_recommend,'small_rank'=>$small_rank,
                                    'android_rank'=>$android_rank,'apple_rank'=>$apple_rank,'small_id'=>$small_id,'android_id'=>$android_id,'apple_id'=>$apple_id));
    }


    public function actionList(){

        $term_id = Category::model()->getTermId('小游戏');
        $id = isset($_GET['id']) ? $_GET['id'] : $term_id;

        $count = 40;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $term_small = Category::model()->getSecTermId('小游戏'); 
        if ($id != $term_id) {
            $term_small_all = Category::model()->getChildTerm($id);
            $nums = Game::model()->getCountGames($term_small_all);
            $all_small_game = Game::model()->getlistgames($term_small_all,$count,$pageCurrent);
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/game/list?id=$id&p=",2);
        } else {
            $term_small_all = Category::model()->getChildTerm($term_id);
            $nums = Game::model()->getCountGames($term_small_all);
            $all_small_game = Game::model()->getlistgames($term_small_all,$count,$pageCurrent);
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/game/list?p=",2);
        }

        $small_rank = Game::model()->getRank();

        $p = $subPages->show_SubPages(2);
        $this->render('game_list',array('all_small_game'=>$all_small_game,'small_rank'=>$small_rank,
                                        'term_small'=>$term_small,'id'=>$id,'term_id'=> $term_id,'pager'=>$p,'nums'=>$nums));
        
    }

    public function actionDetail(){
        $id = isset($_GET['id']) ? $_GET['id'] : 1 ;
        $game = Game::model()->findByPk($id);
        if (empty($game)) {
            return $this->redirect("/game/");
        }
        $small_rank = Game::model()->getRank();
        $term = Category::model()->findByPk($game->category_id);
        $related_games = Game::model()->getDetailRelatedGame($game->category_id);

        $page_title = $game->name."在线玩_".$game->name."小游戏在线玩_".$game->name."小游戏网页版_1378棋牌网";
        $keywords = $game->name."在线玩,".$game->name."小游戏在线玩,".$game->name."小游戏网页版,1378棋牌网";
        $description = "1378棋牌网(www.1378.com)为您提供".$game->name."游戏在线玩服务,".$game->name."游戏是一款很好玩的棋牌游戏,欢迎您来1378玩".$game->name."免费在线游戏。";

        $this->render('game_detail',array('game'=>$game,'small_rank'=>$small_rank,'term'=>$term,'related_games'=>$related_games,'p_title'=>$page_title,'p_k'=>$keywords,'p_d'=>$description));

    }

    public function actionPlay(){

        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        $game = Game::model()->findByPk($id);
        if (empty($game)) {
            return $this->redirect("/game/");
        }
        $term = Category::model()->findByPk($game->category_id);
        $related_games = Game::model()->getPlayRelatedGame($game->category_id);

        $page_title = $game->name."在线玩_".$game->name."小游戏在线玩_".$game->name."小游戏网页版_1378棋牌网";
        $keywords = $game->name."在线玩,".$game->name."小游戏在线玩,".$game->name."小游戏网页版,1378棋牌网";
        $description = "1378棋牌网(www.1378.com)为您提供".$game->name."游戏在线玩服务,".$game->name."游戏是一款很好玩的棋牌游戏,欢迎您来1378玩".$game->name."免费在线游戏。";

        $this->render('game_play',array('game'=>$game,'related_games'=>$related_games,'p_title'=>$page_title,'p_k'=>$keywords,'p_d'=>$description));

    }

    public function actionAndroid(){

        $term_id = Category::model()->getTermId('安卓游戏');
        $id = isset($_GET['id']) ? $_GET['id'] : $term_id;

        $count = 40;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $term_small = Category::model()->getSecTermId('安卓游戏'); 

        if ($id != $term_id) {
            $term_small_all = Category::model()->getChildTerm($id);
            $nums = MobileGame::model()->getCountGames($term_small_all);
            $all_small_game = MobileGame::model()->getlistgames($term_small_all,$count,$pageCurrent);
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/game/android?id=$id&p=",2);
        } else {
            $term_small_all = Category::model()->getChildTerm($term_id);
            $nums = MobileGame::model()->getCountGames($term_small_all);
            $all_small_game = MobileGame::model()->getlistgames($term_small_all,$count,$pageCurrent);
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/game/android?p=",2);
        }

        $small_rank = MobileGame::model()->getRank($term_id);
        
        $p = $subPages->show_SubPages(2);
        $this->render('android_game_list',array('all_small_game'=>$all_small_game,'small_rank'=>$small_rank,
                                        'term_small'=>$term_small,'id'=>$id,'term_id'=>$term_id,
                                        'pager'=>$p,'nums'=>$nums));
    }

    public function actionAndroiddetail(){
        $term_id = Category::model()->getTermId('安卓游戏');

        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        $game = MobileGame::model()->findByPk($id);
        if (empty($game)) {
            return $this->redirect("/game/");
        }
        $android_rank = MobileGame::model()->getRank($term_id);
        $term = Category::model()->findByPk($game->category_id);
        $related_games = MobileGame::model()->getDetailRelatedGame($game->category_id);

        $page_title = $game->name."_".$game->name."安卓版下载_".$game->name."安卓手机免费下载_1378棋牌网";
        $keywords = $game->name.",".$game->name."安卓版下载,".$game->name."安卓手机免费下载,1378棋牌网";
        $description = "1378棋牌网(www.1378.com)为您提供".$game->name.",".$game->name."安卓版下载,".$game->name."安卓手机免费下载等服务。";

        $this->render('android_game_info',array('game'=>$game,'android_rank'=>$android_rank,'term'=>$term,'related_games'=>$related_games,'p_title'=>$page_title,'p_k'=>$keywords,'p_d'=>$description));

    }

    public function actionApple(){

        $term_id = Category::model()->getTermId('苹果游戏');
        $id = isset($_GET['id']) ? $_GET['id'] : $term_id;

        $count = 40;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $term_small = Category::model()->getSecTermId('苹果游戏'); 

        if ($id != $term_id) {
            $term_small_all = Category::model()->getChildTerm($id);
            $nums = MobileGame::model()->getCountGames($term_small_all);
            $all_small_game = MobileGame::model()->getlistgames($term_small_all,$count,$pageCurrent);
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/game/apple?id=$id&p=",2);
        } else {
            $term_small_all = Category::model()->getChildTerm($term_id);
            $nums = MobileGame::model()->getCountGames($term_small_all);
            $all_small_game = MobileGame::model()->getlistgames($term_small_all,$count,$pageCurrent);
            $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/game/apple?p=",2);
        }

        $small_rank = MobileGame::model()->getRank($term_id);

        
        $p = $subPages->show_SubPages(2);
        $this->render('apple_game_list',array('all_small_game'=>$all_small_game,'small_rank'=>$small_rank,
                                        'term_small'=>$term_small,'id'=>$id,'pager'=>$p,'term_id'=>$term_id,
                                        'nums'=>$nums));
    }

    public function actionAppledetail(){

        $term_id = Category::model()->getTermId('苹果游戏');

        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        $game = MobileGame::model()->findByPk($id);
        if (empty($game)) {
            return $this->redirect("/game/");
        }
        $android_rank = MobileGame::model()->getRank($term_id);
        $term = Category::model()->findByPk($game->category_id);
        $related_games = MobileGame::model()->getDetailRelatedGame($game->category_id);

        $page_title = $game->name."_".$game->name."苹果版下载_".$game->name."苹果手机免费下载_1378棋牌网";
        $keywords = $game->name.",".$game->name."苹果版下载,".$game->name."苹果手机免费下载,1378棋牌网";
        $description = "1378棋牌网(www.1378.com)为您提供".$game->name.",".$game->name."苹果版下载,".$game->name."苹果手机免费下载等服务。";

        $this->render('apple_game_info',array('game'=>$game,'android_rank'=>$android_rank,'term'=>$term,'related_games'=>$related_games,'p_title'=>$page_title,'p_k'=>$keywords,'p_d'=>$description));
    }

    public function actionIndexGame(){
        $type = $_POST['type'];
        $id = $_POST['id'];
        if ($type == "gamelist") {
            $indexgame = Game::model()->getIndexShowGame($type,$id);
        } else {
            $indexgame = MobileGame::model()->getIndexShowMobileGame($type,$id);
        }
        $data = $this->render('label',array('all_small_game' => $indexgame,'type'=>$type),true);
        echo json_encode($data);
    }
}
