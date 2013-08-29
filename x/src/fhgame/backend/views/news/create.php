<?php 
    $this->pageTitle=Yii::t('cms','创建新文章'); 
?>
<script type="text/javascript" src="<?php echo Yii::app()->GcommonAssets->url;?>/js/ckeditor/ckeditor.js"></script>
<div id="site-content">
            <?php $this->renderPartial('/news/nav'); ?>
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
                <?php $this->renderPartial('/news/object_publish_sidebar_form',array("isNew"=>$isNew,'form'=>$form,'model'=>$model,'type'=>$type,'templetes'=>$templetes,'templete'=>$templete,"roots"=>$roots)); ?>
            </div>
            <div id="form-body">
                <div id="form-body-content">

                    <?php $this->renderPartial('/news/object_language_name_content',array('model'=>$model,'type'=>$type,'form'=>$form)); ?>

                    <div class="row">
                    
                    <!--Start the Summary and SEO Box -->
                        <div class="content-box ">
                            <!-- //Render Partial for SEO -->
                            <?php $this->renderPartial('/news/object_seo_form',array('model'=>$model,'form'=>$form)); ?>
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
<?php $this->renderPartial('/news/object_form_javascript',array('model'=>$model,'form'=>$form,'type'=>$type)); ?>

                    </div>
            </div>
        </div>
        
</div>

