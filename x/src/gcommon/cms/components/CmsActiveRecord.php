<?php

class CmsActiveRecord extends CActiveRecord {

    /**
     * function_description
     *
     *
     * @return
     */
    public function behaviors() {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
                'cms_type' => array(
                    'class' => 'gcommon.cms.components.CmsTypeBehavior',
                ),
                'cms_event' => array(
                    'class' => 'gcommon.cms.components.CmsEventBehavior',
                ),
                'cms_dependence' => array(
                    'class' => 'gcommon.cms.components.CmsDepBehavior',
                ),
            )
        );
    }


}