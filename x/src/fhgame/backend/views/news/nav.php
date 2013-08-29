<?php $this->renderPartial('/layouts/cmstop',array("menu_name"=>"新闻")); ?>
<div id="left-sidebar">
<?php
$this->widget('zii.widgets.CMenu',array(
  'encodeLabel'=>false,
  'activateItems'=>true,
  'activeCssClass'=>'list_active',
  'items'=>array(


    //Content Menu 
    array(
          'label'=>'<span id="menu_page" class="micon"></span>', 
          'url'=>'javascript:void(0);',
          'linkOptions'=>array('id'=>'menu_3','class'=>'menu_3'), 
          'itemOptions'=>array('id'=>'menu_3'),
          'items'=>array(
            array(
              'label'=>Yii::t('cms','管理新闻'), 
              'url'=>array("news/admin"),
              'active'=> ((Yii::app()->controller->id=='news') && (in_array(Yii::app()->controller->action->id,array('preview','view','admin','index')))) ? true : false
            ),
            array(
              'label'=>Yii::t('cms','新建新闻'), 
              'url'=>array("news/create"),
              'active'=> ((Yii::app()->controller->id=='news') && (in_array(Yii::app()->controller->action->id,array('create','update')))) ? true : false
            ),
          )
      ),
    ),
));
?>
</div>