jQuery(document).ready(function(){
	jQuery('#seopress-flush-permalinks,#seopress-flush-permalinks2').on('click', function() {
		jQuery.ajax({
			method : 'GET',
			url : seopressAjaxResetPermalinks.seopress_flush_permalinks,
			data: {
				action: 'seopress_flush_permalinks',
				_ajax_nonce: seopressAjaxResetPermalinks.seopress_nonce,
			},
			success : function( data ) {
				window.location.reload(true);
			},
		});
	});
});
jQuery(document).ready(function(){
	jQuery('#seopress-flush-permalinks,#seopress-flush-permalinks2').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '.spinner' ).css( "visibility", "visible" );
		jQuery( '.spinner' ).css( "float", "none" );
	});
});