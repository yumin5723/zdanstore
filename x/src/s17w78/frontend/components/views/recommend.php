<div class="hotbox">
    <div class="title">
        <h3 class="bg1">
            热门礼包
        </h3>
    </div>
    <div class="content clearfix">
    	<?php foreach ($bigRecommend as $ret):?>
	        <div class="hot_pic imgborder">
        	<a href="<?php echo Yii::app()->createUrl('libao/detail',array("id"=>$ret->id)) ?>" target="_blank">
            	<img src="<?php  echo $ret->recommend_image ?>" width="231">
        	</a>
        </div>
    	<?php endforeach ?>
        <?php foreach ($smallRecommend as $ret):?>
	        <div class="hot_game clearfix">
	            <div class="hot_game_l imgborder3">
	            	<a href="<?php echo Yii::app()->createUrl('libao/detail',array("id"=>$ret->id)) ?>" target="_blank">
	                	<img src="<?php echo $ret->recommend_image ?>" height="81" width="109">
	            	</a>
	            </div>
	            <div class="hot_game_r">
	                <span class="time">
	                    	<a href="<?php echo Yii::app()->createUrl('libao/detail',array("id"=>$ret->id)) ?>" target="_blank"><?php echo $ret->name ?></a>
	                	<br>
	                <font color="#FFA01A">运营平台:<?php echo $ret->related ?></font><br>
	                开放时间:<?php echo date("m-d",strtotime($ret->publish_date)) ?></span>
	            </div>
	        </div>
    	<?php endforeach ?>
    </div>
</div>