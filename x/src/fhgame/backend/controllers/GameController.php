<?php
class GameController extends BackendController {
    const BLOCK_DOWNLOAD_HOTGAME_ID = 34;
    public $sidebars = array(
        array(
            'name' => '游戏列表',
            'icon' => 'user',
            'url' => 'index',
        ),
        array(
            'name' => '添加游戏',
            'icon' => 'user',
            'url' => 'create',
        ),
        array(
            'name' => '游戏下载页热门游戏',
            'icon' => 'user',
            'url' => 'hot',
        ),
    );
    public function init(){
        $this->importCmsViews();
    }
    const GAME_TEMPLATE_ID = 3;
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
                    'index','create','update','publish','delete','hot','modify'
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'allow',
                'actions' => array(
                    'login'
                ) ,
                'users' => array(
                    '?'
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
     * The function that do Manage User
     *
     */
    public function actionIndex() {
        $result=null;
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $model = new Object;
        $model->unsetAttributes(); 
        if(isset($_GET['Object'])) {
            $model->attributes=$_GET['Object'];
        }                       
        $result=$model->doSearch("game");
        $this->render( 'admin', array(
            'model'=>$model,
            'result'=>$result,
            ) );
    }
    /**
     * create a game
     */
    public function actionCreate(){
        $game = new GameObject;
        // collect user input data
        if (isset($_POST['GameObject'])) {
            $game->attributes = $_POST['GameObject'];
            if ($game->validate()) {
                if($game->save(false)){
                    //save game templete  game templete id is defined 2
                    ObjectTemplete::model()->saveObjectTemplete($game->id,self::GAME_TEMPLATE_ID);
                    $fhmodel = new FhObjectTerm;
                    $fhmodel->saveGameTerm($game->object_id,$_POST['GameObject']['term']);
                    Yii::app()->user->setFlash('success', '新建成功！');
                    $this->redirect('index');
                }
            }
        }
        $this->render('create', array(
            'game' => $game
        ));
    }
    /**
     * The function that do Update game
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        $game = $this->loadModel($id);
        // collect user input data
        if (isset($_POST['GameObject'])) {
            $game->attributes = $_POST['GameObject'];
            if ($game->updateAttrs($_POST['GameObject'])) {
                $fhmodel = new FhObjectTerm;
                $fhmodel->updateGameTerm($game->object_id,$_POST['GameObject']['term']);
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
            }
        }
        $game->downurl = ObjectMeta::model()->findByAttributes(array("meta_object_id"=>$id,"meta_key"=>"downurl"))->meta_value;
        $gameterm = ObjectTerm::model()->findByAttributes(array("object_id"=>$id));
        if(empty($gameterm)){
            $game->term = "";
        }else{
            $game->term = $gameterm->term_id;
        }
        $this->render('update', array(
            "game" => $game
        ));
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = GameObject::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $object = Object::model()->findByPk($id);
        $result = $object->doPublish();
        if($result !== false){
            Yii::app()->user->setFlash( 'success', Yii::t( 'cms', '发布成功!' ) );
            $this->redirect("/game/index");
        }
    }
     /**
     * The function is to Delete a Game
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('GameObject', $id);
    }
    // download page hot game list 
    public function actionHot(){
        $gamelist = $this->loadBlock(self::BLOCK_DOWNLOAD_HOTGAME_ID);
        $this->render("hot",array("block"=>$gamelist));
    }
    /**
     * Displays the create page
     */
    public function actionModify($id) {
        $model = $this->loadBlock($id);
        $types = ConstantDefine::getBlockType();
        // collect user input data
        if (isset($_POST['Block'])) {
            $model->attributes = $_POST['Block'];
            if($model->updateBlock(Yii::app()->user->id)){
                Yii::app()->user->setFlash('success', '修改成功！');
                // $this->redirect("/block/admin");
            }
        }
        $this->render($types[$model->type], array(
            'block' => $model,"type"=>$model->type
        ));
    }
    /**
     * load  custom block
     */
    public function loadBlock($id) {
        $model = Block::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
