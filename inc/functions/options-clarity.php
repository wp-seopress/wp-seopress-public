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
        })(window, document, "clarity", "script", "'.seopress_get_service('GoogleAnalyticsOption')->getClarityProjectId().'");';
		$js .= "</script>\n";

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
