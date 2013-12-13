<?php
class ProfileController extends GController {
     public $sidebars = array(
        array(
            'name' => '属性管理',
            'icon' => 'tasks',
            'url' => 'index',
        ),
        array(
            'name' => '新建属性',
            'icon' => 'tasks',
            'url' => 'create',
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
                'actions'=>array('index','update','gettermid','create','edit','root','delete','show','view','preview','publish'),
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


        $this->render("index",array("root"=>$root,"descendants"=>$results));
    }
    /**
     * create category
     */
    public function actionCreate(){
        $root = 14;
        $model = new TermProfile;
        $roots = Oterm::model()->findByAttributes(array("root"=>$root));
        $descendants = $roots->descendants()->findAll();
        if(isset($_POST['TermProfile'])){
            $model->setAttributes($_POST['TermProfile']);
            if($model->validate()){
                if($model->saveTermProfile($_POST['term_id'],$_POST['TermProfile'])){
                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Profile Successfully!'));
                }
            }
        }
        $this->render('create',array('model'=>$model,'root'=>$roots,'descendants'=>$descendants,'isNew'=>true));
    }
    /**
     * update term profile
     * @return [type] [description]
     */
    public function actionUpdate(){
        $id = $_GET['id'];
        $term = Oterm::model()->findByPk($id);
        if(empty($term)){
            throw new Exception("this page is not found ", 404);
        }
        $profile = TermProfile::model()->getProfileByTerm($id);
        $model = new TermProfile;
        if(isset($_POST['TermProfile'])){
            // print_r($_POST);exit;/
            if($model->updateTermProfile($_POST['term_id'],$_POST['TermProfile'])){
                Yii::app()->user->setFlash('success', Yii::t('cms', 'Update new Profile Successfully!'));
            }
        }
        $this->render('update',array('profiles'=>$profile,'term'=>$term,'isNew'=>false));
    }
}