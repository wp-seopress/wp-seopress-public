<?php // phpcs:ignore

namespace SEOPress\Services\Sitemap\Render;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single
 */
class Single {
	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'SitemapRenderSingle';

	/**
	 * The hooksWPMLCompatibility function.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	protected function hooksWPMLCompatibility() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			// If multidomain setup.
			if ( 2 == apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
				// Home URL.
				add_filter(
					'seopress_sitemaps_home_url',
					function ( $home_url ) {
						$home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
						return trailingslashit( $home_url );
					}
				);
			}

			add_filter(
				'seopress_sitemaps_single_query',
				function ( $args ) {
					global $sitepress, $sitepress_settings;

					$sitepress_settings['auto_adjust_ids'] = 0;
					remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
					remove_filter( 'category_link', array( $sitepress, 'category_link_adjust_id' ), 1 );

					// If multidomain setup.
					if ( 2 == apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
						$args['suppress_filters'] = false;
					}

					return $args;
				}
			);

			add_filter( 'wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5 );
			add_action( 'the_post', 'seopress_sitemap_switch_wpml_language' );
		}

		add_filter(
			'seopress_sitemaps_single_url',
			function ( $url, $post ) {
				// Exclude custom canonical from sitemaps.
				if ( get_post_meta( $post->ID, '_seopress_robots_canonical', true ) && htmlspecialchars( urldecode( get_permalink( $post->ID ) ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) !== get_post_meta( $post->ID, '_seopress_robots_canonical', true ) ) {
					return null;
				}

				// If noindex, continue to next post.
				if ( get_post_meta( $post->ID, '_seopress_robots_index', true ) === 'yes' ) {
					return null;
				}

				// Skip entries WPML no longer serves, whether their language was
				// hidden or removed from the site without its content being
				// deleted. Both helpers are no-ops without WPML.
				if ( isset( $post->ID ) ) {
					$language_code = seopress_wpml_get_element_language( $post->ID, $post->post_type );

					if ( seopress_wpml_is_language_excluded( $language_code ) ) {
						return null;
					}
				}

				return $url;
			},
			10,
			2
		);
	}

	/**
	 * The render function.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function render() {
		if ( ! function_exists( 'seopress_get_service' ) ) {
			return;
		}

		seopress_get_service( 'SitemapHeaders' )->printHeaders();

		// Remove primary category.
		remove_filter( 'post_link_category', 'seopress_titles_primary_cat_hook', 10, 3 );

		$this->hooksWPMLCompatibility();

		ob_start();
		include_once SEOPRESS_TEMPLATE_SITEMAP_DIR . '/single.php';
		$xml = ob_get_contents();
		ob_end_clean();

		echo apply_filters( 'seopress_sitemaps_xml_single', $xml ); // phpcs:ignore.
	}
}
