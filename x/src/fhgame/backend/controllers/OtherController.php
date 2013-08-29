<?php

class OtherController extends PageController {
    public $sidebars = array(
        array(
            'name' => '页面列表',
            'icon' => 'user',
            'url' => 'admin',
        ),
        array(
            'name' => '新建页面',
            'icon' => 'user',
            'url' => 'create',
        ),
    );
    /**
     * push rar file
     */
    public function actionPublish($id) {
        $page = $this->loadModel($id);
        if($page->doPublish()){
            Yii::app()->user->setFlash('success', '发布成功！');
        }else{
            Yii::app()->user->setFlash('success', '发布失败请重新尝试!');
        }
        $this->redirect("/other/admin");
    }
}
