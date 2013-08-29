/* 加入收藏  -------------------------------------- */
function addFavorite(){
    var title = document.title;
    var url = window.location.href;
    var ctrl = (navigator.userAgent.toLowerCase()).indexOf('mac') != -1 ? 'Command/Cmd': 'CTRL';
    if (document.all) {
            window.external.addFavorite(url, title);
    } else if (window.sidebar) {
            window.sidebar.addPanel(title, url, "");
    } else {
            alert('您可以尝试通过快捷键' + ctrl + ' + D 加入到收藏夹~');
    }
    return false;
}

/* 首页tab切换 -------------------------------------- */
$(document).ready(function(){
    $("#sgame_tab li").click(function(){      
        $("#sgame_tab li").removeClass("cur");      
        $(this).addClass("cur");      
        var tab_content = $(this).attr("data-tab");     
        $(".sgame_tabc").hide();      
        $("#"+tab_content).show();    
    });     
    $("#andgame_tab li").click(function(){                  
        $("#andgame_tab li").removeClass("cur");                  
        $(this).addClass("cur");                    
        var tab_content = $(this).attr("data-tab");                   
        $(".andgame_tabc").hide();                  
        $("#"+tab_content).show();              
    });
    $("#appgame_tab li").click(function(){                  
        $("#appgame_tab li").removeClass("cur");                  
        $(this).addClass("cur");                    
        var tab_content = $(this).attr("data-tab");                   
        $(".appgame_tabc").hide();                  
        $("#"+tab_content).show();              
    });
});
/* 1378侧边栏工具 -------------------------------------- */
$('.toolbox').ready(function(){
    $(".tool_fav").click(function(){
        addFavorite();
    });
    $(".tool_gotop").click(function(){
        $("html, body").animate({ scrollTop: 0 }, 200);
    });
    var $backToTopFun = function() {
        var st = $(document).scrollTop(), winh = $(window).height();
        (st > 0)? $(".tool_gotop").css("display","block"): $(".tool_gotop").hide();    
        //IE6下的定位
        if ($.browser.version == 6) {
            $(".toolbox").css("top", st + winh - 100);    
        }
    };

    $(window).bind("scroll", $backToTopFun);
    $(function() { $backToTopFun(); });

});

/* 复制代码 */
function copy() {
    var s = $("#copytext").val();
    $("#copytext").select();
    if (window.clipboardData) {
        window.clipboardData.setData("Text", s);
        alert("已经复制！");
    } else{
        alert("当前浏览器不支持复制功能！\n请直接按ctrl+c复制。");
    }
}

/* 设为首页代码 */
function SetHome(url){
    if (document.all) {
        document.body.style.behavior='url(#default#homepage)';
        document.body.setHomePage(url);
    }else{
        alert("您好,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");
    }
}