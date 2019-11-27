jQuery(document).ready(function($) {
	//Yoast SEO
	$('#seopress-yoast-migrate').on('click', function(e) {
		e.preventDefault();
		self.process_offset( 0, self );
	});
	process_offset = function( offset, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_yoast_migrate.seopress_yoast_migration,
			data : {
				action: 'seopress_yoast_migration',
				offset: offset,
				_ajax_nonce: seopressAjaxMigrate.seopress_yoast_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset ) {
		        	$('#seopress-yoast-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#yoast-migration-tool .log' ).html(seopressAjaxMigrate.i18n);
		        } else {
		        	self.process_offset( parseInt( data.data.offset ), self );
		        }					
			},
		});
	};
	$('#seopress-yoast-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#yoast-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#yoast-migration-tool .spinner' ).css( "float", "none" );
		$( '#yoast-migration-tool .log' ).html('');
	});

	//All In One
	$('#seopress-aio-migrate').on('click', function(e2) {
		e2.preventDefault();
		self.process_offset2( 0, self );
	});
	process_offset2 = function( offset2, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_aio_migrate.seopress_aio_migration,
			data : {
				action: 'seopress_aio_migration',
				offset2: offset2,
				_ajax_nonce: seopressAjaxMigrate.seopress_aio_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset2 ) {
		        	$('#seopress-aio-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#aio-migration-tool .log' ).html(seopressAjaxMigrate.i18n);
		        } else {
		        	self.process_offset2( parseInt( data.data.offset2 ), self );
		        }					
			},
		});
	};

	$('#seopress-aio-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#aio-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#aio-migration-tool .spinner' ).css( "float", "none" );
		$( '#aio-migration-tool .log' ).html('');
	});	

	//SEO Framework
	$('#seopress-seo-framework-migrate').on('click', function(e3) {
		e3.preventDefault();
		self.process_offset3( 0, self );
	});
	process_offset3 = function( offset3, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_seo_framework_migrate.seopress_seo_framework_migration,
			data : {
				action: 'seopress_seo_framework_migration',
				offset3: offset3,
				_ajax_nonce: seopressAjaxMigrate.seopress_seo_framework_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset3 ) {
		        	$('#seopress-seo-framework-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#seo-framework-migration-tool .log' ).html(seopressAjaxMigrate.i18n);
		        } else {
		        	self.process_offset3( parseInt( data.data.offset3 ), self );
		        }					
			},
		});
	};

	$('#seopress-seo-framework-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#seo-framework-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#seo-framework-migration-tool .spinner' ).css( "float", "none" );
		$( '#seo-framework-migration-tool .log' ).html('');
	});

	//RK
	$('#seopress-rk-migrate').on('click', function(e4) {
		e4.preventDefault();
		self.process_offset4( 0, self );
	});
	process_offset4 = function( offset4, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_rk_migrate.seopress_rk_migration,
			data : {
				action: 'seopress_rk_migration',
				offset4: offset4,
				_ajax_nonce: seopressAjaxMigrate.seopress_rk_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset4 ) {
		        	$('#seopress-rk-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#rk-migration-tool .log' ).html(seopressAjaxMigrate.i18n);
		        } else {
		        	self.process_offset4( parseInt( data.data.offset4 ), self );
		        }					
			},
		});
	};

	$('#seopress-rk-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#rk-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#rk-migration-tool .spinner' ).css( "float", "none" );
		$( '#rk-migration-tool .log' ).html('');
	});
});