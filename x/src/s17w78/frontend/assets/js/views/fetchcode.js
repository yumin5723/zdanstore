define(["backbone","underscore","jquery", "models/webuser", "mustache","text!templates/fetchcode.html",  "bootstrap", "bootstrapmodal"], function(Backbone, _, $, user, Mustache, fetchcodeTemplate) {
    	'use strict';
    	var fetchcodeView = Backbone.View.extend({
		events: {
		    'click [data-bind="fetchcode"]':"fetchcode"
		}
		,initialize: function() {
		    _.bindAll(this, "fetchcode", "fetch_success", "fetchcode");
		}
		,fetchcode: function(e) {
			var self = this;
			var fn = function(){
				var id = e.currentTarget.getAttribute("data-id");
			    if (!id) {
				return false;
			    }
			    var url = "/libao/fetch";
			    var formValues = {
					package_id:id
			    }
			    $.ajax({
				url:url,
				type:'POST',
				dataType:'json',
				data:formValues,
				success: self.fetch_success,
				error: self.show_error
			    });
			}
			user.logged_call(fn);
		}
		,fetch_success: function(data) {
			if(data['codes'].code == undefined){
				if(data['codes'].error == 5001){
					return new Backbone.BootstrapModal({ content:"礼包暂未开放领取!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
				if(data['codes'].error == 5002){
					return new Backbone.BootstrapModal({ content:"您已经领取礼包,请到<a href='http://i.1378.com/user/code'>存号箱</a>查看!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
				if(data['codes'].error == 5003){
					return new Backbone.BootstrapModal({ content:"您的邮箱还未激活!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
				if(data['codes'].error == 5004){
					return new Backbone.BootstrapModal({ content:"礼包被领光了!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
				if(data['codes'].error == 5005){
					return new Backbone.BootstrapModal({ content:"系统错误!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
				if(data['codes'].error == 5006){
					return new Backbone.BootstrapModal({ content:"您还未登录!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
				if(data['codes'].error == 5007){
					return new Backbone.BootstrapModal({ content:"领取失败!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
				}
			}else{
	            var html = Mustache.render(fetchcodeTemplate, {"codes":data['codes']});
	            var modal = new Backbone.BootstrapModal({ content: html,allowCancel:false,title: "领取奖券",okText : "确认" }).open();
	            modal.on('ok',function(){
	                
	            });
			}
		}
		,show_error: function() {
		    new Backbone.BootstrapModal({ content:"未能领取成功",title: "领取礼包",okText : "确认",allowCancel : false}).open();
		}
    });
    return fetchcodeView;
});
