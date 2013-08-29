<?php
include_once(Yii::getPathOfAlias("gcommon.lib")."/simple_html_dom.php");
class CmsRenderer extends CApplicationComponent {
    public $widgetMap = array(
        'hotlist'=>'Hotlist',
        'contentmenu'=>'Contentmenu',
        'newslist'=>'Newslist',
        'topblock'=>'Topblock',
        'reclist'=>'Reclist',
        'relationlist' =>'Relationlist',
        'block'=>'Block',
    );

    /**
     * render view
     *
     * @param $view:
     * @param $data:
     *
     * @return string the rendering result
     */
    public function render($context, $view, $data=array()) {
        $html = str_get_html($view);
        $this->replace_seo($context, $html);
        $this->replace_values($context, $html);
        foreach ($html->find("[data-widget]") as $node) {
            foreach ($node->attr as $key => $val) {
                if ($key == "data-widget") {
                    continue;
                }
                if (strpos($key, "data-") === 0) {
                    $data[substr($key, 5)] = $val;
                }
            }
            $node->innertext=$this->createWidget($node->attr['data-widget'], $data)->run();
        }
        foreach($html->find("[data-block]") as $block_node){
            $block_id = $block_node->attr["data-block"];
            $block = Block::model()->findByPk($block_id);
            if ($block) {
                $block_node->innertext=$block->html;
            }
        }
        foreach($html->find("[categorylist]") as $node){
            $params['category_id'] = $context->id;
            $params += $data;
            $content = CmsWidgetFactory::factory("categorylist", $params)->run();
            $node->innertext = $content;
        }
        $result = $html->__toString();
        unset($html);
        gc_collect_cycles();
        return $result;
    }

    /**
     * replace values from context to view html
     *
     * @param $context:
     * @param $html:
     *
     * @return
     */
    protected function replace_values($context, $html) {
        foreach ($html->find("[data-value]") as $node) {
            $v_name = $node->attr['data-value'];
            $val = $context->$v_name;
            if (isset($node->attr['data-filter'])) {
                $val = $this->getFilter($node->attr['data-filter'])->filter($val);
            }
            if (isset($context->$v_name)) {
                $node->innertext=$val;
            }
        }
    }
    /**
     * replace values from context to page seo
     *
     * @param $context:
     * @param $html:
     *
     * @return
     */
    protected function replace_seo($context, $html) {
        $meta = $html->find("meta[name=keywords]");
        if(!empty($meta)){
            $meta[0]->content = $context->getKeywords();
        }
        $meta = $html->find("meta[name=description]");
        if(!empty($meta)){
            $meta[0]->content = $context->getDescription();
        }
        $title = $html->find("title");
        if(!empty($title)){
            $title = array_shift($title)->innertext = $context->getTitle();
        }
    }

    /**
     * function_description
     *
     * @param $widget_name:
     *
     * @return
     */
    protected function getWidgetClassname($widget_name) {
        if (!isset($this->widgetMap[$widget_name])) {
             return null;
        } else {
            return "gcommon.cms.components.widgets.".$this->widgetMap[$widget_name]."Widget";
        }
    }


    /**
     * create a widget and initializes it.
     *
     * @param $widget_name:
     * @param $properties:
     *
     * @return
     */
    protected function createWidget($widget_name, $properties=array()) {
        $className = $this->getWidgetClassname($widget_name);
        if (empty($className)) {
            throw new CException("Can not find class name for widget: ".$widget_name);
        }
        $className = Yii::import($className, true);
        $widget= new $className();
        foreach ($properties as $name=>$value) {
            if (isset($widget->$name)) {
                $widget->$name = $value;
            }
        }
        $widget->init();
        return $widget;
    }

    /**
     * function_description
     *
     * @param $filter_name:
     *
     * @return
     */
    protected function getFilter($filter_name) {
        $className = Yii::import("gcommon.cms.components.filters.".ucwords($filter_name)."Filter");
        $filter = new $className();
        $filter->init();
        return $filter;
    }



}
