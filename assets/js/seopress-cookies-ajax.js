//GA user consent
jQuery(document).ready(function($) {
	if(Cookies.get('seopress-user-consent-close') ==undefined && Cookies.get('seopress-user-consent-accept') ==undefined) {
		$('.seopress-user-consent').removeClass('seopress-user-consent-hide');
	}
	$('#seopress-user-consent-accept').on('click', function() {
		$('.seopress-user-consent').remove();
		$.ajax({
			method : 'GET',
			url : seopressAjaxGAUserConsent.seopress_cookies_user_consent,
			data : {
				action: 'seopress_cookies_user_consent',
				_ajax_nonce: seopressAjaxGAUserConsent.seopress_nonce,
			},
			success : function( data ) {
				if (data.data) {
					$('head').append(data.data.gtag_js);
					$('head').append(data.data.matomo_js);
					$('head').append(data.data.custom);
					$('head').append(data.data.head_js);
					$('body').prepend(data.data.body_js);
					$('body').append(data.data.footer_js);
				}
				Cookies.set('seopress-user-consent-accept', '1', { expires: 30 });
			},
		});
	});
	$('#seopress-user-consent-close').on('click', function() {
		$('.seopress-user-consent').remove();
		Cookies.set('seopress-user-consent-close', '1', { expires: 30 });
	});
});