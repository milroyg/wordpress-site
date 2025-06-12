var edited_internal_setting;

jQuery(document).ready(function(){
	
	jQuery( "#dc_sortable" ).sortable({
		 update: function(event, ui) { 
			dc_submit_form();
		}
	});
	
	jQuery(".dc_checkbox").click(function() {
		if(jQuery(this).attr('checked')) {
			
			jQuery(this).parent().children(".dc_checkbox_hack").remove();
			
		} else {
		
			if(jQuery(this).parent().children(".dc_checkbox_hack").length == 0) {
				jQuery(this).parent().prepend('<input class="dc_checkbox_hack" type="hidden" name="defined_constants[internal_warning][]" value="0">');
			}
		
		}
	});
	
	jQuery('img.dc_delete').click(function(){
		jQuery(this).parent().parent().remove();
		dc_submit_form();	
	});
	
	jQuery('img.dc_delete_iw').click(function(){
		if(confirm("This is an internal setting, deleting or editing this setting could corrupt your website. Are you sure you want to delete this constant?")) {
			jQuery(this).parent().parent().remove();
			dc_submit_form();	
		}
	});
	
});

function dc_submit_form()
{
	var dataString = jQuery("#dc_defined_constants_form").serialize();	
	
	jQuery.ajax({
		type: "POST",
		url: "options.php",
		data: dataString
	});
}

