<?php
Yii::import("gcommon.cms.components.*");
Yii::import("gcommon.cms.components.widgets.CmsWidget");
Yii::import("gcommon.cms.models.Object");
Yii::import("gcommon.cms.models.Oterm");
class CategorylistWidget extends CmsWidget {
    public $category_id;

    public $count;

    public $page;
    public $offset = 0;
    public $default_count = Oterm::LIST_PAGE_DISPLAY_COUNT;
    public $default_template = <<<EOF
    <ul class="news_clist">
         {% for obj in data.objs %}
              <li class="clearfix">
                <a target="_blank" href="{{ obj.url }}">{{ obj.object_name }}</a> <span>[{{obj.object_date|date("m-d")}}]</span>
              </li>
         {% endfor %}
    </ul>
    <div class="page">
        <div>
            {{ data.p }}
        </div>
    </div>
EOF;

    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        return $this->renderContent();
    }

    /**
     * get objects for render
     *
     *
     * @return
     */
    protected function getObjects() {
        if (empty($this->category_id)) {
            throw new CmsException("ObjectListWidget can not find a category id");
        }

        $objects = Object::model()->fetchObjectsByTermId($this->category_id,$this->getCount(),$this->page);
        $sum = Object::model()->getObjectsCountByTermId($this->category_id);
        $sub_pages = 6;
        $url = "/list/{$this->category_id}_";
        $subPages=new SubPages($this->getCount(),$sum,$this->page,$sub_pages,$url,2);
        $p = $subPages->show_SubPages(2);
        return array("objs"=>$objects,"p"=>$p);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getTemplate() {
        if (!empty($this->block_content)) {
            return $this->block_content;
        }
        return $this->default_template;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getCount() {
        if (!empty($this->count) || $this->count != 0) {
            return $this->count;
        }
        return $this->default_count;
    }

    /**
     * render html use objects and template
     *
     *
     * @return
     */
    public function renderContent() {
        $data = $this->getObjects();
        $template = $this->getTemplate();
        $viewRender = Yii::app()->getComponent("stringRender");
        if (empty($viewRender)) {
            throw new CmsException("ObjectListWidget must use app 'stringRender'.");
        }
        return $viewRender->render($template,array("data"=>$data));
    }
}