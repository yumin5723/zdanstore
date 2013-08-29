//滚动图片构造函数
function $g(e)
{
   var targetObj = document.getElementById(e);
   return targetObj;
}
/*横向滚动*/
var sina = {
	$: function(objName) {
		if (document.getElementById) {
			return eval('document.getElementById("' + objName + '")')
		} else {
			return eval('document.all.' + objName)
		}
	},
	isIE: navigator.appVersion.indexOf("MSIE") != -1 ? true: false,
	addEvent: function(l, i, I) {
		if (l.attachEvent) {
			l.attachEvent("on" + i, I)
		} else {
			l.addEventListener(i, I, false)
		}
	},
	delEvent: function(l, i, I) {
		if (l.detachEvent) {
			l.detachEvent("on" + i, I)
		} else {
			l.removeEventListener(i, I, false)
		}
	},
	readCookie: function(O) {
		var o = "",
		l = O + "=";
		if (document.cookie.length > 0) {
			var i = document.cookie.indexOf(l);
			if (i != -1) {
				i += l.length;
				var I = document.cookie.indexOf(";", i);
				if (I == -1) I = document.cookie.length;
				o = unescape(document.cookie.substring(i, I))
			}
		};
		return o
	},
	writeCookie: function(i, l, o, c) {
		var O = "",
		I = "";
		if (o != null) {
			O = new Date((new Date).getTime() + o * 3600000);
			O = "; expires=" + O.toGMTString()
		};
		if (c != null) {
			I = ";domain=" + c
		};
		document.cookie = i + "=" + escape(l) + O + I
	},
	readStyle: function(I, l) {
		if (I.style[l]) {
			return I.style[l]
		} else if (I.currentStyle) {
			return I.currentStyle[l]
		} else if (document.defaultView && document.defaultView.getComputedStyle) {
			var i = document.defaultView.getComputedStyle(I, null);
			return i.getPropertyValue(l)
		} else {
			return null
		}
	}
};
function ScrollPic(scrollContId, arrLeftId, arrRightId) {
	this.scrollContId = scrollContId;
	this.arrLeftId = arrLeftId;
	this.arrRightId = arrRightId;
	this.pageWidth = 0;
	this.frameWidth = 0;
	this.speed = 10;
	this.space = 10;
	this.autoPlay = true;
	this.autoPlayTime = 5;
	var _autoTimeObj, _scrollTimeObj, _state = "ready", _picDownload = false;
	this.stripDiv = document.createElement("div");
	this.listDiv01 = document.createElement("ul");
	this.listDiv02 = document.createElement("ul");
	if (!ScrollPic.childs) {
		ScrollPic.childs = []
	};
	this.ID = ScrollPic.childs.length;
	ScrollPic.childs.push(this);
	this.initialize = function() {
		if (!this.scrollContId) {
			throw new Error("必须指定scrollContId.");
			return
		};
		this.scrollContDiv = $g(this.scrollContId);
		if (!this.scrollContDiv) {
			throw new Error("scrollContId不是正确的对象.(scrollContId = \"" + this.scrollContId + "\")");
			return
		};
		this.scrollContDiv.style.width = this.frameWidth + "px";
		this.scrollContDiv.style.overflow = "hidden";
		this.listDiv01.innerHTML = this.listDiv02.innerHTML = this.scrollContDiv.innerHTML;
		this.scrollContDiv.innerHTML = "";
		this.scrollContDiv.appendChild(this.stripDiv);
		this.stripDiv.appendChild(this.listDiv01);
		this.stripDiv.appendChild(this.listDiv02);
		this.stripDiv.style.overflow = "hidden";
		this.stripDiv.style.zoom = "1";
		this.stripDiv.style.width = "32766px";
		this.stripDiv.style.height = "144px";
		this.listDiv01.className = "ulleft";
		this.listDiv02.className = "ulleft";
		sina.addEvent(this.scrollContDiv, "mouseover", Function("ScrollPic.childs[" + this.ID + "].stop()"));
		sina.addEvent(this.scrollContDiv, "mouseout", Function("ScrollPic.childs[" + this.ID + "].play()"));
		if (this.arrLeftId) {
			this.arrLeftObj = $g(this.arrLeftId);
			if (this.arrLeftObj) {
				sina.addEvent(this.arrLeftObj, "mousedown", Function("ScrollPic.childs[" + this.ID + "].rightMouseDown()"));
				sina.addEvent(this.arrLeftObj, "mouseup", Function("ScrollPic.childs[" + this.ID + "].rightEnd()"));
				sina.addEvent(this.arrLeftObj, "mouseout", Function("ScrollPic.childs[" + this.ID + "].rightEnd()"))
			}
		};
		if (this.arrRightId) {
			this.arrRightObj = $g(this.arrRightId);
			if (this.arrRightObj) {
				sina.addEvent(this.arrRightObj, "mousedown", Function("ScrollPic.childs[" + this.ID + "].leftMouseDown()"));
				sina.addEvent(this.arrRightObj, "mouseup", Function("ScrollPic.childs[" + this.ID + "].leftEnd()"));
				sina.addEvent(this.arrRightObj, "mouseout", Function("ScrollPic.childs[" + this.ID + "].leftEnd()"))
			}
		};
		if (this.autoPlay) {
			this.play()
		}
	};
	this.leftMouseDown = function() {
		if (_state != "ready") {
			return
		};
		_state = "floating";
		if (!_picDownload){
			var arrImg=this.scrollContDiv.getElementsByTagName("IMG");
			for (var j=11;j<arrImg.length;j++){
				if (arrImg[j].name!=""){
					arrImg[j].src = arrImg[j].name;
				}
			}
			_picDownload = true;
		}
		_scrollTimeObj = setInterval("ScrollPic.childs[" + this.ID + "].moveLeft()", this.speed)
	};
	this.rightMouseDown = function() {
		if (_state != "ready") {
			return
		};
		_state = "floating";
		if (!_picDownload){
			var arrImg=this.scrollContDiv.getElementsByTagName("IMG");
			for (var j=11;j<arrImg.length;j++){
				if (arrImg[j].name!=""){
					arrImg[j].src = arrImg[j].name;
				}
			}
			_picDownload = true;
		}
		_scrollTimeObj = setInterval("ScrollPic.childs[" + this.ID + "].moveRight()", this.speed)
	};
	this.moveLeft = function() {
		//alert(this.scrollContDiv.scrollLeft)
		if (this.scrollContDiv.scrollLeft + this.space >= this.listDiv01.scrollWidth) {
			this.scrollContDiv.scrollLeft = this.scrollContDiv.scrollLeft + this.space - this.listDiv01.scrollWidth
		} else {
			this.scrollContDiv.scrollLeft += this.space
		};
	};
	this.moveRight = function() {
		if (this.scrollContDiv.scrollLeft - this.space <= 0) {
			this.scrollContDiv.scrollLeft = this.listDiv01.scrollWidth + this.scrollContDiv.scrollLeft - this.space
		} else {
			this.scrollContDiv.scrollLeft -= this.space
		};
	};
	this.leftEnd = function() {
		if (_state != "floating") {
			return
		};
		_state = "stoping";
		clearInterval(_scrollTimeObj);
		var fill = this.pageWidth - this.scrollContDiv.scrollLeft % this.pageWidth;
		this.move(fill)
	};
	this.rightEnd = function() {
		if (_state != "floating") {
			return
		};
		_state = "stoping";
		clearInterval(_scrollTimeObj);
		var fill = -this.scrollContDiv.scrollLeft % this.pageWidth;
		this.move(fill)
	};
	this.move = function(num, quick) {
		var thisMove = num / 5;
		if (!quick) {
			if (thisMove > this.space) {
				thisMove = this.space
			};
			if (thisMove < -this.space) {
				thisMove = -this.space
			}
		};
		if (Math.abs(thisMove) < 1 && thisMove != 0) {
			thisMove = thisMove >= 0 ? 1 : -1
		} else {
			thisMove = Math.round(thisMove)
		};
		var temp = this.scrollContDiv.scrollLeft + thisMove;
		if (thisMove > 0) {
			if (this.scrollContDiv.scrollLeft + thisMove >= this.listDiv01.scrollWidth) {
				this.scrollContDiv.scrollLeft = this.scrollContDiv.scrollLeft + thisMove - this.listDiv01.scrollWidth
			} else {
				this.scrollContDiv.scrollLeft += thisMove
			}
		} else {
			if (this.scrollContDiv.scrollLeft - thisMove <= 0) {
				this.scrollContDiv.scrollLeft = this.listDiv01.scrollWidth + this.scrollContDiv.scrollLeft - thisMove
			} else {
				this.scrollContDiv.scrollLeft += thisMove
			}
		};
		num -= thisMove;
		if (Math.abs(num) == 0) {
			_state = "ready";
			if (this.autoPlay) {
				this.play()
			};
			return
		} else {
			setTimeout("ScrollPic.childs[" + this.ID + "].move(" + num + "," + quick + ")", this.speed)
		}
	};
	this.next = function() {
		if (_state != "ready") {
			return
		};
		_state = "stoping";
		this.move(this.pageWidth, true)
	};
	this.play = function() {
		if (!this.autoPlay) {
			return
		};
		clearInterval(_autoTimeObj);
		_autoTimeObj = setInterval("ScrollPic.childs[" + this.ID + "].next()", this.autoPlayTime * 1000)
	};
	this.stop = function() {
		clearInterval(_autoTimeObj)
	};
	this.pageTo = function(num) {
		if (_state != "ready") {
			return
		};
		_state = "stoping";
		var fill = num * this.frameWidth - this.scrollContDiv.scrollLeft;
		this.move(fill, true)
	};
};