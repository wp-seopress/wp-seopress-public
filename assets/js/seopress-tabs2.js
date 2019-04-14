jQuery(document).ready(function($) {
    $("#seopress-tabs .hidden").removeClass('hidden');
    $("#seopress-tabs").tabs();
    jQuery('#seopress-tag-single-title').click(function() {
        jQuery("#seopress_titles_title_meta").val(jQuery("#seopress_titles_title_meta").val() + ' ' + jQuery('#seopress-tag-single-title').attr('data-tag'));
    });
    jQuery('#seopress-tag-single-site-title').click(function() {
        jQuery("#seopress_titles_title_meta").val(jQuery("#seopress_titles_title_meta").val() + ' ' + jQuery('#seopress-tag-single-site-title').attr('data-tag'));
    });    
    jQuery('#seopress-tag-single-excerpt').click(function() {
        jQuery("#seopress_titles_desc_meta").val(jQuery("#seopress_titles_desc_meta").val() + ' ' + jQuery('#seopress-tag-single-excerpt').attr('data-tag'));
    });
    jQuery('#seopress-tag-single-sep').click(function() {
        jQuery("#seopress_titles_title_meta").val(jQuery("#seopress_titles_title_meta").val() + ' ' + jQuery('#seopress-tag-single-sep').attr('data-tag'));
    });    
});