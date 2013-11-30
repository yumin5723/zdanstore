<?php
/**
 * BrandController Controller.
 *
 * @version 1.0
 *
 */

class BrandsController extends GController {
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
                    'index','view','term'
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
     * action for brand index
     * @return [type] [description]
     */
    public function actionIndex(){
        // $brand = Brand::model()->findByPk($_GET['id']);
        // if(empty($brand)){
        //     throw new Exception("this page is not found", 404);
        // }
        // $terms = BrandTerm::model()->getBrandTerms($_GET['id']);
        // $this->render("index",array("terms"=>$terms));
        // 
        //brand page banner ad
        $banner = Click::model()->getAdsByType(Click::AD_POSITION_BRAND_BANNER,1);
        $brands = Brand::model()->getBrandsForIndex(100);
        $this->render("index",array('banner'=>$banner,'brands'=>$brands));
    }
    /**
     * action for brand view
     * @return [type] [description]
     */
    public function actionView(){
        $id = $_GET['id'];
        $brand = Brand::model()->findByPk($id);
        if(empty($brand)){
            throw new Exception("this page is not found", 404);
            
        }
        $terms = BrandTerm::model()->getBrandTerms($_GET['id']);
        // $this->render('view',array('brand'=>$brand,'terms'=>$terms));

        $count = 24;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $nums = Product::model()->getCountProductsByBrand($id);
        $results = Product::model()->getProductsByBrand($id,$count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/brand/view/id/".$id."?p=",2);
        $p = $subPages->show_SubPages(2);

        $this->render('view',array('brand'=>$brand,'terms'=>$terms,'nums'=>$nums,'results'=>$results,'pager'=>$p));
    }
    /**
     * action for brand term
     * @return [type] [description]
     */
    public function actionTerm(){
        $brand_id = intval($_GET['id']);
        $brand = Brand::model()->findByPk($brand_id);
        if(empty($brand)){
            throw new Exception("this page is not found", 404);
            
        }
        $term_id = intval($_GET['cid']);

        //left menu category
        $leftCategory = Oterm::model()->getTreeByTermId($term_id);
        //left menu term profile
        $leftProfile = Oterm::model()->getTermProfile($term_id);
        //left brand
        $leftBrands = BrandTerm::model()->getBrandsByTerm($term_id);

        $ssid = isset($_GET['ssid']) ? $_GET['ssid'] : '';
        // $brid = isset($_GET['brid']) ? $_GET['brid'] : '';
        $ppid = isset($_GET['ppid']) ? $_GET['ppid'] : '';

        $options = $_GET;
        foreach($options as $k=>$o){
            if(empty($o)|| $o == 'all' || $k == 'p' || $k == 'id' || $k == 'cid'){
                unset($options[$k]);
            }
        }

        $url = '';
        foreach($options as $k=>$o){
            $url.="&".$k."=".$o; 
        }
        $link = "/brands/term/id/".$brand_id."/cid/".$term_id;
        $pageurl = $link.ltrim($url,'&');
        $pageurl.="&p=";

        $count = 24;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $objects = Product::model()->fetchProductsByTermIdAndBrandAndOptions($term_id,$brand_id,$count,$pageCurrent,$ssid,$ppid);
        $sum = Product::model()->getProductsCountByTermIdAndBrandAndOptions($term_id,$brand_id,$ssid,$ppid);
        $sub_pages = 6;
        $subPages=new SubPages($count,$sum,$pageCurrent,$sub_pages,$pageurl,2);
        $p = $subPages->show_SubPages(2);

        $this->render('term',array('results'=>$objects,'pager'=>$p,'brand'=>$brand,'nums'=>$sum,'leftCategory'=>$leftCategory
            ,'leftProfiles'=>$leftProfile,'leftbrands'=>$leftBrands,'link'=>$link,'option'=>$options
            ));
        // $this->render('term');
    }

}
