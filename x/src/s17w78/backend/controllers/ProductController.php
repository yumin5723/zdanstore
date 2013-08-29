<?php
Yii::import("gcommon.cms.components.ConstantDefine");
Yii::import("gcommon.cms.components.GxcHelpers");
/**
 * Backend Game Controller.
 *
 * @version 1.0
 * @package backend.controllers
 *
 */

class ProductController extends BackendController {
    public $sidebars = array(
        array(
            'name' => '麻将游戏',
            'icon' => 'tasks',
            'url' => 'admin?type_id=44',
        ),
        array(
            'name' => '棋类游戏',
            'icon' => 'tasks',
            'url' => 'admin?type_id=45',
        ),
        array(
            'name' => '分类管理',
            'icon' => 'tasks',
            'url' => '/category/show?root=50',
        ),
        // array(
        //     'name' => '手机游戏管理',
        //     'icon' => 'tasks',
        //     'url' => '/mobilegame/admin',
        // ),
        // array(
        //     'name' => '创建手机游戏',
        //     'icon' => 'tasks',
        //     'url' => '/mobilegame/create',
        // ),
    );
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
                    'index',
                    'create',
                    'admin',
                    'view',
                    'update',
                    'delete',
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'allow', // all all users
                'actions' => array(
                    'error'
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
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Product::model()->findByPk((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * The function that do Update Settings
     *
     */
    public function actionUpdateSettings() {
        $this->menu = array();
        $this->render('user_update_settings');
    }

    /**
     * The function that do Update Settings
     *
     */
    public function actionIndex() {

        $this->render('index');
    }




    /**
     * The function that do Create new User
     *
     */
    public function actionCreate() {
        $type_id = $_GET['type_id'] ? $_GET['type_id'] : 51;
        $model = new Product;

        Yii::import("config.related");
        Yii::import("config.rules");
        $related = require(Yii::getPathOfAlias("config")."/related.php");
        $rules = require(Yii::getPathOfAlias("config")."/rules.php");
        $node = Category::model()->findByPk($type_id);
        $descendants = $node->descendants()->findAll();
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'usercreate-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        $model->publish_date = date('Y-m-d H:i:s');
        // collect user input data
        if (isset($_POST['Product'])) {
            // var_dump($_POST['Rules']);exit;
            $model->setAttributes($_POST['Product']);
            // validate user input password
            if ($model->validate()) {
                $model->created_uid = Yii::app()->user->id;
                $model->modified_uid = $model->created_uid;
                if ($model->save()) {
                    $id = $model->attributes['id']; 
                    $game_related = GameRelated::model()->saveRelated($_POST['Related'],$id);
                    $game_rules = GameRules::model()->saveRules($_POST['Rules'],$id);

                    $this->redirect(array('admin','type_id'=>$type_id));
                        Yii::app()->user->setFlash('success', Yii::t('cms', 'Create new Game Successfully!'));

                }
            }
        }
        $this->render('create', array(
            "model" => $model,
            "rules" => $rules[$type_id],
            "type_id" => $type_id,
            "related" => $related[1],
            'isNew'=>true,
            "descendants" => $descendants,
            "node" => $node
        ));
    }
    /**
     * The function that do Manage User
     *
     */
    public function actionAdmin() {
        $type_id = $_GET['type_id'] ? $_GET['type_id'] : 44;
        $model = new Product;
        $model->search($type_id);
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET["Product"]))
                    $model->attributes=$_GET["Product"];  
        $this->render('admin', array(
            "model" => $model,'type_id'=>$type_id,
        ));
    }
    /**
     * The function that do View User
     *
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
        $model_name = "Game";
        $model = GxcHelpers::loadDetailModel($model_name, $id);
        $this->render('view', array(
            "model" => $model
        ));
    }
    /**
     * The function that do Update User
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;

        Yii::import("config.related");
        Yii::import("config.rules");
        $related = require(Yii::getPathOfAlias("config")."/related.php");
        $rules = require(Yii::getPathOfAlias("config")."/rules.php");

        if ($id !== 0) {
            $model = $this->loadModel($id);

            $type_id = Category::model()->getProductLevelTwo($model->category_id);
            $node = Category::model()->findByPk($type_id); 
            $descendants = $node->descendants()->findAll();
            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'userupdate-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            // collect user input data
            if (isset($_POST['Product'])) {
                $model->modified_uid = Yii::app()->user->id;
                if ($model->updateAttrs($_POST['Product'])) {
                    GameRelated::model()->updateRelated($_POST['Related'],$model->id);
                    GameRules::model()->updateRules($_POST['Rules'],$id);



                    Yii::app()->user->setFlash('success', Yii::t('cms', 'Updated Successfully!'));
                }
            }
            $game_related = GameRelated::model()->getGameRelated($model->id);
            $game_rules = GameRules::model()->getGameRules($model->id,$node->id);
        } else {
            throw new CHttpException(404, Yii::t('cms', 'The requested page does not exist.'));
        }
        $this->render('create', array(
            "model" => $model,
            "rules" => $rules[$type_id],
            "type_id" => $type_id,
            "related" => $related[1],
            'isNew'=>false,
            "descendants" => $descendants,
            "game_related" => $game_related,
            "game_rules" => $game_rules,
            "node" => $node,
        ));
    }
    /**
     * The function is to Delete a User
     *
     */
    public function actionDelete($id) {
        $model = $this->loadModel($id);
        if ($model->delete()) {
            $rules_lines = GameRules::model()->deleteAllByAttributes(array('product_id'=>$id));
            $related_lines = GameRelated::model()->deleteAllByAttributes(array('product_id'=>$id));
            if ($rules_lines > 0 && $related_lines > 0) {
                echo "删除成功！";
            }
        } else {
            echo "删除失败！";
        }
    }
}
