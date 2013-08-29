// Require.js allows us to configure shortcut alias
require.config({
	// The shim config allows us to configure dependencies for
	// scripts that do not call define() to register a module
	shim: {
		'underscore': {
			exports: '_'
		}
	    ,'jquery': {
	        exports: '$'
	    }
		,'backbone': {
			deps: [
				'underscore'
				,'jquery'
			]
			,exports: 'Backbone'
		}
		,'lib':{
			deps:[
				'jquery'
			]
		}
	}
	,paths: {
		jquery: 'lib/jquery'
		,underscore: 'lib/underscore'
		,backbone: 'lib/backbone'
        ,bootstrap: 'lib/bootstrap'
        ,bootstrapmodal: 'lib/backbone.bootstrap-modal'
        ,text: 'lib/text'
        ,mustache: 'lib/mustache'
        ,lib:'lib/lib'
	}
});

require(['jquery', 'underscore', 'backbone'],function($){
    // if there is no element need to pre load, show body
    if($("[data-preload='true']").length == 0){
        $('body').show();
    }

    // force something load before body show
    var preloader = {
        loaders: []
    };
    _.extend(preloader, Backbone.Events);
    preloader.on("load_done", function(id) {
        preloader.loaders.pop(id);
        if (preloader.loaders.length == 0) {
            $('body').show();
        }
    });
    load_id = 0;

    _.each($("[data-view]"), function(role_el){
	    role = role_el.getAttribute('data-view');
	    if(role!=""){
	        roleView = "views/"+role;
            load_id++;
            if(role_el.getAttribute("data-preload")){
                preloader.loaders.push(load_id);
            }
	        require([roleView], function(RoleView) {
		        r = new RoleView({'el':role_el,'load_id':load_id,'preloader':preloader});
	        });
	    }
    });
	$(document).ready(function(e) {
		require(["models/webuser"], function(user) {
		if(user.getIsGuest() && $("body")[0].getAttribute("data-login-required")=="true"){
			require(['views/surveyloginModal'], function(LoginModalView){
				new LoginModalView();
			});
		}});
		$('input[type=password]').bind('copy paste', function (e) {
			e.preventDefault();
        });
	});
});

