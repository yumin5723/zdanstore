<?php
class CategoryController extends BackendController {
    public $sidebars = array(
        // array(
        //     'name' => '管理分类',
        //     'icon' => 'tasks',
        //     'url' => 'index',
        // ),
        // array(
        //     'name' => '创建分类',
        //     'icon' => 'tasks',
        //     'url' => 'root',
        // ),
    );
    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {
        return array('accessControl');
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions'=>array('index','getroot','gettermid'),
                'users'=>array('@'),
//                'roles' => array('分类管理首页'),
            ),
            array(
                'allow',
                'actions'=>array('create'),
                'users'=>array('@'),
//                'roles' => array('创建分类'),
            ),
            array(
                'allow',
                'actions'=>array('edit'),
                'users'=>array('@'),
//                'roles' => array('编辑分类'),
            ),
            array(
                'allow',
                'actions'=>array('root'),
                'users'=>array('@'),
//                'roles' => array('创建root'),
            ),
            array(
                'allow',
                'actions'=>array('delete'),
                'users'=>array('@'),
//                'roles' => array('编辑分类'),
            ),
            array(
                'allow',
                'actions'=>array('show'),
                'users'=>array('@'),
//                'roles' => array('查看分类'),
            ),
            array(
                'deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    /*
     * first level category
     */
    public function actionIndex(){
        $id = isset($_GET['id']) ? $_GET['id'] : "zTreeAsyncTest";
        $roots=Category::model()->roots()->findAll();
        // var_dump($roots);exit;

        $results = new CActiveDataProvider("Category", array(
            'data'=>$roots,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
        $this->render('index',array('roots'=>$results,"id"=>$id));
    }
    /**
     * show root descendants
     */
    public function actionShow(){
        $root = $_GET['root'];
        $model = new Category;
        $root = Category::model()->findByAttributes(array("root"=>$root));
        $descendants = $root->descendants()->findAll();

        $results = new CActiveDataProvider("Category", array(
            'data'=>$descendants,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));


        $this->render("show",array("root"=>$root,"descendants"=>$results));
    }
    /**
     * create category
     */
    public function actionCreate(){
        $root = $_GET['root'];
        $model = new Category;
        $roots = Category::model()->findByAttributes(array("root"=>$root));
        $descendants = $roots->descendants()->findAll();
        if(isset($_POST['Category'])){
            $model->setAttributes($_POST['Category']);
            $result = $model->setCategory($_POST['Category']['id'],Yii::app()->user->id);
            if($result[0] == true){
                return $this->redirect('/category/show/root/'.$root);
            }
            $model->setCategory($root);
        }
        $this->render('create',array('model'=>$model,'root'=>$roots,'descendants'=>$descendants));
    }
    /**
     * create root
     */
    public function actionRoot(){
        $model = new Category;
        if(isset($_POST['Category'])){
            $model->name = $_POST['Category']['name'];
            $model->short_name = $_POST['Category']['short_name'];
            $model->saveNode();
            $this->redirect("/category/index");
        }
        $this->render("root",array('model'=>$model));
    }


    public function actionEdit(){
        $id = $_GET['id'];
        $category = Category::model()->findByPk($id);
        $parent=$category->parent()->find();
        if(isset($_POST['Category'])){
            // var_dump($_POST['Category']);exit;
            if($category->saveAttributes($_POST['Category'])){
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Game Successfully!'));
                return $this->redirect(array('edit','id'=>$id));
            }
        }
        $this->render("edit",array("model"=>$category,'parent'=>$parent));
    }
    /**
     * delete category
     */
    public function actionDelete($id){
        $node = Category::model()->findByPk($id);
        $node->deleteNode();
        $this->redirect(Yii::app()->request->urlReferrer);
    }
    /**
     * get root
     */
    public function actionGetRoot(){
        if(array_key_exists('id',$_REQUEST)) {
            $pId = $_REQUEST['id'];
        }else{
            $pId = $_REQUEST['otherParam'];
        }
        $roots = Category::model()->findByPk($pId);
        $childrens = $roots->children()->findAll();
        $str = "";
        foreach($childrens as $i=>$child){
            $children = $child->children()->findAll();
            $nId = $child->id;
            $nName = $child->name;
            $str .="{ id:'".$nId."',name:'".$nName."',isParent:".( !empty($children)?"true":"false")."}";
            $str .=",";
        }
        echo "[".$str."]";
    }
}