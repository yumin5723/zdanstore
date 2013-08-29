<?php 
    $this->pageTitle=Yii::t('cms','创建新文章'); 
?>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/ckeditor/ckeditor.js"></script>
<div id="site-content">
            <?php $this->renderPartial('/object/nav'); ?>
        <div id="main-content-zone">
            <div class="page-content"> 
                    <?php               
                        $this->renderPartial('/layouts/notify',array());         
                    ?>                               
                    <div id="inner" style="width:100%;float:left;">
                        <h2><?php echo (isset($this->titleImage)&&($this->titleImage!=''))? '<img src="'. bu().'/'.$this->titleImage.'" />' : ''; ?><?php echo isset($this->pageTitle)? $this->pageTitle : '';  ?></h2>
                        <?php if (isset($this->pageHint)&&($this->pageHint!='')) : ?>
                            <p><?php echo $this->pageHint; ?></p>
                        <?php endif; ?>
                        <div class="form">
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'object-form',
                'enableAjaxValidation'=>false,       
                )); 
        ?>
        <?php echo $form->errorSummary($model); ?>
        <div class="form-wrapper">
            <div id="form-sidebar">
                <?php $this->renderPartial('/object/object_publish_sidebar_form',array("isNew"=>$isNew,'form'=>$form,'model'=>$model,'content_status'=>$content_status,'type'=>$type,'templetes'=>$templetes,'templete'=>$templete,"roots"=>$roots)); ?>
            </div>
            <div id="form-body">
                <div id="form-body-content">

                    <?php $this->renderPartial('/object/object_language_name_content',array('model'=>$model,'type'=>$type,'form'=>$form)); ?>

                    <div class="row">
                            <!-- //Render Partial for Resource Binding -->
                            <?php $this->renderPartial('/object/object_resource_form',array('model'=>$model,'type'=>$type,'content_resources'=>$content_resources)); ?>                     
                    </div>
                    <div class="row">
                    
                    <!--Start the Summary and SEO Box -->
                        <div class="content-box ">
                            <!-- //Render Partial for SEO -->
                            <?php $this->renderPartial('/object/object_seo_form',array('model'=>$model,'form'=>$form)); ?>
                        </div>
                    <!-- End Summary and SEO Box -->
                    </div>
                    

                </div>
            </div>
                        
        </div>
        <br class="clear" />
        <?php $this->endWidget(); ?>
</div><!-- form -->
<!-- //Render Partial for Javascript Stuff -->
<?php $this->renderPartial('/object/object_form_javascript',array('model'=>$model,'form'=>$form,'type'=>$type)); ?>

                    </div>
            </div>
        </div>
        
</div>

