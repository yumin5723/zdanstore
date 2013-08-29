<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class RelationlistWidget extends CmsWidget {
    /**
     * category id
     *
     */
    public $count = 5;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        if (empty($this->id)) {
            return "";
        }
        return $this->getRelationlist();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getRelationlist() {
        // $category = ObjectTerm::model()->findByAttributes(array("object_id"=>$this->id));
        $lists = Object::model()->getRelationList($this->id,$this->count);
        $html ='<h3>相关阅读</h3>';
        foreach($lists as $list){
            $date = date('Y.m.d',strtotime($list->object_date));
            $url = $list->url;
            $html .= "<li><a href='$url' target='_blank' class='ablue'>$list->object_title<span>$date</span></a></li>";
        }
        return $html;
    }
}