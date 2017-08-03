<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Google Analytics
//=================================================================================================
//Enabled
function seopress_google_analytics_enable_option() {
	$seopress_google_analytics_enable_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_enable_option ) ) {
		foreach ($seopress_google_analytics_enable_option as $key => $seopress_google_analytics_enable_value)
			$options[$key] = $seopress_google_analytics_enable_value;
		 if (isset($seopress_google_analytics_enable_option['seopress_google_analytics_enable'])) { 
		 	return $seopress_google_analytics_enable_option['seopress_google_analytics_enable'];
		 }
	}
}

//UA
function seopress_google_analytics_ua_option() {
	$seopress_google_analytics_ua_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_ua_option ) ) {
		foreach ($seopress_google_analytics_ua_option as $key => $seopress_google_analytics_ua_value)
			$options[$key] = $seopress_google_analytics_ua_value;
		 if (isset($seopress_google_analytics_ua_option['seopress_google_analytics_ua'])) { 
		 	return $seopress_google_analytics_ua_option['seopress_google_analytics_ua'];
		 }
	}
}

//User roles
function seopress_google_analytics_roles_option() {
	$seopress_google_analytics_roles_option = get_option("seopress_google_analytics_option_name");
	if ( ! empty ( $seopress_google_analytics_roles_option ) ) {
		foreach ($seopress_google_analytics_roles_option as $key => $seopress_google_analytics_roles_value)
			$options[$key] = $seopress_google_analytics_roles_value;
		 if (isset($seopress_google_analytics_roles_option['seopress_google_analytics_roles'])) { 
		 	return $seopress_google_analytics_roles_option['seopress_google_analytics_roles'];
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

//Events tracking Enable
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
function seopress_google_analytics_js() {
	$seopress_google_analytics_html = "\n";
	$seopress_google_analytics_html .=
"<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
";
	if (seopress_google_analytics_ua_option() && seopress_google_analytics_ip_anonymization_option() =='1') {
		$seopress_google_analytics_html .= "ga('create', '".seopress_google_analytics_ua_option()."', 'auto', {anonymizeIp: true});";
		$seopress_google_analytics_html .= "\n";
	} elseif (seopress_google_analytics_ua_option()) {
		$seopress_google_analytics_html .= "ga('create', '".seopress_google_analytics_ua_option()."', 'auto');";
		$seopress_google_analytics_html .= "\n";
	}
	
	if (seopress_google_analytics_cross_enable_option() =='1' && seopress_google_analytics_cross_domain_option()) {
		$seopress_google_analytics_html .= "ga('require', 'linker');";
		$seopress_google_analytics_html .= "\n";
		$seopress_google_analytics_html .= "ga('linker:autoLink', ['".seopress_google_analytics_cross_domain_option()."'], false, true);";
		$seopress_google_analytics_html .= "\n";
	}

	if (seopress_google_analytics_remarketing_option() =='1') {
		$seopress_google_analytics_html .= "ga('require', 'displayfeatures');";
		$seopress_google_analytics_html .= "\n";
	}

	if (seopress_google_analytics_link_attribution_option() =='1') {
		$seopress_google_analytics_html .= "ga('require', 'linkid', 'linkid.js');";
		$seopress_google_analytics_html .= "\n";
	}

	if (seopress_google_analytics_cd_author_option() !='') {
		if (seopress_google_analytics_cd_author_option() !='none') {
			if (is_singular()) {
				$seopress_google_analytics_html .= "ga('set', '".seopress_google_analytics_cd_author_option()."', '".get_the_author()."');";
				$seopress_google_analytics_html .= "\n";
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
				$seopress_google_analytics_html .= "ga('set', '".seopress_google_analytics_cd_category_option()."', '".$get_first_category."');";
				$seopress_google_analytics_html .= "\n";
			}
		}	
	}

	if (seopress_google_analytics_cd_tag_option() !='') {
		if (seopress_google_analytics_cd_tag_option() !='none') {
			if (is_single() && has_tag()) {
				$tags = get_the_tags();
				if ( ! empty( $tags ) ) {
					$seopress_comma_count = count($tags);
					foreach ($tags as $key => $value) {
						$get_tags .= esc_html( $value->name );
						if ( $key < $seopress_comma_count -1){
							$get_tags .= ', ';
						}
					}
				}
				$seopress_google_analytics_html .= "ga('set', '".seopress_google_analytics_cd_tag_option()."', '".$get_tags."');";
				$seopress_google_analytics_html .= "\n";
			}
		}
	}

	if (seopress_google_analytics_cd_post_type_option() !='') {
		if (seopress_google_analytics_cd_post_type_option() !='none') {
			if (is_single()) {
				$seopress_google_analytics_html .= "ga('set', '".seopress_google_analytics_cd_post_type_option()."', '".get_post_type()."');";
				$seopress_google_analytics_html .= "\n";
			}
		}
	}

	if (seopress_google_analytics_cd_logged_in_user_option() !='') {
		if (seopress_google_analytics_cd_logged_in_user_option() !='none') {
			if (wp_get_current_user()->user_login) {
				$seopress_google_analytics_html .= "ga('set', '".seopress_google_analytics_cd_logged_in_user_option()."', '".wp_get_current_user()->user_login."');";
				$seopress_google_analytics_html .= "\n";
			}
		}
	}

	$seopress_google_analytics_html .= "ga('send', 'pageview');";
	$seopress_google_analytics_html .= "\n";

	if (seopress_google_analytics_download_tracking_enable_option() !='') {
		if (seopress_google_analytics_download_tracking_option() !='') {
			$seopress_google_analytics_html .= "				
				jQuery(document).ready(function() {
					jQuery('a').filter(function() {
		                return this.href.match(/.*\.(".seopress_google_analytics_download_tracking_option().")(\?.*)?$/);
		            }).click(function(e) {
		                ga('send','event', 'downloads', 'click', this.href);
		            });
		        });
            ";
			$seopress_google_analytics_html .= "\n";
		}
	}

	$seopress_google_analytics_html .= "</script>";
	$seopress_google_analytics_html .= "\n";

	echo $seopress_google_analytics_html;
}

if (seopress_google_analytics_enable_option() =='1' && seopress_google_analytics_ua_option() !='') {
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
					add_action( 'wp_head', 'seopress_google_analytics_js', 999 );
				}
			} else {
				add_action( 'wp_head', 'seopress_google_analytics_js', 999 );
			}
		}
	} else {
		add_action( 'wp_head', 'seopress_google_analytics_js', 999 );
	}
}
