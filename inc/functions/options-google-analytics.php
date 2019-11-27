<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Google Analytics
//=================================================================================================
if (seopress_google_analytics_disable_option() =='1' && ( (empty($_COOKIE["seopress-user-consent-accept"]) || $_COOKIE["seopress-user-consent-accept"] !='1') || (empty($_COOKIE["seopress-user-consent-close"]) || $_COOKIE["seopress-user-consent-close"] !='1'))) {
	if ((empty($_COOKIE["seopress-user-consent-accept"]) || $_COOKIE["seopress-user-consent-accept"] !='1') && (empty($_COOKIE["seopress-user-consent-close"]) || $_COOKIE["seopress-user-consent-close"] !='1')) {

		function seopress_google_analytics_opt_out_msg_ok_option() {
			$seopress_google_analytics_opt_out_msg_ok_option = get_option("seopress_google_analytics_option_name");
			if ( ! empty ( $seopress_google_analytics_opt_out_msg_ok_option ) ) {
				foreach ($seopress_google_analytics_opt_out_msg_ok_option as $key => $seopress_google_analytics_opt_out_msg_ok_value)
					$options[$key] = $seopress_google_analytics_opt_out_msg_ok_value;
				 if (isset($seopress_google_analytics_opt_out_msg_ok_option['seopress_google_analytics_opt_out_msg_ok'])) { 
				 	return $seopress_google_analytics_opt_out_msg_ok_option['seopress_google_analytics_opt_out_msg_ok'];
				 }
			}
		}

		function seopress_cookies_user_consent_html() {
			if (seopress_google_analytics_opt_out_msg_option() !='') {
				$msg = seopress_google_analytics_opt_out_msg_option();
			} elseif (get_option('wp_page_for_privacy_policy')) {
				$msg = __('By visiting our site, you agree to our privacy policy regarding cookies, tracking statistics, etc.&nbsp;<a href="[seopress_privacy_page]" tabindex="10">Read more</a>','wp-seopress');
			} else {
				$msg = __('By visiting our site, you agree to our privacy policy regarding cookies, tracking statistics, etc.','wp-seopress');
			}

			if (get_option('wp_page_for_privacy_policy') && $msg !='') {
				$seopress_privacy_page = esc_url(get_permalink(get_option('wp_page_for_privacy_policy')));
				$msg = str_replace('[seopress_privacy_page]', $seopress_privacy_page, $msg);
			}
			
			$msg = apply_filters('seopress_rgpd_message', $msg);

			if (seopress_google_analytics_opt_out_msg_ok_option() !='') {
				$consent_btn = seopress_google_analytics_opt_out_msg_ok_option();
			} else {
				$consent_btn = __('Accept','wp-seopress');
			} 
		    $user_msg = '<style>.seopress-user-consent {position: fixed;z-index: 8000;width: 100%;bottom: 0;background: #F1F1F1;padding: 10px;left: 0;text-align: center;}.seopress-user-consent p {margin: 0;font-size: 0.8em;justify-content: center;}.seopress-user-consent button {vertical-align: middle;margin: 0 10px;padding: 5px 20px;font-size: 14px;}#seopress-user-consent-close{margin: 0 0 0 20px;position: relative;line-height: 26px;background: none;font-weight: bold;border: 1px solid #ccc;padding: 0 10px;color:inherit;}#seopress-user-consent-close:hover{background:#222;cursor:pointer;color:#fff}.seopress-user-consent-hide{display:none;}</style>
		    <div class="seopress-user-consent seopress-user-consent-hide" tabindex="10"><p>'.$msg.'<button id="seopress-user-consent-accept" tabindex="11">'.$consent_btn.'</button><button id="seopress-user-consent-close" tabindex="12">'.__('X','wp-seopress').'</button></p></div>';

		    $user_msg = apply_filters('seopress_rgpd_full_message', $user_msg, $msg, $consent_btn);

		    echo $user_msg;
		}
		if (seopress_google_analytics_disable_option() =='1') {
			if (is_user_logged_in()) {
				global $wp_roles;
				
				//Get current user role
				if(isset(wp_get_current_user()->roles[0])) {
					$seopress_user_role = wp_get_current_user()->roles[0];
					//If current user role matchs values from SEOPress GA settings then apply
					if (function_exists('seopress_google_analytics_roles_option') && seopress_google_analytics_roles_option() !='') {
						if( array_key_exists( $seopress_user_role, seopress_google_analytics_roles_option())) {
							//do nothing
						} else {
							add_action('wp_footer', 'seopress_cookies_user_consent_html');
						}
					} else {
						add_action('wp_footer', 'seopress_cookies_user_consent_html');
					}
				} else {
					add_action('wp_footer', 'seopress_cookies_user_consent_html');
				}
			} else {
				add_action('wp_footer', 'seopress_cookies_user_consent_html');
			}
		}
	}
}

//Optimize
function seopress_google_analytics_optimize_option() {
	$seopress_google_analytics_optimize_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_optimize_option ) ) {
		foreach ($seopress_google_analytics_optimize_option as $key => $seopress_google_analytics_optimize_value)
			$options[$key] = $seopress_google_analytics_optimize_value;
			if (isset($seopress_google_analytics_optimize_option['seopress_google_analytics_optimize'])) {
				return $seopress_google_analytics_optimize_option['seopress_google_analytics_optimize'];
			}
	}
}

//Ads
function seopress_google_analytics_ads_option() {
	$seopress_google_analytics_ads_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_ads_option ) ) {
		foreach ($seopress_google_analytics_ads_option as $key => $seopress_google_analytics_ads_value)
			$options[$key] = $seopress_google_analytics_ads_value;
			if (isset($seopress_google_analytics_ads_option['seopress_google_analytics_ads'])) {
				return $seopress_google_analytics_ads_option['seopress_google_analytics_ads'];
			}
	}
}

//Additional tracking code - head
function seopress_google_analytics_other_tracking_option() {
	$seopress_google_analytics_other_tracking_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_other_tracking_option ) ) {
		foreach ($seopress_google_analytics_other_tracking_option as $key => $seopress_google_analytics_other_tracking_value)
			$options[$key] = $seopress_google_analytics_other_tracking_value;
			if (isset($seopress_google_analytics_other_tracking_option['seopress_google_analytics_other_tracking'])) {
				return $seopress_google_analytics_other_tracking_option['seopress_google_analytics_other_tracking'];
			}
	}
}

//Additional tracking code - body
function seopress_google_analytics_other_tracking_body_option() {
	$seopress_google_analytics_other_tracking_body_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_other_tracking_body_option ) ) {
		foreach ($seopress_google_analytics_other_tracking_body_option as $key => $seopress_google_analytics_other_tracking_body_value)
			$options[$key] = $seopress_google_analytics_other_tracking_body_value;
			if (isset($seopress_google_analytics_other_tracking_body_option['seopress_google_analytics_other_tracking_body'])) {
				return $seopress_google_analytics_other_tracking_body_option['seopress_google_analytics_other_tracking_body'];
			}
	}
}

//Remarketing
function seopress_google_analytics_remarketing_option() {
	$seopress_google_analytics_remarketing_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_remarketing_option ) ) {
		foreach ($seopress_google_analytics_remarketing_option as $key => $seopress_google_analytics_remarketing_value)
			$options[$key] = $seopress_google_analytics_remarketing_value;
			if (isset($seopress_google_analytics_remarketing_option['seopress_google_analytics_remarketing'])) {
				return $seopress_google_analytics_remarketing_option['seopress_google_analytics_remarketing'];
			}
	}
}

//IP Anonymization
function seopress_google_analytics_ip_anonymization_option() {
	$seopress_google_analytics_ip_anonymization_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_ip_anonymization_option ) ) {
		foreach ($seopress_google_analytics_ip_anonymization_option as $key => $seopress_google_analytics_ip_anonymization_value)
			$options[$key] = $seopress_google_analytics_ip_anonymization_value;
			if (isset($seopress_google_analytics_ip_anonymization_option['seopress_google_analytics_ip_anonymization'])) {
				return $seopress_google_analytics_ip_anonymization_option['seopress_google_analytics_ip_anonymization'];
			}
	}
}

//Link attribution
function seopress_google_analytics_link_attribution_option() {
	$seopress_google_analytics_link_attribution_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_link_attribution_option ) ) {
		foreach ($seopress_google_analytics_link_attribution_option as $key => $seopress_google_analytics_link_attribution_value)
			$options[$key] = $seopress_google_analytics_link_attribution_value;
			if (isset($seopress_google_analytics_link_attribution_option['seopress_google_analytics_link_attribution'])) {
				return $seopress_google_analytics_link_attribution_option['seopress_google_analytics_link_attribution'];
			}
	}
}

//Cross Domain Enable
function seopress_google_analytics_cross_enable_option() {
	$seopress_google_analytics_cross_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cross_enable_option ) ) {
		foreach ($seopress_google_analytics_cross_enable_option as $key => $seopress_google_analytics_cross_enable_value)
			$options[$key] = $seopress_google_analytics_cross_enable_value;
			if (isset($seopress_google_analytics_cross_enable_option['seopress_google_analytics_cross_enable'])) {
				return $seopress_google_analytics_cross_enable_option['seopress_google_analytics_cross_enable'];
			}
	}
}

//Cross Domain
function seopress_google_analytics_cross_domain_option() {
	$seopress_google_analytics_cross_domain_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cross_domain_option ) ) {
		foreach ($seopress_google_analytics_cross_domain_option as $key => $seopress_google_analytics_cross_domain_value)
			$options[$key] = $seopress_google_analytics_cross_domain_value;
			if (isset($seopress_google_analytics_cross_domain_option['seopress_google_analytics_cross_domain'])) {
				return $seopress_google_analytics_cross_domain_option['seopress_google_analytics_cross_domain'];
			}
	}
}

//Events external links tracking Enable
function seopress_google_analytics_link_tracking_enable_option() {
	$seopress_google_analytics_link_tracking_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_link_tracking_enable_option ) ) {
		foreach ($seopress_google_analytics_link_tracking_enable_option as $key => $seopress_google_analytics_link_tracking_enable_value)
			$options[$key] = $seopress_google_analytics_link_tracking_enable_value;
			if (isset($seopress_google_analytics_link_tracking_enable_option['seopress_google_analytics_link_tracking_enable'])) {
				return $seopress_google_analytics_link_tracking_enable_option['seopress_google_analytics_link_tracking_enable'];
			}
	}
}

//Events downloads tracking Enable
function seopress_google_analytics_download_tracking_enable_option() {
	$seopress_google_analytics_download_tracking_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_download_tracking_enable_option ) ) {
		foreach ($seopress_google_analytics_download_tracking_enable_option as $key => $seopress_google_analytics_download_tracking_enable_value)
			$options[$key] = $seopress_google_analytics_download_tracking_enable_value;
			if (isset($seopress_google_analytics_download_tracking_enable_option['seopress_google_analytics_download_tracking_enable'])) {
				return $seopress_google_analytics_download_tracking_enable_option['seopress_google_analytics_download_tracking_enable'];
			}
	}
}

//Events tracking file types
function seopress_google_analytics_download_tracking_option() {
	$seopress_google_analytics_download_tracking_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_download_tracking_option ) ) {
		foreach ($seopress_google_analytics_download_tracking_option as $key => $seopress_google_analytics_download_tracking_value)
			$options[$key] = $seopress_google_analytics_download_tracking_value;
			if (isset($seopress_google_analytics_download_tracking_option['seopress_google_analytics_download_tracking'])) {
				return $seopress_google_analytics_download_tracking_option['seopress_google_analytics_download_tracking'];
			}
	}
}

//Events affiliate links tracking Enable
function seopress_google_analytics_affiliate_tracking_enable_option() {
	$seopress_google_analytics_affiliate_tracking_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_affiliate_tracking_enable_option ) ) {
		foreach ($seopress_google_analytics_affiliate_tracking_enable_option as $key => $seopress_google_analytics_affiliate_tracking_enable_value)
			$options[$key] = $seopress_google_analytics_affiliate_tracking_enable_value;
			if (isset($seopress_google_analytics_affiliate_tracking_enable_option['seopress_google_analytics_affiliate_tracking_enable'])) {
				return $seopress_google_analytics_affiliate_tracking_enable_option['seopress_google_analytics_affiliate_tracking_enable'];
			}
	}
}

//Events tracking affiliate match
function seopress_google_analytics_affiliate_tracking_option() {
	$seopress_google_analytics_affiliate_tracking_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_affiliate_tracking_option ) ) {
		foreach ($seopress_google_analytics_affiliate_tracking_option as $key => $seopress_google_analytics_affiliate_tracking_value)
			$options[$key] = $seopress_google_analytics_affiliate_tracking_value;
			if (isset($seopress_google_analytics_affiliate_tracking_option['seopress_google_analytics_affiliate_tracking'])) {
				return $seopress_google_analytics_affiliate_tracking_option['seopress_google_analytics_affiliate_tracking'];
			}
	}
}

//Custom Dimension Author
function seopress_google_analytics_cd_author_option() {
	$seopress_google_analytics_cd_author_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cd_author_option ) ) {
		foreach ($seopress_google_analytics_cd_author_option as $key => $seopress_google_analytics_cd_author_value)
			$options[$key] = $seopress_google_analytics_cd_author_value;
			if (isset($seopress_google_analytics_cd_author_option['seopress_google_analytics_cd_author'])) {
				return $seopress_google_analytics_cd_author_option['seopress_google_analytics_cd_author'];
			}
	}
}

//Custom Dimension Category
function seopress_google_analytics_cd_category_option() {
	$seopress_google_analytics_cd_category_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cd_category_option ) ) {
		foreach ($seopress_google_analytics_cd_category_option as $key => $seopress_google_analytics_cd_category_value)
			$options[$key] = $seopress_google_analytics_cd_category_value;
			if (isset($seopress_google_analytics_cd_category_option['seopress_google_analytics_cd_category'])) {
				return $seopress_google_analytics_cd_category_option['seopress_google_analytics_cd_category'];
			}
	}
}

//Custom Dimension Tag
function seopress_google_analytics_cd_tag_option() {
	$seopress_google_analytics_cd_tag_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cd_tag_option ) ) {
		foreach ($seopress_google_analytics_cd_tag_option as $key => $seopress_google_analytics_cd_tag_value)
			$options[$key] = $seopress_google_analytics_cd_tag_value;
			if (isset($seopress_google_analytics_cd_tag_option['seopress_google_analytics_cd_tag'])) {
				return $seopress_google_analytics_cd_tag_option['seopress_google_analytics_cd_tag'];
			}
	}
}

//Custom Dimension Post Type
function seopress_google_analytics_cd_post_type_option() {
	$seopress_google_analytics_cd_post_type_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cd_post_type_option ) ) {
		foreach ($seopress_google_analytics_cd_post_type_option as $key => $seopress_google_analytics_cd_post_type_value)
			$options[$key] = $seopress_google_analytics_cd_post_type_value;
			if (isset($seopress_google_analytics_cd_post_type_option['seopress_google_analytics_cd_post_type'])) {
				return $seopress_google_analytics_cd_post_type_option['seopress_google_analytics_cd_post_type'];
			}
	}
}

//Custom Dimension Logged In
function seopress_google_analytics_cd_logged_in_user_option() {
	$seopress_google_analytics_cd_logged_in_user_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_cd_logged_in_user_option ) ) {
		foreach ($seopress_google_analytics_cd_logged_in_user_option as $key => $seopress_google_analytics_cd_logged_in_user_value)
			$options[$key] = $seopress_google_analytics_cd_logged_in_user_value;
			if (isset($seopress_google_analytics_cd_logged_in_user_option['seopress_google_analytics_cd_logged_in_user'])) {
				return $seopress_google_analytics_cd_logged_in_user_option['seopress_google_analytics_cd_logged_in_user'];
			}
	}
}

//Build Custom GA
function seopress_google_analytics_js($echo) {
	if (seopress_google_analytics_ua_option() !='') {
		//Init
		$seopress_google_analytics_config = array();
		$seopress_google_analytics_event = array();
		
		$seopress_google_analytics_html = "\n";
		$seopress_google_analytics_html .=
		"<script async src='https://www.googletagmanager.com/gtag/js?id=".seopress_google_analytics_ua_option()."'></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}";
$seopress_google_analytics_html .= "gtag('js', new Date());\n";
		
		//Cross domains
		if (seopress_google_analytics_cross_enable_option() =='1' && seopress_google_analytics_cross_domain_option()) {

			$domains = array_map('trim',array_filter(explode(',',seopress_google_analytics_cross_domain_option())));

			
			if (!empty($domains)) { 
				$domains_count = count($domains);

				$link_domains = '';

				foreach ($domains as $key => $domain) {
					$link_domains .= "'".$domain."'";
					if ( $key < $domains_count -1){
						$link_domains .= ',';
					}
				}
				$seopress_google_analytics_config['linker'] = "'linker': {'domains': [".$link_domains."]},";
				$seopress_google_analytics_config['linker'] = apply_filters('seopress_gtag_linker', $seopress_google_analytics_config['linker']);
			}
		}
		
		//Optimize
		if (seopress_google_analytics_optimize_option() !='') {
			$seopress_google_analytics_config['optimize'] = "'optimize_id': '".seopress_google_analytics_optimize_option()."',";
			$seopress_google_analytics_config['optimize'] = apply_filters('seopress_gtag_optimize_id', $seopress_google_analytics_config['optimize']);
		}

		//Remarketing
		if (seopress_google_analytics_remarketing_option() !='1') {
			$seopress_google_analytics_config['allow_display_features'] = "'allow_display_features': false,";
			$seopress_google_analytics_config['allow_display_features'] = apply_filters('seopress_gtag_allow_display_features', $seopress_google_analytics_config['allow_display_features']);
		}
		
		//Link attribution
		if (seopress_google_analytics_link_attribution_option() =='1') {
			$seopress_google_analytics_config['link_attribution'] = "'link_attribution': true,";
			$seopress_google_analytics_config['link_attribution'] = apply_filters('seopress_gtag_link_attribution', $seopress_google_analytics_config['link_attribution']);
		}
		
		//Dimensions		
		$seopress_google_analytics_config['cd']['cd_hook'] = apply_filters('seopress_gtag_cd_hook_cf', isset($seopress_google_analytics_config['cd']['cd_hook']));
		if (!has_filter('seopress_gtag_cd_hook_cf')) {
			unset($seopress_google_analytics_config['cd']['cd_hook']);
		}
		
		$seopress_google_analytics_event['cd_hook'] = apply_filters('seopress_gtag_cd_hook_ev', isset($seopress_google_analytics_event['cd_hook']));
		if (!has_filter('seopress_gtag_cd_hook_ev')) {
			unset($seopress_google_analytics_config['cd']['cd_hook']);
		}
		
		if ((seopress_google_analytics_cd_author_option() !='' && seopress_google_analytics_cd_author_option() !='none')
				|| (seopress_google_analytics_cd_category_option() !='' && seopress_google_analytics_cd_category_option() !='none')
				|| (seopress_google_analytics_cd_tag_option() !='' && seopress_google_analytics_cd_tag_option() !='none')
				|| (seopress_google_analytics_cd_post_type_option() !='' && seopress_google_analytics_cd_post_type_option() !='none')
				|| (seopress_google_analytics_cd_logged_in_user_option() !='' && seopress_google_analytics_cd_logged_in_user_option() !='none')
				|| (isset($seopress_google_analytics_config['cd']['cd_hook']) !='' && isset($seopress_google_analytics_event['cd_hook']) !='')
			) {
			$seopress_google_analytics_config['cd']['cd_start'] = "{";
		} else {
			unset($seopress_google_analytics_config['cd']);
		}		
		
		if (seopress_google_analytics_cd_author_option() !='') {
			if (seopress_google_analytics_cd_author_option() !='none') {
				if (is_singular()) {
					$seopress_google_analytics_config['cd']['cd_author'] = "'".seopress_google_analytics_cd_author_option()."': 'cd_author',";

					$seopress_google_analytics_event['cd_author'] = "gtag('event', '".__('Authors','wp-seopress')."', {'cd_author': '".get_the_author()."', 'non_interaction': true});";
					
					$seopress_google_analytics_config['cd']['cd_author'] = apply_filters('seopress_gtag_cd_author_cf', $seopress_google_analytics_config['cd']['cd_author']);
					
					$seopress_google_analytics_event['cd_author'] = apply_filters('seopress_gtag_cd_author_ev', $seopress_google_analytics_event['cd_author']);
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

					$seopress_google_analytics_config['cd']['cd_categories'] = "'".seopress_google_analytics_cd_category_option()."': 'cd_categories',";

					$seopress_google_analytics_event['cd_categories'] = "gtag('event', '".__('Categories','wp-seopress')."', {'cd_categories': '".$get_first_category."', 'non_interaction': true});";
					
					$seopress_google_analytics_config['cd']['cd_categories'] = apply_filters('seopress_gtag_cd_categories_cf', $seopress_google_analytics_config['cd']['cd_categories']);
					
					$seopress_google_analytics_event['cd_categories'] = apply_filters('seopress_gtag_cd_categories_ev', $seopress_google_analytics_event['cd_categories']);
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

					$seopress_google_analytics_config['cd']['cd_tags'] = "'".seopress_google_analytics_cd_tag_option()."': 'cd_tags',";

					$seopress_google_analytics_event['cd_tags'] = "gtag('event', '".__('Tags','wp-seopress')."', {'cd_tags': '".$get_tags."', 'non_interaction': true});";
					
					$seopress_google_analytics_config['cd']['cd_tags'] = apply_filters('seopress_gtag_cd_tags_cf', $seopress_google_analytics_config['cd']['cd_tags']);
					
					$seopress_google_analytics_event['cd_tags'] = apply_filters('seopress_gtag_cd_tags_ev', $seopress_google_analytics_event['cd_tags']);
				}
			}
		}
		
		if (seopress_google_analytics_cd_post_type_option() !='') {
			if (seopress_google_analytics_cd_post_type_option() !='none') {
				if (is_single()) {
					$seopress_google_analytics_config['cd']['cd_cpt'] = "'".seopress_google_analytics_cd_post_type_option()."': 'cd_cpt',";

					$seopress_google_analytics_event['cd_cpt'] = "gtag('event', '".__('Post types','wp-seopress')."', {'cd_cpt': '".get_post_type()."', 'non_interaction': true});";
					
					$seopress_google_analytics_config['cd']['cd_cpt'] = apply_filters('seopress_gtag_cd_cpt_cf', $seopress_google_analytics_config['cd']['cd_cpt']);
					
					$seopress_google_analytics_event['cd_cpt'] = apply_filters('seopress_gtag_cd_cpt_ev', $seopress_google_analytics_event['cd_cpt']);
				}
			}
		}
		
		if (seopress_google_analytics_cd_logged_in_user_option() !='') {
			if (seopress_google_analytics_cd_logged_in_user_option() !='none') {
				if (wp_get_current_user()->ID) {
					$seopress_google_analytics_config['cd']['cd_logged_in'] = "'".seopress_google_analytics_cd_logged_in_user_option()."': 'cd_logged_in',";

					$seopress_google_analytics_event['cd_logged_in'] = "gtag('event', '".__('Connected users','wp-seopress')."', {'cd_logged_in': '".wp_get_current_user()->ID."', 'non_interaction': true});";
					
					$seopress_google_analytics_config['cd']['cd_logged_in'] = apply_filters('seopress_gtag_cd_logged_in_cf', $seopress_google_analytics_config['cd']['cd_logged_in']);
					
					$seopress_google_analytics_event['cd_logged_in'] = apply_filters('seopress_gtag_cd_logged_in_ev', $seopress_google_analytics_event['cd_logged_in']);
				}
			}
		}
		
		if (!empty($seopress_google_analytics_config['cd']['cd_logged_in']) ||
				!empty($seopress_google_analytics_config['cd']['cd_cpt']) ||
				!empty($seopress_google_analytics_config['cd']['cd_tags']) ||
				!empty($seopress_google_analytics_config['cd']['cd_categories']) ||
				!empty($seopress_google_analytics_config['cd']['cd_author']) ||
				(!empty($seopress_google_analytics_config['cd']['cd_hook']) && !empty($seopress_google_analytics_event['cd_hook']))) {
			$seopress_google_analytics_config['cd']['cd_end'] = "}, ";
		} 
		else {
			$seopress_google_analytics_config['cd']['cd_start'] = '';
		}

		//External links
		if (seopress_google_analytics_link_tracking_enable_option() !='') {
			if (seopress_google_analytics_link_tracking_enable_option() !='') {
				$seopress_google_analytics_click_event['link_tracking'] =
"window.addEventListener('load', function () {
    var links = document.querySelectorAll('a[target=\"_blank\"]');
    for (let i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function(e) {
            gtag('event', 'click', {'event_category': 'external links','event_label' : this.href});
        });
    }
});
";
				$seopress_google_analytics_click_event['link_tracking'] = apply_filters('seopress_gtag_link_tracking_ev', $seopress_google_analytics_click_event['link_tracking']);
				$seopress_google_analytics_html .= $seopress_google_analytics_click_event['link_tracking'];
			}
		}
		
		//Downloads tracking
		if (seopress_google_analytics_download_tracking_enable_option() !='') {
			if (seopress_google_analytics_download_tracking_option() !='') {
				$seopress_google_analytics_click_event['download_tracking'] = 
"jQuery(document).ready(function() {
	jQuery('a').filter(function() {
		return this.href.match(/.*\.(".seopress_google_analytics_download_tracking_option().")(\?.*)?$/);
	}).click(function(e) {
		gtag('event', 'click', {'event_category': 'downloads','event_label' : this.href});
	});
});
";
				$seopress_google_analytics_click_event['download_tracking'] = apply_filters('seopress_gtag_download_tracking_ev', $seopress_google_analytics_click_event['download_tracking']);
				$seopress_google_analytics_html .= $seopress_google_analytics_click_event['download_tracking'];
			}
		}
		
		//Affiliate tracking
		if (seopress_google_analytics_affiliate_tracking_enable_option() !='') {
			if (seopress_google_analytics_affiliate_tracking_option() !='') {
				$seopress_google_analytics_click_event['outbound_tracking'] = 
"jQuery(document).ready(function() {
	jQuery('a').filter(function() {
    	return this.href.match(/(?:\/".seopress_google_analytics_affiliate_tracking_option()."\/)/gi);
    }).click(function(e) {
		gtag('event', 'click', {'event_category': 'outbound/affiliate','event_label' : this.href});
    });
});
";
				$seopress_google_analytics_click_event['outbound_tracking'] = apply_filters('seopress_gtag_outbound_tracking_ev', $seopress_google_analytics_click_event['outbound_tracking']);
				$seopress_google_analytics_html .= $seopress_google_analytics_click_event['outbound_tracking'];
			}
		}
		
		//Anonymize IP
		if (seopress_google_analytics_ip_anonymization_option() =='1') {
			$seopress_google_analytics_config['anonymize_ip'] = "'anonymize_ip': true,";
			$seopress_google_analytics_config['anonymize_ip'] = apply_filters('seopress_gtag_anonymize_ip', $seopress_google_analytics_config['anonymize_ip']);
		}		

		//Send data
		$features = '';
		if (!empty($seopress_google_analytics_config['cd']['cd_logged_in']) ||
				!empty($seopress_google_analytics_config['cd']['cd_cpt']) ||
				!empty($seopress_google_analytics_config['cd']['cd_tags']) ||
				!empty($seopress_google_analytics_config['cd']['cd_categories']) ||
				!empty($seopress_google_analytics_config['cd']['cd_author']) ||
				!empty($seopress_google_analytics_config['cd']['cd_hook'])) {
			$seopress_google_analytics_config['cd']['cd_start'] = "'custom_map': {";
		}
		if (!empty($seopress_google_analytics_config)) {
			if (!empty($seopress_google_analytics_config['cd']['cd_start'])) {
				array_unshift($seopress_google_analytics_config['cd'], $seopress_google_analytics_config['cd']['cd_start']);
				unset($seopress_google_analytics_config['cd']['cd_start']);
			}
			$features = ', {';
			foreach ($seopress_google_analytics_config as $key => $feature) {
				if ($key =='cd') {
					foreach ($feature as $_key => $cd) {
						$features .= $cd;
					}
				} else {
					$features .= $feature;
				}
			}
			$features .= '}';
		}

		//UA
		$seopress_gtag_ua = "gtag('config', '".seopress_google_analytics_ua_option()."' ".$features.");";
		$seopress_gtag_ua = apply_filters('seopress_gtag_ua', $seopress_gtag_ua);
		$seopress_google_analytics_html .= $seopress_gtag_ua;
		$seopress_google_analytics_html .= "\n";

		//Ads
		if (seopress_google_analytics_ads_option() !='') {
			$seopress_gtag_ads = "gtag('config', '".seopress_google_analytics_ads_option()."');";
			$seopress_gtag_ads = apply_filters('seopress_gtag_ads', $seopress_gtag_ads);
			$seopress_google_analytics_html .= $seopress_gtag_ads;
			$seopress_google_analytics_html .= "\n";
		}

		$events = '';
		if (!empty($seopress_google_analytics_event)) {
			foreach ($seopress_google_analytics_event as $event) {
				$seopress_google_analytics_html .= $event;
				$seopress_google_analytics_html .= "\n";
			}
		}
		
		$seopress_google_analytics_html .= "</script>";
		$seopress_google_analytics_html .= "\n";
		
		$seopress_google_analytics_html = apply_filters('seopress_gtag_html', $seopress_google_analytics_html);

		if ($echo == true) {
			echo $seopress_google_analytics_html;
		} else {
			return $seopress_google_analytics_html;
		}
	}
}
add_action('seopress_google_analytics_html', 'seopress_google_analytics_js', 10, 1);

function seopress_google_analytics_js_arguments() {
	$echo = true;
	do_action('seopress_google_analytics_html', $echo);
}

function seopress_custom_tracking_hook() {
	$data['custom'] = '';
	$data['custom'] = apply_filters( 'seopress_custom_tracking', $data['custom'] );
	echo $data['custom'];
}

//Build custom code after body tag opening
function seopress_google_analytics_body_code($echo) {
	if (seopress_google_analytics_other_tracking_body_option() !='') {
		$seopress_html_body = seopress_google_analytics_other_tracking_body_option();
		$seopress_html_body = apply_filters('seopress_custom_body_tracking', $seopress_html_body);
		if ($echo == true) {
			echo "\n".$seopress_html_body;
		} else {
			return "\n".$seopress_html_body;
		}
	}
}
add_action('seopress_custom_body_tracking_html', 'seopress_google_analytics_body_code', 10, 1);

function seopress_custom_tracking_body_hook() {
	$echo = true;
	do_action('seopress_custom_body_tracking_html', $echo);
}

//Build custom code in head
function seopress_google_analytics_head_code($echo) {
	if (seopress_google_analytics_other_tracking_option() !='') {
		$seopress_html_head = seopress_google_analytics_other_tracking_option();
		$seopress_html_head = apply_filters('seopress_gtag_after_additional_tracking_html', $seopress_html_head);

		if ($echo == true) {
			echo "\n".$seopress_html_head;
		} else {
			return "\n".$seopress_html_head;
		}
	}
}
add_action('seopress_custom_head_tracking_html', 'seopress_google_analytics_head_code', 10, 1);

function seopress_custom_tracking_head_hook() {
	$echo = true;
	do_action('seopress_custom_head_tracking_html', $echo);
}

if (seopress_google_analytics_half_disable_option() =='1' || (((isset($_COOKIE["seopress-user-consent-accept"]) && $_COOKIE["seopress-user-consent-accept"] =='1') && seopress_google_analytics_disable_option() =='1') || (seopress_google_analytics_disable_option() !='1'))) { //User consent cookie OK
	if (is_user_logged_in()) {
		global $wp_roles;
		
		//Get current user role
		if(isset(wp_get_current_user()->roles[0])) {
			$seopress_user_role = wp_get_current_user()->roles[0];
			//If current user role matchs values from SEOPress GA settings then apply
			if (function_exists('seopress_google_analytics_roles_option') && seopress_google_analytics_roles_option() !='') {
				if( array_key_exists( $seopress_user_role, seopress_google_analytics_roles_option())) {
					//do nothing
				} else {
					if (seopress_google_analytics_enable_option() =='1' && seopress_google_analytics_ua_option() !='') {
						add_action( 'wp_head', 'seopress_google_analytics_js_arguments', 999, 1 );
						add_action( 'wp_head', 'seopress_custom_tracking_hook', 1000, 1 );
					}	
					add_action( 'wp_head', 'seopress_custom_tracking_head_hook', 1010, 1 );
					add_action( 'wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1 );
				}
			} else {
				if (seopress_google_analytics_enable_option() =='1' && seopress_google_analytics_ua_option() !='') {
					add_action( 'wp_head', 'seopress_google_analytics_js_arguments', 999, 1 );
					add_action( 'wp_head', 'seopress_custom_tracking_hook', 1000, 1 );
				}
				add_action( 'wp_head', 'seopress_custom_tracking_head_hook', 1010, 1 );
				add_action( 'wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1 );
			}
		}
	} else {
		if (seopress_google_analytics_enable_option() =='1' && seopress_google_analytics_ua_option() !='') {
			add_action( 'wp_head', 'seopress_google_analytics_js_arguments', 999, 1 );
			add_action( 'wp_head', 'seopress_custom_tracking_hook', 1000, 1 );
		}
		add_action( 'wp_head', 'seopress_custom_tracking_head_hook', 1010, 1 );
		add_action( 'wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1 );
	}
}