jQuery(document).ready(function(){
	jQuery('#seopress-yoast-migrate').on('click', function() {
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxYoastMigrate.seopress_yoast_migration,
			_ajax_nonce: seopressAjaxYoastMigrate.seopress_nonce,
			data : {
				action: 'seopress_yoast_migration',
			},
			success : function( data ) {
				jQuery('#seopress-yoast-migrate').removeAttr("disabled");
				jQuery( '.spinner' ).css( "visibility", "hidden" );
				jQuery( '#yoast-migration-tool .log' ).html('Migration completed!');
			},
		});
	});
});
jQuery(document).ready(function(){
	jQuery('#seopress-yoast-migrate').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '.spinner' ).css( "visibility", "visible" );
		jQuery( '.spinner' ).css( "float", "none" );
		jQuery( '#yoast-migration-tool .log' ).html('');
	});
});