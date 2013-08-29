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

class PayController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '订单列表',
            'icon' => 'tasks',
            'url' => 'index',
        ),
        array(
            'name' => '订单详情',
            'icon' => 'tasks',
            'url' => 'detail',
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
                    'list',
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
        Yii::app()->payment;
        $model = new Order;
        $count = 15;
        $sub_pages = 6;
        $type = isset($_GET['type']) ? $_GET["type"] : 0;
        $dtype = isset($_GET['dtype']) ? $_GET["dtype"] : 1;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $nums = $model->getCounts($type,$dtype);
        $all_product = $model->getAllproducts($count,$pageCurrent,$type,$dtype);
        $datas=array();
        foreach ($all_product as $key => $value) {
            $datas[$key]=$value;
            if($value->app_id == 0){
                $datas[$key]['app_id']="无";
            }else{
                $appname = App::model()->getAppname($value->app_id);
                $datas[$key]['app_id']=$appname;
            }
        }
        if($type){$typeurl = "/type/$type";}else{$typeurl="";}
        if($dtype){$dtypeurl = "/dtype/$dtype";}else{$dtypeurl="";}
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/pay/index$typeurl$dtypeurl/p/",2);
        $p = $subPages->show_SubPages(2);
        $this->render("index",array('model'=>$datas,'type'=>$type,'dtype'=>$dtype,'nums'=>$nums,'pages'=>$p));
    }
    
    /**
     * The function that order's detail
     *
     */
    public function actionDetail(){
        Yii::app()->payment;
        $datas=array();
        $model = new Order;
        $id = isset($_GET['id'])?$_GET['id']:'';
        if(isset($id)){
            $pay = explode(',', $id);
            $data = $model->doSearch($pay);
            foreach ($data as $key => $value) {
                $datas[$key]=$value;
                if($value->app_id == 0){
                    $datas[$key]['app_id']="无";
                }else{
                    $appname = App::model()->getAppname($value->app_id);
                    $datas[$key]['app_id']=$appname;
                }
            }
        }
        $this->render('detail', array('model'=>$datas,'id'=>$id));
    }
    

    
    
}
