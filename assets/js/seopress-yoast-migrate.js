jQuery(document).ready(function(){
	jQuery('#seopress-yoast-migrate').on('click', function(e) {
		e.preventDefault();
		self.process_offset( 0, self );
	});
	process_offset = function( offset, self ) {
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxYoastMigrate.seopress_yoast_migration,
			data : {
				action: 'seopress_yoast_migration',
				offset: offset,
				_ajax_nonce: seopressAjaxYoastMigrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset ) {
		        	jQuery('#seopress-yoast-migrate').removeAttr("disabled");
					jQuery( '.spinner' ).css( "visibility", "hidden" );
					jQuery( '#yoast-migration-tool .log' ).html('Migration completed!');
		        } else {
		        	self.process_offset( parseInt( data.data.offset ), self );
		        }					
			},
		});
	};
});
jQuery(document).ready(function(){
	jQuery('#seopress-yoast-migrate').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '.spinner' ).css( "visibility", "visible" );
		jQuery( '.spinner' ).css( "float", "none" );
		jQuery( '#yoast-migration-tool .log' ).html('');
	});
});