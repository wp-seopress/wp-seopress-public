//GA user consent
jQuery(document).ready(function ($) {
    if (Cookies.get('seopress-user-consent-close') == undefined && Cookies.get('seopress-user-consent-accept') == undefined) {
        $('.seopress-user-consent.seopress-user-message').removeClass('seopress-user-consent-hide');
        $('.seopress-user-consent-backdrop').removeClass('seopress-user-consent-hide');
    }
    $('#seopress-user-consent-accept').on('click', function () {
        $('.seopress-user-consent.seopress-user-message').addClass('seopress-user-consent-hide');
        $('.seopress-user-consent-backdrop').addClass('seopress-user-consent-hide');
        Cookies.remove('seopress-user-consent-close');
        Cookies.set('seopress-user-consent-accept', '1', { expires: Number(seopressAjaxGAUserConsent.seopress_cookies_expiration_days) });

        $.ajax({
            method: 'POST',
            url: seopressAjaxGAUserConsent.seopress_cookies_user_consent,
            data: {
                action: 'seopress_cookies_user_consent',
                consent: 'update',
                _ajax_nonce: seopressAjaxGAUserConsent.seopress_nonce,
            },
            success: function (data) {
                if (data.data) {
                    $('head').append(data.data.gtag_js);
                    $('head').append(data.data.matomo_js);
                    $('head').append(data.data.clarity_js);
                    $('head').append(data.data.custom);
                    $('head').append(data.data.head_js);
                    $('body').prepend(data.data.body_js);
                    $('body').prepend(data.data.matomo_body_js);
                    $('body').append(data.data.footer_js);
                }
            },
        });
    });
    $('#seopress-user-consent-close').on('click', function () {
        $('.seopress-user-consent.seopress-user-message').addClass('seopress-user-consent-hide');
        $('.seopress-user-consent-backdrop').addClass('seopress-user-consent-hide');

        Cookies.remove('seopress-user-consent-accept');
        Cookies.set('seopress-user-consent-close', '1', { expires: Number(seopressAjaxGAUserConsent.seopress_cookies_expiration_days) });

        $.ajax({
            method: 'POST',
            url: seopressAjaxGAUserConsent.seopress_cookies_user_consent,
            data: {
                action: 'seopress_cookies_user_consent_close',
                consent: 'update',
                _ajax_nonce: seopressAjaxGAUserConsent.seopress_nonce,
            },
            success: function (data) {
                if (data.data) {
                    $('head').append(data.data.gtag_consent_js);
                    $('head').append(data.data.clarity_consent_js);
                }
            },
        });
    });
    $('#seopress-user-consent-edit').on('click', function () {
        $('.seopress-user-consent.seopress-user-message').removeClass('seopress-user-consent-hide');
        $('.seopress-user-consent-backdrop').removeClass('seopress-user-consent-hide');
    });
});
