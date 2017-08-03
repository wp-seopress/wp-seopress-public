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

//Google +
function seopress_social_accounts_google_option() {
	$seopress_social_accounts_google_option = get_option("seopress_social_option_name");
	if ( ! empty ( $seopress_social_accounts_google_option ) ) {
		foreach ($seopress_social_accounts_google_option as $key => $seopress_social_accounts_google_value)
			$options[$key] = $seopress_social_accounts_google_value;
		 if (isset($seopress_social_accounts_google_option['seopress_social_accounts_google'])) { 
		 	return $seopress_social_accounts_google_option['seopress_social_accounts_google'];
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

	if (seopress_social_accounts_facebook_option() !='') { 
 		$seopress_social_accounts_facebook_option = json_encode(seopress_social_accounts_facebook_option());
 		array_push($seopress_comma_array, $seopress_social_accounts_facebook_option);	
	}	
	if (seopress_social_accounts_twitter_option() !='') { 
 		$seopress_social_accounts_twitter_option = json_encode('https://twitter.com/'.seopress_social_accounts_twitter_option());	
 	 	array_push($seopress_comma_array, $seopress_social_accounts_twitter_option);	
	}	
	if (seopress_social_accounts_google_option() !='') { 
 		$seopress_social_accounts_google_option = json_encode(seopress_social_accounts_google_option());
 		array_push($seopress_comma_array, $seopress_social_accounts_google_option);
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
		$seopress_social_knowledge_type_option = 'Organization';
	}
	if (seopress_social_knowledge_name_option() !='') {
		$seopress_social_knowledge_name_option = json_encode(seopress_social_knowledge_name_option());
	} else {
		$seopress_social_knowledge_name_option = json_encode(get_bloginfo('name'));
	}
	if (seopress_social_knowledge_img_option() !='') {
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

	echo '<script type="application/ld+json">';
	echo '{"@context" : "'.seopress_check_ssl().'schema.org","@type" : '.$seopress_social_knowledge_type_option.',';
	if (seopress_social_knowledge_img_option() !='') {
		echo '"logo": '.$seopress_social_knowledge_img_option.',';
	}
	echo '"name" : '.$seopress_social_knowledge_name_option.',"url" : '.json_encode(get_home_url());
	
	if (seopress_social_knowledge_type_option() =='Organization' 
		&& seopress_social_knowledge_phone_number_option() !=''
		&& seopress_social_knowledge_contact_type_option() !=''
		&& seopress_social_knowledge_contact_option_option() !=''
		) {
		if ($seopress_social_knowledge_phone_number_option && $seopress_social_knowledge_contact_type_option && $seopress_social_knowledge_contact_option_option ) {
			echo ',"contactPoint": [{
				"@type": "ContactPoint",
				"telephone": '.$seopress_social_knowledge_phone_number_option.',
				"contactType": '.$seopress_social_knowledge_contact_type_option.',
				"contactOption": '.$seopress_social_knowledge_contact_option_option.'
			}]';
		}
	}

	if (seopress_social_accounts_facebook_option() !='' || seopress_social_accounts_twitter_option() !='' || seopress_social_accounts_google_option() !='' || seopress_social_accounts_pinterest_option() !='' || seopress_social_accounts_instagram_option() !='' || seopress_social_accounts_youtube_option() !='' || seopress_social_accounts_linkedin_option() !='' || seopress_social_accounts_myspace_option() !='' || seopress_social_accounts_soundcloud_option() !='' || seopress_social_accounts_tumblr_option() !='' ) {
		echo ',"sameAs" : [';
		$seopress_comma_count = count($seopress_comma_array);
		for ($i = 0; $i < $seopress_comma_count; $i++) {
			echo $seopress_comma_array[$i];
		   	if ($i < ($seopress_comma_count - 1)) {
		    	echo ', ';
		   	}
		}
		echo ']';
	}
	echo '}';
	echo '</script>';
	echo "\n";
}
add_action( 'wp_head', 'seopress_social_accounts_jsonld_hook', 1 );

//Website Schema.org in JSON-LD - Sitelinks
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
			$seopress_social_og_url = '<meta property="og:url" content="'.get_home_url().'/search/'.get_search_query().'" />';
		} else {
			$seopress_social_og_url = '<meta property="og:url" content="'.$current_url.'" />';
		}
		
		echo $seopress_social_og_url."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_url_hook', 1 );

//OG Site Name
function seopress_social_facebook_og_site_name_hook() {
	if (seopress_social_facebook_og_option() =='1' && get_bloginfo('name') !='') {

		$seopress_social_og_site_name = '<meta property="og:site_name" content="'.get_bloginfo('name').'" />';
		
		echo $seopress_social_og_site_name."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_site_name_hook', 1 );

//OG Locale
function seopress_social_facebook_og_locale_hook() {
	if (seopress_social_facebook_og_option() =='1') {

		$seopress_social_og_locale = '<meta property="og:locale" content="'.get_locale().'" />';
		
		echo $seopress_social_og_locale."\n";
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_locale_hook', 1 );

//OG Type
function seopress_social_facebook_og_type_hook() {
	if (seopress_social_facebook_og_option() =='1') {
		if (is_home() || is_front_page()) {
			$seopress_social_og_type = '<meta property="og:type" content="website" />';
		} elseif (is_singular()) {
			$seopress_social_og_type = '<meta property="og:type" content="article" />';
		} 
		elseif (is_search() || is_archive() || is_404()) {
			$seopress_social_og_type = '<meta property="og:type" content="object" />';
		}
		if ($seopress_social_og_type !='') {
			echo $seopress_social_og_type."\n";
		}
	}
}
add_action( 'wp_head', 'seopress_social_facebook_og_type_hook', 1 );

//Facebook Title
function seopress_social_fb_title_post_option() {
	$_seopress_social_fb_title = get_post_meta(get_the_ID(),'_seopress_social_fb_title',true);
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
	if (is_home()) {
		if (seopress_social_fb_title_home_option() !='') {
			echo '<meta property="og:title" content="'.seopress_social_fb_title_home_option().'" />'; 
	 		echo "\n";
	 	}
	} elseif (seopress_social_facebook_og_option() =='1' && seopress_social_fb_title_post_option() !='') { 
 		echo '<meta property="og:title" content="'.seopress_social_fb_title_post_option().'" />'; 
 		echo "\n";
	} elseif (is_tax() || is_category() || is_tag()) {
		if (seopress_social_fb_title_term_option() !='') {
			echo '<meta property="og:title" content="'.seopress_social_fb_title_term_option().'" />'; 
 			echo "\n";
 		} else {
 			echo '<meta property="og:title" content="'.single_term_title('', false).' - '.get_bloginfo('name').'" />'; 
 			echo "\n";
 		}
	} elseif (seopress_social_facebook_og_option() =='1' && get_the_title() !='') { 
 		echo '<meta property="og:title" content="'.the_title_attribute('echo=0').'" />'; 
 		echo "\n";
	}
}
add_action( 'wp_head', 'seopress_social_fb_title_hook', 1 );

//Facebook Desc
function seopress_social_fb_desc_post_option() {
	$_seopress_social_fb_desc = get_post_meta(get_the_ID(),'_seopress_social_fb_desc',true);
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
	global $post;
	setup_postdata( $post );
	if (is_home()) {
		if (seopress_social_fb_desc_home_option() !='') {
			echo '<meta property="og:description" content="'.seopress_social_fb_desc_home_option().'" />'; 
	 		echo "\n";
	 	}
	} elseif (seopress_social_facebook_og_option() =='1' && seopress_social_fb_desc_post_option() !='') { 
 		echo '<meta property="og:description" content="'.seopress_social_fb_desc_post_option().'" />'; 
 		echo "\n";
	} elseif (is_tax() || is_category() || is_tag()) {
		if (seopress_social_fb_desc_term_option() !='') {
			echo '<meta property="og:description" content="'.seopress_social_fb_desc_term_option().'" />'; 
	 		echo "\n";
	 	} elseif (term_description() !='') {
	 		echo '<meta property="og:description" content="'.esc_html(term_description()).' - '.get_bloginfo('name').'" />'; 
	 		echo "\n";
	 	}
	} elseif (seopress_social_facebook_og_option() && get_the_excerpt() !='') {
		echo '<meta property="og:description" content="'.wp_trim_words(esc_html(get_the_excerpt()), 30).'" />'; 
 		echo "\n";
	} 
}
add_action( 'wp_head', 'seopress_social_fb_desc_hook', 1 );

//Facebook Thumbnail
function seopress_social_fb_img_post_option() {
	$_seopress_social_fb_img = get_post_meta(get_the_ID(),'_seopress_social_fb_img',true);
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

function seopress_social_fb_img_hook() {
	if (is_home()) {
		if (seopress_social_fb_img_home_option() !='' && 'page' == get_option( 'show_on_front' )) {
			echo '<meta property="og:image" content="'.seopress_social_fb_img_home_option().'" />'; 
 			echo "\n";
		}
	} elseif (is_singular() && seopress_social_facebook_og_option() =='1' && seopress_social_fb_img_post_option() !='') { 
 		echo '<meta property="og:image" content="'.seopress_social_fb_img_post_option().'" />'; 
 		echo "\n";
	} elseif (is_singular() && seopress_social_facebook_og_option() =='1' && has_post_thumbnail() ) {
		echo '<meta property="og:image" content="'.get_the_post_thumbnail_url().'" />'; 
 		echo "\n";
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_social_fb_img_term_option() !='') {
		echo '<meta property="og:image" content="'.seopress_social_fb_img_term_option().'" />';
 		echo "\n";
	} elseif (seopress_social_facebook_og_option() =='1' && seopress_social_facebook_img_option() !='') { 
 		echo '<meta property="og:image" content="'.seopress_social_facebook_img_option().'" />'; 
 		echo "\n";
 	}
}
add_action( 'wp_head', 'seopress_social_fb_img_hook', 1 );

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

		echo $seopress_social_twitter_card_summary."\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_card_summary_hook', 1 );

//Twitter Site
function seopress_social_twitter_card_site_hook() {
	if (seopress_social_twitter_card_option() =='1' && seopress_social_accounts_twitter_option() !='' ) {

		$seopress_social_twitter_card_site = '<meta name="twitter:site" content="'.seopress_social_accounts_twitter_option().'" />';
		
		echo $seopress_social_twitter_card_site."\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_card_site_hook', 1 );

//Twitter Creator
function seopress_social_twitter_card_creator_hook() {
	if (seopress_social_twitter_card_option() =='1' && get_the_author_meta('twitter') ) {

		$seopress_social_twitter_card_creator = '<meta name="twitter:creator" content="@'.get_the_author_meta('twitter').'" />';
		echo $seopress_social_twitter_card_creator."\n";

	} elseif (seopress_social_twitter_card_option() =='1' && seopress_social_accounts_twitter_option() !='' ) {

		$seopress_social_twitter_card_creator = '<meta name="twitter:creator" content="'.seopress_social_accounts_twitter_option().'" />';
		echo $seopress_social_twitter_card_creator."\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_card_creator_hook', 1 );

//Twitter Title
function seopress_social_twitter_title_post_option() {
	$_seopress_social_twitter_title = get_post_meta(get_the_ID(),'_seopress_social_twitter_title',true);
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
	if (is_home()) {
		if (seopress_social_twitter_title_home_option() !='') {
			echo '<meta name="twitter:title" content="'.seopress_social_twitter_title_home_option().'" />';
 			echo "\n";
	 	}
	} elseif (seopress_social_twitter_card_option() =='1' && seopress_social_twitter_title_post_option() !='') { 
 		echo '<meta name="twitter:title" content="'.seopress_social_twitter_title_post_option().'" />';
 		echo "\n";
	} elseif (is_tax() || is_category() || is_tag()) {
		if (seopress_social_twitter_title_term_option() !='') {
			echo '<meta name="twitter:title" content="'.seopress_social_twitter_title_term_option().'" />'; 
 			echo "\n";
 		} else {
 			echo '<meta name="twitter:title" content="'.single_term_title('', false).' - '.get_bloginfo('name').'" />'; 
 			echo "\n";
 		}
	} elseif (seopress_social_twitter_card_option() =='1' && get_the_title() !='') { 
 		echo '<meta name="twitter:title" content="'.the_title_attribute('echo=0').'" />';
 		echo "\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_title_hook', 1 );

//Twitter Desc
function seopress_social_twitter_desc_post_option() {
	$_seopress_social_twitter_desc = get_post_meta(get_the_ID(),'_seopress_social_twitter_desc',true);
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
	global $post;
	setup_postdata( $post );
	if (is_home()) {
		if (seopress_social_twitter_desc_home_option() !='') {
			echo '<meta name="twitter:description" content="'.seopress_social_twitter_desc_home_option().'" />';
 			echo "\n";
	 	}
	} elseif (seopress_social_twitter_card_option() =='1' && seopress_social_twitter_desc_post_option() !='') { 
 		echo '<meta name="twitter:description" content="'.seopress_social_twitter_desc_post_option().'" />';
 		echo "\n";
	} elseif (is_tax() || is_category() || is_tag()) {
 		if (seopress_social_twitter_desc_term_option() !='') {
	 		echo '<meta name="twitter:description" content="'.seopress_social_twitter_desc_term_option().'" />';
	 		echo "\n";
	 	} elseif (term_description() !='') {
	 		echo '<meta name="twitter:description" content="'.esc_html(term_description()).' - '.get_bloginfo('name').'" />'; 
	 		echo "\n";
	 	}
	} elseif (seopress_social_twitter_card_option() =='1' && get_the_excerpt() !='') { 
		setup_postdata( $post );
		echo '<meta name="twitter:description" content="'.wp_trim_words(esc_html(get_the_excerpt()), 30).'" />';
 		echo "\n";
	}
}
add_action( 'wp_head', 'seopress_social_twitter_desc_hook', 1 );

//Twitter Thumbnail
function seopress_social_twitter_img_post_option() {
	$_seopress_social_twitter_img = get_post_meta(get_the_ID(),'_seopress_social_twitter_img',true);
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
		if (is_home()) {
			if (seopress_social_twitter_img_home_option() !='' && 'page' == get_option( 'show_on_front' )) { 
				if (seopress_social_twitter_img_size_option() =='large') {
			 		echo '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_home_option().'" />'; 
			 		echo "\n";
			 	} else {
			 		echo '<meta name="twitter:image" content="'.seopress_social_twitter_img_home_option().'" />'; 
			 		echo "\n";
			 	}
			}
		} elseif (seopress_social_twitter_img_post_option() !='' && is_singular()) { 
			if (seopress_social_twitter_img_size_option() =='large') {
		 		echo '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_post_option().'" />'; 
		 		echo "\n";
		 	} else {
		 		echo '<meta name="twitter:image" content="'.seopress_social_twitter_img_post_option().'" />'; 
		 		echo "\n";
		 	}
		} elseif (has_post_thumbnail() && is_singular()) {
			if (seopress_social_twitter_img_size_option() =='large') {
				echo '<meta name="twitter:image:src" content="'.get_the_post_thumbnail_url().'" />'; 
	 			echo "\n";
	 		} else {
	 			echo '<meta name="twitter:image" content="'.get_the_post_thumbnail_url().'" />'; 
	 			echo "\n";
	 		}
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_social_twitter_img_term_option() !='') {
			if (seopress_social_twitter_img_size_option() =='large') {
				echo '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_term_option().'" />'; 
	 			echo "\n";
	 		} else {
	 			echo '<meta name="twitter:image" content="'.seopress_social_twitter_img_term_option().'" />'; 
	 			echo "\n";
	 		}
		} elseif (seopress_social_twitter_img_option() !='') { 
			if (seopress_social_twitter_img_size_option() =='large') {
		 		echo '<meta name="twitter:image:src" content="'.seopress_social_twitter_img_option().'" />'; 
		 		echo "\n";
		 	} else {
		 		echo '<meta name="twitter:image" content="'.seopress_social_twitter_img_option().'" />'; 
		 		echo "\n";
		 	}
	 	}
	}
}
add_action( 'wp_head', 'seopress_social_twitter_img_hook', 1 );
