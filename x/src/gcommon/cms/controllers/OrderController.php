<?php
/**
 * Backend App Controller.
 *
 * @version 1.0
 * @Cpuser backend.controllers
 *
 */

class OrderController extends GController {
    public $sidebars = array(
        array(
            'name' => '订单列表',
            'icon' => 'tasks',
            'url' => 'index',
        ),
        array(
            'name' => '订单搜索',
            'icon' => 'tasks',
            'url' => 'search',
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
                    'search',
                    'view',
                    'changestatus'
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
    public function actionSearch(){
        $datas=array();
        $model = new Order;
        $id = isset($_GET['id'])?$_GET['id']:'';
        if(isset($id)){
            $pay = explode(',', $id);
            $data = $model->doSearch($pay);
            foreach ($data as $key => $value) {
                $datas[$key]=$value;
            }
        }
        $this->render('search', array('model'=>$datas,'id'=>$id));
    }
    /**
     * action for order detail
     * @return [type] [description]
     */
    public function actionView(){
        $id = $_GET['id'];
        $order = Order::model()->findByPk($id);
        if(empty($order)){
            throw new Exception("this page is not find", 404);
        }
        $order_detail = OrderProduct::model()->getOrderDetail($id);
        $status = Order::model()->getAllStatus();
        $this->render('detail',array('detail'=>$order_detail,'order'=>$order,'status'=>$status));
    }
    /**
     * action for change status
     * @return [type] [description]
     */
    public function actionChangestatus(){
        if(Yii::app()->request->isPostRequest){
            if(isset($_POST['order_id'])){
                $order =  Order::model()->findByPk($_POST['order_id']);
                if(empty($order)){
                    throw new Exception("the request is not valid", 404);
                }
                $status = $_POST['status'];
                $order->status = $_POST['status'];
                $order->save(false);

                $this->redirect("/pp/order/view/id/".$_POST['order_id']);
            }
        }
    }
}