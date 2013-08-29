jQuery(function () {
    // 1、首页焦点图效果 ---------------------------------------------
    var index = 0;
    //自动播放
    var MyTime;
    showbanner(index);
    playAuto();
    
	var slideCount = $('.bannerImg').children('li').length || 6;
    function playAuto() {
        MyTime = setInterval(function () {
            showbanner(index);
            index++;
            if (index >= slideCount) {
                index = 0;
            }
        }, 3000);
    }

    
    function showbanner(i) {
        $(".bannerImg li").stop().eq(i).animate({opacity:1}, 500).css({"z-index":"1"}).siblings().animate({opacity:0}, 500).css({"z-index":"0"});
        $(".bannerNum li").eq(i).addClass("cur").siblings().removeClass("cur");
    }

    $(".bannerNum li").hover(function () {
        if (MyTime) {
            clearInterval(MyTime);
        }
        index = $(".bannerNum li").index(this);
        MyTime = setTimeout(function () {
            showbanner(index);
            $(".bannerImg").stop();
        }, 100);

    }, function () {
        clearTimeout(MyTime);
        playAuto();
    });
    //滑入 停止动画，滑出开始动画.
    $(".bannerImg").hover(function () {
        if (MyTime) {
            clearInterval(MyTime);
        }
    }, function () {
        playAuto();
    });


    // 2、首页滚动代码 ---------------------------------------------
    var speed=40;
	var demo=$('#matchArea');
	var demo1=$('#matchScroll');
	var demo2=$('#matchScroll1');
	demo2.html(demo1.html());
	function gundong(){
	    var h = demo1.innerHeight();
	    if(demo.scrollTop()>=(h)){
	        demo.scrollTop(0);
	    }else{
	        demo.scrollTop(demo.scrollTop()+1);
	    }
	}
	var MyMar=setInterval(gundong,speed);
	demo.mouseover(function(){
	    clearInterval(MyMar);
	});
	demo.mouseleave(function(){
	    MyMar=setInterval(gundong,speed);
	});

	// 3、ie6下固定位置div ---------------------------------------------
	$.fn.extend({
	    ie6fixedbug: function () {
	        $(this).css("position", "absolute");
	        var m = (window.screen.height - $(this).height()) / 4;
	        var obj = $(this)[0];
	        window.onscroll = function () {
	            obj.style.top = (document.body.scrollTop || document.documentElement.scrollTop) + m + 'px';
	        }
	        window.onresize = function () {
	            obj.style.top = (document.body.scrollTop || document.documentElement.scrollTop) + m + 'px';
	        }
	    }
	});

	// 4、首页新闻切换
	$(".index_tab li").click(function(){
		$(".index_tab li").removeClass("cur");
		$(this).addClass("cur");
		$(".news_content").hide();
		var ctab = $(this).attr("data-tab");
		$("#"+ctab).show();
	});

	// 7、设置默认选中菜单
	if($("#setNav")){
		var curIndex = $("#setNav").val();
		if( curIndex != 0){
			preIndex = curIndex -1 ;
			$(".menu_content").find("li").eq(preIndex).addClass("nobg");
		}
		$(".menu_content").find("li").eq(curIndex).addClass("cur");
	}

	// 8、关闭浮动客服图层
	$(".qq_shut").click(function(){
		$(".qqbox").fadeOut(200);
	});

});

// 5、加入收藏代码
function addFavorite(){
	var ctrl = (navigator.userAgent.toLowerCase()).indexOf('mac') != -1 ? 'Command/Cmd': 'CTRL';
	if (document.all) {
			window.external.addFavorite('http://www.fhgame.com', '凤凰山庄');
	} else if (window.sidebar) {
			window.sidebar.addPanel('凤凰山庄', 'http://www.fhgame.com', "");
	} else {
			alert('您可以尝试通过快捷键' + ctrl + ' + D 加入到收藏夹~');
	}
	return false;
}

// 6、设为首页
function setHome(url){
	if (document.all) {
		document.body.style.behavior='url(#default#homepage)';
		   document.body.setHomePage(url);
	}else{
		alert("您好,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");
	}
}