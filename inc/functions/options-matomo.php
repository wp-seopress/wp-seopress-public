<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//MATOMO TRACKING


//Matomo Tracking
function seopress_google_analytics_matomo_enable_option() {
	$seopress_google_analytics_matomo_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_enable_option ) ) {
		foreach ($seopress_google_analytics_matomo_enable_option as $key => $seopress_google_analytics_matomo_enable_value)
			$options[$key] = $seopress_google_analytics_matomo_enable_value;
			if (isset($seopress_google_analytics_matomo_enable_option['seopress_google_analytics_matomo_enable'])) {
				return $seopress_google_analytics_matomo_enable_option['seopress_google_analytics_matomo_enable'];
			}
	}
}

//Matomo ID
function seopress_google_analytics_matomo_id_option() {
	$seopress_google_analytics_matomo_id_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_id_option ) ) {
		foreach ($seopress_google_analytics_matomo_id_option as $key => $seopress_google_analytics_matomo_id_value)
			$options[$key] = $seopress_google_analytics_matomo_id_value;
			if (isset($seopress_google_analytics_matomo_id_option['seopress_google_analytics_matomo_id'])) {
				return $seopress_google_analytics_matomo_id_option['seopress_google_analytics_matomo_id'];
			}
	}
}

//Matomo site ID
function seopress_google_analytics_matomo_site_id_option() {
	$seopress_google_analytics_matomo_site_id_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_site_id_option ) ) {
		foreach ($seopress_google_analytics_matomo_site_id_option as $key => $seopress_google_analytics_matomo_site_id_value)
			$options[$key] = $seopress_google_analytics_matomo_site_id_value;
			if (isset($seopress_google_analytics_matomo_site_id_option['seopress_google_analytics_matomo_site_id'])) {
				return $seopress_google_analytics_matomo_site_id_option['seopress_google_analytics_matomo_site_id'];
			}
	}
}

//Matomo subdomains
function seopress_google_analytics_matomo_subdomains_option() {
	$seopress_google_analytics_matomo_subdomains_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_subdomains_option ) ) {
		foreach ($seopress_google_analytics_matomo_subdomains_option as $key => $seopress_google_analytics_matomo_subdomains_value)
			$options[$key] = $seopress_google_analytics_matomo_subdomains_value;
			if (isset($seopress_google_analytics_matomo_subdomains_option['seopress_google_analytics_matomo_subdomains'])) {
				return $seopress_google_analytics_matomo_subdomains_option['seopress_google_analytics_matomo_subdomains'];
			}
	}
}

//Matomo site domain
function seopress_google_analytics_matomo_site_domain_option() {
	$seopress_google_analytics_matomo_site_domain_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_site_domain_option ) ) {
		foreach ($seopress_google_analytics_matomo_site_domain_option as $key => $seopress_google_analytics_matomo_site_domain_value)
			$options[$key] = $seopress_google_analytics_matomo_site_domain_value;
			if (isset($seopress_google_analytics_matomo_site_domain_option['seopress_google_analytics_matomo_site_domain'])) {
				return $seopress_google_analytics_matomo_site_domain_option['seopress_google_analytics_matomo_site_domain'];
			}
	}
}

//Matomo no js
function seopress_google_analytics_matomo_no_js_option() {
	$seopress_google_analytics_matomo_no_js_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_no_js_option ) ) {
		foreach ($seopress_google_analytics_matomo_no_js_option as $key => $seopress_google_analytics_matomo_no_js_value)
			$options[$key] = $seopress_google_analytics_matomo_no_js_value;
			if (isset($seopress_google_analytics_matomo_no_js_option['seopress_google_analytics_matomo_no_js'])) {
				return $seopress_google_analytics_matomo_no_js_option['seopress_google_analytics_matomo_no_js'];
			}
	}
}

//Matomo cross domain
function seopress_google_analytics_matomo_cross_domain_option() {
	$seopress_google_analytics_matomo_cross_domain_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_cross_domain_option ) ) {
		foreach ($seopress_google_analytics_matomo_cross_domain_option as $key => $seopress_google_analytics_matomo_cross_domain_value)
			$options[$key] = $seopress_google_analytics_matomo_cross_domain_value;
			if (isset($seopress_google_analytics_matomo_cross_domain_option['seopress_google_analytics_matomo_cross_domain'])) {
				return $seopress_google_analytics_matomo_cross_domain_option['seopress_google_analytics_matomo_cross_domain'];
			}
	}
}

//Matomo cross domain sites
function seopress_google_analytics_matomo_cross_domain_sites_option() {
	$seopress_google_analytics_matomo_cross_domain_sites_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_cross_domain_sites_option ) ) {
		foreach ($seopress_google_analytics_matomo_cross_domain_sites_option as $key => $seopress_google_analytics_matomo_cross_domain_sites_value)
			$options[$key] = $seopress_google_analytics_matomo_cross_domain_sites_value;
			if (isset($seopress_google_analytics_matomo_cross_domain_sites_option['seopress_google_analytics_matomo_cross_domain_sites'])) {
				return $seopress_google_analytics_matomo_cross_domain_sites_option['seopress_google_analytics_matomo_cross_domain_sites'];
			}
	}
}

//Matomo DNT
function seopress_google_analytics_matomo_dnt_option() {
	$seopress_google_analytics_matomo_dnt_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_dnt_option ) ) {
		foreach ($seopress_google_analytics_matomo_dnt_option as $key => $seopress_google_analytics_matomo_dnt_value)
			$options[$key] = $seopress_google_analytics_matomo_dnt_value;
			if (isset($seopress_google_analytics_matomo_dnt_option['seopress_google_analytics_matomo_dnt'])) {
				return $seopress_google_analytics_matomo_dnt_option['seopress_google_analytics_matomo_dnt'];
			}
	}
}

//Matomo no cookies
function seopress_google_analytics_matomo_no_cookies_option() {
	$seopress_google_analytics_matomo_no_cookies_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_no_cookies_option ) ) {
		foreach ($seopress_google_analytics_matomo_no_cookies_option as $key => $seopress_google_analytics_matomo_no_cookies_value)
			$options[$key] = $seopress_google_analytics_matomo_no_cookies_value;
			if (isset($seopress_google_analytics_matomo_no_cookies_option['seopress_google_analytics_matomo_no_cookies'])) {
				return $seopress_google_analytics_matomo_no_cookies_option['seopress_google_analytics_matomo_no_cookies'];
			}
	}
}

//Matomo link tracking
function seopress_google_analytics_matomo_link_tracking_option() {
	$seopress_google_analytics_matomo_link_tracking_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_link_tracking_option ) ) {
		foreach ($seopress_google_analytics_matomo_link_tracking_option as $key => $seopress_google_analytics_matomo_link_tracking_value)
			$options[$key] = $seopress_google_analytics_matomo_link_tracking_value;
			if (isset($seopress_google_analytics_matomo_link_tracking_option['seopress_google_analytics_matomo_link_tracking'])) {
				return $seopress_google_analytics_matomo_link_tracking_option['seopress_google_analytics_matomo_link_tracking'];
			}
	}
}

//Matomo no heatmaps
function seopress_google_analytics_matomo_no_heatmaps_option() {
	$seopress_google_analytics_matomo_no_heatmaps_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_matomo_no_heatmaps_option ) ) {
		foreach ($seopress_google_analytics_matomo_no_heatmaps_option as $key => $seopress_google_analytics_matomo_no_heatmaps_value)
			$options[$key] = $seopress_google_analytics_matomo_no_heatmaps_value;
			if (isset($seopress_google_analytics_matomo_no_heatmaps_option['seopress_google_analytics_matomo_no_heatmaps'])) {
				return $seopress_google_analytics_matomo_no_heatmaps_option['seopress_google_analytics_matomo_no_heatmaps'];
			}
	}
}

//Build Custom Matomo
function seopress_matomo_js($echo) {
	if (seopress_google_analytics_matomo_id_option() !='' && seopress_google_analytics_matomo_site_id_option() !='') {
		//Init
		$seopress_matomo_config = [];
		$seopress_matomo_event = [];
		
		$seopress_matomo_html = "\n";
		$seopress_matomo_html .="<script async>
var _paq = window._paq || [];\n";
		
		//subdomains
		if (seopress_google_analytics_matomo_subdomains_option() =='1') {
			$seopress_matomo_config['subdomains'] = "_paq.push(['setCookieDomain', '*.".get_home_url()."']);\n";
			$seopress_matomo_config['subdomains'] = apply_filters('seopress_matomo_cookie_domain', $seopress_matomo_config['subdomains']);
		}

		//site domain
		if (seopress_google_analytics_matomo_site_domain_option() =='1') {
			$seopress_matomo_config['site_domain'] = "_paq.push(['setDocumentTitle', document.domain + '/' + document.title]);\n";
			$seopress_matomo_config['site_domain'] = apply_filters('seopress_matomo_site_domain', $seopress_matomo_config['site_domain']);
		}

		//DNT
		if (seopress_google_analytics_matomo_dnt_option() =='1') {
			$seopress_matomo_config['dnt'] = "_paq.push(['setDoNotTrack', true]);\n";
			$seopress_matomo_config['dnt'] = apply_filters('seopress_matomo_dnt', $seopress_matomo_config['dnt']);
		}

		//disable cookies
		if (seopress_google_analytics_matomo_no_cookies_option() =='1') {
			$seopress_matomo_config['no_cookies'] = "_paq.push(['disableCookies']);\n";
			$seopress_matomo_config['no_cookies'] = apply_filters('seopress_matomo_disable_cookies', $seopress_matomo_config['no_cookies']);
		}

		//cross domains
		if (seopress_google_analytics_matomo_cross_domain_option() =='1' && seopress_google_analytics_matomo_cross_domain_sites_option()) {

			$domains = array_map('trim',array_filter(explode(',',seopress_google_analytics_matomo_cross_domain_sites_option())));
			
			if (!empty($domains)) { 
				$domains_count = count($domains);

				$link_domains = '';

				foreach ($domains as $key => $domain) {
					$link_domains .= "'*.".$domain."'";
					if ( $key < $domains_count -1){
						$link_domains .= ',';
					}
				}
				$seopress_matomo_config['set_domains'] = "_paq.push(['setDomains', [".$link_domains."]]);\n_paq.push(['enableCrossDomainLinking']);\n";
				$seopress_matomo_config['set_domains'] = apply_filters('seopress_matomo_linker', $seopress_matomo_config['set_domains']);
			}
		}
		
		//link tracking
		if (seopress_google_analytics_matomo_link_tracking_option() =='1') {
			$seopress_matomo_config['link_tracking'] = "_paq.push(['enableLinkTracking']);\n";
			$seopress_matomo_config['link_tracking'] = apply_filters('seopress_matomo_link_tracking', $seopress_matomo_config['link_tracking']);
		}

		//no heatmaps
		if (seopress_google_analytics_matomo_no_heatmaps_option() =='1') {
			$seopress_matomo_config['no_heatmaps'] = "_paq.push(['HeatmapSessionRecording::disable']);\n";
			$seopress_matomo_config['no_heatmaps'] = apply_filters('seopress_matomo_no_heatmaps', $seopress_matomo_config['no_heatmaps']);
		}

		//dimensions
		if (seopress_google_analytics_cd_author_option() !='') {
			if (seopress_google_analytics_cd_author_option() !='none') {
				if (is_singular()) {
					$seopress_matomo_event['cd_author'] = "_paq.push(['setCustomVariable', '".substr(seopress_google_analytics_cd_author_option(),-1)."', '".__('Authors','wp-seopress')."', '".get_the_author()."', 'visit']);\n";
					$seopress_matomo_event['cd_author'] = apply_filters('seopress_matomo_cd_author_ev', $seopress_matomo_event['cd_author']);
				}
			}
		}

		if (seopress_google_analytics_cd_category_option() !='') {
			if (seopress_google_analytics_cd_category_option() !='none') {
				if (is_single() && has_category()) {
					$categories = get_the_category();
					
					if ( ! empty( $categories ) ) {
						$get_first_category = esc_html( $categories[0]->name );
					}
					$seopress_matomo_event['cd_categories'] = "_paq.push(['setCustomVariable', '".substr(seopress_google_analytics_cd_category_option(),-1)."', '".__('Categories','wp-seopress')."', '".$get_first_category."', 'visit']);\n";
					$seopress_matomo_event['cd_categories'] = apply_filters('seopress_matomo_cd_categories_ev', $seopress_matomo_event['cd_categories']);
				}
			}
		}
		
		if (seopress_google_analytics_cd_tag_option() !='') {
			if (seopress_google_analytics_cd_tag_option() !='none') {
				if (is_single() && has_tag()) {
					$tags = get_the_tags();
					if ( ! empty( $tags ) ) {
						$seopress_comma_count = count($tags);
						$get_tags = '';
						foreach ($tags as $key => $value) {
							$get_tags .= esc_html( $value->name );
							if ( $key < $seopress_comma_count -1){
								$get_tags .= ', ';
							}
						}
					}
					$seopress_matomo_event['cd_tags'] = "_paq.push(['setCustomVariable', '".substr(seopress_google_analytics_cd_tag_option(),-1)."', '".__('Tags','wp-seopress')."', '".$get_tags."', 'visit']);\n";
					$seopress_matomo_event['cd_tags'] = apply_filters('seopress_matomo_cd_tags_ev', $seopress_matomo_event['cd_tags']);
				}
			}
		}
		
		if (seopress_google_analytics_cd_post_type_option() !='') {
			if (seopress_google_analytics_cd_post_type_option() !='none') {
				if (is_single()) {
					$seopress_matomo_event['cd_cpt'] = "_paq.push(['setCustomVariable', '".substr(seopress_google_analytics_cd_post_type_option(),-1)."', '".__('Post types','wp-seopress')."', '".get_post_type()."', 'visit']);\n";
					$seopress_matomo_event['cd_cpt'] = apply_filters('seopress_matomo_cd_cpt_ev', $seopress_matomo_event['cd_cpt']);
				}
			}
		}
		
		if (seopress_google_analytics_cd_logged_in_user_option() !='') {
			if (seopress_google_analytics_cd_logged_in_user_option() !='none') {
				if (wp_get_current_user()->ID) {
					$seopress_matomo_event['cd_logged_in'] = "_paq.push(['setCustomVariable', '".substr(seopress_google_analytics_cd_logged_in_user_option(),-1)."', '".__('Connected users','wp-seopress')."', '".wp_get_current_user()->ID."', 'visit']);\n";
					$seopress_matomo_event['cd_logged_in'] = apply_filters('seopress_matomo_cd_logged_in_ev', $seopress_matomo_event['cd_logged_in']);
				}
			}
		}

		//send data config
		if (!empty($seopress_matomo_config)) {
			foreach($seopress_matomo_config as $key => $value) {
				$seopress_matomo_html .= $value;
			}
		}
		
		//send data dimensions
		if (!empty($seopress_matomo_event)) {
			foreach($seopress_matomo_event as $key => $value) {
				$seopress_matomo_html .= $value;
			}
		}
		
		$seopress_matomo_html .= "_paq.push(['trackPageView']);
(function() {
	var u='https://".seopress_google_analytics_matomo_id_option()."/';
	_paq.push(['setTrackerUrl', u+'matomo.php']);
	_paq.push(['setSiteId', '".seopress_google_analytics_matomo_site_id_option()."']);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.type='text/javascript'; g.async=true; g.defer=true; g.src='//cdn.matomo.cloud/".seopress_google_analytics_matomo_id_option()."/matomo.js'; s.parentNode.insertBefore(g,s);
	})();\n";
		
		$seopress_matomo_html .= "</script>\n";
		
		//no JS
		$no_js = NULL;
		if (seopress_google_analytics_matomo_no_js_option() =='1') {
			$no_js = '<noscript><p><img src="https://'.seopress_google_analytics_matomo_id_option().'/matomo.php?idsite='.seopress_google_analytics_matomo_site_id_option().'&amp;rec=1" style="border:0;" alt="" /></p></noscript>';
			$no_js = apply_filters('seopress_matomo_no_js', $no_js);
		}
		
		if ($no_js) {
			$seopress_matomo_html .= $no_js;
		}
		
		$seopress_matomo_html = apply_filters('seopress_matomo_tracking_html', $seopress_matomo_html);

		if ($echo == true) {
			echo $seopress_matomo_html;
		} else {
			return $seopress_matomo_html;
		}
	}
}
add_action('seopress_matomo_html', 'seopress_matomo_js', 10, 1);

function seopress_matomo_js_arguments() {
	$echo = true;
	do_action('seopress_matomo_html', $echo);
}