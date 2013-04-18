jQuery(document).ready(function(){
	
	if (jQuery('.image_background_colourpicker').length > 0) {
		jQuery('.image_background_colourpicker').hide();
	    jQuery('.image_background_colourpicker').farbtastic(".image_background_colour");
	    jQuery(".image_background_colour").click(function(){jQuery('.image_background_colourpicker').slideToggle()});	
	}
	
	if (jQuery('.image_border_colourpicker').length > 0) {
		jQuery('.image_border_colourpicker').hide();
	    jQuery('.image_border_colourpicker').farbtastic(".image_border_colour");
	    jQuery(".image_border_colour").click(function(){jQuery('.image_border_colourpicker').slideToggle()});	
	}
	
});