$ = jQuery;
$(document).ready(function(){
	
	// For backwards compatibility, this nasty hack will be replaced with the Context object in symphony when 2.2 is the default Symphony version.
	var section_handle = window.location.href.match('publish/([^/]*)')[1];
	// Here too, see previous comment.
	$.getJSON(window.location.href.match('(.*)/symphony')[1] + '/symphony/extension/dynamic_section_editor/data/' + section_handle, function(data){
		if(data == false){
			return;
		}
		for(var i in data){
			DSE.addFilter(data[i]);
		}
	});
});