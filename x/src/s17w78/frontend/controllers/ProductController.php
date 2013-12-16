<?php

class ProductController extends GController {

	/**
     * function_description
     *
     *
     * @return
     */
    public function filters() {

        return array(
            'accessControl'
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules() {

        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                    'list',
                    'view',
                    'quick',
                    'getsize'
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
     * function_description
     *
     *
     * @return
     */
    public function actionList() {
        $term_id = isset($_GET['cid']) ? $_GET['cid'] : "0";
        // $count = 20;
        $count = 20;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;


        $sum = Product::model()->getProductsCountByTermId($term_id);
        $products = Product::model()->fetchProductsByTermId($term_id,$count,$pageCurrent);
        $url = "/product/index/cid/".$term_id."/p/";

        $subPages=new SubPages($count,$sum,$pageCurrent,$sub_pages,$url,2);

        $p = $subPages->show_SubPages(2);
        $this->render("index",array('pager'=>$p,'products'=>$products));
    }
    /**
     * action for default home page
     * @return [type] [description]
     */
    public function actionIndex(){
        Yii::app()->shoppingcart->shareShoppintCartAfterLogin(Yii::app()->user->id);
    }
    /**
     * a detail page for product
     * @return [type] [description]
     */
    public function actionView(){
        $id = intval($_GET['id']);
        $product = Product::model()->with('brand')->findByPk($id);

        //relation products
        $alsolikes = Product::model()->getRelationList($id);
        // $categories = Product::model()->getObjectTermById($id);
        if(empty($product)){
            throw new Exception("this page is not find", 404);
        }
        $product_images = ProductImage::model()->findAllByAttributes(array('product_id'=>$id));
        $product_profiles = ProductProfile::model()->getProfilesByProductId($id);
        $this->render('view',array('product'=>$product,'product_images'=>$product_images,'product_profiles'=>$product_profiles,'alsolikes'=>$alsolikes));
    }
    /**
     * get product menu
     * @return [type] [description]
     */
    public function getMenu($product_id){
        $categories = Product::model()->getObjectTermById($product_id);

        $html = '<a href="/">Home</a> &gt;';
        if(empty($categories)){
            return "";
        }
        foreach($categories as $key=>$category){
            $name = $category['term_name'];
            $url = $category['url'];
            $html .= " <a href='#'>$name</a> &gt;";
        }
        return $html;
    }
    /**
     * [actionQuick description]
     * @return [type] [description]
     */
    public function actionQuick(){
        $id = intval($_POST['id']);
        $product = Product::model()->with('brand')->findByPk($id);

        // $categories = Product::model()->getObjectTermById($id);
        if(empty($product)){
            throw new Exception("this page is not find", 404);
        }
        $product_images = ProductImage::model()->findAllByAttributes(array('product_id'=>$id));
        $product_profiles = ProductProfile::model()->getProfilesByProductId($id);
        $html = $this->render('quick',array('product'=>$product,'product_images'=>$product_images,'product_profiles'=>$product_profiles),true);
        echo json_encode($html);
    }
    /**
     * [actionGetsize description]
     * @return [type] [description]
     */
    public function actionGetsize(){
        if(Yii::app()->request->isPostRequest){
            $id = $_POST['id'];
            $color = $_POST['color'];
            $sizesResults = Product::model()->getAllsizesById($id);

            $sizes = array();
            foreach($sizesResults as $result){
                $sizes[] = $result->profile_value;
            }
            $can_buy = ProductStock::model()->findAllByAttributes(array("color"=>$color,"product_id"=>$id));
            $can_buy_sies = array();

            foreach($can_buy as $buy){
                $can_buy_sies[] = $buy->size;
            }
            $ret = array();
            foreach($sizes as $size){
                if(in_array($size, $can_buy_sies)){
                    $ret[$size] = 1;
                }else{
                    $ret[$size] = 0;
                }
            }
            echo json_encode($ret);
        }
    }
}
