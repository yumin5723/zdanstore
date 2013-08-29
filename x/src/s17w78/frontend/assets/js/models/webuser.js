define(["backbone","underscore","jquery"], function(Backbone, _, $) {
    'use strict';
    if (window.user) {
        return window.user;
    }

    var WebUser = Backbone.Model.extend({
        // set fetch immediately to true if model info need by follow actions
        urlRoot: '/user/info'
        ,fetch_immediately:false

        ,initialize: function(attributes, options) {
            _.bindAll(this, "fetch_userinfo", "getIsGuest");
            if (options && _.has(options, "fetch_immediately")) {
                this.fetch_immediately=options.fetch_immediately;
            }
            this.fetch_userinfo();
        }
        ,fetch_userinfo: function() {
            this.fetch({
                'async':!this.fetch_immediately
            });
        }
        ,getIsGuest: function(){
            if(this.get("isGuest")==undefined){
                return true;
            } else {
                return !!this.get("isGuest");
            }
        }
        ,logged_call: function(fn) {
            // for action need user logined in
            // if guest user, open login form modal
            if (!this.getIsGuest()) {
                fn();
            } else {
                // open login form modal
                require(['views/loginModal'], function(LoginModalView){
                    new LoginModalView();
                });
            }
        }
    });
    window.user = new WebUser({},{fetch_immediately:true});
    return window.user;
});
