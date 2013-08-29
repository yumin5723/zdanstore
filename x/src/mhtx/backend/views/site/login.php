<?php $this->layout=false; ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
 <head>
 <meta charset="UTF-8">
 <title>卡趣管理后台</title>
 </head>
 <body>
 <div class="login_l"></div>
 <div class="login_r">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'login-form',
 'enableAjaxValidation'=>false,
 )); ?>

<span>邮箱：
<?php echo $form->textField($model,'username',array('class'=>'login_input','size'=>'28')); ?>
<?php echo $form->error($model,'username'); ?>
</span>

<span>密码：
<?php echo $form->passwordField($model,'password',array('class'=>'login_input','size'=>'28')); ?>
<?php echo $form->error($model,'password'); ?>
</span>

<span><?php echo CHtml::submitButton('登录',array('class'=>'login_btn')); ?></span>
<?php $this->endWidget(); ?>
</div>
</body>
</html>