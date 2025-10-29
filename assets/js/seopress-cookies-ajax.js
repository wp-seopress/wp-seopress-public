//User consent
document.addEventListener('DOMContentLoaded', function () {
    if (Cookies.get('seopress-user-consent-close') == undefined && Cookies.get('seopress-user-consent-accept') == undefined) {
        document.querySelectorAll('.seopress-user-consent.seopress-user-message').forEach(function(element) {
            element.classList.remove('seopress-user-consent-hide');
        });
        document.querySelectorAll('.seopress-user-consent-backdrop').forEach(function(element) {
            element.classList.remove('seopress-user-consent-hide');
        });
    }
    
    document.getElementById('seopress-user-consent-accept').addEventListener('click', function () {
        document.querySelectorAll('.seopress-user-consent.seopress-user-message').forEach(function(element) {
            element.classList.add('seopress-user-consent-hide');
        });
        document.querySelectorAll('.seopress-user-consent-backdrop').forEach(function(element) {
            element.classList.add('seopress-user-consent-hide');
        });
        Cookies.remove('seopress-user-consent-close');
        Cookies.set('seopress-user-consent-accept', '1', { expires: Number(seopressAjaxGAUserConsent.seopress_cookies_expiration_days) });

        fetch(seopressAjaxGAUserConsent.seopress_cookies_user_consent, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'seopress_cookies_user_consent',
                consent: 'update',
                _ajax_nonce: seopressAjaxGAUserConsent.seopress_nonce,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                const head = document.head;
                if (data.data.gtag_js) head.insertAdjacentHTML('beforeend', data.data.gtag_js);
                if (data.data.matomo_js) head.insertAdjacentHTML('beforeend', data.data.matomo_js);
                if (data.data.clarity_js) head.insertAdjacentHTML('beforeend', data.data.clarity_js);
                if (data.data.custom) head.insertAdjacentHTML('beforeend', data.data.custom);
                if (data.data.head_js) head.insertAdjacentHTML('beforeend', data.data.head_js);
                
                const body = document.body;
                if (data.data.body_js) body.insertAdjacentHTML('afterbegin', data.data.body_js);
                if (data.data.matomo_body_js) body.insertAdjacentHTML('afterbegin', data.data.matomo_body_js);
                if (data.data.footer_js) body.insertAdjacentHTML('beforeend', data.data.footer_js);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    document.getElementById('seopress-user-consent-close').addEventListener('click', function () {
        document.querySelectorAll('.seopress-user-consent.seopress-user-message').forEach(function(element) {
            element.classList.add('seopress-user-consent-hide');
        });
        document.querySelectorAll('.seopress-user-consent-backdrop').forEach(function(element) {
            element.classList.add('seopress-user-consent-hide');
        });

        Cookies.remove('seopress-user-consent-accept');
        Cookies.set('seopress-user-consent-close', '1', { expires: Number(seopressAjaxGAUserConsent.seopress_cookies_expiration_days) });

        fetch(seopressAjaxGAUserConsent.seopress_cookies_user_consent, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'seopress_cookies_user_consent_close',
                consent: 'update',
                _ajax_nonce: seopressAjaxGAUserConsent.seopress_nonce,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                const head = document.head;
                if (data.data.gtag_consent_js) head.insertAdjacentHTML('beforeend', data.data.gtag_consent_js);
                if (data.data.clarity_consent_js) head.insertAdjacentHTML('beforeend', data.data.clarity_consent_js);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    document.getElementById('seopress-user-consent-edit').addEventListener('click', function () {
        document.querySelectorAll('.seopress-user-consent.seopress-user-message').forEach(function(element) {
            element.classList.remove('seopress-user-consent-hide');
        });
        document.querySelectorAll('.seopress-user-consent-backdrop').forEach(function(element) {
            element.classList.remove('seopress-user-consent-hide');
        });
    });
});
