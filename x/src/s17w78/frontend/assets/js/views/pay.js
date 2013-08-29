define(["backbone","underscore","jquery","models/webuser", "mustache","text!templates/payconfirm.html",  "bootstrap", "bootstrapmodal","lib"], function(Backbone, _, $, user, Mustache, payconfrimTemplate) {
    'use strict';
    var payView = Backbone.View.extend({
    events: {
        'click [data-bind="pay"]':"show_pay_modal"
    }
    ,initialize: function() {
        _.bindAll(this, "show_pay_modal","pay");
    }
    ,show_pay_modal: function(e) {
        var self = this;
        var fn = function() {
            var goods = [];
            var app_name = "1378点券";
            var account = $("#my_account").html();
            var amount = $(".amount_box").find("input:checked").val();
            if(amount == "other"){
                amount = $("#to_amount").val();
            }
            if(amount == "" || amount == 0){
                $("#tip_amount").html("请输入正确的金额！");
                return false;
            }
            var paymethod = $("input:radio[name='Order[pay_method]']:checked").parent().find("img").attr("src");
            goods.push({'account':account, 'amount':amount,'paymethod':paymethod,'app_name':app_name});
            var html = Mustache.render(payconfrimTemplate, {"goods":goods});
            var modal = new Backbone.BootstrapModal({ content: html,title: "充值",okText : "确认充值",cancelText :"取消" });
            var paymethod_value = $("input:radio[name='Order[pay_method]']:checked").val();
            if (paymethod_value == "telecom" || paymethod_value == "unicom" || paymethod_value == "szx"){
                var card_no = $("#card_no").val();
                var card_pwd = $("#card_pwd").val();
                if( card_no == ""){
                    $("#tip_card_no").html("充值卡号不能为空");
                    return false;
                }
                if( card_pwd == ""){
                    $("#tip_card_pwd").html("充值卡密不能为空");
                    return false;
                }
                var formValues = {
                    "Order[amount]":amount,
                    "Order[card_type]":paymethod_value,
                    "Order[card_no]":card_no,
                    "Order[card_pwd]":card_pwd
                };
                modal.on("ok", function(){
                    submitForm("/pay/card",formValues,"POST");
                    // self.fetch_success();
                });
            } else {
                var formValues = {
                    "Order[amount]":amount,
                    "Order[pay_method]":paymethod_value,
                    "user_id":account
                };
                modal.on("ok", function(){
                    submitForm("/pay/index",formValues,"POST",true);
                    self.fetch_success();
                });
            }
            modal.open();
        };
        if($("#tip_amount").html()!="" || $("#tip_agreement").html()!=""){
            alert("您填写的信息有错误！");
        }else{
            fn();
        }
    }
    ,pay: function(e) {
        var account = $("#to_account").val();
        var amount = $(".amount_box").find("input:checked").val();
        if(amount == "other"){
            amount = $("#to_amount").val();
        }
        var paymethod = $("input:radio[name='Order[pay_method]']:checked").val();
        if (!account) {
            return false;
        }
        var url = "/pay/index";
        var formValues = {
            "Order":{
                "amount":amount,
                "pay_method":paymethod
            },
            "user_id":account
        };
        $.ajax({
            url:url,
            type:'POST',
            data:formValues,
            success: this.fetch_success,
            error: this.show_error
        });
    }
    ,fetch_success:function(data){
        $(".modal-backdrop").remove();
        $(".modal").remove();
        $(".pay_callback").html(data);
        var html = "<div class='pay_tip'><p class='warning'>请您在新打开的网上银行页面进行支付！支付完成前请不要关闭该窗口。<br><a href='/pay/list' class='pay_abtn'>支付已经完成</a> <a href='/pay/help' class='pay_abtn'>支付遇到问题</a></p></div>";
        new Backbone.BootstrapModal({ content:html,title: "充值",okText : "确认",allowCancel : false}).open();
    }
    ,show_error: function() {
        new Backbone.BootstrapModal({ content:"对不起，支付遇到问题",title: "充值",okText : "确认",allowCancel : false}).open();
    }
    });
    return payView;
});
