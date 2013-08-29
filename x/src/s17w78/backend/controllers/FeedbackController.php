<?php
Yii::import("gcommon.cms.components.ConstantDefine");
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * Backend App Controller.
 *
 * @version 1.0
 * @Cpuser backend.controllers
 *
 */

class FeedbackController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '反馈问题列表',
            'icon' => 'tasks',
            'url' => 'index',
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
                    'index',
                    'detail',
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
     * The function that do show order list
     *
     */
    public function actionIndex(){
        $model = new Feedback;
        $count = 15;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $model->count();
        $all_product = $model->getAllproducts($count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/feedback/index/p/",2);
        $p = $subPages->show_SubPages(2);
        $this->render("index",array('model'=>$all_product,'nums'=>$nums,'pages'=>$p));
    }  
     /**
     * The function that do show order list
     *
     */
    public function actionDetail(){
        $model = new Feedback;
        $id = isset($_GET['id'])?$_GET['id']:'';
        if(isset($id)){
            $data = $model->doSearch($id);
        }
        $this->render('detail', array('model'=>$data));
    }  
}
