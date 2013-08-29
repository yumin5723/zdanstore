<?php
Yii::import("gcommon.cms.components.*");
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class PictureListWidget extends CmsWidget {


    public $picture = array();

    public $default_template = <<<EOF
    <ul>
    {% for obj in objs %}
      <li>{{ obj.imgname }}</li>
      <li><a href="{{ obj.imglink }}">{{ obj.imgname }}</a></li>
      <li><img src="{{ obj.url }}"></li>
      <li>{{ obj.time }}</li>
      <li>{{ obj.desc }}</li>
    {% endfor %}
    </ul>
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
     * render html use objects and template
     *
     *
     * @return
     */
    public function renderContent() {
        $template = $this->getTemplate();
        $viewRender = Yii::app()->getComponent("stringRender");
        if (empty($viewRender)) {
            throw new CmsException("ActiveWidget must use app 'stringRender'.");
        }
        return $viewRender->render($template,array("objs"=>$this->picture));
    }



}