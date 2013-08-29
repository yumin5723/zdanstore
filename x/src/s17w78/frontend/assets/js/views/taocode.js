define(["backbone","underscore","jquery", "models/webuser", "mustache","text!templates/tao.html",  "bootstrap", "bootstrapmodal"], function(Backbone, _, $, user, Mustache, taoTemplate) {
    	'use strict';
    	var taocodeView = Backbone.View.extend({
		events: {
		    'click [data-bind="taocode"]':"taocode"
		}
		,initialize: function() {
		    _.bindAll(this, "taocode", "tao_success", "taocode");
		}
		,taocode: function(e) {
			var self = this;
			var fn = function(){
				var id = e.currentTarget.getAttribute("data-id");
			    if (!id) {
				return false;
			    }
			    var url = "/libao/tao";
			    var formValues = {
					package_id:id
			    }
			    $.ajax({
				url:url,
				type:'POST',
				dataType:'json',
				data:formValues,
				success: self.tao_success,
				error: self.show_tao_error
			    });
			}
			user.logged_call(fn);
		}
		,tao_success: function(data) {
			if(data['codes'].length == 0){
				return new Backbone.BootstrapModal({ content:"暂时还没有可供淘的号!",title: "领取礼包",okText : "确认",allowCancel : false}).open();
			}
			var _code,taocodes = [];
            for( _code in data['codes']){
                taocodes.push({'code':data['codes'][_code][0], 'count':data['codes'][_code][1]});
            }
            var html = Mustache.render(taoTemplate, {"taocodes":taocodes});
            var modal = new Backbone.BootstrapModal({ content: html,allowCancel:false,title: "领取奖券",okText : "确认" }).open();
		}
		,show_tao_error: function() {
		    new Backbone.BootstrapModal({ content:"未能领取成功",title: "领取礼包",okText : "确认",allowCancel : false}).open();
		}
    });
    return taocodeView;
});
