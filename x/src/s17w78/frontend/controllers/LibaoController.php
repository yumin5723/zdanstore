<?php
class LibaoController extends ERestController {
    /**
     * @return array action filters
     */
    public function filters() {
        $filters = array(
            'accessControl', // perform access control for CRUD operations
        );
        // if (isset(Yii::app()->params['needAlphaCode']) && Yii::app()->params['needAlphaCode']) {
        //     $filters[] = array('application.filters.AlphaCodeFilter + register');
        // }
        return $filters;
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('fetch','tao'),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('index', 'detail','help','search'),
                'users' => array('*')
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    /**
     * @return array action filters
     */
    public function actionIndex() {

        $count = 16;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $search = "";
        $nums = Package::model()->getCountPages($search);
        $result = Package::model()->getPagePackages($count,$pageCurrent,$search);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/libao/index?p=",2);
        $p = $subPages->show_SubPages(2);

        $this->render('card',array('result'=>$result,'pager'=>$p,"nums"=>$nums));
    }

    public function actionSearch(){
        $count = 16;
        $sub_pages = 6;
        $pageCurrent = isset($_GET['p']) ? $_GET["p"] : 1;

        $keywords = !empty($_GET['keywords']) ? $_GET['keywords'] : "";
        $nums = Package::model()->getCountPages($keywords);
        $result = Package::model()->getPagePackages($count,$pageCurrent,$keywords);
        $subPages=new SubPages($count,$nums,$pageCurrent,$sub_pages,"/libao/search?keywords={$keywords}&p=",2);
        $p = $subPages->show_SubPages(2);

        $this->render('card',array('result'=>$result,'pager'=>$p,"nums"=>$nums,'keywords'=>$keywords));

    }

    public function actionDetail(){

            $package_id = isset($_GET['id']) ? $_GET['id'] :1;
            $result = Package::model()->findByPk($package_id);
            if (empty($result)) {
                return $this->redirect("/libao");
            }
            return $this->render('card_content',array('result'=>$result));
    }

    public function actionHelp(){
        $this->render('help');
    }
    /**
     * user get activecode 
     * @return [type] [description]
     */
    public function actionFetch(){
        $uid = Yii::app()->user->id;
        $package_id = $_POST['package_id'];
        $code = ActiveCode::model()->getActiveCode($package_id,$uid);
        return $this->renderJson(array(
                'codes'=>$code,
            ));
    }
    /**
     * user tao code
     * @return [type] [description]
     */
    public function actionTao(){
        $uid = Yii::app()->user->id;
        $package_id = $_POST['package_id'];
        $code = ActiveCode::model()->taoCode($package_id);
        return $this->renderJson(array(
                'codes'=>$code,
            ));
    }

}
