<?php
/**
 * Subject Brand Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class SubjectController extends GController {
    public $sidebars = array(
        array(
            'name' => '折扣活动',
            'icon' => 'tasks',
            'url' => 'discount',
        ),
         array(
            'name' => '满减活动',
            'icon' => 'tasks',
            'url' => 'discount',
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
                    'create',
                    'admin',
                    'update',
                    'delete',
                    'product',
                    'discount',
                    'view',
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
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Subject::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Update Settings
     *
     */
    public function actionUpdateSettings() {
        $this->menu = array();
        $this->render('user_update_settings');
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Subject;
        // collect user input data
        if (isset($_POST['Subject'])) {
            $model->setAttributes($_POST['Subject']);
            // validate user input password
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Subject Successfully!'));
                    $this->redirect('discount');
                }
            }
        }
        $this->render('create', array(
            "model" => $model,'isNew'=>true,
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $this->render('admin');
    }
    /**
     * The function that do View User
     *
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
        $brands = array();
        $root = "";
        $results = array();
        $flag = false;
        if(isset($_GET['type']) && $_GET['type'] == 'brand'){
            $brands = new Brand('search');
            $brands->unsetAttributes(); // clear any default values
            if(isset($_GET["Brand"]))
                        $brands->attributes=$_GET["Brand"];
            // return $this->render('brands', array(
            //     "model" => $model
            // ));
            $flag = true;
        }elseif(isset($_GET['type']) && $_GET['type'] == 'term'){
            $root = 14;
            $model = new Oterm;
            $root = Oterm::model()->findByAttributes(array("root"=>$root));
            $descendants = $root->descendants()->findAll();
            $results = new CActiveDataProvider("Oterm", array(
                'data'=>$descendants,
                'pagination' => array(
                        'pageSize' => 20,
                    )
            ));
            $flag = true;
            // $this->render("show",array("root"=>$root,"descendants"=>$results));
        }
        $this->render("view",array('flag'=>$flag,'subjectid'=>$id,'brands'=>$brands,"root"=>$root,"descendants"=>$results));
    }
    /**
     * [actionDetail description]
     * @return [type] [description]
     */
    public function actionDetail(){
        $subjectid = $_REQUEST['id'];
        $brand_id = "";
        $term_id = "";
        $terms = array();
        if(isset($_REQUEST['brandid'])){
            $brand_id = $_REQUEST['brandid'];
            $terms = BrandTerm::model()->getBrandTerms($_GET['id']);
            //all products
            $products = Product::model()->findAllByAttributes(array('brand_id'=>$brand_id,'status'=>Product::PRODUCT_STATUS_SELL));
            if(isset($_REQUEST['cid'])){
                $products = Product::model()->fetchProductsByTermIdAndBrand($_REQUEST['cid'],$_REQUEST['brandid']);
            }
            //select products
            $selected = SubjectProduct::model()->fetchAllSelectProductsByBrandId($brand_id,$subjectid);
            if(Yii::app()->request->isPostRequest){
                $model = new SubjectProduct;
                $result = $model->updateSubjectProduct($subjectid,$_POST['Product']);

                if($result[0] == false){
                    Yii::app()->user->setFlash('error', Yii::t('cms', '以下商品已经参加了其他打折活动，一件商品暂时不能参加多个打折活动!:'.$result[1]));
                }else{
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'update success!!'));
                }
            }
        }
        if(isset($_REQUEST['termid'])){
            $term_id = $_REQUEST['termid'];
            //all products
            $products = Product::model()->getAllProductsByTermId($term_id);
            //select products
            $selected = SubjectProduct::model()->fetchAllSelectProductsByTermId($term_id,$subjectid);
            if(Yii::app()->request->isPostRequest){
                $model = new SubjectProduct;
                $result = $model->updateSubjectProduct($subjectid,$_POST['Product']);

                if($result[0] == false){
                    Yii::app()->user->setFlash('error', Yii::t('cms', '以下商品已经参加了其他打折活动，一件商品暂时不能参加多个打折活动!:'.$result[1]));
                }else{
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'update success!!'));
                }
            }
        }
        $this->render("detail",array('all'=>$products,"select"=>$selected,'id'=>$subjectid,"brandid"=>$brand_id,"termid"=>$term_id,'terms'=>$terms));
    }
    /**
     * The function that do Update User
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        if ($id !== 0) {
            $model = $this->loadModel($id);
            // collect user input data
            if (isset($_POST['Subject'])) {
                if ($model->updateAttrs($_POST['Subject'])) {
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('update', array(
            "model" => $model,"isNew"=>false,
        ));
    }
    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('Brand', $id);
    }
    /**
     * action for view product belong to brand
     * @return [type] [description]
     */
    public function actionProduct(){
        $id = $_GET['id'];
        $brand = Brand::model()->findByPk($id);
        if(empty($brand)){
            throw new Exception("Error Processing Request", 404);
        }
        $products = Product::model()->getAllProductsByBrand($id);
        $this->render('product',array('model'=> new Product,'id'=>$id));
    }
    /**
     * action for discount subject
     * @return [type] [description]
     */
    public function actionDiscount(){
        $model = new Subject('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Subject"]))
                    $model->attributes=$_GET["Subject"];  
        $this->render('discount', array(
            "model" => $model
        ));
    }
}
