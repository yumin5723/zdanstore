<?php

class CategoryController extends BackendController {
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
                'actions'=>array('index','create','edit','root','getroot','delete','show'),
                'users'=>array('@'),
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
        $roots=Category::model()->roots()->findAll();
        $this->render('index',array('roots'=>$roots));
    }
    /**
     * show root descendants
     */
    public function actionShow(){
        $root = $_GET['root'];
        $model = new Category;
        $root = Category::model()->findByAttributes(array("root"=>$root));
        $descendants = $root->descendants()->findAll();
        $this->render("show",array("root"=>$root,"descendants"=>$descendants));
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
            $result = $model->setCategory($_POST['Category']['id']);
            if($result[0] == true){
                Yii::app()->user->setFlash('success',$_POST['Category']['name'].'创建成功！');
            }
        }
        $this->render('create',array('model'=>$model,'root'=>$roots,'descendants'=>$descendants));
    }
    /**
     * create root
     */
    public function actionRoot(){
        $model = new Category;
        if(isset($_POST['Category'])){
            $model->setScenario("createroot");
            $model->setAttributes($_POST['Category']);
            if($model->validate()){
                $model->name = $_POST['Category']['name'];
                $model->short_name = $_POST['Category']['short_name'];
                $model->admin_id = Yii::app()->user->id;
                $model->saveNode();
                Yii::app()->user->setFlash('success',$_POST['Category']['name'].'创建成功！');
            }
        }
        $this->render("root",array('model'=>$model));
    }
    public function actionEdit(){
        $id = $_GET['id'];
        $category = Category::model()->findByPk($id);
        $parent=$category->parent()->find();
        if(isset($_POST['Category'])){
            if($category->saveAttributes($_POST['Category'])){
                Yii::app()->user->setFlash('success','修改成功！');
            }
        }
        $this->render("edit",array("catgory"=>$category,'parent'=>$parent));
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