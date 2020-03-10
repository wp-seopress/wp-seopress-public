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
if (seopress_get_toggle_option('titles') =='1') {
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

		if (seopress_titles_archives_author_disable_option() =='1' && $wp_query->is_author && !is_feed()) {
			wp_redirect(get_home_url(), '301');
	        exit;
		}
		if (seopress_titles_archives_date_disable_option() =='1' && $wp_query->is_date && !is_feed()) {
			wp_redirect(get_home_url(), '301');
	        exit;
		}
		return false;
	}

	//SEO metaboxes
	function seopress_hide_metaboxes() {
		if (is_admin()) {
			global $typenow;
			global $pagenow;

			//Post type?
			if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
				function seopress_titles_single_enable_option() {
					global $post;
					$seopress_get_current_cpt = get_post_type($post);

					$seopress_titles_single_enable_option = get_option("seopress_titles_option_name");
					if ( ! empty ( $seopress_titles_single_enable_option ) ) {
						foreach ($seopress_titles_single_enable_option as $key => $seopress_titles_single_enable_value)
							$options[$key] = $seopress_titles_single_enable_value;
						if (isset($seopress_titles_single_enable_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['enable'])) {
							return $seopress_titles_single_enable_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['enable'];
						}
					}
				}
				function seopress_titles_single_enable_metabox($seopress_get_post_types) {
					global $post;
					if (seopress_titles_single_enable_option() == 1 && get_post_type($post) !='') {
						unset($seopress_get_post_types[get_post_type($post)]);
					}
					return $seopress_get_post_types;
				}
				add_filter('seopress_metaboxe_seo', 'seopress_titles_single_enable_metabox');
				add_filter('seopress_metaboxe_content_analysis', 'seopress_titles_single_enable_metabox');
				add_filter('seopress_pro_metaboxe_sdt', 'seopress_titles_single_enable_metabox');
			}

			//Taxonomy?
			if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
				if (!empty($_GET['taxonomy'])) {
					$seopress_get_current_tax = sanitize_title(esc_attr($_GET['taxonomy']));

					function seopress_tax_single_enable_option($seopress_get_current_tax) {
						$seopress_tax_single_enable_option = get_option("seopress_titles_option_name");
						if ( ! empty ( $seopress_tax_single_enable_option ) ) {
							foreach ($seopress_tax_single_enable_option as $key => $seopress_tax_single_enable_value)
								$options[$key] = $seopress_tax_single_enable_value;
							if (isset($seopress_tax_single_enable_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['enable'])) {
								return $seopress_tax_single_enable_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['enable'];
							}
						}
					}

					function seopress_tax_single_enable_metabox($seopress_get_taxonomies) {
						$seopress_get_current_tax = sanitize_title(esc_attr($_GET['taxonomy']));
						if (seopress_tax_single_enable_option($seopress_get_current_tax) == 1 && $seopress_get_current_tax !='') {
							unset($seopress_get_taxonomies[$seopress_get_current_tax]);
						}
						return $seopress_get_taxonomies;
					}
					add_filter('seopress_metaboxe_term_seo', 'seopress_tax_single_enable_metabox');
				}
			}
		}
	}
	add_action('after_setup_theme', 'seopress_hide_metaboxes');

	//Titles and metas
	add_action('template_redirect', 'seopress_titles_disable_archives', 0);
	add_action('wp_head', 'seopress_load_titles_options', 0);
	function seopress_load_titles_options() {
		if (!is_admin()){
			if( function_exists('is_wpforo_page') && is_wpforo_page() ){//disable on wpForo pages to avoid conflicts
				//do nothing
			} else {
				require_once ( dirname( __FILE__ ) . '/options-titles-metas.php'); //Titles & metas
			}
		}
	}
}
if (seopress_get_toggle_option('social') =='1') {
	// add_action('wp_head', 'seopress_load_oembed_options');
	// function seopress_load_oembed_options() {
	// 	if (!is_admin()){
	// 		require_once ( dirname( __FILE__ ) . '/options-oembed.php'); //Oembed
	// 	}
	// }

	add_action('wp_head', 'seopress_load_social_options', 0);
	function seopress_load_social_options() {
		if (!is_admin()){
			if( function_exists('is_wpforo_page') && is_wpforo_page() ){//disable on wpForo pages to avoid conflicts
				//do nothing
			} else {
				require_once ( dirname( __FILE__ ) . '/options-social.php'); //Social
			}
		}
	}
}
if (seopress_get_toggle_option('google-analytics') =='1') {
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

	//Ecommerce enabled
	function seopress_google_analytics_ecommerce_enable_option() {
		$seopress_google_analytics_ecommerce_enable_option = get_option("seopress_google_analytics_option_name");
		if ( ! empty ( $seopress_google_analytics_ecommerce_enable_option ) ) {
			foreach ($seopress_google_analytics_ecommerce_enable_option as $key => $seopress_google_analytics_ecommerce_enable_value)
				$options[$key] = $seopress_google_analytics_ecommerce_enable_value;
			 if (isset($seopress_google_analytics_ecommerce_enable_option['seopress_google_analytics_e_commerce_enable'])) {
			 	return $seopress_google_analytics_ecommerce_enable_option['seopress_google_analytics_e_commerce_enable'];
			 }
		}
	}

	//Disable Tracking
	function seopress_google_analytics_disable_option() {
		$seopress_google_analytics_disable_option = get_option("seopress_google_analytics_option_name");
		if ( ! empty ( $seopress_google_analytics_disable_option ) ) {
			foreach ($seopress_google_analytics_disable_option as $key => $seopress_google_analytics_disable_value)
				$options[$key] = $seopress_google_analytics_disable_value;
				if (isset($seopress_google_analytics_disable_option['seopress_google_analytics_disable'])) {
					return $seopress_google_analytics_disable_option['seopress_google_analytics_disable'];
				}
		}
	}

	//Auto accept user consent
	function seopress_google_analytics_half_disable_option() {
		$seopress_google_analytics_half_disable_option = get_option("seopress_google_analytics_option_name");
		if ( ! empty ( $seopress_google_analytics_half_disable_option ) ) {
			foreach ($seopress_google_analytics_half_disable_option as $key => $seopress_google_analytics_half_disable_value)
				$options[$key] = $seopress_google_analytics_half_disable_value;
				if (isset($seopress_google_analytics_half_disable_option['seopress_google_analytics_half_disable'])) {
					return $seopress_google_analytics_half_disable_option['seopress_google_analytics_half_disable'];
				}
		}
	}

	//Disable Tracking - Message
	function seopress_google_analytics_opt_out_msg_option() {
		$seopress_google_analytics_opt_out_msg_option = get_option("seopress_google_analytics_option_name");
		if ( ! empty ( $seopress_google_analytics_opt_out_msg_option ) ) {
			foreach ($seopress_google_analytics_opt_out_msg_option as $key => $seopress_google_analytics_opt_out_msg_value)
				$options[$key] = $seopress_google_analytics_opt_out_msg_value;
				if (isset($seopress_google_analytics_opt_out_msg_option['seopress_google_analytics_opt_out_msg'])) {
					return $seopress_google_analytics_opt_out_msg_option['seopress_google_analytics_opt_out_msg'];
				}
		}
	}

	//User Consent JS
	function seopress_google_analytics_cookies_js() {
		$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script('seopress-cookies', plugins_url( 'assets/js/seopress-cookies' . $prefix . '.js', dirname( dirname( __FILE__ ) ) ), [], SEOPRESS_VERSION, true);
		wp_enqueue_script('seopress-cookies');

		wp_enqueue_script( 'seopress-cookies-ajax', plugins_url( 'assets/js/seopress-cookies-ajax' . $prefix . '.js', dirname( dirname( __FILE__ ) ) ), [ 'jquery', 'seopress-cookies' ], SEOPRESS_VERSION, true );

        $seopress_cookies_user_consent = [
	        'seopress_nonce'                => wp_create_nonce('seopress_cookies_user_consent_nonce'),
	        'seopress_cookies_user_consent' => admin_url('admin-ajax.php'),
	    ];
	    wp_localize_script( 'seopress-cookies-ajax', 'seopressAjaxGAUserConsent', $seopress_cookies_user_consent );
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
						add_action('wp_enqueue_scripts','seopress_google_analytics_cookies_js', 20, 1);
					}
				} else {
					add_action('wp_enqueue_scripts','seopress_google_analytics_cookies_js', 20, 1);
				}
			} else {
				add_action('wp_enqueue_scripts','seopress_google_analytics_cookies_js', 20, 1);
			}
		} else {
			add_action('wp_enqueue_scripts','seopress_google_analytics_cookies_js', 20, 1);
		}
	}

	add_action('wp_head', 'seopress_load_google_analytics_options', 0);
	function seopress_load_google_analytics_options() {
	    require_once ( dirname( __FILE__ ) . '/options-google-analytics.php'); //Google Analytics
	}

	function seopress_cookies_user_consent() {
	    check_ajax_referer( 'seopress_cookies_user_consent_nonce', $_GET['_ajax_nonce'], true );
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
					 	include_once ( dirname( __FILE__ ) . '/options-google-analytics.php'); //Google Analytics
					 	$data = array();
					 	$data['gtag_js'] = seopress_google_analytics_js(false);
					 	$data['body_js'] = seopress_google_analytics_body_code(false);
					 	$data['head_js'] = seopress_google_analytics_head_code(false);
					 	$data['custom'] = '';
					 	$data['custom'] = apply_filters( 'seopress_custom_tracking', $data['custom'] );
						wp_send_json_success($data);
					}
				} else {
					include_once ( dirname( __FILE__ ) . '/options-google-analytics.php'); //Google Analytics
				 	$data = array();
					$data['gtag_js'] = seopress_google_analytics_js(false);
					$data['body_js'] = seopress_google_analytics_body_code(false);
					$data['head_js'] = seopress_google_analytics_head_code(false);
				 	$data['custom'] = '';
				 	$data['custom'] = apply_filters( 'seopress_custom_tracking', $data['custom'] );
					wp_send_json_success($data);
				}
			}
		} else {
			include_once ( dirname( __FILE__ ) . '/options-google-analytics.php'); //Google Analytics
		 	$data = array();
			$data['gtag_js'] = seopress_google_analytics_js(false);
			$data['body_js'] = seopress_google_analytics_body_code(false);
			$data['head_js'] = seopress_google_analytics_head_code(false);
		 	$data['custom'] = '';
		 	$data['custom'] = apply_filters( 'seopress_custom_tracking', $data['custom'] );
			wp_send_json_success($data);
		}
	}
	add_action('wp_ajax_seopress_cookies_user_consent', 'seopress_cookies_user_consent');
	add_action('wp_ajax_nopriv_seopress_cookies_user_consent', 'seopress_cookies_user_consent');
}

add_action('wp', 'seopress_load_redirections_options', 0);
function seopress_load_redirections_options() {
	if (!is_admin()){
	    require_once ( dirname( __FILE__ ) . '/options-redirections.php'); //Redirections
	}
}

if (seopress_get_toggle_option('xml-sitemap') =='1') {
	add_action('init', 'seopress_load_sitemap', 999);
	function seopress_load_sitemap() {
		if (!is_admin()) {
			require_once ( dirname( __FILE__ ) . '/options-sitemap.php'); //XML / HTML Sitemap
		}
	}
}
if (seopress_get_toggle_option('advanced') =='1') {
	//Remove comment author url
	function seopress_advanced_advanced_comments_author_url_option() {
		$seopress_advanced_advanced_comments_author_url_option = get_option("seopress_advanced_option_name");
		if ( ! empty ( $seopress_advanced_advanced_comments_author_url_option ) ) {
			foreach ($seopress_advanced_advanced_comments_author_url_option as $key => $seopress_advanced_advanced_comments_author_url_value)
				$options[$key] = $seopress_advanced_advanced_comments_author_url_value;
			if (isset($seopress_advanced_advanced_comments_author_url_option['seopress_advanced_advanced_comments_author_url'])) {
				return $seopress_advanced_advanced_comments_author_url_option['seopress_advanced_advanced_comments_author_url'];
			}
		}
	}
	if (seopress_advanced_advanced_comments_author_url_option() =='1') {
		add_filter( 'get_comment_author_url', '__return_empty_string' );
	}

	//Remove website field in comments
	function seopress_advanced_advanced_comments_website_option() {
		$seopress_advanced_advanced_comments_website_option = get_option("seopress_advanced_option_name");
		if ( ! empty ( $seopress_advanced_advanced_comments_website_option ) ) {
			foreach ($seopress_advanced_advanced_comments_website_option as $key => $seopress_advanced_advanced_comments_website_value)
				$options[$key] = $seopress_advanced_advanced_comments_website_value;
			if (isset($seopress_advanced_advanced_comments_website_option['seopress_advanced_advanced_comments_website'])) {
				return $seopress_advanced_advanced_comments_website_option['seopress_advanced_advanced_comments_website'];
			}
		}
	}
	if (seopress_advanced_advanced_comments_website_option() =='1') {
		function seopress_advanced_advanced_comments_website_hook($fields) {
			unset($fields['url']);
			return $fields;
		}
		add_filter('comment_form_default_fields','seopress_advanced_advanced_comments_website_hook', 40);
	}

	add_action('wp_head', 'seopress_load_advanced_options', 0);
	function seopress_load_advanced_options() {
		if (!is_admin()){
		    require_once ( dirname( __FILE__ ) . '/options-advanced.php'); //Advanced
		}
	}
	add_action('init', 'seopress_load_advanced_admin_options', 11);
	function seopress_load_advanced_admin_options() {
	    require_once ( dirname( __FILE__ ) . '/options-advanced-admin.php'); //Advanced (admin)
		//Admin bar
		function seopress_advanced_appearance_adminbar_option() {
			$seopress_advanced_appearance_adminbar_option = get_option("seopress_advanced_option_name");
			if ( ! empty ( $seopress_advanced_appearance_adminbar_option ) ) {
				foreach ($seopress_advanced_appearance_adminbar_option as $key => $seopress_advanced_appearance_adminbar_value)
					$options[$key] = $seopress_advanced_appearance_adminbar_value;
				if (isset($seopress_advanced_appearance_adminbar_option['seopress_advanced_appearance_adminbar'])) {
					return $seopress_advanced_appearance_adminbar_option['seopress_advanced_appearance_adminbar'];
				}
			}
		}

		if (seopress_advanced_appearance_adminbar_option() !='') {
			add_action( 'admin_bar_menu', 'seopress_advanced_appearance_adminbar_hook', 999 );

			function seopress_advanced_appearance_adminbar_hook( $wp_admin_bar ) {
				$wp_admin_bar->remove_node( 'seopress_custom_top_level' );
			}
		}
	}
	//primary category
	function seopress_titles_primary_cat_hook($cats_0,  $cats,  $post) {
		$primary_cat	= NULL;
		if (!is_admin()) {
			global $post;
			global $product;
		}
		$post			= get_post( $post );
		if (function_exists('wc_get_product')) {
			$product		= wc_get_product( $post->ID );
		}
		if ($post) {
			$_seopress_robots_primary_cat = get_post_meta($post->ID,'_seopress_robots_primary_cat',true);
			if (isset($_seopress_robots_primary_cat) && $_seopress_robots_primary_cat !='' && $_seopress_robots_primary_cat !='none') {
				if ($post->post_type !=NULL && $post->post_type =='post') {
					$primary_cat = get_category($_seopress_robots_primary_cat);
				} elseif ($post->post_type !=NULL && $post->post_type =='product') {
					$primary_cat = get_term($_seopress_robots_primary_cat, 'product_cat');
				}
				if (!is_wp_error($primary_cat) && $primary_cat !=NULL) {
					return $primary_cat;
				}
			} else {
				//no primary cat
				return $cats_0;
			}
		} else {
			return $cats_0;
		}
	}
	add_filter( 'post_link_category', 'seopress_titles_primary_cat_hook', 10, 3 );
	//add_filter( 'post_link', 'seopress_titles_primary_cat_hook', 10, 3 );
	//add_filter( 'post_type_link', 'seopress_titles_primary_cat_hook', 10, 3 );
	//https://developer.wordpress.org/reference/hooks/post_link_category/
	//https://developer.wordpress.org/reference/hooks/post_type_link/
	//https://rudrastyh.com/wordpress/taxonomy-slug-in-post-type-url.html
	//https://rudrastyh.com/plugins/meta-boxes-options-pages#gutenbers_sidebars

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
	}

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
		    $rules = apply_filters('seopress_category_rewrite_rules', $rules);
		    return $rules;
		}

		function seopress_remove_category_base( $termlink, $term, $taxonomy ) {
			if ($taxonomy =='category') {
				$category_base = get_option( 'category_base' );
				if ( '' == $category_base ) {
					$category_base = 'category';
				}

				if ( '/' == substr( $category_base, 0, 1 ) ) {
					$category_base = substr( $category_base, 1 );
				}
				$category_base .= '/';

				return preg_replace( '`' . preg_quote( $category_base, '`' ) . '`u', '', $termlink, 1 );
			} else {
				return $termlink;
			}
		}
		add_filter( 'term_link', 'seopress_remove_category_base', 10, 3 );

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
				if (preg_match('/\/'.$category_base.'\//', $current_url)) {
					$new_url = str_replace('/'.$category_base, '', $current_url);
					wp_redirect($new_url, 301 );
					exit();
				}
			} else {
				$category_base = 'category';
				if (preg_match('/\/'.$category_base.'\//', $current_url)) {
					$new_url = str_replace('/'.$category_base, '', $current_url);
					wp_redirect($new_url, 301 );
		    		exit();
	    		}
			}
		}
	}
}
