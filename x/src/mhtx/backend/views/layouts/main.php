<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/smoothness.pick.css" />

<script type="text/javascript">
		$(document).ready(function(){
				var loaded = true;
				var top = $("#srcoll").offset().top;
				function Add_Data()
				{              
						var scrolla=$(window).scrollTop();
						var cha=parseInt(top)-parseInt( scrolla);
						if(loaded && cha<=0)
						{                
								$("#srcoll").removeClass("tool2").addClass("tool1");
								loaded=false;
						}
						if(!loaded && cha>0)
						{
								$("#srcoll").removeClass("tool1").addClass("tool2");
								loaded=true;
						}
				}
				$(window).scroll(Add_Data);
		});
</script>
</head>
<body>
<div class="header">
	<div class="banner"></div>
    <div class="topbar">
    	<div class="date">今天是：<?php echo date("Y年m月d日");?></div>
        <div class="user"><?php if(!Yii::app()->user->id){
              $loginUrl = Yii::app()->createUrl("/site/login");
              echo "<a href='{$loginUrl}'>登录</a>";
        }else{
              $logoutUrl = Yii::app()->createUrl("/site/logout");
              echo "欢迎你:".Yii::app()->user->name."&nbsp;&nbsp;<a href='{$logoutUrl}'>退出</a>";
        }
        ?>
    </div>
</div>
<div class="main clearfix">
	<div class="left">
        <?php if(Yii::app()->user->id){
            $cpUrl = Yii::app()->createUrl("/cpuser/cplist");
            $cpAddUrl = Yii::app()->createUrl("/cpuser/addcp");
            $orderUrl = Yii::app()->createUrl("/order/index");
            echo "<dl class='menu'>
        	<dt>CP管理</dt>
            <dd><a href='{$cpUrl}'>CP列表</a></dd>
            <dd><a href='{$cpAddUrl}'>新增CP</a></dd>
            
        </dl>
                <dl class='menu'>
        	<dt>订单管理</dt>
            <dd><a href='{$orderUrl}'>订单列表</a></dd>
        </dl>
        ";
        }
    	?>
        </div>
    <div class="right">
    	
        <?php echo $content; ?>
  </div>
</div>
<div class="tool2" id="srcoll">相关操作：<a href="#">创建游戏</a> <a href="#">创建账号</a> <a href="#">管理游戏</a></div>
<div style="height:1000px;"></div>
</body>
</html>