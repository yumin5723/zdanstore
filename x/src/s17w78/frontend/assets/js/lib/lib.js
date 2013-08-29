window.submitForm = function(url,data, method, isNewWindow){
	var newForm = jQuery("<form>", {
        action:url
	});
	if (method == undefined) {
		method = "POST"
	};
	newForm.attr("method", method);
	if (isNewWindow) {
		newForm.attr("target","_blank");
	}
    for (var key in data) {
        newForm.append(jQuery('<input>',{
        	'type':'hidden',
        	'name':key,
        	'value':data[key]
        }));
    }
    jQuery("body").append(newForm);
    newForm.submit();
}