$ = jQuery;
$(document).ready(function(){
	$('div.dse_settings').hide();
	$('label.dse_dynamic input:checkbox:checked:visible').each(function(){
		$(this).parent().parent().find('div.dse_settings').show();
	});
	$('input[name="meta[dse_dynamic]"]').bind("change",function(){
		if($(this).is(':checked') == true){
			$('label.dse_dynamic').show().find('input').change();
		}
		else{
			$('label.dse_dynamic').hide().find('input').change();
		}
	}).change();
	$('label.dse_dynamic input').live("change",function(){
		if(($(this).is(':checked') == true) && $(this).is(':visible')){
			$(this).parent().parent().find('div.dse_settings::not(:visible)').slideDown("fast");
		}
		else{
			$(this).parent().parent().find('div.dse_settings:visible').slideUp("fast");
		}
	}).change();
});