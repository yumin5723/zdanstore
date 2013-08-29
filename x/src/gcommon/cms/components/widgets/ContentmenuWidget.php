<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class ContentmenuWidget extends CmsWidget {

    public $listmenu = "";
    public $categoryid = 0;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        return $this->getContentMenu();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getContentMenu() {
        if($this->listmenu != ""){
            $categories = Oterm::model()->getLevelByTermId($this->categoryid);
        }else{
            $categories = Object::model()->getObjectTermById($this->id);
        }
        $html = '当前位置：<a class="ablue" href="/">1378棋牌</a> &gt;';
        if(empty($categories)){
            return "";
        }
        foreach($categories as $key=>$category){
            $name = $category['term_name'];
            $url = $category['url'];
            $html .= " <a class='blue' href='{$url}'>$name</a> &gt;";
        }
        if($this->listmenu == ""){
            $html .= '正文';
        }
        return $html;
    }


}