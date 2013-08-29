<?php
Yii::import("gcommon.cms.components.widgets.*");
class CmsWidgetFactory extends CWidget {
    protected static $widgets = array(
        1 => 'customBlock',
        2 => 'objectList',
        3 => 'gameList',
        4 => 'pictureList',
        'categorylist'=>'categorylist',
    );

    /**
     * return widget by widget type
     *
     * @param $widget_type:
     * @param array $params:
     *
     * @return
     */
    public static function factory($widget_type_id, array $params) {
        // get widget class
        if (!isset(self::$widgets[$widget_type_id])) {
            return null;
        }
        $classname = ucfirst(self::$widgets[$widget_type_id])."Widget";
        $widget = new $classname();
        foreach ($params as $name=>$value) {
            if (property_exists($widget,$name)) {
                $widget->$name = $value;
            }
        }
        $widget->init();
        return $widget;
    }


}