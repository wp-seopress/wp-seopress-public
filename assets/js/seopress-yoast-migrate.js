jQuery(document).ready(function(){
	//Yoast SEO
	jQuery('#seopress-yoast-migrate').on('click', function(e) {
		e.preventDefault();
		self.process_offset( 0, self );
	});
	process_offset = function( offset, self ) {
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_yoast_migrate.seopress_yoast_migration,
			data : {
				action: 'seopress_yoast_migration',
				offset: offset,
				_ajax_nonce: seopressAjaxMigrate.seopress_yoast_migrate.seopress_nonce,
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
	jQuery('#seopress-yoast-migrate').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '#yoast-migration-tool .spinner' ).css( "visibility", "visible" );
		jQuery( '#yoast-migration-tool .spinner' ).css( "float", "none" );
		jQuery( '#yoast-migration-tool .log' ).html('');
	});

	//All In One
	jQuery('#seopress-aio-migrate').on('click', function(e2) {
		e2.preventDefault();
		self.process_offset2( 0, self );
	});
	process_offset2 = function( offset2, self ) {
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_aio_migrate.seopress_aio_migration,
			data : {
				action: 'seopress_aio_migration',
				offset2: offset2,
				_ajax_nonce: seopressAjaxMigrate.seopress_aio_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset2 ) {
		        	jQuery('#seopress-aio-migrate').removeAttr("disabled");
					jQuery( '.spinner' ).css( "visibility", "hidden" );
					jQuery( '#aio-migration-tool .log' ).html('Migration completed!');
		        } else {
		        	self.process_offset2( parseInt( data.data.offset2 ), self );
		        }					
			},
		});
	};

	jQuery('#seopress-aio-migrate').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '#aio-migration-tool .spinner' ).css( "visibility", "visible" );
		jQuery( '#aio-migration-tool .spinner' ).css( "float", "none" );
		jQuery( '#aio-migration-tool .log' ).html('');
	});	

	//SEO Framework
	jQuery('#seopress-seo-framework-migrate').on('click', function(e3) {
		e3.preventDefault();
		self.process_offset3( 0, self );
	});
	process_offset3 = function( offset3, self ) {
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_seo_framework_migrate.seopress_seo_framework_migration,
			data : {
				action: 'seopress_seo_framework_migration',
				offset3: offset3,
				_ajax_nonce: seopressAjaxMigrate.seopress_seo_framework_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset3 ) {
		        	jQuery('#seopress-seo-framework-migrate').removeAttr("disabled");
					jQuery( '.spinner' ).css( "visibility", "hidden" );
					jQuery( '#seo-framework-migration-tool .log' ).html('Migration completed!');
		        } else {
		        	self.process_offset3( parseInt( data.data.offset3 ), self );
		        }					
			},
		});
	};

	jQuery('#seopress-seo-framework-migrate').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '#seo-framework-migration-tool .spinner' ).css( "visibility", "visible" );
		jQuery( '#seo-framework-migration-tool .spinner' ).css( "float", "none" );
		jQuery( '#seo-framework-migration-tool .log' ).html('');
	});
});