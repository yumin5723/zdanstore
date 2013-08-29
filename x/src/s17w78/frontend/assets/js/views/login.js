define(["backbone","underscore","jquery","models/webuser","mustache","text!templates/userinfo.html"], function(Backbone, _, $, user, Mustache, userInfoTemplate) {
// define(["backbone","underscore","jquery","models/webuser","mustache",""], function(Backbone, _, $, user, Mustache, userInfoTemplate) {
//     'use strict';

    var Credentials = Backbone.Model.extend({});

    var LoginView = Backbone.View.extend({
        events: {
	        "click [data-bind='login']": "login"
        },

        initialize: function(){
            _.bindAll(this, "on_login_success", "login", "flush_vals", "clear_inputs");

            var self = this;
            self.model = new Credentials();
            
            //if user has logged in, show info
            if(user && !user.getIsGuest()){
                // show user info 
                $(self.el).html(Mustache.render(userInfoTemplate, {'user':user.attributes}));
                self.options.preloader.trigger("load_done", self.options.load_id);
                require(["views/fetchreward"], function(){});
                return;
            }
            if (self.options.load_id != undefined) {
                self.options.preloader.trigger("load_done", self.options.load_id);
            }

	        this.username = $('[data-bind="username"]', this.el);
	        this.password = $('[data-bind="password"]', this.el);
			this.captcha = $('[data-bind="captcha"]', this.el);
	        this.error_show = $('[data-bind="error_show"]', this.el);

            this.flush_vals();

            this.username.change(function(e){
                self.error_show.text("");
                self.model.set({username: self.username.val()});
            });

            this.password.change(function(e){
                self.error_show.text("");
                self.model.set({password: self.password.val()});
            });
			
			this.captcha.change(function(e){
                self.error_show.text("");
                self.model.set({captcha: self.captcha.val()});
            });
        }
        ,on_login_success: function(data) {
            //console.log(["Login request details: ", data]);
            if(!data.success) {  // If there is an error, show the error messages
		        this.error_show.text(data.message).show();
				if(data.data["needCaptcha"]){
					$(".captcha_div").show();
					$(".captchaImg").attr("src",data.data["captcha_url"]);
				}
            } else { // If not, send them back to the home page
                window.location.reload();
            }
        }
        ,flush_vals: function() {
            this.model.set({username: this.username.val(),
                            password: this.password.val(),
                            captcha: this.captcha.val()
                           });
        }
        ,clear_inputs: function() {
            this.password.val("");
            this.captcha.val("");
        }
        ,login: function(){
            this.flush_vals();
            if(this.model.get('username')==""){
                this.error_show.text("请输入用户名").show();
                this.username.focus();
                return false;
            }
            if(this.model.get('password')==""){
                this.error_show.text("请输入密码").show();
                this.password.focus();
                return false;
            }
            var self = this;
            var url = "/user/login";
            var formValues = {
                username: this.model.get('username'),
                password: this.model.get('password'),
				captcha : this.model.get('captcha')
            };
            this.clear_inputs();
            $.ajax({
                url:url,
                type:'POST',
                dataType:"json",
                data: formValues,
                success: this.on_login_success,
                error: function(data) {
                    self.error_show.text("连接超时，请重新尝试").show();
                }
            });
            return false;
        }
    });
	
    return LoginView;
});
