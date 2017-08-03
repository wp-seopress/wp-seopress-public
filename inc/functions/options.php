<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Permalink structure for TrailingSlash
///////////////////////////////////////////////////////////////////////////////////////////////////

function seopress_advanced_advanced_trailingslash_option() {
    $seopress_advanced_advanced_trailingslash_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_trailingslash_option ) ) {
        foreach ($seopress_advanced_advanced_trailingslash_option as $key => $seopress_advanced_advanced_trailingslash_value)
            $options[$key] = $seopress_advanced_advanced_trailingslash_value;
         if (isset($seopress_advanced_advanced_trailingslash_option['seopress_advanced_advanced_trailingslash'])) { 
            return $seopress_advanced_advanced_trailingslash_option['seopress_advanced_advanced_trailingslash'];
         }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Core
///////////////////////////////////////////////////////////////////////////////////////////////////

//Import / Export tool
add_action('init', 'seopress_enable', 999);
function seopress_enable() {
	if (is_admin()){
		require_once ( dirname( __FILE__ ) . '/options-import-export.php'); //Import Export
	}
}

//Front END
if (seopress_get_toggle_titles_option() =='1') {
	//Author archive Disabled
	function seopress_titles_archives_author_disable_option() {
		$seopress_titles_archives_author_disable_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_author_disable_option ) ) {
			foreach ($seopress_titles_archives_author_disable_option as $key => $seopress_titles_archives_author_disable_value)
				$options[$key] = $seopress_titles_archives_author_disable_value;
			 if (isset($seopress_titles_archives_author_disable_option['seopress_titles_archives_author_disable'])) { 
			 	return $seopress_titles_archives_author_disable_option['seopress_titles_archives_author_disable'];
			 }
		}
	};

	//Date archive Disabled
	function seopress_titles_archives_date_disable_option() {
		$seopress_titles_archives_date_disable_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_date_disable_option ) ) {
			foreach ($seopress_titles_archives_date_disable_option as $key => $seopress_titles_archives_date_disable_value)
				$options[$key] = $seopress_titles_archives_date_disable_value;
			 if (isset($seopress_titles_archives_date_disable_option['seopress_titles_archives_date_disable'])) { 
			 	return $seopress_titles_archives_date_disable_option['seopress_titles_archives_date_disable'];
			 }
		}
	};

	function seopress_titles_disable_archives() {
		global $wp_query;

		if (seopress_titles_archives_author_disable_option() =='1' && $wp_query->is_author) {
			wp_redirect(get_home_url(), '301');
	        exit;
		}
		if (seopress_titles_archives_author_disable_option() =='1' && $wp_query->is_date) {
			wp_redirect(get_home_url(), '301');
	        exit;
		}
		return false;
	}

	add_action('template_redirect', 'seopress_titles_disable_archives', 0);
	add_action('wp_head', 'seopress_load_titles_options', 0);
	function seopress_load_titles_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-titles-metas.php'); //Titles & metas
		}
	}
}
if (seopress_get_toggle_social_option() =='1') {
	add_action('wp_head', 'seopress_load_social_options', 0);
	function seopress_load_social_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-social.php'); //Social
		}
	}
}
if (seopress_get_toggle_google_analytics_option() =='1') {
	add_action('wp_head', 'seopress_load_google_analytics_options', 0);
	function seopress_load_google_analytics_options() {
	    require_once ( dirname( __FILE__ ) . '/options-google-analytics.php'); //Google Analytics
	}
}
add_action('wp', 'seopress_load_redirections_options', 0);
function seopress_load_redirections_options() {
	if (!is_admin()){
	    require_once ( dirname( __FILE__ ) . '/options-redirections.php'); //Redirections
	}
}
if (seopress_get_toggle_xml_sitemap_option() =='1') {
	add_action('init', 'seopress_load_sitemap', 999);
	function seopress_load_sitemap() {
		if (!is_admin()) {
			require_once ( dirname( __FILE__ ) . '/options-sitemap.php'); //XML / HTML Sitemap
		}
	}	
}
if (seopress_get_toggle_advanced_option() =='1') {
	add_action('wp_head', 'seopress_load_advanced_options', 0);
	function seopress_load_advanced_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-advanced.php'); //Advanced
		}
	}
	add_action('init', 'seopress_load_advanced_admin_options', 0);
	function seopress_load_advanced_admin_options() {
		if (is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-advanced-admin.php'); //Advanced (admin)
		}
	}
	//No /category/ in URL
	function seopress_advanced_advanced_category_url_option() {
		$seopress_advanced_advanced_category_url_option = get_option("seopress_advanced_option_name");
		if ( ! empty ( $seopress_advanced_advanced_category_url_option ) ) {
			foreach ($seopress_advanced_advanced_category_url_option as $key => $seopress_advanced_advanced_category_url_value)
				$options[$key] = $seopress_advanced_advanced_category_url_value;
			 if (isset($seopress_advanced_advanced_category_url_option['seopress_advanced_advanced_category_url'])) { 
			 	return $seopress_advanced_advanced_category_url_option['seopress_advanced_advanced_category_url'];
			 }
		}
	};

	if (seopress_advanced_advanced_category_url_option() !='') {
		//@credits : WordPress VIP
		add_filter( 'category_rewrite_rules', 'seopress_filter_category_rewrite_rules' );
		function seopress_filter_category_rewrite_rules( $rules ) {
			 if ( class_exists( 'Sitepress' ) ) {
				global $sitepress;
				remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
				$categories = get_categories( array( 'hide_empty' => false ) );
				add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
			} else {
		    	$categories = get_categories( array( 'hide_empty' => false ) );
		 	}
		    if ( is_array( $categories ) && ! empty( $categories ) ) {
		        $slugs = array();
		 
		        foreach ( $categories as $category ) {
		            if ( is_object( $category ) && ! is_wp_error( $category ) ) {
		                if ( 0 == $category->category_parent )
		                    $slugs[] = $category->slug;
		                else
		                    $slugs[] = trim( get_category_parents( $category->term_id, false, '/', true ), '/' );
		            }
		        }
		 
		        if ( ! empty( $slugs ) ) {
		            $rules = array();
		 
		            foreach ( $slugs as $slug ) {
		                $rules[ '(' . $slug . ')/feed/(feed|rdf|rss|rss2|atom)?/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
		                $rules[ '(' . $slug . ')/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
		                $rules[ '(' . $slug . ')(/page/(\d+))?/?$' ] = 'index.php?category_name=$matches[1]&paged=$matches[3]';
		            }
		        }
		    }
		    return $rules;
		}
		add_action('template_redirect', 'seopress_category_redirect', 1);
		function seopress_category_redirect(){
			global $wp;

			if (seopress_advanced_advanced_trailingslash_option()) {
				$current_url = home_url(add_query_arg(array(), $wp->request));
			} else {
				$current_url = trailingslashit(home_url(add_query_arg(array(), $wp->request)));
			}

			$category_base = get_option( 'category_base' );

			if ($category_base !='') {
				if (preg_match('/'.$category_base.'/', $current_url)) {
					$new_url = str_replace('/'.$category_base, '', $current_url);
					wp_redirect($new_url, 301 );
					exit();
				}
			} else {
				$category_base = 'category';
				if (preg_match('/'.$category_base.'/', $current_url)) {
					$new_url = str_replace('/'.$category_base, '', $current_url);
					wp_redirect($new_url, 301 );
		    		exit();
	    		}
			}
		}
	}
}