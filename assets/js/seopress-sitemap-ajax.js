jQuery(document).ready(function($) {
	$('#seopress-flush-permalinks,#seopress-flush-permalinks2').on('click', function() {
		$.ajax({
			method : 'GET',
			url : seopressAjaxResetPermalinks.seopress_ajax_permalinks,
			data: {
				action: 'seopress_flush_permalinks',
				_ajax_nonce: seopressAjaxResetPermalinks.seopress_nonce,
			},
			success : function( data ) {
				window.location.reload(true);
			},
		});
	});
	$('#seopress-flush-permalinks,#seopress-flush-permalinks2').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '.spinner' ).css( "visibility", "visible" );
		$( '.spinner' ).css( "float", "none" );
	});
});