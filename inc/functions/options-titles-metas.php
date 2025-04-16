<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

include_once ABSPATH . 'wp-admin/includes/plugin.php';

//Titles & metas
//=================================================================================================
//THE Title Tag
function seopress_titles_the_title() {

	$variables = null;
	$variables = apply_filters('seopress_dyn_variables_fn', $variables);

	$post                                     = $variables['post'];
	$term                                     = $variables['term'];
	$seopress_titles_title_template           = $variables['seopress_titles_title_template'];
	$seopress_titles_description_template     = $variables['seopress_titles_description_template'];
	$seopress_paged                           = $variables['seopress_paged'];
	$the_author_meta                          = $variables['the_author_meta'];
	$sep                                      = $variables['sep'];
	$seopress_excerpt                         = $variables['seopress_excerpt'];
	$post_category                            = $variables['post_category'];
	$post_tag                                 = $variables['post_tag'];
	$get_search_query                         = $variables['get_search_query'];
	$woo_single_cat_html                      = $variables['woo_single_cat_html'];
	$woo_single_tag_html                      = $variables['woo_single_tag_html'];
	$woo_single_price                         = $variables['woo_single_price'];
	$woo_single_price_exc_tax                 = $variables['woo_single_price_exc_tax'];
	$woo_single_sku                           = $variables['woo_single_sku'];
	$author_bio                               = $variables['author_bio'];
	$seopress_get_the_excerpt                 = $variables['seopress_get_the_excerpt'];
	$seopress_titles_template_variables_array = $variables['seopress_titles_template_variables_array'];
	$seopress_titles_template_replace_array   = $variables['seopress_titles_template_replace_array'];
	$seopress_excerpt_length                  = $variables['seopress_excerpt_length'];
	$page_id                                  = get_option('page_for_posts');

	if (is_front_page() && is_home() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_title', true)) { //HOMEPAGE
		if ('' !== seopress_get_service('TitleOption')->getHomeSiteTitle()) {
			$seopress_titles_the_title = esc_attr(seopress_get_service('TitleOption')->getHomeSiteTitle());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif (is_front_page() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_title', true)) { //STATIC HOMEPAGE
		if ('' !== seopress_get_service('TitleOption')->getHomeSiteTitle()) {
			$seopress_titles_the_title = esc_attr(seopress_get_service('TitleOption')->getHomeSiteTitle());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif (is_home() && '' != get_post_meta($page_id, '_seopress_titles_title', true)) { //BLOG PAGE
		if (get_post_meta($page_id, '_seopress_titles_title', true)) { //IS METABOXE
			$seopress_titles_the_title = esc_attr(get_post_meta($page_id, '_seopress_titles_title', true));

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif (is_home() && ('posts' == get_option('show_on_front'))) { //YOUR LATEST POSTS
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if (is_plugin_active('polylang/polylang.php') || is_plugin_active('polylang-pro/polylang.php')) {
		}
		if ('' !== seopress_get_service('TitleOption')->getHomeSiteTitle()) {
			$seopress_titles_the_title = esc_attr(seopress_get_service('TitleOption')->getHomeSiteTitle());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif (function_exists('bp_is_group') && bp_is_group()) {
		if ('' !== seopress_get_service('TitleOption')->getTitleBpGroups()) {
			$seopress_titles_the_title = esc_attr(seopress_get_service('TitleOption')->getTitleBpGroups());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif (is_singular()) { //IS SINGULAR
		//IS BUDDYPRESS ACTIVITY PAGE
		if (function_exists('bp_is_current_component') && true == bp_is_current_component('activity')) {
			$post->ID = buddypress()->pages->activity->id;
		}
		//IS BUDDYPRESS MEMBERS PAGE
		if (function_exists('bp_is_current_component') && true == bp_is_current_component('members')) {
			$post->ID = buddypress()->pages->members->id;
		}

		//IS BUDDYPRESS GROUPS PAGE
		if (function_exists('bp_is_current_component') && true == bp_is_current_component('groups')) {
			$post->ID = buddypress()->pages->groups->id;
		}

		if (get_post_meta($post->ID, '_seopress_titles_title', true)) { //IS METABOXE
			$seopress_titles_the_title = esc_attr(get_post_meta($post->ID, '_seopress_titles_title', true));

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_the_title, $matches); //custom fields

			if ( ! empty($matches)) {
				$seopress_titles_cf_template_variables_array = [];
				$seopress_titles_cf_template_replace_array   = [];

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$custom_field = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_meta($post->ID, $value, true), true))))), $seopress_excerpt_length);
					$seopress_titles_cf_template_replace_array[] = apply_filters('seopress_titles_custom_field', $custom_field, $value);
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_the_title, $matches2); //custom terms taxonomy

			if ( ! empty($matches2)) {
				$seopress_titles_ct_template_variables_array = [];
				$seopress_titles_ct_template_replace_array   = [];

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms($post->ID, $value);
					if ( ! is_wp_error($term) && isset($term[0])) {
						$terms                                       = esc_attr($term[0]->name);
						$seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
					}
				}
			}

			preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_the_title, $matches3); //user meta

			if ( ! empty($matches3)) {
				$seopress_titles_ucf_template_variables_array = [];
				$seopress_titles_ucf_template_replace_array   = [];

				foreach ($matches3['0'] as $key => $value) {
					$seopress_titles_ucf_template_variables_array[] = $value;
				}

				foreach ($matches3['1'] as $key => $value) {
					$user_meta = esc_attr(get_user_meta(get_the_author_meta('ID'), $value, true));
					$seopress_titles_ucf_template_replace_array[] = apply_filters('seopress_titles_user_meta', $user_meta, $value);
				}
			}

			//Default
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);

			//Custom fields
			if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_title_template);
			}

			//Custom terms taxonomy
			if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_title_template);
			}

			//User meta
			if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $seopress_titles_title_template);
			}
		} else { //DEFAULT GLOBAL
			$seopress_titles_single_titles_option = null !== $post ? esc_attr(seopress_get_service('TitleOption')->getSingleCptTitle($post->ID)) : '';

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_single_titles_option, $matches); //custom fields

			if ( ! empty($matches)) {
				$seopress_titles_cf_template_variables_array = [];
				$seopress_titles_cf_template_replace_array   = [];

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$seopress_titles_cf_template_replace_array[] = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_meta($post->ID, $value, true), true))))), $seopress_excerpt_length);
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_single_titles_option, $matches2); //custom terms taxonomy

			if ( ! empty($matches2)) {
				$seopress_titles_ct_template_variables_array = [];
				$seopress_titles_ct_template_replace_array   = [];

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms($post->ID, $value);
					if ( ! is_wp_error($term) && isset($term[0])) {
						$terms                                       = esc_attr($term[0]->name);
						$seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
					}
				}
			}

			preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_single_titles_option, $matches3); //user meta

			if ( ! empty($matches3)) {
				$seopress_titles_ucf_template_variables_array = [];
				$seopress_titles_ucf_template_replace_array   = [];

				foreach ($matches3['0'] as $key => $value) {
					$seopress_titles_ucf_template_variables_array[] = $value;
				}

				foreach ($matches3['1'] as $key => $value) {
					$seopress_titles_ucf_template_replace_array[] = esc_attr(get_user_meta(get_the_author_meta('ID'), $value, true));
				}
			}

			//Default
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_single_titles_option);

			//Custom fields
			if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_title_template);
			}

			//Custom terms taxonomy
			if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_title_template);
			}

			//User meta
			if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $seopress_titles_title_template);
			}
		}
	} elseif (is_post_type_archive() && !is_search() && !is_tax() && seopress_get_service('TitleOption')->getArchivesCPTTitle()) { //IS POST TYPE ARCHIVE (!is_tax required for TEC, !is_search required for WC search box)
		$seopress_titles_archive_titles_option = esc_attr(seopress_get_service('TitleOption')->getArchivesCPTTitle());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archive_titles_option);
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_get_service('TitleOption')->getTaxTitle()) { //IS TAX
		$seopress_titles_tax_titles_option = esc_attr(seopress_get_service('TitleOption')->getTaxTitle());

		if (get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_title', true)) {
			$seopress_titles_title_template = esc_attr(get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_title', true));
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_title_template);
		} else {
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_tax_titles_option);
		}

		preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_title_template, $matches); //custom fields

		if ( ! empty($matches)) {
			$seopress_titles_cf_template_variables_array = [];
			$seopress_titles_cf_template_replace_array   = [];

			foreach ($matches['0'] as $key => $value) {
				$seopress_titles_cf_template_variables_array[] = $value;
			}

			foreach ($matches['1'] as $key => $value) {
				$seopress_titles_cf_template_replace_array[] = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_term_meta(get_queried_object()->{'term_id'}, $value, true), true))))), $seopress_excerpt_length);
			}
		}

		//Custom fields
		if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
			$seopress_titles_title_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_title_template);
		}
	} elseif (is_author() && seopress_get_service('TitleOption')->getArchivesAuthorTitle()) { //IS AUTHOR
		$seopress_titles_archives_author_title_option = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorTitle());

		preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_archives_author_title_option, $matches); //custom fields

		if ( ! empty($matches)) {
			$seopress_titles_cf_template_variables_array = [];
			$seopress_titles_cf_template_replace_array   = [];

			foreach ($matches['0'] as $key => $value) {
				$seopress_titles_cf_template_variables_array[] = $value;
			}

			foreach ($matches['1'] as $key => $value) {
				$seopress_titles_cf_template_replace_array[] = esc_attr(get_user_meta(get_the_author_meta('ID'), $value, true));
			}
		}

		//Default
		$seopress_titles_title_template = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorTitle());

		//User meta
		if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
			$seopress_titles_title_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_title_template);
		}

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_title_template);
	} elseif (is_date() && seopress_get_service('TitleOption')->getTitleArchivesDate()) { //IS DATE
		$seopress_titles_archives_date_title_option = esc_attr(seopress_get_service('TitleOption')->getTitleArchivesDate());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_date_title_option);
	} elseif (is_search() && seopress_get_service('TitleOption')->getTitleArchivesSearch()) { //IS SEARCH
		$seopress_titles_archives_search_title_option = esc_attr(seopress_get_service('TitleOption')->getTitleArchivesSearch());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_search_title_option);
	} elseif (is_404() && seopress_get_service('TitleOption')->getTitleArchives404()) { //IS 404
		$seopress_titles_archives_404_title_option = esc_attr(seopress_get_service('TitleOption')->getTitleArchives404());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_404_title_option);
	}

	//Hook on Title tag - 'seopress_titles_title'
	if (has_filter('seopress_titles_title')) {
		$seopress_titles_title_template = apply_filters('seopress_titles_title', $seopress_titles_title_template);
	}

	//Return Title tag
	return $seopress_titles_title_template;
}

if (apply_filters('seopress_old_pre_get_document_title', true)) {
	$priority = apply_filters( 'seopress_titles_the_title_priority', 10 );
	add_filter('pre_get_document_title', 'seopress_titles_the_title', $priority);

	//Avoid TEC rewriting our title tag on Venue and Organizer pages
	if (is_plugin_active('the-events-calendar/the-events-calendar.php')) {
		if (
			function_exists('tribe_is_event') && tribe_is_event() ||
			function_exists('tribe_is_venue') && tribe_is_venue() ||
			function_exists('tribe_is_organizer') && tribe_is_organizer()
			// function_exists('tribe_is_month') && tribe_is_month() && is_tax() ||
			// function_exists('tribe_is_upcoming') && tribe_is_upcoming() && is_tax() ||
			// function_exists('tribe_is_past') && tribe_is_past() && is_tax() ||
			// function_exists('tribe_is_week') && tribe_is_week() && is_tax() ||
			// function_exists('tribe_is_day') && tribe_is_day() && is_tax() ||
			// function_exists('tribe_is_map') && tribe_is_map() && is_tax() ||
			// function_exists('tribe_is_photo') && tribe_is_photo() && is_tax()
		) {
			add_filter('pre_get_document_title', 'seopress_titles_the_title', 20);
		}
	}

	//Avoid Surecart rewriting our title tag
	if (is_plugin_active('surecart/surecart.php')) {
		if (is_singular( 'sc_product' )) {
			add_filter('pre_get_document_title', 'seopress_titles_the_title', 214748364);
		}
	}
}

//THE Meta Description
function seopress_titles_the_description_content() {
	$variables = null;
	$variables = apply_filters('seopress_dyn_variables_fn', $variables);

	$post 										                           = $variables['post'];
	$term 										                           = $variables['term'];
	$seopress_titles_title_template 			        = $variables['seopress_titles_title_template'];
	$seopress_titles_description_template 		   = $variables['seopress_titles_description_template'];
	$seopress_paged 							                    = $variables['seopress_paged'];
	$the_author_meta 							                   = $variables['the_author_meta'];
	$sep 										                            = $variables['sep'];
	$seopress_excerpt 							                  = $variables['seopress_excerpt'];
	$post_category 								                    = $variables['post_category'];
	$post_tag 									                        = $variables['post_tag'];
	$post_thumbnail_url 						                 = $variables['post_thumbnail_url'];
	$get_search_query 							                  = $variables['get_search_query'];
	$woo_single_cat_html 						                = $variables['woo_single_cat_html'];
	$woo_single_tag_html 						                = $variables['woo_single_tag_html'];
	$woo_single_price 							                  = $variables['woo_single_price'];
	$woo_single_price_exc_tax					             = $variables['woo_single_price_exc_tax'];
	$woo_single_sku 							                    = $variables['woo_single_sku'];
	$author_bio 								                       = $variables['author_bio'];
	$seopress_get_the_excerpt 					            = $variables['seopress_get_the_excerpt'];
	$seopress_titles_template_variables_array 	= $variables['seopress_titles_template_variables_array'];
	$seopress_titles_template_replace_array 	  = $variables['seopress_titles_template_replace_array'];
	$seopress_excerpt_length 					             = $variables['seopress_excerpt_length'];
	$page_id 									                         = get_option('page_for_posts');

	if (is_front_page() && is_home() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_desc', true)) { //HOMEPAGE
		if ('' !== seopress_get_service('TitleOption')->getHomeDescriptionTitle()) { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getHomeDescriptionTitle());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (is_front_page() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_desc', true)) { //STATIC HOMEPAGE
		if ('' !== seopress_get_service('TitleOption')->getHomeDescriptionTitle() && '' == get_post_meta($post->ID, '_seopress_titles_desc', true)) { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getHomeDescriptionTitle());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (is_home() && '' != get_post_meta($page_id, '_seopress_titles_desc', true)) { //BLOG PAGE
		if (get_post_meta($page_id, '_seopress_titles_desc', true)) {
			$seopress_titles_the_description_meta = esc_html(get_post_meta($page_id, '_seopress_titles_desc', true));
			$seopress_titles_the_description      = $seopress_titles_the_description_meta;

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (is_home() && ('posts' == get_option('show_on_front'))) { //YOUR LATEST POSTS
		if ('' !== seopress_get_service('TitleOption')->getHomeDescriptionTitle()) { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getHomeDescriptionTitle());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (function_exists('bp_is_group') && bp_is_group()) {
		if ('' !== seopress_get_service('TitleOption')->getBpGroupsDesc()) {
			$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getBpGroupsDesc());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (is_singular()) { //IS SINGLE
		if (get_post_meta($post->ID, '_seopress_titles_desc', true)) { //IS METABOXE
			$seopress_titles_the_description = esc_attr(get_post_meta($post->ID, '_seopress_titles_desc', true));

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_the_description, $matches); //custom fields

			if ( ! empty($matches)) {
				$seopress_titles_cf_template_variables_array = [];
				$seopress_titles_cf_template_replace_array   = [];

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$custom_field = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_meta($post->ID, $value, true), true))))), $seopress_excerpt_length);
					$seopress_titles_cf_template_replace_array[] = apply_filters('seopress_titles_custom_field', $custom_field, $value);
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_the_description, $matches2); //custom terms taxonomy

			if ( ! empty($matches2)) {
				$seopress_titles_ct_template_variables_array = [];
				$seopress_titles_ct_template_replace_array   = [];

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms($post->ID, $value);
					if ( ! is_wp_error($term) && isset($term[0])) {
						$terms                                       = esc_attr($term[0]->name);
						$seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
					}
				}
			}

			preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_the_description, $matches3); //user meta

			if ( ! empty($matches3)) {
				$seopress_titles_ucf_template_variables_array = [];
				$seopress_titles_ucf_template_replace_array   = [];

				foreach ($matches3['0'] as $key => $value) {
					$seopress_titles_ucf_template_variables_array[] = $value;
				}

				foreach ($matches3['1'] as $key => $value) {
					$user_meta = esc_attr(get_user_meta(get_the_author_meta('ID'), $value, true));
					$seopress_titles_ucf_template_replace_array[] = apply_filters('seopress_titles_user_meta', $user_meta, $value);
				}
			}

			//Default
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);

			//Custom fields
			if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_description_template);
			}

			//Custom terms taxonomy
			if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_description_template);
			}

			//User meta
			if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $seopress_titles_description_template);
			}
		} elseif ('' !== seopress_get_service('TitleOption')->getSingleCptDesc($post->ID)) { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getSingleCptDesc($post->ID));

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_the_description, $matches); //custom fields

			if ( ! empty($matches)) {
				$seopress_titles_cf_template_variables_array = [];
				$seopress_titles_cf_template_replace_array   = [];

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$seopress_titles_cf_template_replace_array[] = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_meta($post->ID, $value, true), true))))), $seopress_excerpt_length);
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_the_description, $matches2); //custom terms taxonomy

			if ( ! empty($matches2)) {
				$seopress_titles_ct_template_variables_array = [];
				$seopress_titles_ct_template_replace_array   = [];

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms($post->ID, $value);
					if ( ! is_wp_error($term) && isset($term[0])) {
						$terms                                       = esc_attr($term[0]->name);
						$seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
					}
				}
			}

			preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_the_description, $matches3); //user meta

			if ( ! empty($matches3)) {
				$seopress_titles_ucf_template_variables_array = [];
				$seopress_titles_ucf_template_replace_array   = [];

				foreach ($matches3['0'] as $key => $value) {
					$seopress_titles_ucf_template_variables_array[] = $value;
				}

				foreach ($matches3['1'] as $key => $value) {
					$seopress_titles_ucf_template_replace_array[] = esc_attr(get_user_meta(get_the_author_meta('ID'), $value, true));
				}
			}

			//Default
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);

			//Custom fields
			if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_description_template);
			}

			//Custom terms taxonomy
			if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_description_template);
			}

			//User meta
			if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $seopress_titles_description_template);
			}
		} else {
			setup_postdata($post);
			if ('' != $seopress_get_the_excerpt || '' != get_the_content()) { //DEFAULT EXCERPT OR THE CONTENT
				$seopress_titles_the_description = wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses($seopress_get_the_excerpt)), $seopress_excerpt_length);

				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			}
		}
	} elseif (is_post_type_archive() && !is_search() && !is_tax() && seopress_get_service('TitleOption')->getArchivesCPTDesc()) { //IS POST TYPE ARCHIVE (!is_tax required for TEC, !is_search required for WC search box)
		$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getArchivesCPTDesc());

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_get_service('TitleOption')->getTaxDesc()) { //IS TAX
		$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getTaxDesc());

		if (get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_desc', true)) {
			$seopress_titles_description_template = esc_attr(get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_desc', true));
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_description_template);
		} else {
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}

		preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_description_template, $matches); //custom fields

		if ( ! empty($matches)) {
			$seopress_titles_cf_template_variables_array = [];
			$seopress_titles_cf_template_replace_array   = [];

			foreach ($matches['0'] as $key => $value) {
				$seopress_titles_cf_template_variables_array[] = $value;
			}

			foreach ($matches['1'] as $key => $value) {
				$seopress_titles_cf_template_replace_array[] = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_term_meta(get_queried_object()->{'term_id'}, $value, true), true))))), $seopress_excerpt_length);
			}
		}

		//Custom fields
		if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
			$seopress_titles_description_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_description_template);
		}
	} elseif (is_author() && seopress_get_service('TitleOption')->getArchivesAuthorDescription()) { //IS AUTHOR
		$seopress_titles_archives_author_desc_option = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorDescription());

		preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_archives_author_desc_option, $matches); //custom fields

		if ( ! empty($matches)) {
			$seopress_titles_cf_template_variables_array = [];
			$seopress_titles_cf_template_replace_array   = [];

			foreach ($matches['0'] as $key => $value) {
				$seopress_titles_cf_template_variables_array[] = $value;
			}

			foreach ($matches['1'] as $key => $value) {
				$seopress_titles_cf_template_replace_array[] = esc_attr(get_user_meta(get_the_author_meta('ID'), $value, true));
			}
		}

		//Default
		$seopress_titles_description_template = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorDescription());

		//User meta
		if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
			$seopress_titles_description_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_description_template);
		}

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_description_template);
	} elseif (is_date() && seopress_get_service('TitleOption')->getArchivesDateDesc()) { //IS DATE
		$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getArchivesDateDesc());

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif (is_search() && seopress_get_service('TitleOption')->getArchivesSearchDesc()) { //IS SEARCH
		$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getArchivesSearchDesc());

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif (is_404() && seopress_get_service('TitleOption')->getArchives404Desc()) { //IS 404
		$seopress_titles_the_description = esc_attr(seopress_get_service('TitleOption')->getArchives404Desc());

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	}
	//Hook on meta description - 'seopress_titles_desc'
	if (has_filter('seopress_titles_desc')) {
		$seopress_titles_description_template = apply_filters('seopress_titles_desc', $seopress_titles_description_template);
	}
	//Return meta desc tag
	return $seopress_titles_description_template;
}
function seopress_titles_the_description() {
	if ('' != seopress_titles_the_description_content()) {
		$html = '<meta name="description" content="' . seopress_titles_the_description_content() . '">';
		$html .= "\n";
		echo $html;
	}
}

if (apply_filters('seopress_old_wp_head_description', true)) {
	add_action('wp_head', 'seopress_titles_the_description', 1);
}

//Advanced
//noindex single CPT
function seopress_titles_noindex_post_option() {
	$_seopress_robots_index = get_post_meta(get_the_ID(), '_seopress_robots_index', true);
	if ('yes' == $_seopress_robots_index) {
		return $_seopress_robots_index;
	}
}

function seopress_titles_noindex_bypass() {
	//init
	$seopress_titles_noindex ='';
	$page_id                 = get_option('page_for_posts');
	if (is_singular() && true === post_password_required()) { //if password required, set noindex
		$seopress_titles_noindex = 'noindex';
	} else {
		if (seopress_get_service('TitleOption')->getTitleNoIndex()) { //Global Advanced tab
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getTitleNoIndex();
		} elseif (is_singular() && seopress_get_service('TitleOption')->getSingleCptNoIndex()) { //Single CPT Global
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getSingleCptNoIndex();
		} elseif (is_singular() && seopress_titles_noindex_post_option()) { //Single CPT Metaboxe
			$seopress_titles_noindex = seopress_titles_noindex_post_option();
		} elseif (is_home() && '' != get_post_meta($page_id, '_seopress_robots_index', true)) { //BLOG PAGE
			$seopress_titles_noindex = get_post_meta($page_id, '_seopress_robots_index', true);
		} elseif (is_post_type_archive() && seopress_get_service('TitleOption')->getArchivesCPTNoIndex()) { //Is POST TYPE ARCHIVE
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getArchivesCPTNoIndex();
		} elseif (((is_tax() || is_category() || is_tag()) && !is_search()) && seopress_get_service('TitleOption')->getTaxNoIndex()) { //Is TAX
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getTaxNoIndex();
		} elseif (is_author() && seopress_get_service('TitleOption')->getArchiveAuthorNoIndex()) { //Is Author archive
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getArchiveAuthorNoIndex();
		} elseif (function_exists('bp_is_group') && bp_is_group() && seopress_get_service('TitleOption')->getBpGroupsNoIndex()) { //Is BuddyPress group single
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getBpGroupsNoIndex();
		} elseif (is_date() && seopress_get_service('TitleOption')->getArchivesDateNoIndex()) { //Is Date archive
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getArchivesDateNoIndex();
		} elseif (is_search() && seopress_get_service('TitleOption')->getArchivesSearchNoIndex()) {//Is Search
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getArchivesSearchNoIndex();
		} elseif (is_paged() && seopress_get_service('TitleOption')->getPagedNoIndex()) {//Is paged archive
			$seopress_titles_noindex = seopress_get_service('TitleOption')->getPagedNoIndex();
		} elseif (is_404()) { //Is 404 page
			$seopress_titles_noindex = 'noindex';
		} elseif (is_attachment() && seopress_get_service('TitleOption')->getAttachmentsNoIndex()) {
			$seopress_titles_noindex = 'noindex';
		} elseif(isset( $_GET['replytocom'] ) ) {
			$seopress_titles_noindex = 'noindex';
			remove_filter( 'wp_robots', 'wp_robots_no_robots' );
		}
	}

	$seopress_titles_noindex = apply_filters('seopress_titles_noindex_bypass', $seopress_titles_noindex);

	//remove hreflang if noindex
	if ('1' == $seopress_titles_noindex || true == $seopress_titles_noindex) {
		//WPML
		add_filter('wpml_hreflangs', '__return_false');

		//MultilingualPress v2
		add_filter('multilingualpress.render_hreflang', '__return_false');

		//TranslatePress
		add_filter('trp-exclude-hreflang', '__return_true');
	}
	//Return noindex tag
	return $seopress_titles_noindex;
}

//nofollow
//nofollow Global Avanced tab
function seopress_titles_nofollow_post_option() {
	$_seopress_robots_follow = get_post_meta(get_the_ID(), '_seopress_robots_follow', true);
	if ('yes' == $_seopress_robots_follow) {
		return $_seopress_robots_follow;
	}
}

function seopress_titles_nofollow_bypass() {
	//init
	$seopress_titles_nofollow ='';
	$page_id                  = get_option('page_for_posts');
	if (seopress_get_service('TitleOption')->getTitleNoFollow()) { //Single CPT Global Advanced tab
		$seopress_titles_nofollow = seopress_get_service('TitleOption')->getTitleNoFollow();
	} elseif (is_singular() && seopress_get_service('TitleOption')->getSingleCptNoFollow()) { //Single CPT Global
		$seopress_titles_nofollow = seopress_get_service('TitleOption')->getSingleCptNoFollow();
	} elseif (is_singular() && seopress_titles_nofollow_post_option()) { //Single CPT Metaboxe
		$seopress_titles_nofollow = seopress_titles_nofollow_post_option();
	} elseif (is_home() && '' != get_post_meta($page_id, '_seopress_robots_follow', true)) { //BLOG PAGE
		$seopress_titles_nofollow = get_post_meta($page_id, '_seopress_robots_follow', true);
	} elseif (is_post_type_archive() && seopress_get_service('TitleOption')->getArchivesCPTNoFollow()) { //IS POST TYPE ARCHIVE
		$seopress_titles_nofollow = seopress_get_service('TitleOption')->getArchivesCPTNoFollow();
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_get_service('TitleOption')->getTaxNoFollow()) { //IS TAX
		$seopress_titles_nofollow = seopress_get_service('TitleOption')->getTaxNoFollow();
	}

	return $seopress_titles_nofollow;
}

//date in SERPs
function seopress_titles_single_cpt_date_hook() {
	if ( ! is_front_page() && ! is_home()) {
		if (is_singular() && '1' === seopress_get_service('TitleOption')->getSingleCptDate()) {
			$seopress_get_current_pub_post_date = get_the_date('c');
			$seopress_get_current_up_post_date  = get_the_modified_date('c');
			$html                               = '<meta property="article:published_time" content="' . $seopress_get_current_pub_post_date . '">';
			$html .= "\n";

			$html = apply_filters('seopress_titles_article_published_time', $html);

			echo $html;

			$html = '<meta property="article:modified_time" content="' . $seopress_get_current_up_post_date . '">';
			$html .= "\n";

			$html = apply_filters('seopress_titles_article_modified_time', $html);

			echo $html;

			$html = '<meta property="og:updated_time" content="' . $seopress_get_current_up_post_date . '">';
			$html .= "\n";

			$html = apply_filters('seopress_titles_og_updated_time', $html);

			echo $html;
		}
	}
}
add_action('wp_head', 'seopress_titles_single_cpt_date_hook', 1);

//thumbnail in Google Custom Search
function seopress_titles_single_cpt_thumb_gcs() {
	if ( ! is_front_page() && ! is_home()) {
		if (is_singular() && '1' === seopress_get_service('TitleOption')->getSingleCptThumb()) {
			if (get_the_post_thumbnail_url(get_the_ID())) {
				$html = '<meta name="thumbnail" content="' . get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') . '">';
				$html .= "\n";

				$html = apply_filters('seopress_titles_gcs_thumbnail', $html);

				echo $html;
			}
		}
	}
}
add_action('wp_head', 'seopress_titles_single_cpt_thumb_gcs', 1);

//nosnippet
function seopress_titles_nosnippet_post_option() {
	$_seopress_robots_snippet = get_post_meta(get_the_ID(), '_seopress_robots_snippet', true);
	if ('yes' == $_seopress_robots_snippet) {
		return $_seopress_robots_snippet;
	}
}

function seopress_titles_nosnippet_bypass() {
	$page_id = get_option('page_for_posts');
	if (seopress_get_service('TitleOption')->getTitleNoSnippet()) {
		return seopress_get_service('TitleOption')->getTitleNoSnippet();
	} elseif (is_singular() && seopress_titles_nosnippet_post_option()) {
		return seopress_titles_nosnippet_post_option();
	} elseif (is_home() && '' != get_post_meta($page_id, '_seopress_robots_snippet', true)) { //BLOG PAGE
		return get_post_meta($page_id, '_seopress_robots_snippet', true);
	} elseif ((is_tax() || is_category() || is_tag()) && !is_search()) {
		$queried_object = get_queried_object();
		if ($queried_object !== null && 'yes' == get_term_meta($queried_object->term_id, '_seopress_robots_snippet', true)) {
			return get_term_meta($queried_object->term_id, '_seopress_robots_snippet', true);
		}
	}
}

//noimageindex
function seopress_titles_noimageindex_post_option() {
	$_seopress_robots_imageindex = get_post_meta(get_the_ID(), '_seopress_robots_imageindex', true);
	if ('yes' == $_seopress_robots_imageindex) {
		return $_seopress_robots_imageindex;
	}
}

function seopress_titles_noimageindex_bypass() {
	if (seopress_get_service('TitleOption')->getTitleNoImageIndex()) {
		return seopress_get_service('TitleOption')->getTitleNoImageIndex();
	} elseif (is_singular() && seopress_titles_noimageindex_post_option()) {
		return seopress_titles_noimageindex_post_option();
	} elseif (is_tax() || is_category() || is_tag()) {
		$queried_object = get_queried_object();
		if (null != $queried_object) {
			if ('yes' == get_term_meta($queried_object->term_id, '_seopress_robots_imageindex', true)) {
				return get_term_meta($queried_object->term_id, '_seopress_robots_imageindex', true);
			}
		}
	}
}

//Polylang
function seopress_remove_hreflang_polylang($hreflangs) {
	$hreflangs = [];

	return $hreflangs;
}

if ('0' != get_option('blog_public')) {// Discourage search engines from indexing this site is OFF
	function seopress_titles_advanced_robots_hook() {
		$seopress_comma_array = [];

		if ('' != seopress_titles_noindex_bypass()) {
			$seopress_titles_noindex = 'noindex';
			//Hook on meta robots noindex - 'seopress_titles_noindex'
			if (has_filter('seopress_titles_noindex')) {
				$seopress_titles_noindex = apply_filters('seopress_titles_noindex', $seopress_titles_noindex);
			}
			array_push($seopress_comma_array, $seopress_titles_noindex);
		}
		if ('' != seopress_titles_nofollow_bypass()) {
			$seopress_titles_nofollow = 'nofollow';
			//Hook on meta robots nofollow - 'seopress_titles_nofollow'
			if (has_filter('seopress_titles_nofollow')) {
				$seopress_titles_nofollow = apply_filters('seopress_titles_nofollow', $seopress_titles_nofollow);
			}
			array_push($seopress_comma_array, $seopress_titles_nofollow);
		}
		if ('' != seopress_titles_noimageindex_bypass()) {
			$seopress_titles_noimageindex = 'noimageindex';
			//Hook on meta robots noimageindex - 'seopress_titles_noimageindex'
			if (has_filter('seopress_titles_noimageindex')) {
				$seopress_titles_noimageindex = apply_filters('seopress_titles_noimageindex', $seopress_titles_noimageindex);
			}
			array_push($seopress_comma_array, $seopress_titles_noimageindex);
		}
		if ('' != seopress_titles_nosnippet_bypass()) {
			$seopress_titles_nosnippet = 'nosnippet';
			//Hook on meta robots nosnippet - 'seopress_titles_nosnippet'
			if (has_filter('seopress_titles_nosnippet')) {
				$seopress_titles_nosnippet = apply_filters('seopress_titles_nosnippet', $seopress_titles_nosnippet);
			}
			array_push($seopress_comma_array, $seopress_titles_nosnippet);
		}

		//remove hreflang tag from Polylang if noindex
		if (in_array('noindex', $seopress_comma_array)) {
			add_filter('pll_rel_hreflang_attributes', 'seopress_remove_hreflang_polylang');
		}

		if ( ! in_array('noindex', $seopress_comma_array) && ! in_array('nofollow', $seopress_comma_array)) {
			$seopress_titles_max_snippet = 'index, follow';
			array_unshift($seopress_comma_array, $seopress_titles_max_snippet);
		}

		if (in_array('nofollow', $seopress_comma_array) && ! in_array('noindex', $seopress_comma_array)) {
			$seopress_titles_max_snippet = 'index';
			array_unshift($seopress_comma_array, $seopress_titles_max_snippet);
		}

		if (in_array('noindex', $seopress_comma_array) && ! in_array('nofollow', $seopress_comma_array)) {
			$seopress_titles_max_snippet = 'follow';
			array_unshift($seopress_comma_array, $seopress_titles_max_snippet);
		}

		if ( ! in_array('noindex', $seopress_comma_array)) {
			$seopress_titles_max_snippet = 'max-snippet:-1, max-image-preview:large, max-video-preview:-1';
			array_push($seopress_comma_array, $seopress_titles_max_snippet);
		}

		//Default meta robots
		$seopress_titles_robots = '<meta name="robots" content="';

		$seopress_comma_array = apply_filters('seopress_titles_robots_attrs', $seopress_comma_array);

		$seopress_comma_count = count($seopress_comma_array);
		for ($i = 0; $i < $seopress_comma_count; ++$i) {
			$seopress_titles_robots .= $seopress_comma_array[$i];
			if ($i < ($seopress_comma_count - 1)) {
				$seopress_titles_robots .= ', ';
			}
		}

		$seopress_titles_robots .= '">';
		$seopress_titles_robots .= "\n";

		//Hook on meta robots all - 'seopress_titles_robots'
		if (has_filter('seopress_titles_robots')) {
			$seopress_titles_robots = apply_filters('seopress_titles_robots', $seopress_titles_robots);
		}
		echo $seopress_titles_robots;
	}
	add_action('wp_head', 'seopress_titles_advanced_robots_hook', 1);
}

//link rel prev/next
if (seopress_get_service('TitleOption')->getPagedRel()) {
	function seopress_titles_paged_rel_hook() {
		global $paged;
		$html = '';
		
		if (get_previous_posts_link()) { 
			$html .= '<link rel="prev" href="' . get_pagenum_link($paged - 1) . '">';
		}
		if (get_next_posts_link()) { 
			$html .= '<link rel="next" href="' . get_pagenum_link($paged + 1) . '">';
		}

		$html = apply_filters('seopress_titles_paged_rel', $html, $paged);
			
		echo $html;
	}
	add_action('wp_head', 'seopress_titles_paged_rel_hook', 9);
}

//canonical
function seopress_titles_canonical_post_option() {
	$_seopress_robots_canonical = get_post_meta(get_the_ID(), '_seopress_robots_canonical', true);
	if ('' != $_seopress_robots_canonical) {
		return $_seopress_robots_canonical;
	}
}

function seopress_titles_canonical_term_option() {
	$queried_object = get_queried_object();
	$termId         =  null !== $queried_object ? $queried_object->term_id : '';
	if ( ! empty($termId)) {
		$_seopress_robots_canonical = get_term_meta($termId, '_seopress_robots_canonical', true);
		if ('' != $_seopress_robots_canonical) {
			return $_seopress_robots_canonical;
		}
	}
}

if (function_exists('seopress_titles_noindex_bypass') && '1' != seopress_titles_noindex_bypass() && 'yes' != seopress_titles_noindex_bypass()) {//Remove Canonical if noindex
	$page_id = get_option('page_for_posts');
	if (is_singular() && seopress_titles_canonical_post_option()) { //CUSTOM SINGLE CANONICAL
		function seopress_titles_canonical_post_hook() {
			$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode(seopress_titles_canonical_post_option())) . '">';
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
			}
			echo $seopress_titles_canonical . "\n";
		}
		add_action('wp_head', 'seopress_titles_canonical_post_hook', 1);
	} elseif (is_home() && '' != get_post_meta($page_id, '_seopress_robots_canonical', true)) { //BLOG PAGE
		function seopress_titles_canonical_post_hook() {
			$page_id                   = get_option('page_for_posts');
			$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode(get_post_meta($page_id, '_seopress_robots_canonical', true))) . '">';
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
			}
			echo $seopress_titles_canonical . "\n";
		}
		add_action('wp_head', 'seopress_titles_canonical_post_hook', 1, 1);
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_canonical_term_option()) { //CUSTOM TERM CANONICAL
		function seopress_titles_canonical_term_hook() {
			$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode(seopress_titles_canonical_term_option())) . '">';
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
			}
			echo $seopress_titles_canonical . "\n";
		}
		add_action('wp_head', 'seopress_titles_canonical_term_hook', 1);
	} elseif ( ! is_404()) { //DEFAULT CANONICAL
		function seopress_titles_canonical_hook() {
			global $wp;
		 
			$current_url = user_trailingslashit(home_url(add_query_arg([], $wp->request)));
			// WPML
			if (class_exists('SitePress')) {
				$my_default_lang = '';
				$my_current_lang = '';
				$post_ID = get_the_ID();
				$post_type = get_post_type( $post_ID );
				$transl_status = apply_filters( 'wpml_element_translation_type', NULL, $post_ID, $post_type );
			
				// If the post is not translated, switch to the default language
				if ($transl_status != 1) { 
					$my_default_lang = apply_filters('wpml_default_language', NULL );
					$my_current_lang = apply_filters( 'wpml_current_language', NULL );
					do_action( 'wpml_switch_language', $my_default_lang );
				}
			}

			if (is_search()) {
				$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode(get_home_url() . '/search/' . get_search_query())) . '">';
			} elseif (is_paged() && is_singular()) {//Paginated pages
				$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode(get_permalink())) . '">';
			} elseif (is_paged()) {
				$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode($current_url)) . '">';
			} elseif (is_singular()) {
				$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode(get_permalink())) . '">';
			} else {
				$seopress_titles_canonical = '<link rel="canonical" href="' . htmlspecialchars(urldecode($current_url)) . '">';
			}
			
			// WPML: Then switch back to the current language
			if (class_exists('SitePress')) {
				if ($transl_status != 1) {
					do_action( 'wpml_switch_language', $my_current_lang );
				}
			}
			
			// Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
			}
			echo $seopress_titles_canonical . "\n";
		}
		add_action('wp_head', 'seopress_titles_canonical_hook', 1);
	}
}
