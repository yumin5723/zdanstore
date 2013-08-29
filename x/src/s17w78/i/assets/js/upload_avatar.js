document.domain = "admin.131qz.com";
function AvatarUploader(container_id) {
    container_id = container_id || "profile_avatar";
    copy_properties(
        this, {
            containerId: container_id
        });
    AvatarUploader.instance = this;
    return this;
	
}

copy_properties(
    AvatarUploader, {
        toggleLoadingState: function() {
            //set indicator
            CSS.toggle(ge('profile_avatar_upload_indicator'));
            CSS.toggle(ge('profile_avatar_form'));
        },
        submitToIframe: function (formId) {
			ge(formId).submit();
            this.toggleLoadingState();
        }
    }
);

copy_properties(
    AvatarUploader.prototype, {
        onUploadComplete: function(pic_url,pic_type,path) {
            if (pic_url) {
                var avatar_img = ge(pic_type);
                if (avatar_img) {
                    avatar_img.src = pic_url;
                        $("#"+pic_type).attr("surl",path);                    
                }
                AvatarUploader.toggleLoadingState();
            } else avatar_upload_fail();
        }
    }
);

function avatar_upload_success(pic_url,pic_type,path) {
	AvatarUploader.instance.onUploadComplete(pic_url,pic_type,path);
}

function avatar_upload_fail() {
    
}
function addGameSubmit(){
   var list_pic = $("#list_pic").attr("surl");
   var recommend_pic = $("#recommend_pic").attr("surl");
   var subject_pic = $("#subject_pic").attr("surl");
   var focus_pic = $("#focus_pic").attr("surl");
   $("#admin_pic1").val(list_pic);
   $("#admin_pic2").val(recommend_pic);
   $("#admin_pic3").val(subject_pic);
   $("#admin_pic4").val(focus_pic);
   var introduction = $("#introduction").parent().find(".nicEdit-main").html();
   var feature = $("#feature").parent().find(".nicEdit-main").html();
   var active_desc = $("#active_desc").parent().find(".nicEdit-main").html();
   var gift_desc = $("#gift_desc").parent().find(".nicEdit-main").html();
   $("#introduction").val(introduction);
   $("#feature").val(feature);
   $("#active_desc").val(active_desc);
   $("#gift_desc").val(gift_desc);
   document.getElementById("addForm").submit();
}
function addPrizeSubmit(){
   var prize_pic = $("#prize_pic").attr("surl");
   $("#admin_pic").val(prize_pic);
   $("#description").val($(".nicEdit-main").html());
   var isnull = 1;
   $.each($(".level_name"),function(index){
		if($(".level_name").eq(index).val() == ""){
			alert("奖品等级不能为空且名字不能相同！");
			isnull = 0 ;
			return false;
		}   
	});
   if(isnull == 1){$("#addPrizeForm").submit();}
}
function addMedalSubmit(){
   var medal_pic = $("#medal_pic").attr("surl");
   $("#admin_pic").val(medal_pic);
   $("#addMedalForm").submit();
}
function addAdSubmit(){
   var prize_pic = $("#ad_pic").attr("surl");
   $("#admin_pic").val(prize_pic);
   $("#addAdForm").submit();
}
Arbiter.registerCallback(
    function () {
        new AvatarUploader();
}.bind(window),[OnloadEvent.ONLOAD_DOMCONTENT]);
