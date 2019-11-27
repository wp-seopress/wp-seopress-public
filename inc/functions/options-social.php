<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Social
//=================================================================================================
//Knowledge Graph
//Type
function seopress_social_knowledge_type_option() {
	$seopress_social_knowledge_type_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_knowledge_type_option ) ) {
		foreach ($seopress_social_knowledge_type_option as $key => $seopress_social_knowledge_type_value)
			$options[$key] = $seopress_social_knowledge_type_value;
		 if (isset($seopress_social_knowledge_type_option['seopress_social_knowledge_type'])) { 
		 	return $seopress_social_knowledge_type_option['seopress_social_knowledge_type'];
		 }
	}
}

//Person name
function seopress_social_knowledge_name_option() {
	$seopress_social_knowledge_name_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_knowledge_name_option ) ) {
		foreach ($seopress_social_knowledge_name_option as $key => $seopress_social_knowledge_name_value)
			$options[$key] = $seopress_social_knowledge_name_value;
		 if (isset($seopress_social_knowledge_name_option['seopress_social_knowledge_name'])) { 
		 	return $seopress_social_knowledge_name_option['seopress_social_knowledge_name'];
		 }
	}
}

//Logo
function seopress_social_knowledge_img_option() {
	$seopress_social_knowledge_img_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_knowledge_img_option ) ) {
		foreach ($seopress_social_knowledge_img_option as $key => $seopress_social_knowledge_img_value)
			$options[$key] = $seopress_social_knowledge_img_value;
		 if (isset($seopress_social_knowledge_img_option['seopress_social_knowledge_img'])) { 
		 	return $seopress_social_knowledge_img_option['seopress_social_knowledge_img'];
		 }
	}
}
//Phone number
function seopress_social_knowledge_phone_number_option() {
	$seopress_social_knowledge_phone_number_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_knowledge_phone_number_option ) ) {
		foreach ($seopress_social_knowledge_phone_number_option as $key => $seopress_social_knowledge_phone_number_value)
			$options[$key] = $seopress_social_knowledge_phone_number_value;
		 if (isset($seopress_social_knowledge_phone_number_option['seopress_social_knowledge_phone'])) { 
		 	return $seopress_social_knowledge_phone_number_option['seopress_social_knowledge_phone'];
		 }
	}
}
//Contact type
function seopress_social_knowledge_contact_type_option() {
	$seopress_social_knowledge_contact_type_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_knowledge_contact_type_option ) ) {
		foreach ($seopress_social_knowledge_contact_type_option as $key => $seopress_social_knowledge_contact_type_value)
			$options[$key] = $seopress_social_knowledge_contact_type_value;
		 if (isset($seopress_social_knowledge_contact_type_option['seopress_social_knowledge_contact_type'])) { 
		 	return $seopress_social_knowledge_contact_type_option['seopress_social_knowledge_contact_type'];
		 }
	}
}
//Contact option
function seopress_social_knowledge_contact_option_option() {
	$seopress_social_knowledge_contact_option_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_knowledge_contact_option_option ) ) {
		foreach ($seopress_social_knowledge_contact_option_option as $key => $seopress_social_knowledge_contact_option_value)
			$options[$key] = $seopress_social_knowledge_contact_option_value;
		 if (isset($seopress_social_knowledge_contact_option_option['seopress_social_knowledge_contact_option'])) { 
		 	return $seopress_social_knowledge_contact_option_option['seopress_social_knowledge_contact_option'];
		 }
	}
}

//Accounts
//Facebook
function seopress_social_accounts_facebook_option() {
	$seopress_social_accounts_facebook_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_facebook_option ) ) {
		foreach ($seopress_social_accounts_facebook_option as $key => $seopress_social_accounts_facebook_value)
			$options[$key] = $seopress_social_accounts_facebook_value;
		 if (isset($seopress_social_accounts_facebook_option['seopress_social_accounts_facebook'])) { 
		 	return $seopress_social_accounts_facebook_option['seopress_social_accounts_facebook'];
		 }
	}
}

//Twitter
function seopress_social_accounts_twitter_option() {
	$seopress_social_accounts_twitter_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_twitter_option ) ) {
		foreach ($seopress_social_accounts_twitter_option as $key => $seopress_social_accounts_twitter_value)
			$options[$key] = $seopress_social_accounts_twitter_value;
		 if (isset($seopress_social_accounts_twitter_option['seopress_social_accounts_twitter'])) { 
		 	return $seopress_social_accounts_twitter_option['seopress_social_accounts_twitter'];
		 }
	}
}

//Pinterest
function seopress_social_accounts_pinterest_option() {
	$seopress_social_accounts_pinterest_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_pinterest_option ) ) {
		foreach ($seopress_social_accounts_pinterest_option as $key => $seopress_social_accounts_pinterest_value)
			$options[$key] = $seopress_social_accounts_pinterest_value;
		 if (isset($seopress_social_accounts_pinterest_option['seopress_social_accounts_pinterest'])) { 
		 	return $seopress_social_accounts_pinterest_option['seopress_social_accounts_pinterest'];
		 }
	}
}

//Instagram
function seopress_social_accounts_instagram_option() {
	$seopress_social_accounts_instagram_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_instagram_option ) ) {
		foreach ($seopress_social_accounts_instagram_option as $key => $seopress_social_accounts_instagram_value)
			$options[$key] = $seopress_social_accounts_instagram_value;
		 if (isset($seopress_social_accounts_instagram_option['seopress_social_accounts_instagram'])) { 
		 	return $seopress_social_accounts_instagram_option['seopress_social_accounts_instagram'];
		 }
	}
}

//YouTube
function seopress_social_accounts_youtube_option() {
	$seopress_social_accounts_youtube_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_youtube_option ) ) {
		foreach ($seopress_social_accounts_youtube_option as $key => $seopress_social_accounts_youtube_value)
			$options[$key] = $seopress_social_accounts_youtube_value;
		 if (isset($seopress_social_accounts_youtube_option['seopress_social_accounts_youtube'])) { 
		 	return $seopress_social_accounts_youtube_option['seopress_social_accounts_youtube'];
		 }
	}
}

//LinkedIn
function seopress_social_accounts_linkedin_option() {
	$seopress_social_accounts_linkedin_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_linkedin_option ) ) {
		foreach ($seopress_social_accounts_linkedin_option as $key => $seopress_social_accounts_linkedin_value)
			$options[$key] = $seopress_social_accounts_linkedin_value;
		 if (isset($seopress_social_accounts_linkedin_option['seopress_social_accounts_linkedin'])) { 
		 	return $seopress_social_accounts_linkedin_option['seopress_social_accounts_linkedin'];
		 }
	}
}

//MySpace
function seopress_social_accounts_myspace_option() {
	$seopress_social_accounts_myspace_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_myspace_option ) ) {
		foreach ($seopress_social_accounts_myspace_option as $key => $seopress_social_accounts_myspace_value)
			$options[$key] = $seopress_social_accounts_myspace_value;
		 if (isset($seopress_social_accounts_myspace_option['seopress_social_accounts_myspace'])) { 
		 	return $seopress_social_accounts_myspace_option['seopress_social_accounts_myspace'];
		 }
	}
}

//Soundcloud
function seopress_social_accounts_soundcloud_option() {
	$seopress_social_accounts_soundcloud_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_soundcloud_option ) ) {
		foreach ($seopress_social_accounts_soundcloud_option as $key => $seopress_social_accounts_soundcloud_value)
			$options[$key] = $seopress_social_accounts_soundcloud_value;
		 if (isset($seopress_social_accounts_soundcloud_option['seopress_social_accounts_soundcloud'])) { 
		 	return $seopress_social_accounts_soundcloud_option['seopress_social_accounts_soundcloud'];
		 }
	}
}

//Tumblr
function seopress_social_accounts_tumblr_option() {
	$seopress_social_accounts_tumblr_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_tumblr_option ) ) {
		foreach ($seopress_social_accounts_tumblr_option as $key => $seopress_social_accounts_tumblr_value)
			$options[$key] = $seopress_social_accounts_tumblr_value;
		 if (isset($seopress_social_accounts_tumblr_option['seopress_social_accounts_tumblr'])) { 
		 	return $seopress_social_accounts_tumblr_option['seopress_social_accounts_tumblr'];
		 }
	}
}

function seopress_social_accounts_jsonld_hook() {

	$seopress_comma_array = array();

	//If enable (!=none)
	if (seopress_social_knowledge_type_option() !='none') {
		if (seopress_social_accounts_facebook_option() !='') { 
	 		$seopress_social_accounts_facebook_option = json_encode(seopress_social_accounts_facebook_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_facebook_option);	
		}	
		if (seopress_social_accounts_twitter_option() !='') { 
	 		$seopress_social_accounts_twitter_option = json_encode('https://twitter.com/'.seopress_social_accounts_twitter_option());	
	 	 	array_push($seopress_comma_array, $seopress_social_accounts_twitter_option);	
		}	
		if (seopress_social_accounts_pinterest_option() !='') { 
	 		$seopress_social_accounts_pinterest_option = json_encode(seopress_social_accounts_pinterest_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_pinterest_option);	
		}
		if (seopress_social_accounts_instagram_option() !='') { 
	 		$seopress_social_accounts_instagram_option = json_encode(seopress_social_accounts_instagram_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_instagram_option);	
		}
		if (seopress_social_accounts_youtube_option() !='') { 
	 		$seopress_social_accounts_youtube_option = json_encode(seopress_social_accounts_youtube_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_youtube_option);	
		}
		if (seopress_social_accounts_linkedin_option() !='') { 
	 		$seopress_social_accounts_linkedin_option = json_encode(seopress_social_accounts_linkedin_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_linkedin_option);	
		}
		if (seopress_social_accounts_myspace_option() !='') { 
	 		$seopress_social_accounts_myspace_option = json_encode(seopress_social_accounts_myspace_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_myspace_option);	
		}
		if (seopress_social_accounts_soundcloud_option() !='') { 
	 		$seopress_social_accounts_soundcloud_option = json_encode(seopress_social_accounts_soundcloud_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_soundcloud_option);	
		}
		if (seopress_social_accounts_tumblr_option() !='') { 
	 		$seopress_social_accounts_tumblr_option = json_encode(seopress_social_accounts_tumblr_option());
	 		array_push($seopress_comma_array, $seopress_social_accounts_tumblr_option);	
		}
		if (seopress_social_knowledge_type_option() !='') {
			$seopress_social_knowledge_type_option = json_encode(seopress_social_knowledge_type_option());
		} else {
			$seopress_social_knowledge_type_option = json_encode('Organization');
		}
		if (seopress_social_knowledge_name_option() !='' && seopress_social_knowledge_type_option() !='none') {
			$seopress_social_knowledge_name_option = json_encode(seopress_social_knowledge_name_option());
		} elseif (seopress_social_knowledge_type_option() !='none') {
			$seopress_social_knowledge_name_option = json_encode(get_bloginfo('name'));
		}
		if (seopress_social_knowledge_img_option() !='' && seopress_social_knowledge_type_option() =='Organization') {
			$seopress_social_knowledge_img_option = json_encode(seopress_social_knowledge_img_option());
		}
		if (seopress_social_knowledge_phone_number_option() !='') {
			$seopress_social_knowledge_phone_number_option = json_encode(seopress_social_knowledge_phone_number_option());
		}
		if (seopress_social_knowledge_contact_type_option() !='') {
			$seopress_social_knowledge_contact_type_option = json_encode(seopress_social_knowledge_contact_type_option());
		}
		if (seopress_social_knowledge_contact_option_option() !='') {
			$seopress_social_knowledge_contact_option_option = json_encode(seopress_social_knowledge_contact_option_option());
		}

		$html = '<script type="application/ld+json">';
		$html .= '{"@context" : "'.seopress_check_ssl().'schema.org","@type" : '.$seopress_social_knowledge_type_option.',';
		if (seopress_social_knowledge_img_option() !='' && seopress_social_knowledge_type_option() =='Organization') {
			$html .= '"logo": '.$seopress_social_knowledge_img_option.',';
		}
		$html .= '"name" : '.$seopress_social_knowledge_name_option.',"url" : '.json_encode(get_home_url());
		
		if (seopress_social_knowledge_type_option() =='Organization' 
			&& seopress_social_knowledge_phone_number_option() !=''
			&& seopress_social_knowledge_contact_type_option() !=''
			) {
			if ($seopress_social_knowledge_phone_number_option && $seopress_social_knowledge_contact_type_option ) {
				$html .= ',"contactPoint": [{
					"@type": "ContactPoint",
					"telephone": '.$seopress_social_knowledge_phone_number_option.',';
					if ($seopress_social_knowledge_contact_option_option !='' && $seopress_social_knowledge_contact_option_option !='None') {
						$html .= '"contactOption": '.$seopress_social_knowledge_contact_option_option.',';
					}
					$html .= '"contactType": '.$seopress_social_knowledge_contact_type_option.'
				}]';
			}
		}

		if (seopress_social_accounts_facebook_option() !='' || seopress_social_accounts_twitter_option() !='' ||  seopress_social_accounts_pinterest_option() !='' || seopress_social_accounts_instagram_option() !='' || seopress_social_accounts_youtube_option() !='' || seopress_social_accounts_linkedin_option() !='' || seopress_social_accounts_myspace_option() !='' || seopress_social_accounts_soundcloud_option() !='' || seopress_social_accounts_tumblr_option() !='' ) {
			$html .= ',"sameAs" : [';
			$seopress_comma_count = count($seopress_comma_array);
			for ($i = 0; $i < $seopress_comma_count; $i++) {
				$html .= $seopress_comma_array[$i];
			   	if ($i < ($seopress_comma_count - 1)) {
			    	$html .= ', ';
			   	}
			}
			$html .= ']';
		}
		$html .= '}';
		$html .= '</script>';
		$html .= "\n";

		$html = apply_filters('seopress_schemas_organization_html', $html);
		echo $html;
	}
}
add_action( 'wp_head', 'seopress_social_accounts_jsonld_hook', 1 );

//Website Schema.org in JSON-LD - Sitelinks
if (function_exists('seopress_titles_nositelinkssearchbox_option') && seopress_titles_nositelinkssearchbox_option() =='1') {
	//do not display searchbox schema	
} else {
	function seopress_social_website_option() {
		$target = get_home_url().'/?s={search_term_string}';
		echo '<script type="application/ld+json">';
		echo '{
				"@context": "'.seopress_check_ssl().'schema.org",
				"@type": "WebSite",
				"url" : '.json_encode(get_home_url()).',
				"potentialAction": {
					"@type": "SearchAction",
					"target": '.json_encode($target).',
					"query-input": "required name=search_term_string"
				}
			}';
		echo '</script>';
		echo "\n";
	}
	if (is_home() || is_front_page()) {
		add_action( 'wp_head', 'seopress_social_website_option', 1 );
	}
}

//Facebook
//OG Enabled
function seopress_social_facebook_og_option() {
	$seopress_social_facebook_og_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_facebook_og_option ) ) {
		foreach ($seopress_social_facebook_og_option as $key => $seopress_social_facebook_og_value)
			$options[$key] = $seopress_social_facebook_og_value;
		 if (isset($seopress_social_facebook_og_option['seopress_social_facebook_og'])) { 
		 	return $seopress_social_facebook_og_option['seopress_social_facebook_og'];
		 }
	}
}

//OG URL
function seopress_social_facebook_og_url_hook() {
	if (seopress_social_facebook_og_option() =='1') {

		global $wp;
		if (seopress_advanced_advanced_trailingslash_option()) {
			$current_url = home_url(add_query_arg(array(), $wp->request));
		} else {
			$current_url = trailingslashit(home_url(add_query_arg(array(), $wp->request)));
		}

		if (is_search()) {
			$seopress_social_og_url = '<meta property="og:url" content="'.htmlspecialchars(urldecode(get_home_url().'/search/'.get_search_query())).'" />';
		} else {
			$seopress_social_og_url = '<meta property="og:url" content="'.htmlspecialchars(urldecode($current_url),ENT_COMPAT, 'UTF-8').'" />';
		}

		//Hook on post OG URL - 'seopress_social_og_url'
		if (has_filter('seopress_social_og_url')) {
			$seopress_social_og_url = apply_filters('seopress_social_og_url', $seopress_social_og_url);
	    }
		
		echo $seopress_social_og_url."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_url_hook', 1 );

//OG Site Name
function seopress_social_facebook_og_site_name_hook() {
	if (seopress_social_facebook_og_option() =='1' && get_bloginfo('name') !='') {

		$seopress_social_og_site_name = '<meta property="og:site_name" content="'.get_bloginfo('name').'" />';
		
		//Hook on post OG site name - 'seopress_social_og_site_name'
		if (has_filter('seopress_social_og_site_name')) {
			$seopress_social_og_site_name = apply_filters('seopress_social_og_site_name', $seopress_social_og_site_name);
	    }

		echo $seopress_social_og_site_name."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_site_name_hook', 1 );

//OG Locale
function seopress_social_facebook_og_locale_hook() {
	if (seopress_social_facebook_og_option() =='1') {
		$seopress_social_og_locale = '<meta property="og:locale" content="'.get_locale().'" />';

		//Polylang
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' )) {
			//@credits Polylang
			if (did_action('pll_init') && function_exists('PLL')) {
				$alternates = array();

				foreach ( PLL()->model->get_languages_list() as $language ) {
					if ( PLL()->curlang->slug !== $language->slug && PLL()->links->get_translation_url( $language ) && isset( $language->facebook ) ) {
						$alternates[] = $language->facebook;
					}
				}

				// There is a risk that 2 languages have the same Facebook locale. So let's make sure to output each locale only once.
				$alternates = array_unique( $alternates );

				foreach ( $alternates as $lang ) {
					$seopress_social_og_locale .= "\n";
					$seopress_social_og_locale .= '<meta property="og:locale:alternate" content="'.$lang.'" />';
				}
			}
		}

		//Hook on post OG locale - 'seopress_social_og_locale'
		if (has_filter('seopress_social_og_locale')) {
			$seopress_social_og_locale = apply_filters('seopress_social_og_locale', $seopress_social_og_locale);
	    }
		
		if (isset($seopress_social_og_locale) && $seopress_social_og_locale !='') {
			echo $seopress_social_og_locale."\n";
		}
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_locale_hook', 1 );

//OG Type
function seopress_social_facebook_og_type_hook() {
	if (seopress_social_facebook_og_option() =='1') {
		if (is_home() || is_front_page()) {
			$seopress_social_og_type = '<meta property="og:type" content="website" />';
		} elseif (is_singular('product') || is_singular('download')) {
			$seopress_social_og_type = '<meta property="og:type" content="product" />';
		} elseif (is_singular()) {
			global $post;
			$seopress_video_disabled     	= get_post_meta($post->ID,'_seopress_video_disabled', true);
		  	$seopress_video     			= get_post_meta($post->ID,'_seopress_video');

		  	if (!empty($seopress_video[0][0]['url']) && $seopress_video_disabled =='') {
				$seopress_social_og_type = '<meta property="og:type" content="video.other" />';
		  	} else {
		  		$seopress_social_og_type = '<meta property="og:type" content="article" />';
		  	}
		} 
		elseif (is_search() || is_archive() || is_404()) {
			$seopress_social_og_type = '<meta property="og:type" content="object" />';
		}
		if (isset($seopress_social_og_type)) {
			//Hook on post OG type - 'seopress_social_og_type'
			if (has_filter('seopress_social_og_type')) {
				$seopress_social_og_type = apply_filters('seopress_social_og_type', $seopress_social_og_type);
		    }
			echo $seopress_social_og_type."\n";
		}
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_type_hook', 1 );

//Article Author / Article Publisher
function seopress_social_facebook_og_author_hook() {
	if (seopress_social_facebook_og_option() =='1' && seopress_social_accounts_facebook_option() !='') {
		if (is_singular() && !is_home() && !is_front_page()) {
			global $post;
			$seopress_video_disabled     	= get_post_meta($post->ID,'_seopress_video_disabled', true);
		  	$seopress_video     			= get_post_meta($post->ID,'_seopress_video');

		  	if (!empty($seopress_video[0][0]['url']) && $seopress_video_disabled =='') {		
				//do nothing
			} else {
				$seopress_social_og_author = '<meta property="article:author" content="'.seopress_social_accounts_facebook_option().'" />';
				$seopress_social_og_author .= "\n";
				$seopress_social_og_author .= '<meta property="article:publisher" content="'.seopress_social_accounts_facebook_option().'" />';
			}
		}
		if (isset($seopress_social_og_author)) {
			//Hook on post OG author - 'seopress_social_og_author'
			if (has_filter('seopress_social_og_author')) {
				$seopress_social_og_author = apply_filters('seopress_social_og_author', $seopress_social_og_author);
		    }
			echo $seopress_social_og_author."\n";
		}
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_author_hook', 1 );

//Facebook Title
function seopress_social_fb_title_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_social_fb_title = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_fb_title',true);
	} else {
		$_seopress_social_fb_title = get_post_meta(get_the_ID(),'_seopress_social_fb_title',true);
	}
	if ($_seopress_social_fb_title != '') {
		return $_seopress_social_fb_title;
	}
}

function seopress_social_fb_title_term_option() {
	$_seopress_social_fb_title = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_title',true);
	if ($_seopress_social_fb_title != '') {
		return $_seopress_social_fb_title;
	}
}

function seopress_social_fb_title_home_option() {
	$_seopress_social_fb_title = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_title',true);
	if ($_seopress_social_fb_title != '') {
		return $_seopress_social_fb_title;
	}
}

function seopress_social_fb_title_hook() {
	if (seopress_social_facebook_og_option() =='1') {
		//Init
		$seopress_social_og_title ='';
		
		if (is_home()) {
			if (seopress_social_fb_title_home_option() !='') {
				$seopress_social_og_title .= '<meta property="og:title" content="'.seopress_social_fb_title_home_option().'" />'; 
		 		$seopress_social_og_title .= "\n";
		 	} elseif (function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
				$seopress_social_og_title .= '<meta property="og:title" content="'.esc_attr(seopress_titles_the_title()).'" />'; 
		 		$seopress_social_og_title .= "\n";
		 	}
		} elseif (is_tax() || is_category() || is_tag()) {
			if (seopress_social_fb_title_term_option() !='') {
				$seopress_social_og_title .= '<meta property="og:title" content="'.seopress_social_fb_title_term_option().'" />'; 
	 			$seopress_social_og_title .= "\n";
	 		} else {
	 			$seopress_social_og_title .= '<meta property="og:title" content="'.single_term_title('', false).' - '.get_bloginfo('name').'" />'; 
	 			$seopress_social_og_title .= "\n";
	 		}
	 	} elseif (is_singular() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_title_post_option() !='') { 
	 		$seopress_social_og_title .= '<meta property="og:title" content="'.seopress_social_fb_title_post_option().'" />'; 
	 		$seopress_social_og_title .= "\n";
	 	} elseif(function_exists("is_shop") && is_shop() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_title_post_option() !='') {
	 		$seopress_social_og_title .= '<meta property="og:title" content="'.seopress_social_fb_title_post_option().'" />'; 
	 		$seopress_social_og_title .= "\n";
		} elseif (seopress_social_facebook_og_option() =='1' && function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
	 		$seopress_social_og_title .= '<meta property="og:title" content="'.esc_attr(seopress_titles_the_title()).'" />'; 
		 	$seopress_social_og_title .= "\n";
		} elseif (seopress_social_facebook_og_option() =='1' && get_the_title() !='') { 
	 		$seopress_social_og_title .= '<meta property="og:title" content="'.the_title_attribute('echo=0').'" />'; 
	 		$seopress_social_og_title .= "\n";
		}

		//Hook on post OG title - 'seopress_social_og_title'
		if (has_filter('seopress_social_og_title')) {
			$seopress_social_og_title = apply_filters('seopress_social_og_title', $seopress_social_og_title);
	    }
	    if (isset($seopress_social_og_title) && $seopress_social_og_title !='') {
	    	echo $seopress_social_og_title;
	    }
	}
}
add_action( 'wp_head', 'seopress_social_fb_title_hook', 1 );

//Facebook Desc
function seopress_social_fb_desc_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_social_fb_desc = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_fb_desc',true);
	} else {
		$_seopress_social_fb_desc = get_post_meta(get_the_ID(),'_seopress_social_fb_desc',true);
	}
	if ($_seopress_social_fb_desc != '') {
		return $_seopress_social_fb_desc;
	}
}

function seopress_social_fb_desc_term_option() {
	$_seopress_social_fb_desc = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_desc',true);
	if ($_seopress_social_fb_desc != '') {
		return $_seopress_social_fb_desc;
	}
}

function seopress_social_fb_desc_home_option() {
	$_seopress_social_fb_desc = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_desc',true);
	if ($_seopress_social_fb_desc != '') {
		return $_seopress_social_fb_desc;
	}
}

function seopress_social_fb_desc_hook() {
	if (seopress_social_facebook_og_option() =='1') {
		global $post;
		//Init
		$seopress_social_og_desc ='';

		//Excerpt length
		$seopress_excerpt_length = 50;
		$seopress_excerpt_length = apply_filters('seopress_excerpt_length',$seopress_excerpt_length);

		setup_postdata( $post );
		if (is_home()) {
			if (seopress_social_fb_desc_home_option() !='') {
				$seopress_social_og_desc .= '<meta property="og:description" content="'.seopress_social_fb_desc_home_option().'" />'; 
		 		$seopress_social_og_desc .= "\n";
		 	} elseif (function_exists('seopress_titles_the_description_content') && seopress_titles_the_description_content() !='') {
		 		$seopress_social_og_desc .= '<meta property="og:description" content="'.seopress_titles_the_description_content().'" />'; 
		 		$seopress_social_og_desc .= "\n";
		 	}
		} elseif (is_tax() || is_category() || is_tag()) {
			if (seopress_social_fb_desc_term_option() !='') {
				$seopress_social_og_desc .= '<meta property="og:description" content="'.seopress_social_fb_desc_term_option().'" />'; 
		 		$seopress_social_og_desc .= "\n";
		 	} elseif (term_description() !='') {
		 		$seopress_social_og_desc .= '<meta property="og:description" content="'.wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(term_description())),$seopress_excerpt_length).' - '.get_bloginfo('name').'" />'; 
		 		$seopress_social_og_desc .= "\n";
		 	}
		} elseif (is_singular() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_desc_post_option() !='') { 
	 		$seopress_social_og_desc .= '<meta property="og:description" content="'.seopress_social_fb_desc_post_option().'" />'; 
	 		$seopress_social_og_desc .= "\n";
	 	} elseif(function_exists("is_shop") && is_shop() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_desc_post_option() !='') {
			$seopress_social_og_desc .= '<meta property="og:description" content="'.seopress_social_fb_desc_post_option().'" />'; 
	 		$seopress_social_og_desc .= "\n";
		} elseif (seopress_social_facebook_og_option() =='1' && function_exists('seopress_titles_the_description_content') && seopress_titles_the_description_content() !='') {
	 		$seopress_social_og_desc .= '<meta property="og:description" content="'.seopress_titles_the_description_content().'" />'; 
	 		$seopress_social_og_desc .= "\n";
	 	} elseif (seopress_social_facebook_og_option() =='1' && get_the_excerpt() !='') {
			$seopress_social_og_desc .= '<meta property="og:description" content="'.wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(get_the_excerpt()))), $seopress_excerpt_length).'" />'; 
	 		$seopress_social_og_desc .= "\n";
		}

		//Hook on post OG description - 'seopress_social_og_desc'
		if (has_filter('seopress_social_og_desc')) {
			$seopress_social_og_desc = apply_filters('seopress_social_og_desc', $seopress_social_og_desc);
	    }
	    if (isset($seopress_social_og_desc) && $seopress_social_og_desc !='') {
	    	echo $seopress_social_og_desc;
		}
	}
}
add_action( 'wp_head', 'seopress_social_fb_desc_hook', 1 );

//Facebook Thumbnail
function seopress_social_fb_img_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_social_fb_img = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_fb_img',true);
	} else {
		$_seopress_social_fb_img = get_post_meta(get_the_ID(),'_seopress_social_fb_img',true);
	}
	if ($_seopress_social_fb_img != '') {
		return $_seopress_social_fb_img;
	}
}

function seopress_social_fb_img_term_option() {
	$_seopress_social_fb_img = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_img',true);
	if ($_seopress_social_fb_img != '') {
		return $_seopress_social_fb_img;
	}
}

function seopress_social_facebook_img_option() {
	$seopress_social_facebook_img_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_facebook_img_option ) ) {
		foreach ($seopress_social_facebook_img_option as $key => $seopress_social_facebook_img_value)
			$options[$key] = $seopress_social_facebook_img_value;
		 if (isset($seopress_social_facebook_img_option['seopress_social_facebook_img'])) { 
		 	return $seopress_social_facebook_img_option['seopress_social_facebook_img'];
		 }
	}
}

function seopress_social_fb_img_home_option() {
	$_seopress_social_fb_img = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_img',true);
	if ($_seopress_social_fb_img != '') {
		return $_seopress_social_fb_img;
	} elseif (has_post_thumbnail(get_option( 'page_for_posts' ))) {
		return get_the_post_thumbnail_url(get_option( 'page_for_posts' ));
	}
}

function seopress_thumbnail_in_content() {
	//Get post content
    $seopress_get_the_content = get_post_field('post_content', get_the_ID());

    if ($seopress_get_the_content !='') {
		//DomDocument
	    $dom = new domDocument;
	    $internalErrors = libxml_use_internal_errors(true);

	    if (function_exists('mb_convert_encoding')) {
	    	$dom->loadHTML(mb_convert_encoding($seopress_get_the_content, 'HTML-ENTITIES', 'UTF-8'));
	    } else {
	    	$dom->loadHTML('<?xml encoding="utf-8" ?>'.$seopress_get_the_content);
		}

	    $dom->preserveWhiteSpace = false;
	    if ($dom->getElementsByTagName('img') !='') {
			$images = $dom->getElementsByTagName('img');
		}
		if (isset($images) && !empty ($images)) {
			if ($images->length>=1) {
				foreach($images as $img) {
			        $url = $img->getAttribute('src');
			        //Exclude Base64 img
					if (strpos($url, 'data:image/') === false) {
				        if (seopress_is_absolute($url) ===true) {
				        	//do nothing
				        } else {
				        	$url = get_home_url().$url;
						}
						//cleaning url
						$url = htmlspecialchars(urldecode(esc_attr(wp_filter_nohtml_kses($url))));
						
						//remove query strings
						$parse_url = wp_parse_url($url);

						if (!empty($parse_url['scheme']) && !empty($parse_url['host'])	&& !empty($parse_url['path'])) {
							return $parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'];
						} else {
							return $url;
						}
				    }
				}
			}
		}
		libxml_use_internal_errors($internalErrors);
	}
}

function seopress_social_fb_img_size_from_url($url) {
	if (function_exists('attachment_url_to_postid')) {
		$post_id 			= attachment_url_to_postid( $url );
		//If cropped image
		if ( !$post_id ){
			$dir = wp_upload_dir();
			$path = $url;
			if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
				$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
			}

			if ( preg_match( '/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches ) ){
				$url = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
				$post_id = attachment_url_to_postid( $url );
			}
		}

		$image_src = wp_get_attachment_image_src( $post_id, 'full' );

		//OG:IMAGE
		$seopress_social_og_img = '';
		$seopress_social_og_img .= '<meta property="og:image" content="'.$url.'" />';
		$seopress_social_og_img .= "\n";

		//OG:IMAGE:SECURE_URL IF SSL
		if (is_ssl()) {
			$seopress_social_og_img .= '<meta property="og:image:secure_url" content="'.$url.'" />';
			$seopress_social_og_img .= "\n";
		}

		//OG:IMAGE:WIDTH + OG:IMAGE:HEIGHT
		if (!empty($image_src)) {
			$seopress_social_og_img .= '<meta property="og:image:width" content="'.$image_src[1].'" />';
			$seopress_social_og_img .= "\n";
			$seopress_social_og_img .= '<meta property="og:image:height" content="'.$image_src[2].'" />';
			$seopress_social_og_img .= "\n";
		}

		//OG:IMAGE:ALT
		if (get_post_meta($post_id, '_wp_attachment_image_alt', true) !='') {
			$seopress_social_og_img .= '<meta property="og:image:alt" content="'.esc_attr(get_post_meta($post_id, '_wp_attachment_image_alt', true)).'" />';
			$seopress_social_og_img .= "\n";
		}

		return $seopress_social_og_img;
	}
}

function seopress_social_fb_img_hook() {
	if (seopress_social_facebook_og_option() =='1') {
		//Init
		$seopress_social_og_thumb ='';

		if (is_home() && seopress_social_fb_img_home_option() !='' && 'page' == get_option( 'show_on_front' )) {
			$seopress_social_og_thumb .= seopress_social_fb_img_size_from_url(seopress_social_fb_img_home_option());
		} elseif ((is_singular() || (function_exists("is_shop") && is_shop())) && seopress_social_facebook_og_option() =='1' && seopress_social_fb_img_post_option() !='') {
			$seopress_social_og_thumb .= seopress_social_fb_img_size_from_url(seopress_social_fb_img_post_option());
		} elseif ((is_singular() || (function_exists("is_shop") && is_shop())) && seopress_social_facebook_og_option() =='1' && has_post_thumbnail() ) {
			$seopress_social_og_thumb .= seopress_social_fb_img_size_from_url(get_the_post_thumbnail_url());
		} elseif ((is_singular() || (function_exists("is_shop") && is_shop())) && seopress_social_facebook_og_option() =='1' && seopress_thumbnail_in_content() !='' ) {
			$seopress_social_og_thumb .= seopress_social_fb_img_size_from_url(seopress_thumbnail_in_content());
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_social_fb_img_term_option() !='') {
			$seopress_social_og_thumb .= seopress_social_fb_img_size_from_url(seopress_social_fb_img_term_option());
		} elseif (seopress_social_facebook_og_option() =='1' && seopress_social_facebook_img_option() !='') { 
			$seopress_social_og_thumb .= seopress_social_fb_img_size_from_url(seopress_social_facebook_img_option());
	 	}

	 	//Hook on post OG thumbnail - 'seopress_social_og_thumb'
		if (has_filter('seopress_social_og_thumb')) {
			$seopress_social_og_thumb = apply_filters('seopress_social_og_thumb', $seopress_social_og_thumb);
	    }
	    if (isset($seopress_social_og_thumb) && $seopress_social_og_thumb !='') {
	    	echo $seopress_social_og_thumb;
	    }
	}
}
add_action( 'wp_head', 'seopress_social_fb_img_hook', 1 );

//OG Facebook Link Ownership ID
function seopress_social_facebook_link_ownership_id_option() {
	$seopress_social_facebook_link_ownership_id_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_facebook_link_ownership_id_option ) ) {
		foreach ($seopress_social_facebook_link_ownership_id_option as $key => $seopress_social_facebook_link_ownership_id_value)
			$options[$key] = $seopress_social_facebook_link_ownership_id_value;
		 if (isset($seopress_social_facebook_link_ownership_id_option['seopress_social_facebook_link_ownership_id'])) { 
		 	return $seopress_social_facebook_link_ownership_id_option['seopress_social_facebook_link_ownership_id'];
		 }
	}
}
function seopress_social_facebook_link_ownership_id_hook() {
	if (seopress_social_facebook_og_option() =='1' && seopress_social_facebook_link_ownership_id_option() !='') {

		$seopress_social_link_ownership_id = '<meta property="fb:pages" content="'.seopress_social_facebook_link_ownership_id_option().'" />';
		
		echo $seopress_social_link_ownership_id."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_link_ownership_id_hook', 1 );

//OG Facebook Admin ID
function seopress_social_facebook_admin_id_option() {
	$seopress_social_facebook_admin_id_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_facebook_admin_id_option ) ) {
		foreach ($seopress_social_facebook_admin_id_option as $key => $seopress_social_facebook_admin_id_value)
			$options[$key] = $seopress_social_facebook_admin_id_value;
		 if (isset($seopress_social_facebook_admin_id_option['seopress_social_facebook_admin_id'])) { 
		 	return $seopress_social_facebook_admin_id_option['seopress_social_facebook_admin_id'];
		 }
	}
}
function seopress_social_facebook_admin_id_hook() {
	if (seopress_social_facebook_og_option() =='1' && seopress_social_facebook_admin_id_option() !='') {

		$seopress_social_admin_id = '<meta property="fb:admins" content="'.seopress_social_facebook_admin_id_option().'" />';
		
		echo $seopress_social_admin_id."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_admin_id_hook', 1 );

//OG Facebook App ID
function seopress_social_facebook_app_id_option() {
	$seopress_social_facebook_app_id_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_facebook_app_id_option ) ) {
		foreach ($seopress_social_facebook_app_id_option as $key => $seopress_social_facebook_app_id_value)
			$options[$key] = $seopress_social_facebook_app_id_value;
		 if (isset($seopress_social_facebook_app_id_option['seopress_social_facebook_app_id'])) { 
		 	return $seopress_social_facebook_app_id_option['seopress_social_facebook_app_id'];
		 }
	}
}
function seopress_social_facebook_app_id_hook() {
	if (seopress_social_facebook_og_option() =='1' && seopress_social_facebook_app_id_option() !="") {

		$seopress_social_app_id = '<meta property="fb:app_id" content="'.seopress_social_facebook_app_id_option().'" />';
		
		echo $seopress_social_app_id."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_app_id_hook', 1 );

//Twitter
//Twitter Card Enabled
function seopress_social_twitter_card_option() {
	$seopress_social_twitter_card_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_twitter_card_option ) ) {
		foreach ($seopress_social_twitter_card_option as $key => $seopress_social_twitter_card_value)
			$options[$key] = $seopress_social_twitter_card_value;
		 if (isset($seopress_social_twitter_card_option['seopress_social_twitter_card'])) { 
		 	return $seopress_social_twitter_card_option['seopress_social_twitter_card'];
		 }
	}
}

//Twitter Card OG
function seopress_social_twitter_card_og_option() {
	$seopress_social_twitter_card_og_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_twitter_card_og_option ) ) {
		foreach ($seopress_social_twitter_card_og_option as $key => $seopress_social_twitter_card_og_value)
			$options[$key] = $seopress_social_twitter_card_og_value;
		 if (isset($seopress_social_twitter_card_og_option['seopress_social_twitter_card_og'])) { 
		 	return $seopress_social_twitter_card_og_option['seopress_social_twitter_card_og'];
		 }
	}
}

//Twitter Card Summary / large
function seopress_social_twitter_card_img_size_option() {
	$seopress_social_twitter_card_img_size_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_twitter_card_img_size_option ) ) {
		foreach ($seopress_social_twitter_card_img_size_option as $key => $seopress_social_twitter_card_value)
			$options[$key] = $seopress_social_twitter_card_value;
		 if (isset($seopress_social_twitter_card_img_size_option['seopress_social_twitter_card_img_size'])) { 
		 	return $seopress_social_twitter_card_img_size_option['seopress_social_twitter_card_img_size'];
		 }
	}
}

//Twitter Summary Card
function seopress_social_twitter_card_summary_hook() {
	if (seopress_social_twitter_card_option() =='1') {
		if (seopress_social_twitter_card_img_size_option() =='large') {
			$seopress_social_twitter_card_summary = '<meta name="twitter:card" content="summary_large_image">';
		} else {
			$seopress_social_twitter_card_summary = '<meta name="twitter:card" content="summary" />';
		}
		//Hook on post Twitter card summary - 'seopress_social_twitter_card_summary'
		if (has_filter('seopress_social_twitter_card_summary')) {
			$seopress_social_twitter_card_summary = apply_filters('seopress_social_twitter_card_summary', $seopress_social_twitter_card_summary);
	    }
		echo $seopress_social_twitter_card_summary."\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_card_summary_hook', 1 );

//Twitter Site
function seopress_social_twitter_card_site_hook() {
	if (seopress_social_twitter_card_option() =='1' && seopress_social_accounts_twitter_option() !='' ) {

		$seopress_social_twitter_card_site = '<meta name="twitter:site" content="'.seopress_social_accounts_twitter_option().'" />';
		
		//Hook on post Twitter card site - 'seopress_social_twitter_card_site'
		if (has_filter('seopress_social_twitter_card_site')) {
			$seopress_social_twitter_card_site = apply_filters('seopress_social_twitter_card_site', $seopress_social_twitter_card_site);
	    }
		echo $seopress_social_twitter_card_site."\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_card_site_hook', 1 );

//Twitter Creator
function seopress_social_twitter_card_creator_hook() {
	//Init
	$seopress_social_twitter_card_creator ='';

	if (seopress_social_twitter_card_option() =='1' && get_the_author_meta('twitter') ) {

		$seopress_social_twitter_card_creator .= '<meta name="twitter:creator" content="@'.get_the_author_meta('twitter').'" />';

	} elseif (seopress_social_twitter_card_option() =='1' && seopress_social_accounts_twitter_option() !='' ) {

		$seopress_social_twitter_card_creator .= '<meta name="twitter:creator" content="'.seopress_social_accounts_twitter_option().'" />';
	}
	//Hook on post Twitter card creator - 'seopress_social_twitter_card_creator'
	if (has_filter('seopress_social_twitter_card_creator')) {
		$seopress_social_twitter_card_creator = apply_filters('seopress_social_twitter_card_creator', $seopress_social_twitter_card_creator);
    }
    if (isset($seopress_social_twitter_card_creator) && $seopress_social_twitter_card_creator !='') {
    	echo $seopress_social_twitter_card_creator."\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_card_creator_hook', 1 );

//Twitter Title
function seopress_social_twitter_title_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_social_twitter_title = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_twitter_title',true);
	} else {
		$_seopress_social_twitter_title = get_post_meta(get_the_ID(),'_seopress_social_twitter_title',true);
	}
	if ($_seopress_social_twitter_title != '') {
		return $_seopress_social_twitter_title;
	}
}

function seopress_social_twitter_title_term_option() {
	$_seopress_social_twitter_title = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_title',true);
	if ($_seopress_social_twitter_title != '') {
		return $_seopress_social_twitter_title;
	}
}

function seopress_social_twitter_title_home_option() {
	$_seopress_social_twitter_title = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_title',true);
	if ($_seopress_social_twitter_title != '') {
		return $_seopress_social_twitter_title;
	}
}

function seopress_social_twitter_title_hook() {
	//If Twitter cards enable
	if (seopress_social_twitter_card_option() =='1') {
		//Init
		$seopress_social_twitter_card_title ='';

		if (is_home()) {//Home
			if (seopress_social_twitter_title_home_option() !='') {
				$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_twitter_title_home_option().'" />';
		 	} elseif (seopress_social_twitter_card_og_option() =='1' && seopress_social_fb_title_home_option() !='') {
		 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_fb_title_home_option().'" />';
		 	} elseif (function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
				$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.esc_attr(seopress_titles_the_title()).'" />'; 
		 	}
		} elseif (is_tax() || is_category() || is_tag()) {//Term archive
			if (seopress_social_twitter_title_term_option() !='') {
				$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_twitter_title_term_option().'" />'; 
	 		} elseif (seopress_social_twitter_card_og_option() =='1' && seopress_social_fb_title_term_option() !='') {
				$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_fb_title_term_option().'" />'; 
	 		} else {
	 			$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.single_term_title('', false).' - '.get_bloginfo('name').'" />'; 
	 		}
		} elseif (is_singular() && seopress_social_twitter_title_post_option() !='') {//Single
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_twitter_title_post_option().'" />';
		} elseif (is_singular() && seopress_social_twitter_card_og_option() =='1' && seopress_social_facebook_og_option() =='1' && seopress_social_fb_title_post_option() !='') { 
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_fb_title_post_option().'" />';
		} elseif (function_exists("is_shop") && is_shop() && seopress_social_twitter_title_post_option() !='') {//Single
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_twitter_title_post_option().'" />';
		} elseif (function_exists("is_shop") && is_shop() && seopress_social_twitter_card_og_option() =='1' && seopress_social_facebook_og_option() =='1' && seopress_social_fb_title_post_option() !='') { 
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.seopress_social_fb_title_post_option().'" />';
		} elseif (function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.esc_attr(seopress_titles_the_title()).'" />'; 
		} elseif (seopress_social_facebook_og_option() =='1' && seopress_social_twitter_card_og_option() =='1' && function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.esc_attr(seopress_titles_the_title()).'" />'; 
		} elseif (get_the_title() !='') {
	 		$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.the_title_attribute('echo=0').'" />';
		}

		//Hook on post Twitter card title - 'seopress_social_twitter_card_title'
		if (has_filter('seopress_social_twitter_card_title')) {
			$seopress_social_twitter_card_title = apply_filters('seopress_social_twitter_card_title', $seopress_social_twitter_card_title);
	    }
	    if (isset($seopress_social_twitter_card_title) && $seopress_social_twitter_card_title !='') {
	    	echo $seopress_social_twitter_card_title."\n";
	    }
	}
}
add_action( 'wp_head', 'seopress_social_twitter_title_hook', 1 );

//Twitter Desc
function seopress_social_twitter_desc_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_social_twitter_desc = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_twitter_desc',true);
	} else {
		$_seopress_social_twitter_desc = get_post_meta(get_the_ID(),'_seopress_social_twitter_desc',true);
	}
	if ($_seopress_social_twitter_desc != '') {
		return $_seopress_social_twitter_desc;
	}
}

function seopress_social_twitter_desc_term_option() {
	$_seopress_social_twitter_desc = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_desc',true);
	if ($_seopress_social_twitter_desc != '') {
		return $_seopress_social_twitter_desc;
	}
}

function seopress_social_twitter_desc_home_option() {
	$_seopress_social_twitter_desc = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_desc',true);
	if ($_seopress_social_twitter_desc != '') {
		return $_seopress_social_twitter_desc;
	}
}

function seopress_social_twitter_desc_hook() {
	//If Twitter cards enable
	if (seopress_social_twitter_card_option() =='1') {
		global $post;
		setup_postdata( $post );
		//Init
		$seopress_social_twitter_card_desc ='';

		//Excerpt length
		$seopress_excerpt_length = 50;
		$seopress_excerpt_length = apply_filters('seopress_excerpt_length',$seopress_excerpt_length);

		if (is_home()) {//Home
			if (seopress_social_twitter_desc_home_option() !='') {
				$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_twitter_desc_home_option().'" />';
		 	} elseif (seopress_social_fb_desc_home_option() !='' && seopress_social_twitter_card_og_option() =='1') {
					$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_fb_desc_home_option().'" />';
			} elseif (function_exists('seopress_titles_the_description_content') && seopress_titles_the_description_content() !='') {
		 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_titles_the_description_content().'" />'; 
		 	}
		} elseif (is_tax() || is_category() || is_tag()) {//Term archive
	 		if (seopress_social_twitter_desc_term_option() !='') {
		 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_twitter_desc_term_option().'" />';
		 	} elseif (seopress_social_fb_desc_term_option() !='' && seopress_social_twitter_card_og_option() =='1') {
			 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_fb_desc_term_option().'" />';
			} elseif (term_description() !='') {
		 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(term_description())),$seopress_excerpt_length).' - '.get_bloginfo('name').'" />'; 
		 	}
		} elseif (is_singular() && seopress_social_twitter_desc_post_option() !='') {//Single
	 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_twitter_desc_post_option().'" />';
		} elseif (is_singular() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_desc_post_option() !='' && seopress_social_twitter_card_og_option() =='1') { 
		 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_fb_desc_post_option().'" />';
		} elseif (function_exists("is_shop") && is_shop() && seopress_social_twitter_desc_post_option() !='') {//Single
	 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_twitter_desc_post_option().'" />';
		} elseif (function_exists("is_shop") && is_shop() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_desc_post_option() !='' && seopress_social_twitter_card_og_option() =='1') { 
		 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_social_fb_desc_post_option().'" />';
		} elseif (function_exists('seopress_titles_the_description_content') && seopress_titles_the_description_content() !='') {
	 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_titles_the_description_content().'" />';
	 	} elseif (seopress_social_facebook_og_option() =='1' && function_exists('seopress_titles_the_description_content') && seopress_titles_the_description_content() !='' && seopress_social_twitter_card_og_option() =='1') {
		 		$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.seopress_titles_the_description_content().'" />'; 
		} elseif (get_the_excerpt() !='') { 
			setup_postdata( $post );
			$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(get_the_excerpt()))), $seopress_excerpt_length).'" />';
		}

		//Hook on post Twitter card description - 'seopress_social_twitter_card_desc'
		if (has_filter('seopress_social_twitter_card_desc')) {
			$seopress_social_twitter_card_desc = apply_filters('seopress_social_twitter_card_desc', $seopress_social_twitter_card_desc);
	    }
	    if (isset($seopress_social_twitter_card_desc) && $seopress_social_twitter_card_desc !='') {
	    	echo $seopress_social_twitter_card_desc."\n";
	    }
	}
}
add_action( 'wp_head', 'seopress_social_twitter_desc_hook', 1 );

//Twitter Thumbnail
function seopress_social_twitter_img_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_social_twitter_img = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_twitter_img',true);
	} else {
		$_seopress_social_twitter_img = get_post_meta(get_the_ID(),'_seopress_social_twitter_img',true);
	}
	if ($_seopress_social_twitter_img != '') {
		return $_seopress_social_twitter_img;
	}
}

function seopress_social_twitter_img_term_option() {
	$_seopress_social_twitter_img = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_img',true);
	if ($_seopress_social_twitter_img !='') {
		return $_seopress_social_twitter_img;
	}
}

function seopress_social_twitter_img_option() {
	$seopress_social_twitter_img_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_twitter_img_option ) ) {
		foreach ($seopress_social_twitter_img_option as $key => $seopress_social_twitter_img_value)
			$options[$key] = $seopress_social_twitter_img_value;
		 if (isset($seopress_social_twitter_img_option['seopress_social_twitter_card_img'])) { 
		 	return $seopress_social_twitter_img_option['seopress_social_twitter_card_img'];
		 }
	}
}

function seopress_social_twitter_img_size_option() {
	$seopress_social_twitter_img_size_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_twitter_img_size_option ) ) {
		foreach ($seopress_social_twitter_img_size_option as $key => $seopress_social_twitter_img_size_value)
			$options[$key] = $seopress_social_twitter_img_size_value;
		 if (isset($seopress_social_twitter_img_size_option['seopress_social_twitter_card_img_size'])) { 
		 	return $seopress_social_twitter_img_size_option['seopress_social_twitter_card_img_size'];
		 }
	}
}

function seopress_social_twitter_img_home_option() {
	$_seopress_social_twitter_img = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_img',true);
	if ($_seopress_social_twitter_img != '') {
		return $_seopress_social_twitter_img;
	} elseif (has_post_thumbnail(get_option( 'page_for_posts' ))) {
		return get_the_post_thumbnail_url(get_option( 'page_for_posts' ));
	}
}

function seopress_social_twitter_img_hook() {
	if (seopress_social_twitter_card_option() =='1') {
		//Init
		$seopress_social_twitter_card_thumb ='';

		if (is_home() && seopress_social_twitter_img_home_option() !='' && 'page' == get_option( 'show_on_front' )) { 
			if (seopress_social_twitter_img_size_option() =='large') {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_home_option().'" />'; 
		 	} else {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_twitter_img_home_option().'" />'; 
		 	}
		} elseif (is_home() && seopress_social_fb_img_home_option() !='' && 'page' == get_option( 'show_on_front' ) && seopress_social_twitter_card_og_option() =='1') { 
			if (seopress_social_twitter_img_size_option() =='large') {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_fb_img_home_option().'" />'; 
		 	} else {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_fb_img_home_option().'" />'; 
		 	}
		} elseif (seopress_social_twitter_img_post_option() !='' && (is_singular() || (function_exists("is_shop") && is_shop()))) {//Single
			if (seopress_social_twitter_img_size_option() =='large') {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_post_option().'" />'; 
		 	} else {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_twitter_img_post_option().'" />'; 
		 	}
		} elseif (seopress_social_fb_img_post_option() !='' && (is_singular() || (function_exists("is_shop") && is_shop())) && seopress_social_twitter_card_og_option() =='1') { 
			if (seopress_social_twitter_img_size_option() =='large') {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_fb_img_post_option().'" />'; 
		 	} else {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_fb_img_post_option().'" />'; 
		 	}
		} elseif (has_post_thumbnail() && (is_singular() || (function_exists("is_shop") && is_shop()))) {
			if (seopress_social_twitter_img_size_option() =='large') {
				$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.get_the_post_thumbnail_url().'" />'; 
	 		} else {
	 			$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.get_the_post_thumbnail_url().'" />'; 
	 		}
		} elseif (seopress_thumbnail_in_content() !='' && (is_singular() || (function_exists("is_shop") && is_shop()))) {
			if (seopress_social_twitter_img_size_option() =='large') {
				$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_thumbnail_in_content().'" />'; 
	 		} else {
	 			$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_thumbnail_in_content().'" />'; 
	 		}
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_social_twitter_img_term_option() !='') {//Term archive
			if (seopress_social_twitter_img_size_option() =='large') {
				$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_term_option().'" />'; 
	 		} else {
	 			$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_twitter_img_term_option().'" />'; 
	 		}
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_social_fb_img_term_option() !='' && seopress_social_twitter_card_og_option() =='1') {
			if (seopress_social_twitter_img_size_option() =='large') {
				$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_fb_img_term_option().'" />'; 
	 		} else {
	 			$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_fb_img_term_option().'" />'; 
	 		}
		} elseif (seopress_social_twitter_img_option() !='') {//Default Twitter
			if (seopress_social_twitter_img_size_option() =='large') {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_option().'" />'; 
		 	} else {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_twitter_img_option().'" />'; 
		 	}
	 	} elseif (seopress_social_facebook_img_option() !='' && seopress_social_twitter_card_og_option() =='1') {//Default Facebook
			if (seopress_social_twitter_img_size_option() =='large') {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image:src" content="'.seopress_social_facebook_img_option().'" />'; 
		 	} else {
		 		$seopress_social_twitter_card_thumb .= '<meta name="twitter:image" content="'.seopress_social_facebook_img_option().'" />'; 
		 	}
	 	}
		//Hook on post Twitter card thumbnail - 'seopress_social_twitter_card_thumb'
		if (has_filter('seopress_social_twitter_card_thumb')) {
			$seopress_social_twitter_card_thumb = apply_filters('seopress_social_twitter_card_thumb', $seopress_social_twitter_card_thumb);
	    }
	    if (isset($seopress_social_twitter_card_thumb) && $seopress_social_twitter_card_thumb !='') {
	    	echo $seopress_social_twitter_card_thumb."\n";
	    }
	}
}
add_action( 'wp_head', 'seopress_social_twitter_img_hook', 1 );
