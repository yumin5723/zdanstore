<?php
/**
 * MansController Controller.
 *
 * @version 1.0
 *
 */

class SaleController extends GController {
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
        //brand page banner ad
        $focus = Click::model()->getAdsByType(Click::AD_POSITION_SALE_FOCUS,3);
        $brands = Brand::model()->getBrandsForIndex(100);
        // $lastest_sales = Newarrivals::model()->findAll();
        //mans left menu
        $mensTerms = Oterm::model()->getMensTreeMenu();
        //womens left menu
        $womensTerms = Oterm::model()->getWomensTreeMenu();

        $count = 24;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $nums = SubjectProduct::model()->getCountSales();
        $results = SubjectProduct::model()->getSaleProducts($count,$pageCurrent);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/sale/view?p=",2);
        $p = $subPages->show_SubPages(2);

        $this->render("index",array('focus'=>$focus,'mensterm'=>$mensTerms,
            'womensterm'=>$womensTerms,'nums'=>$nums,'results'=>$results,'pager'=>$p));
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
        $term_id = intval($_GET['cid']);
        $term = Oterm::model()->findByPk($term_id);
        //left menu category
        $leftCategory = Oterm::model()->getTreeByTermId($term_id);
        //left menu term profile
        $leftProfile = Oterm::model()->getTermProfile($term_id);
        //left brand
        $leftBrands = BrandTerm::model()->getBrandsByTerm($term_id);

        $request_profile = array();
        foreach($leftProfile as $p){
            if(isset($_GET[$p['name']]) && $_GET[$p['name']] != ""){
                $request_profile[$p['name']] = $_GET[$p['name']];
            }
        }
        $ssid = isset($_GET['ssid']) ? $_GET['ssid'] : '';
        $brid = isset($_GET['brid']) ? $_GET['brid'] : '';

        $url = '/womens/term';
        foreach($_GET as $k=>$o){
            $url.="/".$k."/".$o; 
        }
        $url.="/p/";

        $count = 24;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;
        $objects = Product::model()->fetchProductsByTermIdAndOptions($term_id,$count,$pageCurrent,$ssid,$brid,$request_profile);
        $sum = Product::model()->getProductsCountByTermIdAndOptions($term_id,$ssid,$brid,$request_profile);
        $sub_pages = 6;
        $subPages=new SubPages($count,$sum,$pageCurrent,$sub_pages,$url,2);
        $p = $subPages->show_SubPages(2);

        $this->render('term',array('results'=>$objects,'pager'=>$p,'nums'=>$sum,'leftCategory'=>$leftCategory
            ,'leftProfiles'=>$leftProfile,'leftbrands'=>$leftBrands,'term'=>$term
            ));
        // $this->render('term');
    }
    /**
     * get filter url 
     * @return [type] [description]
     */
    public function getUrl($key,$value){
        $options = $_GET;
        foreach($options as $k=>$v){
            if($k == $key){
                $options[$k] = $value;
            }else{
                $options[$key] = $value;
            }
            if($value == ""){
                unset($options[$key]);
            }
        }
        $url = '/womens/term';
        foreach($options as $k=>$o){
            $url.="/".$k."/".$o; 
        }
        return $url;
    }
}
