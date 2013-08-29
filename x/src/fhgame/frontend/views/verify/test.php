<div class="form">
<?php echo CHtml::beginForm(); ?>
 
    <?php echo CHtml::errorSummary($model); ?>
 
    <div class="row">
        <?php echo CHtml::activeLabel($model,'user_name'); ?>
        <?php echo CHtml::activeTextField($model,'user_name') ?>
    </div>
 
    <div class="row">
        <?php echo CHtml::activeLabel($model,'user_pwd'); ?>
        <?php echo CHtml::activePasswordField($model,'user_pwd') ?>
    </div>
 
    <div class="row rememberMe">
        <?php echo CHtml::activeCheckBox($model,'ID_num'); ?>
        <?php echo CHtml::activeLabel($model,'ID_num'); ?>
    </div>
 
    <div class="row submit">
        <?php echo CHtml::submitButton('Login'); ?>
    </div>
 
<?php echo CHtml::endForm(); ?>
</div><!-- form -->