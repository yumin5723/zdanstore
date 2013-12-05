// 下拉菜单js
var timeout         = 500;
var closetimer		= 0;
var ddmenuitem      = 0;
function jsddm_open()
{	jsddm_canceltimer();
	jsddm_close();
	ddmenuitem = $(this).find('.navbox').css('visibility', 'visible');}

function jsddm_close()
{	if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');}

function jsddm_timer()
{	closetimer = window.setTimeout(jsddm_close, timeout);}

function jsddm_canceltimer()
{	if(closetimer)
	{	window.clearTimeout(closetimer);
		closetimer = null;
	}
}
$(document).ready(function()
{	
	$('.menu > li').bind('mouseover', jsddm_open);
	$('.menu > li').bind('mouseout',  jsddm_timer);
});
document.onclick = jsddm_close;

// 首页焦点图效果
$(document).ready(function()
{
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
});