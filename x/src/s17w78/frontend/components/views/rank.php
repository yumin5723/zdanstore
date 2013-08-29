<div class="hotbox">
        <div class="title">
            <h3 class="bg2">
                领取排行
            </h3>
        </div>
        <div class="content">
            <ul class="rank_list clearfix">
                <?php foreach ($rank as $key=>$r):?>
                    <li class="clearfix">
                        <span class="num no<?php if($key + 1 >=5 ) echo 4;else echo $key + 1 ?>"><?php echo $key + 1 ?></span> <span class="name"><?php echo $r['name'] ?></span><span class="led"><a href="<?php echo Yii::app()->createUrl('libao/detail',array("id"=>$r['package_id'])) ?>" target="_blank"><img src="<?php echo Yii::app()->assets->Url ?>/images/card/led.png" height="19" align="absbottom"></a></span> <span class="led"><a href="<?php echo Yii::app()->createUrl('libao/detail',array("id"=>$r['package_id'])) ?>"  target="_blank"><img src="<?php echo Yii::app()->assets->Url ?>/images/card/tao.png"></a></span>
                    </li>
                <?php endforeach ?>
                
            </ul>
        </div>
    </div>