jQuery(document).ready(function($) {
	//Select toggle
	$('#select-wizard-redirects, #select-wizard-import').change(function(e) {
		e.preventDefault();

		var select = $(this).val();

		if (select =='none') {
			$("#select-wizard-redirects option, #select-wizard-import option").each(function() {
				var ids_to_hide = $(this).val();
				$('#'+ids_to_hide).hide();
			});
		} else {
			$("#select-wizard-redirects option:selected, #select-wizard-import option:selected").each(function() {
				var ids_to_show = $(this).val();
				$('#'+ids_to_show).show();
			});
			$("#select-wizard-redirects option:not(:selected), #select-wizard-import option:not(:selected)").each(function() {
				var ids_to_hide = $(this).val();
				$('#'+ids_to_hide).hide();
			});
		}
	}).trigger('change');
	
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
					$( '#yoast-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
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
					$( '#aio-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
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
					$( '#seo-framework-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
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
					$( '#rk-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
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

	//Squirrly
	$('#seopress-squirrly-migrate').on('click', function(e5) {
		e5.preventDefault();
		self.process_offset5( 0, self );
	});
	process_offset5 = function( offset5, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_squirrly_migrate.seopress_squirrly_migration,
			data : {
				action: 'seopress_squirrly_migration',
				offset5: offset5,
				_ajax_nonce: seopressAjaxMigrate.seopress_squirrly_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset5 ) {
		        	$('#seopress-squirrly-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#squirrly-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
		        } else {
		        	self.process_offset5( parseInt( data.data.offset5 ), self );
		        }
			},
		});
	};

	$('#seopress-squirrly-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#squirrly-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#squirrly-migration-tool .spinner' ).css( "float", "none" );
		$( '#squirrly-migration-tool .log' ).html('');
	});

	//SEO Ultimate
	$('#seopress-seo-ultimate-migrate').on('click', function(e7) {
		e7.preventDefault();
		self.process_offset7( 0, self );
	});
	process_offset7 = function( offset7, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_seo_ultimate_migrate.seopress_seo_ultimate_migration,
			data : {
				action: 'seopress_seo_ultimate_migration',
				offset7: offset7,
				_ajax_nonce: seopressAjaxMigrate.seopress_seo_ultimate_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset7 ) {
		        	$('#seopress-seo-ultimate-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#seo-ultimate-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
		        } else {
		        	self.process_offset7( parseInt( data.data.offset7 ), self );
		        }
			},
		});
	};

	$('#seopress-seo-ultimate-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#seo-ultimate-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#seo-ultimate-migration-tool .spinner' ).css( "float", "none" );
		$( '#seo-ultimate-migration-tool .log' ).html('');
	});

	//WP Meta SEO
	$('#seopress-wp-meta-seo-migrate').on('click', function(e8) {
		e8.preventDefault();
		self.process_offset8( 0, self );
	});
	process_offset8 = function( offset8, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_wp_meta_seo_migrate.seopress_wp_meta_seo_migration,
			data : {
				action: 'seopress_wp_meta_seo_migration',
				offset8: offset8,
				_ajax_nonce: seopressAjaxMigrate.seopress_wp_meta_seo_migrate.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset8 ) {
					$('#seopress-wp-meta-seo-migrate').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#wp-meta-seo-migration-tool .log' ).html(seopressAjaxMigrate.i18n.migration);
				} else {
					self.process_offset8( parseInt( data.data.offset8 ), self );
				}
			},
		});
	};

	$('#seopress-wp-meta-seo-migrate').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#wp-meta-seo-migration-tool .spinner' ).css( "visibility", "visible" );
		$( '#wp-meta-seo-migration-tool .spinner' ).css( "float", "none" );
		$( '#wp-meta-seo-migration-tool .log' ).html('');
	});

	//Export metadata to CSV
	$('#seopress-metadata-export').on('click', function(e6) {
		e6.preventDefault();
		self.process_offset6( 0, self );
	});
	process_offset6 = function( offset6, self ) {
		$.ajax({
			method : 'POST',
			url : seopressAjaxMigrate.seopress_metadata_csv.seopress_metadata_export,
			data : {
				action: 'seopress_metadata_export',
				offset6: offset6,
				_ajax_nonce: seopressAjaxMigrate.seopress_metadata_csv.seopress_nonce,
			},
			success : function( data ) {
				if( 'done' == data.data.offset6 && data.data.url !='' ) {
		        	$('#seopress-metadata-export').removeAttr("disabled");
					$( '.spinner' ).css( "visibility", "hidden" );
					$( '#seopress-metadata-tool .log' ).html(seopressAjaxMigrate.i18n.export);
					$(location).attr('href',data.data.url);
		        } else {
		        	self.process_offset6( parseInt( data.data.offset6 ), self );
		        }
			},
		});
	};

	$('#seopress-metadata-export').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#seopress-metadata-tool .spinner' ).css( "visibility", "visible" );
		$( '#seopress-metadata-tool .spinner' ).css( "float", "none" );
		$( '#seopress-metadata-tool .log' ).html('');
	});
});