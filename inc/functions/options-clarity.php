<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

// Clarity

//Clarity Tracking
function seopress_google_analytics_clarity_enable_option() {
	$seopress_google_analytics_clarity_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_clarity_enable_option ) ) {
		foreach ($seopress_google_analytics_clarity_enable_option as $key => $seopress_google_analytics_clarity_enable_value)
			$options[$key] = $seopress_google_analytics_clarity_enable_value;
			if (isset($seopress_google_analytics_clarity_enable_option['seopress_google_analytics_clarity_enable'])) {
				return $seopress_google_analytics_clarity_enable_option['seopress_google_analytics_clarity_enable'];
			}
	}
}

//Clarity Project ID
function seopress_google_analytics_clarity_project_id_option() {
	$seopress_google_analytics_clarity_project_id_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_clarity_project_id_option ) ) {
		foreach ($seopress_google_analytics_clarity_project_id_option as $key => $seopress_google_analytics_clarity_project_id_value)
			$options[$key] = $seopress_google_analytics_clarity_project_id_value;
			if (isset($seopress_google_analytics_clarity_project_id_option['seopress_google_analytics_clarity_project_id'])) {
				return $seopress_google_analytics_clarity_project_id_option['seopress_google_analytics_clarity_project_id'];
			}
	}
}

//Build Clarity Tracking Code
function seopress_clarity_js($echo) {
	if (seopress_google_analytics_clarity_project_id_option() !='' && seopress_google_analytics_clarity_enable_option() === '1') {
		//Init
		$js = "\n";
		$js .= '<script>';
        $js .= "\n";
		$js .= '(function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=seopress";
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "'.seopress_google_analytics_clarity_project_id_option().'");';
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
