jQuery(document).ready(function(){
jQuery("#seopress_titles_desc_counters").after("<div id=\"seopress_titles_desc_counters_val\">/ 160</div>");
     jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").val().length);
     jQuery("#seopress_titles_desc_meta").keyup( function() {
     	if(jQuery(this).val().length > 160){
            jQuery('#seopress_titles_desc_counters_val').css('color', 'red');
        }
     	jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").val().length);
   });
});
