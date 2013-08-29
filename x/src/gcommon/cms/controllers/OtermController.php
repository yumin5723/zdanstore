<?php
class OtermController extends GController {
     public $sidebars = array(
        array(
            'name' => '分类管理',
            'icon' => 'tasks',
            'url' => 'index',
        ),
        array(
            'name' => '新建根分类',
            'icon' => 'tasks',
            'url' => 'root',
        ),
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
                'actions'=>array('index','getroot','gettermid','create','edit','root','delete','show','view','preview','publish'),
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
        $id = isset($_GET['id']) ? $_GET['id'] : "zTreeAsyncTest";
        $roots=Oterm::model()->roots()->findAll();

        $results = new CActiveDataProvider("Oterm", array(
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
        $model = new Oterm;
        $root = Oterm::model()->findByAttributes(array("root"=>$root));
        $descendants = $root->descendants()->findAll();

        $results = new CActiveDataProvider("Oterm", array(
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
        $model = new Oterm;
        $roots = Oterm::model()->findByAttributes(array("root"=>$root));
        $descendants = $roots->descendants()->findAll();
        if(isset($_POST['Oterm'])){
            $model->setAttributes($_POST['Oterm']);
            $result = $model->setCategory($_POST['Oterm']['id'],Yii::app()->user->id);
            if($result[0] == true){
                return $this->redirect('/pp/oterm/show/root/'.$root);
            }
            $model->setCategory($root);
        }
        $this->render('create',array('model'=>$model,'root'=>$roots,'descendants'=>$descendants));
    }
    /**
     * create root
     */
    public function actionRoot(){
        $model = new Oterm;
        if(isset($_POST['Oterm'])){
            $model->name = $_POST['Oterm']['name'];
            $model->short_name = $_POST['Oterm']['short_name'];
            $model->saveNode();
            $this->redirect("/pp/oterm/index");
        }
        $this->render("root",array('model'=>$model));
    }


    public function actionEdit(){
        $id = $_GET['id'];
        $category = Oterm::model()->findByPk($id);
        $roots = Oterm::model()->findByAttributes(array("root"=>$category->root));
        $descendants = $roots->descendants()->findAll();
        $parent=$category->parent()->find();
        if(isset($_POST['Oterm'])){
            if ($category->updateNode($parent,$_POST['Oterm']['parent_id'],Yii::app()->user->id)) {
                    $category->updateCategory($_POST['Oterm']);
                    Yii::app()->user->setFlash('success', Yii::t('pp', 'Successfully!'));
                    return $this->redirect(array('edit','id'=>$id));
                } else {
                    Yii::app()->user->setFlash('error', Yii::t('pp', 'Error!'));
                }
            } 
        $this->render("edit",array("model"=>$category,'parent'=>$parent,'descendants'=>$descendants));
    }
    /**
     * view this term object 
     */
    public function actionView($id){
        $result = ObjectTerm::model()->fetchObjectsByTermid($id);
        $term_name = Oterm::model()->findByPk($id)->name;
        $this->render( 'view', array(
            'result'=>$result,
            'name'=>$term_name
            ) );
    }
    /**
     * delete category
     */
    public function actionDelete($id){
        $node = Oterm::model()->findByPk($id);
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
    /**
     * preview object
     */
    public function actionPreview($id){
        $category = Oterm::model()->findByPk($id);
        echo $category->display(1);
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $category = Oterm::model()->findByPk($id);
        if(empty($category) || $category->template_id == 0){
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $result = $category->doPublish();
        if($result !== false){
            Yii::app()->user->setFlash( 'success', Yii::t( 'pp', '发布成功!' ) );
            return $this->redirect("/pp/oterm/show?root=".$category->root);
        }
    }
}