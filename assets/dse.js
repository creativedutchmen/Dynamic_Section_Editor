var DSE = {
	addFilter: function(data){
		$('#field-' + data.link_id).find("input, textarea, select").bind('keyup change', function(){
			if($(this).is(":checkbox")){
				if($(this).is(":checked")){
					$(this).val("yes");
				}
				else{
					$(this).val("no");
				}
			}
			
			if((($.inArray($(this).val(), data.value) != -1) && (data.show == "yes")) || (($.inArray($(this).val(), data.value) == -1) && (data.show != "yes"))){
				$('#field-' + data.id).show();
			}
			else{
				$('#field-' + data.id).hide();
			}
		}).change();
	}
};