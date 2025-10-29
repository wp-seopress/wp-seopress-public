<?php
/**
 * Elementor Meta Helper
 *
 * @package Elementor
 */

namespace WPSeoPressElementorAddon\Admin;

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Seopress Meta Helper
 */
class Seopress_Meta_Helper {

	/**
	 * Get meta fields.
	 *
	 * @return array
	 */
	public static function get_meta_fields() {
		return array(
			'_seopress_titles_title',
			'_seopress_titles_desc',
			'_seopress_robots_index',
			'_seopress_robots_follow',
			'_seopress_robots_imageindex',
			'_seopress_robots_snippet',
			'_seopress_robots_canonical',
			'_seopress_robots_primary_cat',
			'_seopress_robots_breadcrumbs',
			'_seopress_social_fb_title',
			'_seopress_social_fb_desc',
			'_seopress_social_fb_img',
			'_seopress_social_twitter_title',
			'_seopress_social_twitter_desc',
			'_seopress_social_twitter_img',
			'_seopress_redirections_enabled',
			'_seopress_redirections_type',
			'_seopress_redirections_value',
			'_seopress_analysis_target_kw',
			'_seopress_analysis_data',
		);
	}
}

/**
 * Get meta helper.
 *
 * @return Seopress_Meta_Helper Meta helper.
 */
function seopress_get_meta_helper() {
	return new Seopress_Meta_Helper();
}
