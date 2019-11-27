jQuery(document).ready(function($) {
    $("#seopress-tabs .hidden").removeClass('hidden');
    $("#seopress-tabs").tabs();
    $('#seopress-tag-single-title').click(function() {
        $("#seopress_titles_title_meta").val($("#seopress_titles_title_meta").val() + ' ' + $('#seopress-tag-single-title').attr('data-tag'));
    });
    $('#seopress-tag-single-site-title').click(function() {
        $("#seopress_titles_title_meta").val($("#seopress_titles_title_meta").val() + ' ' + $('#seopress-tag-single-site-title').attr('data-tag'));
    });    
    $('#seopress-tag-single-excerpt').click(function() {
        $("#seopress_titles_desc_meta").val($("#seopress_titles_desc_meta").val() + ' ' + $('#seopress-tag-single-excerpt').attr('data-tag'));
    });
    $('#seopress-tag-single-sep').click(function() {
        $("#seopress_titles_title_meta").val($("#seopress_titles_title_meta").val() + ' ' + $('#seopress-tag-single-sep').attr('data-tag'));
    });    
});