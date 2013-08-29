var loginModalName = "templates/userLoginModal.html";
define(["backbone","underscore","jquery","models/webuser","mustache","text!"+loginModalName,"bootstrap", "bootstrapmodal"], function(Backbone, _, $, user, Mustache, userLoginModalTemplate) {
  //Set custom template settings
    var _interpolateBackup = _.templateSettings;
    _.templateSettings = {
        interpolate: /\{\{(.+?)\}\}/g,
        evaluate: /<%([\s\S]+?)%>/g
    }

    var template = _.template('\
    <% if (title) { %>\
      <div class="modal-header">\
          <a class="close">×</a>\
        <h3>{{title}}</h3>\
      </div>\
    <% } %>\
    <div class="modal-body">{{content}}</div>\
  ');

  //Reset to users' template settings
  _.templateSettings = _interpolateBackup;

    var LoginModalView = Backbone.View.extend({
        template: template,
        modal : null

        ,initialize: function() {
            _.bindAll(this, "open_modal");
            
            this.open_modal();
        }
        , open_modal: function() {
            var self = this;
		        var html = Mustache.render(userLoginModalTemplate, {});
		        if(this.modal == null){
              this.modal = new Backbone.BootstrapModal({ content: html,
                  title: "用户登录",
                  allowCancel : true,
                  template: self.template
                }); 
            }
            this.modal.open();
        		require(['views/login'], function(LoginView) {
        			new LoginView({'el':self.modal.$el.find('.modal-body')});
        		});
        }
    });

    return LoginModalView;
});
