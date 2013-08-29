document.domain = "1378test.com";
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
        submitToIframe: function () {
            ge('form_profile_avatar_upload').submit();
            this.toggleLoadingState();
        }
    }
);

copy_properties(
    AvatarUploader.prototype, {
        onUploadComplete: function(pic_url) {
            if (pic_url) {
				var f1="<img src='"+ pic_url +"' id='ImageDrag'>";
				var f2="<img src='"+ pic_url +"' id='ImageIcon'>";
				$("#ImageDragContainer").html(f1);
				$("#IconContainer").html(f2);
				$("#bigImage").val(pic_url);
				//run(330,250);
				location.href = "http://ka.131.com/user/uploadavatar";
            } else avatar_upload_fail();
        }
    }
);

function avatar_upload_success(pic_url) {
    AvatarUploader.instance.onUploadComplete(pic_url);
}

function avatar_upload_fail() {
    
}

Arbiter.registerCallback(
    function () {
        new AvatarUploader();
}.bind(window),[OnloadEvent.ONLOAD_DOMCONTENT]);