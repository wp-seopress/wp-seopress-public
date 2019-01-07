jQuery(document).ready(function(){
	//If no notices
	if (!jQuery.trim( jQuery("#seopress-notifications-center").html() )) {
		jQuery('#seopress-notifications-center').remove();
	}

	//Hide notices
	//**Divide comments
	jQuery('#notice-divide-comments').on('click', function() {
		jQuery('#notice-divide-comments').attr('data-notice', jQuery('#notice-divide-comments').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-divide-comments',
				notice_value: jQuery('#notice-divide-comments').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-divide-comments-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**Review SEOPress
	jQuery('#notice-review').on('click', function() {
		jQuery('#notice-review').attr('data-notice', jQuery('#notice-review').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-review',
				notice_value: jQuery('#notice-review').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-review-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**Trailingslash
	jQuery('#notice-trailingslash').on('click', function() {
		jQuery('#notice-trailingslash').attr('data-notice', jQuery('#notice-trailingslash').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-trailingslash',
				notice_value: jQuery('#notice-trailingslash').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-trailingslash-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});	
	//**Posts per page
	jQuery('#notice-posts-number').on('click', function() {
		jQuery('#notice-posts-number').attr('data-notice', jQuery('#notice-posts-number').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-posts-number',
				notice_value: jQuery('#notice-posts-number').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-posts-number-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**RSS full text
	jQuery('#notice-rss-use-excerpt').on('click', function() {
		jQuery('#notice-rss-use-excerpt').attr('data-notice', jQuery('#notice-rss-use-excerpt').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-rss-use-excerpt',
				notice_value: jQuery('#notice-rss-use-excerpt').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-rss-use-excerpt-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**Search Console
	jQuery('#notice-search-console').on('click', function() {
		jQuery('#notice-search-console').attr('data-notice', jQuery('#notice-search-console').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-search-console',
				notice_value: jQuery('#notice-search-console').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-search-console-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**Google Business
	jQuery('#notice-google-business').on('click', function() {
		jQuery('#notice-google-business').attr('data-notice', jQuery('#notice-google-business').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-google-business',
				notice_value: jQuery('#notice-google-business').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-google-business-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**SSL
	jQuery('#notice-ssl').on('click', function() {
		jQuery('#notice-ssl').attr('data-notice', jQuery('#notice-ssl').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-ssl',
				notice_value: jQuery('#notice-ssl').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-ssl-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//**Title tag add_theme_support
	jQuery('#notice-title-tag').on('click', function() {
		jQuery('#notice-title-tag').attr('data-notice', jQuery('#notice-title-tag').attr('data-notice') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxHideNotices.seopress_hide_notices,
			data : {
				action: 'seopress_hide_notices',
				notice: 'notice-title-tag',
				notice_value: jQuery('#notice-title-tag').attr('data-notice'),
				_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Notice successfully removed');
				jQuery( '#notice-title-tag-alert' ).fadeOut();
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//Titles
	jQuery('#toggle-titles').on('click', function() {
		jQuery('#toggle-titles').attr('data-toggle', jQuery('#toggle-titles').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-titles',
				feature_value: jQuery('#toggle-titles').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-xml-sitemap',
				feature_value: jQuery('#toggle-xml-sitemap').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-social',
				feature_value: jQuery('#toggle-social').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-google-analytics',
				feature_value: jQuery('#toggle-google-analytics').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-advanced',
				feature_value: jQuery('#toggle-advanced').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
	//Local Business
	jQuery('#toggle-local-business').on('click', function() {
		jQuery('#toggle-local-business').attr('data-toggle', jQuery('#toggle-local-business').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-local-business',
				feature_value: jQuery('#toggle-local-business').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Local Business has been successfully updated!');
				jQuery( '#local-business-state' ).toggleClass('feature-state-on');
				jQuery( '#local-business-state-default' ).toggleClass('feature-state-off');
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-woocommerce',
				feature_value: jQuery('#toggle-woocommerce').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
	//EDD
	jQuery('#toggle-edd').on('click', function() {
		jQuery('#toggle-edd').attr('data-toggle', jQuery('#toggle-edd').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-edd',
				feature_value: jQuery('#toggle-edd').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Easy Digital Downloads has been successfully updated!');
				jQuery( '#edd-state' ).toggleClass('feature-state-on');
				jQuery( '#edd-state-default' ).toggleClass('feature-state-off');
				jQuery( '#edd-notice-save' ).delay(3500).fadeOut();
			},
		});
	});	
	//Dublin Core
	jQuery('#toggle-dublin-core').on('click', function() {
		jQuery('#toggle-dublin-core').attr('data-toggle', jQuery('#toggle-dublin-core').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-dublin-core',
				feature_value: jQuery('#toggle-dublin-core').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Dublin Core has been successfully updated!');
				jQuery( '#dublin-core-state' ).toggleClass('feature-state-on');
				jQuery( '#dublin-core-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
	//Rich Snippets
	jQuery('#toggle-rich-snippets').on('click', function() {
		jQuery('#toggle-rich-snippets').attr('data-toggle', jQuery('#toggle-rich-snippets').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-rich-snippets',
				feature_value: jQuery('#toggle-rich-snippets').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('Structure Data Types has been successfully updated!');
				jQuery( '#rich-snippets-state' ).toggleClass('feature-state-on');
				jQuery( '#rich-snippets-state-default' ).toggleClass('feature-state-off');
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-breadcrumbs',
				feature_value: jQuery('#toggle-breadcrumbs').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-robots',
				feature_value: jQuery('#toggle-robots').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-news',
				feature_value: jQuery('#toggle-news').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-404',
				feature_value: jQuery('#toggle-404').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
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
	//Rewrite
	jQuery('#toggle-rewrite').on('click', function() {
		jQuery('#toggle-rewrite').attr('data-toggle', jQuery('#toggle-rewrite').attr('data-toggle') == '1' ? '0' : '1');
		jQuery.ajax({
			method : 'POST',
			url : seopressAjaxToggleFeatures.seopress_toggle_features,
			data : {
				action: 'seopress_toggle_features',
				feature: 'toggle-rewrite',
				feature_value: jQuery('#toggle-rewrite').attr('data-toggle'),
				_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
			},
			success : function( data ) {
				jQuery( '#seopress-notice-save' ).css('display', 'block');
				jQuery( '#seopress-notice-save .html' ).html('URL Rewriting has been successfully updated!');
				jQuery( '#rewrite-state' ).toggleClass('feature-state-on');
				jQuery( '#rewrite-state-default' ).toggleClass('feature-state-off');
				jQuery( '#seopress-notice-save' ).delay(3500).fadeOut();
			},
		});
	});
});