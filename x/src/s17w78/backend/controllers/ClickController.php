<?php
Yii::import("adlog.models.*");

class ClickController extends BackendController {
    public $nav_name = "click";

    public $sidebars = array(
        array(
            'name' => '概况',
            'icon' => 'tasks',
            'url' => 'index',
        ),
        array(
            'name' => '生成广告链接',
            'icon' => 'tasks',
            'url' => 'create',
        ),
    );

    public $side_nav = 'index';

    /**
     * function_description
     *
     *
     * @return
     */
    public function filters() {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
      return array(
          array('allow', // allow authenticated user to perform 'create' and 'update' actions
	      'actions' => array('index', 'create', "view",'user','register'),
              'users' => array('@'),
          ),
          array('allow', // all all users
              'actions' => array('error'),
              'users' => array('*'),
          ),
          array('deny', // deny all users
              'users' => array('*'),
          ),
      );
    }
    public function actionIndex() {
        $this->side_nav = 'index';
	return $this->render("index",array(
		'model' => Adlink::model(),
		'dataProvider'=>$this->get_links(),
	    ));
    }

    public function actionCreate() {
        $this->side_nav = "create";
	$model = new Adlink;
	if (isset($_POST['Adlink'])) {
	    $model->setAttributes($_POST['Adlink']);
	    if ($model->save()) {
		Yii::app()->user->setFlash("success",
		    sprintf("成功添加新的广告链接<strong> %s </strong>.", $model->name));
		$this->redirect(array('view','id'=>$model->id));
	    }
	}
	return $this->render('create', array(
		'model' => $model,
	    ));
    }

    public function actionView($id) {
	$model = Adlink::model()->findByPk($id);
	$this->render("view", array('adlink'=>$model,
		'dataProvider'=>$this->get_logs($id),
	    ));
    }

    protected function get_links() {
	$criteria = new CDbCriteria();
	$criteria->order = "id DESC";
	return new CActiveDataProvider('Adlink', array(
		'criteria'=>$criteria,
		'pagination'=>array(
		    'pageSize'=>20,
		),
	    ));

    }
    /**
     * function_description
     *
     * @param $id:
     *
     * @return
     */
    protected function get_logs($id) {
    	$criteria = new CDbCriteria();
    	$criteria->order = "id DESC";
    	$criteria->addCondition("link_id=:link_id");
    	$criteria->params[':link_id'] = intval($id);
    	return new CActiveDataProvider('Adlog', array(
    		'criteria'=>$criteria,
    		'pagination'=>array(
    		    'pageSize'=>50,
    		),
    	    ));
    }



}
