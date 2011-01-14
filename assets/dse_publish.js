$ = jQuery;
$(document).ready(function(){
	var section_handle = Symphony.Context.get('env')['section_handle'];
	$.getJSON(Symphony.Context.get('root') + '/symphony/extension/dynamic_section_editor/data/' + section_handle, function(data){
		if(data == false){
			return;
		}
		for(var i in data){
			DSE.addFilter(data[i]);
		}
	});
});