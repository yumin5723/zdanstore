//flash
$(document).ready(function(){
	var oLitimgScroll =  $("#litimgScroll");
	var aLitimgScrollLi =  $("#litimgScroll li");
	var iLength = aLitimgScrollLi.length;
	var aBigimgLi = $(".bigimg li");
	var oLbtn = $(".imginfo .lbtn");
	var oRbtn = $(".imginfo .rbtn");
	var iWidth = 155;
	var iTimer3 = null;
	var iNum2 = 0;
	var iNum3 = 0;   //当前hover位置
	oLitimgScroll.width(iLength * iWidth);

	function doFlashScroll(){
		clearTimeout(iTimer3);
		iNum2++;
		iNum3++;
		if(iNum2>=iLength || iNum3>=iLength){
			iNum2=0;
			iNum3=0;
		}
		
		if(iNum2>=(iLength-3)){
			aLitimgScrollLi.removeClass("hover").eq(iNum3).addClass("hover");
		}else{
			oLitimgScroll.animate({ 
				left:-iNum2*iWidth + "px"
			}, 100);
			aLitimgScrollLi.removeClass("hover").eq(iNum3).addClass("hover");
		}
		
		
		
		aBigimgLi.fadeOut().eq(iNum3).fadeIn(1000);
		
		iTimer3 =setTimeout(doFlashScroll,4000);
	}

	iTimer3 =setTimeout(doFlashScroll,5000);

	aLitimgScrollLi.each(function(i){
		aLitimgScrollLi.eq(i).hover(
			function(){
				iNum3=i;
				iNum2=i;
				clearTimeout(iTimer3)
				aLitimgScrollLi.removeClass("hover").eq(i).addClass("hover");
				aBigimgLi.fadeOut().eq(i).fadeIn(1000)
			},
			function(){
				iTimer3 =setTimeout(doFlashScroll,4000);
			}
		)
	});

	oLbtn.click(function(){
		clearTimeout(iTimer3);
		if(iNum3>3){
			iNum2--;
		}
		iNum3--;
		if(iNum2<0 || iNum3<0){
			iNum2=(iLength-4);
			iNum3=(iLength-1);
		}
		
		if(iNum2>=(iLength-3)){
			aLitimgScrollLi.removeClass("hover").eq(iNum3).addClass("hover");
		}else{
			oLitimgScroll.animate({ 
				left:-iNum2*iWidth + "px"
			}, 100);
			aLitimgScrollLi.removeClass("hover").eq(iNum3).addClass("hover");
		}
		
		aLitimgScrollLi.removeClass("hover").eq(iNum3).addClass("hover");
		aBigimgLi.fadeOut().eq(iNum3).fadeIn(1000)
	})


	oRbtn.click(function(){
		clearTimeout(iTimer3);
		doFlashScroll();
	})

	$(".wgame_box .tabs li").click(function(){
		$(".wgame_box .tabs li").removeClass("cur");
		$(this).addClass("cur");
		var curCon = $(this).attr("data-tab");
		$(".tabcon").hide();
		$("#"+curCon).show();
	});

	$("div.holder").jPages({
        containerID: "itemContainer",
        perPage :21
    });

     $('#scrollbar1').tinyscrollbar();
});