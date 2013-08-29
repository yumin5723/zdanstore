var CANVAS_WIDTH = 250; //画布的宽
var CANVAS_HEIGHT = 250; //画布的高
var ICON_WIDTH = 180;  //截取框的宽
var ICON_HEIGHT = 180;  //截取框的高
var LEFT_EDGE = (CANVAS_WIDTH - ICON_WIDTH) / 2; 
var TOP_EDGE = (CANVAS_HEIGHT - ICON_HEIGHT) / 2; 

var scaleFactor;
var factor;
var minFactor;
var oldWidth;
var oldHeight;
$(window).load(function() {
run(0,0);
canvasImg();
});
function canvasImg(){
	$(".child").draggable({
      cursor: "move", containment: $("#bar"),
      drag: function(e, ui) {
      var left = parseInt($(this).css("left"));
          //前面讲过了y=factor（x），此处是知道x求y的值，即知道滑动条的位置，获取缩放率
          scaleFactor = Math.pow(factor, (left / 100 - 1));
          if (scaleFactor < minFactor) {
              scaleFactor = minFactor;
          }
          if (scaleFactor > factor) {
              scaleFactor = factor;
          }
          //以下代码同初始化过程，因为用到局部变量所以没有
          var iconElement = $("#ImageIcon");
          var imagedrag = $("#ImageDrag");

          var image = new Image();
          image.src = iconElement.attr("src");
          var realWidth = image.width;
          var realHeight = image.height;
          image = null;

          //图片实际尺寸
          var currentWidth = Math.round(scaleFactor * realWidth);
          var currentHeight = Math.round(scaleFactor * realHeight);

          //图片相对CANVAS的初始位置
          var originLeft = parseInt(iconElement.css("left"));
          var originTop = parseInt(iconElement.css("top"));

          originLeft -= Math.round((currentWidth - oldWidth) / 2);
          originTop -= Math.round((currentHeight - oldHeight) / 2);
          dragleft = originLeft - LEFT_EDGE;
          dragtop = originTop - TOP_EDGE;

          //图片当前尺寸和位置
          iconElement.css({ width: currentWidth + "px", height: currentHeight + "px", left: originLeft + "px", top: originTop + "px" });
          imagedrag.css({ width: currentWidth + "px", height: currentHeight + "px", left: dragleft + "px", top: dragtop + "px" });

        
		  valuewrite(originLeft,originTop,currentWidth,currentHeight);
		  valuewrite(dragleft,dragtop,currentWidth,currentHeight);
		  oldWidth = currentWidth;
		  oldHeight = currentHeight;

      }
  });
    var SilderSetValue = function(i) {
        var left = parseInt($(".child").css("left"));
        left += i;

        if (left < 0) {
            left = 0;
        }
        if (left > 200) {
            left = 200;
        }

        scaleFactor = Math.pow(factor, (left / 100 - 1));
        if (scaleFactor < minFactor) {
            scaleFactor = minFactor;
        }
        if (scaleFactor > factor) {
            scaleFactor = factor;
        }
        var iconElement = $("#ImageIcon");
        var imagedrag = $("#ImageDrag");

        var image = new Image();
        image.src = iconElement.attr("src");
        var realWidth = image.width;
        var realHeight = image.height;
        image = null;

        //图片实际尺寸
        var currentWidth = Math.round(scaleFactor * realWidth);
        var currentHeight = Math.round(scaleFactor * realHeight);

        //图片相对CANVAS的初始位置
        var originLeft = parseInt(iconElement.css("left"));
        var originTop = parseInt(iconElement.css("top"));

        originLeft -= Math.round((currentWidth - oldWidth) / 2);
        originTop -= Math.round((currentHeight - oldHeight) / 2);
        dragleft = originLeft - LEFT_EDGE;
        dragtop = originTop - TOP_EDGE;

        //图片当前尺寸和位置
        $(".child").css("left", left + "px");
        iconElement.css({ width: currentWidth + "px", height: currentHeight + "px", left: originLeft + "px", top: originTop + "px" });
        imagedrag.css({ width: currentWidth + "px", height: currentHeight + "px", left: dragleft + "px", top: dragtop + "px" });

        valuewrite(originLeft,originTop,currentWidth,currentHeight);
		valuewrite(dragleft,dragtop,currentWidth,currentHeight);
		oldWidth = currentWidth;
		oldHeight = currentHeight;
    }
    //点击加减号
    $("#moresmall").click(function() {
        SilderSetValue(-20);
    });
    $("#morebig").click(function() {
        SilderSetValue(20);
    });
}