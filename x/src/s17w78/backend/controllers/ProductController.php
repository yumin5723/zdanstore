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
                    'index'
                ) ,
                'users' => array(
                    '@'
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
    public function actionIndex() {
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
}
