<?php
Yii::import('gcommon.cms.controllers.SpecialController');
class SubjectController extends SpecialController {
    public $sidebars = array(
    );
    public function init(){
        $pages = Page::model()->getSubjectPages('subject');
        $this->sidebars = $pages;
    }
}
