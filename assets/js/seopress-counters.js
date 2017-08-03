//Title tag counters / live preview
jQuery(document).ready(function(){
	jQuery("#seopress_titles_title_counters").after("<div id=\"seopress_titles_title_counters_val\">/ 60</div>");
    jQuery("#seopress_titles_title_counters").text(jQuery("#seopress_titles_title_meta").val().length);
	if(jQuery('#seopress_titles_title_meta').val().length > 60){   
        jQuery('#seopress_titles_title_counters').css('color', 'red');
    }
    jQuery("#seopress_titles_title_meta").keyup(function(event) {
    	jQuery('#seopress_titles_title_counters').css('color', 'inherit');
     	if(jQuery(this).val().length > 60){
            jQuery('#seopress_titles_title_counters').css('color', 'red');
        }
     	jQuery("#seopress_titles_title_counters").text(jQuery("#seopress_titles_title_meta").val().length);
     	if(jQuery(this).val().length > 0){
     		jQuery(".snippet-title-custom").text(event.target.value);
            jQuery(".snippet-title").css('display', 'none');
            jQuery(".snippet-title-custom").css('display', 'block');
            jQuery(".snippet-title-default").css('display', 'none');
     	} else if(jQuery(this).val().length == 0) {
     		jQuery(".snippet-title-default").css('display', 'block');
            jQuery(".snippet-title-custom").css('display', 'none');
            jQuery(".snippet-title").css('display', 'none');
     	};
    });
});

//Meta description counters / live preview
jQuery(document).ready(function(){
	jQuery("#seopress_titles_desc_counters").after("<div id=\"seopress_titles_desc_counters_val\">/ 160</div>");
    jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").val().length);
	if(jQuery('#seopress_titles_desc_meta').val().length > 160){   
        jQuery('#seopress_titles_desc_counters').css('color', 'red');
    }
    jQuery("#seopress_titles_desc_meta").keyup(function(event) {
    	jQuery('#seopress_titles_desc_counters').css('color', 'inherit');
     	if(jQuery(this).val().length > 160){
            jQuery('#seopress_titles_desc_counters').css('color', 'red');
        }
     	jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").val().length);
        if(jQuery(this).val().length > 0){
            jQuery(".snippet-description-custom").text(event.target.value);
            jQuery(".snippet-description").css('display', 'none');
            jQuery(".snippet-description-custom").css('display', 'inline');
            jQuery(".snippet-description-default").css('display', 'none');
        } else if(jQuery(this).val().length == 0) {
            jQuery(".snippet-description-default").css('display', 'inline');
            jQuery(".snippet-description-custom").css('display', 'none');
            jQuery(".snippet-description").css('display', 'none');
        };
    });
});