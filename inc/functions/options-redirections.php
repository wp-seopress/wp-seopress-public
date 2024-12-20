<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Redirections
//=================================================================================================
//Enabled
function seopress_redirections_enabled() {
	if (is_home() && get_option( 'page_for_posts' ) !='' && get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_enabled',true)) {
		$seopress_redirections_enabled = get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_enabled',true);
		return $seopress_redirections_enabled;
	} else {
		global $post;
		if ($post) {
			if (get_post_meta($post->ID,'_seopress_redirections_enabled',true)) {
				$seopress_redirections_enabled = get_post_meta($post->ID,'_seopress_redirections_enabled',true);
				return $seopress_redirections_enabled;
			}
		}
	}
}

function seopress_redirections_term_enabled() {
	if (!get_queried_object_id()) {
        return;
	}

    $value = get_term_meta(get_queried_object_id(),'_seopress_redirections_enabled',true);
    if (empty($value)) {
        return;
    }

    return $value;
}

//Login status
function seopress_redirections_logged_status() {
	if (is_home() && get_option( 'page_for_posts' ) !='' && get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_logged_status',true)) {
		$seopress_redirections_logged_status = get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_logged_status',true);
		return $seopress_redirections_logged_status;
	} else {
		global $post;
		if ($post) {
			if (get_post_meta($post->ID,'_seopress_redirections_logged_status',true)) {
				$seopress_redirections_logged_status = get_post_meta($post->ID,'_seopress_redirections_logged_status',true);
				return $seopress_redirections_logged_status;
			}
		}
	}
}
function seopress_redirections_term_logged_status() {
	if (!get_queried_object_id()) {
        return;
	}

    $value = get_term_meta(get_queried_object_id(),'_seopress_redirections_logged_status',true);
    if (empty($value)) {
        return;
    }

    return $value;
}

//Type
function seopress_redirections_type() {
	if (is_home() && get_option( 'page_for_posts' ) !='' && get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_type',true)) {
		$seopress_redirections_type = get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_type',true);
		return $seopress_redirections_type;
	} else {
		global $post;
		if (get_post_meta($post->ID,'_seopress_redirections_type',true)) {
			$seopress_redirections_type = get_post_meta($post->ID,'_seopress_redirections_type',true);
			return $seopress_redirections_type;
		}
	}
}

function seopress_redirections_term_type() {
	if (!get_queried_object_id()) {
        return;
	}
    $value = get_term_meta(get_queried_object_id(),'_seopress_redirections_type',true);
    if (empty($value)) {
        return;
    }

    return $value;
}

//URL to redirect
function seopress_redirections_value() {
	global $post;
	if (is_singular() && get_post_meta($post->ID,'_seopress_redirections_value',true)) {
		$seopress_redirections_value = html_entity_decode(esc_url(get_post_meta($post->ID,'_seopress_redirections_value',true)));
		return $seopress_redirections_value;
	} elseif (is_home() && get_option( 'page_for_posts' ) !='' && get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_value',true)) {
		$seopress_redirections_value = html_entity_decode(esc_url(get_post_meta(get_option( 'page_for_posts' ),'_seopress_redirections_value',true)));
		return $seopress_redirections_value;
 	} elseif ((is_tax() || is_category() || is_tag()) && get_term_meta(get_queried_object_id(),'_seopress_redirections_value',true) !='') {
		$seopress_redirections_value = html_entity_decode(esc_url(get_term_meta(get_queried_object_id(),'_seopress_redirections_value',true)));
		return $seopress_redirections_value;
	} else {
		$request_uri = '';
		if (isset($_SERVER['REQUEST_URI'])) {
			$request_uri = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));
		}
		$seopress_redirections_value = basename(wp_parse_url($request_uri, PHP_URL_PATH));
		$seopress_redirections_query = new WP_Query( array(
				'post_type' => 'seopress_404',
				'posts_per_page' => '-1',
				'update_post_term_cache' => false, // don't retrieve post terms
				'update_post_meta_cache' => false, // don't retrieve post meta
			)
		);
		$all_titles = array();
		if ( $seopress_redirections_query->have_posts() ) {
			while ( $seopress_redirections_query->have_posts() ) {
				$seopress_redirections_query->the_post();
				array_push($all_titles, get_the_title());
			}
			if (in_array($seopress_redirections_value, $all_titles)) {
				//do_redirect
				return $seopress_redirections_value;
			}
			wp_reset_postdata();
		}
	}
}

function seopress_redirections_hook() {
	//If the current screen is: Elementor editor
	if ( class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
		return;
	}

	//If the current screen is: Elementor preview mode
	if ( class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
		return;
	}

    $metaValueByLoggedIn = \is_user_logged_in() ? 'only_logged_in' : 'only_not_logged_in';

    // Term
	if ((is_tax() || is_category() || is_tag()) && seopress_redirections_term_enabled() =='yes') {
        if (seopress_redirections_term_logged_status() === $metaValueByLoggedIn || seopress_redirections_term_logged_status() === 'both' || empty(seopress_redirections_term_logged_status())) {
            if (seopress_redirections_term_type() && seopress_redirections_value() !='') {
                wp_redirect( seopress_redirections_value(), seopress_redirections_term_type() );
                exit();
            }
        }
	}
    // Post
    elseif (seopress_redirections_enabled() =='yes') {
        if (seopress_redirections_logged_status() === $metaValueByLoggedIn || seopress_redirections_logged_status() === 'both' || empty(seopress_redirections_logged_status())) {
            if (seopress_redirections_type() && seopress_redirections_value() !='') {
                wp_redirect( seopress_redirections_value(), seopress_redirections_type() );
                exit();
            }
        }
	}
}
add_action('template_redirect', 'seopress_redirections_hook', 1);
