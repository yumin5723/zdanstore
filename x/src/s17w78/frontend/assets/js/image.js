var suningImages = function(){
	var box = $('#bigpics');
	var image = $('#pics');
	var btn = image.find('li');
	var len = btn.length ;
	var ul = image.find('ul');
	var liwidth = 90;
	
	return{
		init:function(){
			var that = this ;
			var posx ;
			var posy ;
			var i = 0 ;
			ul.css('width',len*liwidth);
			image.prev('div').click(function(e){
				//alert($(this));
				if(i<=0){
					return false;
				}
				i--;
				that.scroll(i);
				e.preventDefault();
			})
			
			image.next('div').click(function(e){
				if(i>= parseInt(len/4) || len<=4 ){
					return false;
				}
				i++;	
				that.scroll(i);
				e.preventDefault();
			})
			
			
			btn.each(function(i){
				$(this).find('a').click(function(e){
					index = i ;							 
					that.addbk(i);
					that.loadimg(i);
					e.preventDefault();
				})
			})
			
			
			
			
		},
		loadimg:function(i){
			box.html('<div class="loading"></div>');
			var src = btn.eq(i).find('img').attr('src');
			var maxlen = src.length ;
			newsrc = src.slice(0,maxlen-4)+".jpg";
			box.html('<img src = ' +newsrc+'  />' ).find('img').hide();
			box.find('img').fadeIn(250);
		},
		addbk:function(i){
			btn.eq(i).find('a').addClass('on').parent().siblings().find('a').removeClass('on');
		},
		scroll:function(i){
			ul.stop().animate({marginLeft: -liwidth*4*i },300);
		},
		next:function(index){
			var that = this ;
			if(((index)%4)==0){
				ul.stop().animate({marginLeft: -liwidth*(index) },300);
			}
			that.addbk(index);
			setTimeout(function(){that.loadimg(index);},400);
		},
		prev:function(index){
			var that = this ;
			if((index+1)%4==0){
				ul.stop().animate({marginLeft: -liwidth*parseInt(index/4)*4 },300);
			}
			that.addbk(index);
			setTimeout(function(){that.loadimg(index);},400);
		}
	}
}
$(document).ready(function(){
	suningImages().init();	
})
