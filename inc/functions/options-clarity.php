<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Build Clarity Tracking Code
function seopress_clarity_js($echo) {
	if (seopress_get_service('GoogleAnalyticsOption')->getClarityProjectId() !='' && seopress_get_service('GoogleAnalyticsOption')->getClarityEnable() === '1') {
		//Init
		$js = "\n";
		$js .= '<script>';
        $js .= "\n";
		$js .= '(function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=seopress";
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "'.sanitize_key(seopress_get_service('GoogleAnalyticsOption')->getClarityProjectId()).'");';

		// User consent
        $consent = "";

        $update = (! empty( $_POST['consent'] ) && $_POST['consent'] === 'update') ? true : false;

        if ($update === true) {
            if (isset($_COOKIE['seopress-user-consent-accept']) && '1' == $_COOKIE['seopress-user-consent-accept']) {
                // User has granted consent
                $consent = "window.clarity('consent');";
            } elseif (isset($_COOKIE['seopress-user-consent-close']) && '1' == $_COOKIE['seopress-user-consent-close']) {
                // User has declined consent
                $consent = "window.clarity('consent', false);";
            }
        } elseif (isset($_COOKIE['seopress-user-consent-accept']) && '1' == $_COOKIE['seopress-user-consent-accept']) {
            // User has previously granted consent
            $consent = "window.clarity('consent');";
        } else {
            // Default to no consent if no cookie is set
            $consent = "window.clarity('consent', false);";
        }

        // Allow developers to modify the consent behavior
        $consent = apply_filters( 'seopress_clarity_user_consent', $consent );

        $js .= $consent;

		$js .= "</script>\n";

		// Allow developers to modify the entire Clarity tracking code
		$js = apply_filters('seopress_clarity_tracking_js', $js);

		if ($echo == true) {
			echo $js;
		} else {
			return $js;
		}
	}
}
add_action('seopress_clarity_html', 'seopress_clarity_js', 10, 1);

function seopress_clarity_js_arguments() {
	$echo = true;
	do_action('seopress_clarity_html', $echo);
}
