jQuery(document).ready(function($) {
	//If no notices
	if (!$.trim( $("#seopress-notifications-center").html() )) {
		$('#seopress-notifications-center').remove();
	}
	const notices = ["notice-wizard","notice-divide-comments","notice-review","notice-trailingslash","notice-posts-number","notice-rss-use-excerpt","notice-search-console","notice-google-business","notice-ssl","notice-title-tag", "notice-go-pro"]
	notices.forEach(function (item) {
		$('#'+item).on('click', function() {
			$('#'+item).attr('data-notice', $('#'+item).attr('data-notice') == '1' ? '0' : '1');
			$.ajax({
				method : 'POST',
				url : seopressAjaxHideNotices.seopress_hide_notices,
				data : {
					action: 'seopress_hide_notices',
					notice: item,
					notice_value: $('#'+item).attr('data-notice'),
					_ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
				},
				success : function( data ) {
					$( '#seopress-notice-save' ).css('display', 'block');
					$( '#seopress-notice-save .html' ).html('Notice successfully removed');
					$( '#'+item+'-alert' ).fadeOut();
					$( '#seopress-notice-save' ).delay(3500).fadeOut();
				},
			});
		});
	});

	const features = ["titles","xml-sitemap","social","google-analytics","advanced","local-business","woocommerce","edd","dublin-core","rich-snippets","breadcrumbs","robots","news","404","bot","rewrite","white-label"]
	features.forEach(function (item) {
		$('#toggle-'+item).on('click', function() {
			$('#toggle-'+item).attr('data-toggle', $('#toggle-'+item).attr('data-toggle') == '1' ? '0' : '1');
			$.ajax({
				method : 'POST',
				url : seopressAjaxToggleFeatures.seopress_toggle_features,
				data : {
					action: 'seopress_toggle_features',
					feature: 'toggle-'+item,
					feature_value: $('#toggle-'+item).attr('data-toggle'),
					_ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
				},
				success : function( data ) {
					$( '#seopress-notice-save' ).css('display', 'block');
					$( '#seopress-notice-save .html' ).html(item + ' ' + seopressAjaxToggleFeatures.i18n);
					$( '#'+item+'-state' ).toggleClass('feature-state-on');
					$( '#'+item+'-state-default' ).toggleClass('feature-state-off');
					$( '#seopress-notice-save' ).delay(3500).fadeOut();
				},
			});
		});
	});
});