<?php

class ActivityController extends PageController {
    public $sidebars = array(
        array(
            'name' => '活动列表',
            'icon' => 'user',
            'url' => 'admin',
        ),
        array(
            'name' => '新建活动',
            'icon' => 'user',
            'url' => 'create',
        ),
    );
    const ACTIVE_ID = 9;
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Page;
        $domains = $model->getCanUseDomain();
        $roots=Oterm::model()->roots()->findAll();
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->attributes = $_POST['Page'];
            if ($model->validate()) {
                if ($model->saveRarFile($_FILES)) {
                    $model->admin_id = Yii::app()->user->id;
                    $model->save(false);
                    if(isset($_POST['Oterm'])){
                        $pageterm = PageTerm::model()->saveActivePageTerm($model->id,self::ACTIVE_ID);
                    }
                    (new CmsTasks())->parsePage($model->id);
                    Yii::app()->user->setFlash('success', '新建成功！');
                    $this->redirect("admin");
                }
            }
        }
        $this->render('create', array(
            'model' => $model,"domains"=>$domains,"roots"=>$roots,
        ));
    }
    /**
     * the page that belongs to activity
     */
    public function actionAdmin(){
        $pages = PageTerm::model()->getActivityPages();
        $this->render("admin",array("pages"=>$pages));
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $domains = $model->getCanUseDomain();
        $roots=Oterm::model()->roots()->findAll();
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->attributes = $_POST['Page'];
            if(!empty($_FILES['Page']['name']['upload'])){
                $model->saveRarFile($_FILES);
                $model->status = Page::STATUS_PARSEING;
            }else{
                $model->content = $_POST['Page']['content'];
                $model->status = Page::STATUS_DRAFT;
            }
            $model->modified_id = Yii::app()->user->id;
            $model->save(false);
            if(!empty($_FILES['Page']['name']['upload'])){
                (new CmsTasks())->parsePage($model->id);
            }
            Yii::app()->user->setFlash('success', '修改成功！');
        }
        $this->render('update', array(
            'model' => $model,"domains"=>$domains,"roots"=>$roots
        ));
    }
    /**
     * update activity index page
     */
    public function actionModify($id){
        $this->actionUpdate($id);
    }
   /**
     * push rar file
     */
    public function actionPublish($id) {
        $page = $this->loadModel($id);
        if($page->doPublish()){
            Yii::app()->user->setFlash('success', '发布成功！');
        }else{
            Yii::app()->user->setFlash('success', '发布失败请重新尝试!');
        }
        $this->redirect("/activity/admin");
    }
}

    