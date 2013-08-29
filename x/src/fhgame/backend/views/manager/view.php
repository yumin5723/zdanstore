<?php 
$this->pageTitle=Yii::t('cms','User details');
?>
<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'username',
        'email',
        'created',
        'modified',
        'last_login_time', 
    ),
)); ?>
