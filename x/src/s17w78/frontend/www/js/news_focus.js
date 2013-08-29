/**
news img focus
**/
function BannerPic(){
    this.init();
}
BannerPic.prototype = {
    init : function(){
        var _this = this;
		$('.bannerImg li').removeClass('show').eq(0).addClass('show');
		$('.bannerIndex li').removeClass('hover').eq(0).addClass('hover');
		$('.bannerIndex div span').addClass('hide').eq(0).removeClass('hide');
        _this.event();
    },
    event : function(){
        var _this = this;
        _this.auto();
		var t;
        $('.bannerIndex li').hover(function(){
			var here = this;
            t = setTimeout(function(){
				clearInterval(_this.autoShow);
				var thisPic = $('.bannerIndex li.hover').index(),
					nextPic = $(here).index();
				_this.picActive(thisPic,nextPic);
				_this.auto();
			},200);
        },function(){
			clearTimeout(t);
		});
    },
    auto : function(){
        var _this = this;
        _this.autoShow = setInterval(function(){
			
            var thisPic = $('.bannerImg ul li.show').index();
            if(thisPic == $('.bannerImg ul li').length - 1){
                var nextPic = 0;
            }else{
                var nextPic = thisPic + 1;
            }
            _this.picActive(thisPic,nextPic);
        },3000);
    },
    picActive : function(thisPic,nextPic){
        $('.bannerImg ul li').eq(thisPic).fadeOut('slow').removeClass('show');
        $('.bannerIndex li').eq(thisPic).removeClass('hover');
        $('.bannerImg ul li').eq(nextPic).fadeIn('slow').addClass('show');
        $('.bannerIndex li').eq(nextPic).addClass('hover');
		$('.bannerIndex div span').eq(thisPic).addClass('hide');
		$('.bannerIndex div span').eq(nextPic).removeClass('hide');
    }
}
$('.news_focus').ready(function(){
    new BannerPic();
});