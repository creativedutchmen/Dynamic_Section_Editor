var DSE = {
	addFilter: function(data){
		$('#field-' + data.link_id).find("input, textarea, select").bind('keyup change', function(){
			if((($.inArray($(this).val(), data.value) != -1) && (data.show == "yes")) || (($.inArray($(this).val(), data.value) == -1) && (data.show != "yes"))){
				$('#field-' + data.id).show();
			}
			else{
				$('#field-' + data.id).hide();
			}
		}).change();
	}
};

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