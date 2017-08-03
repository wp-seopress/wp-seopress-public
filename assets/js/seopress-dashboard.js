jQuery(document).ready(function(){  
	//Titles
	jQuery('#toggle-titles').on('click', function() {
		jQuery('#toggle-titles').attr('data-toggle', jQuery('#toggle-titles').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-titles',
				feature_value: jQuery('#toggle-titles').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Titles & metas has been successfully updated!');
				jQuery( '#titles-state' ).toggleClass('feature-state-on');
				jQuery( '#titles-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//XML Sitemap
	jQuery('#toggle-xml-sitemap').on('click', function() {
		jQuery('#toggle-xml-sitemap').attr('data-toggle', jQuery('#toggle-xml-sitemap').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-xml-sitemap',
				feature_value: jQuery('#toggle-xml-sitemap').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('XML Sitemap has been successfully updated!');
				jQuery( '#sitemap-state' ).toggleClass('feature-state-on');
				jQuery( '#sitemap-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//Social
	jQuery('#toggle-social').on('click', function() {
		jQuery('#toggle-social').attr('data-toggle', jQuery('#toggle-social').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-social',
				feature_value: jQuery('#toggle-social').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Social has been successfully updated!');
				jQuery( '#social-state' ).toggleClass('feature-state-on');
				jQuery( '#social-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//Google Analytics
	jQuery('#toggle-google-analytics').on('click', function() {
		jQuery('#toggle-google-analytics').attr('data-toggle', jQuery('#toggle-google-analytics').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-google-analytics',
				feature_value: jQuery('#toggle-google-analytics').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Google Analytics has been successfully updated!');
				jQuery( '#analytics-state' ).toggleClass('feature-state-on');
				jQuery( '#analytics-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//Advanced
	jQuery('#toggle-advanced').on('click', function() {
		jQuery('#toggle-advanced').attr('data-toggle', jQuery('#toggle-advanced').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-advanced',
				feature_value: jQuery('#toggle-advanced').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Advanced features has been successfully updated!');
				jQuery( '#advanced-state' ).toggleClass('feature-state-on');
				jQuery( '#advanced-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//WooCommerce
	jQuery('#toggle-woocommerce').on('click', function() {
		jQuery('#toggle-woocommerce').attr('data-toggle', jQuery('#toggle-woocommerce').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-woocommerce',
				feature_value: jQuery('#toggle-woocommerce').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('WooCommerce has been successfully updated!');
				jQuery( '#woocommerce-state' ).toggleClass('feature-state-on');
				jQuery( '#woocommerce-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
    //Breadcrumbs
	jQuery('#toggle-breadcrumbs').on('click', function() {
		jQuery('#toggle-breadcrumbs').attr('data-toggle', jQuery('#toggle-breadcrumbs').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-breadcrumbs',
				feature_value: jQuery('#toggle-breadcrumbs').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Breadcrumbs has been successfully updated!');
				jQuery( '#breadcrumbs-state' ).toggleClass('feature-state-on');
				jQuery( '#breadcrumbs-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});    
	//Robots
	jQuery('#toggle-robots').on('click', function() {
		jQuery('#toggle-robots').attr('data-toggle', jQuery('#toggle-robots').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-robots',
				feature_value: jQuery('#toggle-robots').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Robots has been successfully updated!');
				jQuery( '#robots-state' ).toggleClass('feature-state-on');
				jQuery( '#robots-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});	
	//Google News
	jQuery('#toggle-news').on('click', function() {
		jQuery('#toggle-news').attr('data-toggle', jQuery('#toggle-news').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-news',
				feature_value: jQuery('#toggle-news').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Google News Sitemap has been successfully updated!');
				jQuery( '#news-state' ).toggleClass('feature-state-on');
				jQuery( '#news-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});	
	//404
	jQuery('#toggle-404').on('click', function() {
		jQuery('#toggle-404').attr('data-toggle', jQuery('#toggle-404').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-404',
				feature_value: jQuery('#toggle-404').attr('data-toggle'),
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Redirections has been successfully updated!');
				jQuery( '#redirections-state' ).toggleClass('feature-state-on');
				jQuery( '#redirections-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
});