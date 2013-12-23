// 下拉菜单js
var timeout         = 100;
var closetimer		= 0;
var ddmenuitem      = 0;
var cart_show       = 0;
function jsddm_open()
{	jsddm_canceltimer();
	jsddm_close();
	ddmenuitem = $(this).parent().find('.navbox').css('visibility', 'visible');}

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
	$('.menu li a.m').bind('mouseover', jsddm_open);
	$('.menu li a.m').bind('mouseout',  jsddm_timer);
	$('.menu .navbox').bind('mouseover', jsddm_open);
	$('.menu .navbox').bind('mouseout',  jsddm_timer);
	$('.hnav .i3').bind('mouseover',  show_cartdi);
	$(".email_ipt").focus(function(){
		$(this).val("");
	})
});
function show_cartdi(){
	if(cart_show == 0){
		$.ajax({
	        type: "POST",
	        // contentType:"application/json",
	        url:"/shopping/cartshow",
	        data:{},
	        dataType:'json',
	        success:function(data){     
	            $("#cart_back").html(data);
	            $(".cart_dialog").show();
	            cart_show = 1;
	        }
	    });
	}
}
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

/* dialog code */
	function popup(){
	    var _scrollHeight = $(document).scrollTop(),//获取当前窗口距离页面顶部高度
	    _windowHeight = $(window).height(),//获取当前窗口高度
	    _windowWidth = $(window).width(),//获取当前窗口宽度
	    _popupHeight = $("#popup").height(),//获取弹出层高度
	    _popupWeight = $("#popup").width();//获取弹出层宽度
	    _posiTop = (_windowHeight - _popupHeight)/2 + _scrollHeight;
	    _posiLeft = (_windowWidth - _popupWeight)/2;
	    _documentHeight = $(document).height();
	    $(".popmark").css({"height":_documentHeight});
	    $(".popmark").fadeIn();
	    $("#popup").css({"left": _posiLeft + "px","top":_posiTop + "px"});//设置position
	    $("#popup").fadeIn();
	}

	function closePopup(){
		$("#popup").fadeOut();
		$(".popmark").fadeOut();
	}