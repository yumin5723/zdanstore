define(["backbone","underscore","jquery","models/webuser"], function(Backbone, _, $, user) {
    'use strict';

    var loginButtonView = Backbone.View.extend({
        events: {
	        "click": "login_load"
        },

        login_load: function(){
        	var fn = function(e) {
        		window.loaction = e.currentTarget.getAttribute("href");
        	};

        	user.logged_call(fn);
        	return false;
        }
    });

    return loginButtonView;

});


