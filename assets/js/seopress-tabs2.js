jQuery(document).ready(function($) {
	$("#seopress-tabs .hidden").removeClass('hidden');
	$("#seopress-tabs").tabs();

	function sp_get_field_length(e) {
		if (e.val().length > 0) {
			meta = e.val() + ' ';
		} else {
			meta = e.val();
		}
		return meta;
	}

	$('#seopress-tag-single-title').click(function() {
		$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $('#seopress-tag-single-title').attr('data-tag'));
	});
	$('#seopress-tag-single-site-title').click(function() {
		$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $('#seopress-tag-single-site-title').attr('data-tag'));
	});
	$('#seopress-tag-single-excerpt').click(function() {
		$("#seopress_titles_desc_meta").val(sp_get_field_length($("#seopress_titles_desc_meta")) + $('#seopress-tag-single-excerpt').attr('data-tag'));
	});
	$('#seopress-tag-single-sep').click(function() {
		$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $('#seopress-tag-single-sep').attr('data-tag'));
	});
});