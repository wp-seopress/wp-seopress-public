<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Sitemaps Settings
 */
class SitemapsSettings implements ExecuteHooks {
	/**
	 * The Sitemaps Settings hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Sitemaps Settings permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'xml_html_sitemap' ) );
	}

	/**
	 * The Sitemaps Settings register.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/options/sitemaps-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/options/sitemaps-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processPost' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The Sitemaps Settings process post.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function processPost( \WP_REST_Request $request ) {
		$new_options = $request->get_json_params();

		if ( empty( $new_options ) || ! is_array( $new_options ) ) {
			return new \WP_Error(
				'invalid_data',
				__( 'Invalid data provided.', 'wp-seopress' ),
				array( 'status' => 400 )
			);
		}

		// Sanitize using the same function as PHP form saves.
		$sanitized_options = seopress_sanitize_options_fields( $new_options );

		// The screen rewrites only the entry the user toggled, so without this a
		// save carries every other malformed entry straight back into the
		// option. Normalising here also repairs the stored value on the first
		// save, instead of leaving it for the read-time normaliser forever.
		$sanitized_options = $this->normalizeIncludeLists( $sanitized_options );

		// Rewrite flush is handled by the update_option hook in OptionSaveHooks.
		update_option( 'seopress_xml_sitemap_option_name', $sanitized_options );

		do_action( 'seopress_sitemaps_settings_updated', $sanitized_options );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Settings saved successfully.', 'wp-seopress' ),
				'data'    => $sanitized_options,
			),
			200
		);
	}

	/**
	 * Put both include lists into the shape the rest of the plugin expects.
	 *
	 * `SitemapOption` normalises these lists when it reads them, which is what
	 * keeps the sitemap index correct on a malformed option. This controller
	 * never goes through that service: it reads the raw option and writes it
	 * back, so the normaliser was bypassed on both sides.
	 *
	 * The consequence was visible in two places. The screen reads
	 * `list[type]['include']`, absent on a bare value, so a post type whose
	 * sitemap is being served rendered unchecked. And because the screen
	 * rewrites only the entry the user toggled, saving carried every other
	 * malformed entry back into the option.
	 *
	 * @since 10.2.0
	 *
	 * @param array $options The sitemap option group.
	 *
	 * @return array
	 */
	protected function normalizeIncludeLists( $options ) {
		return seopress_get_service( 'SitemapOption' )->normalizeIncludeLists( $options );
	}

	/**
	 * The Sitemaps Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 */
	public function processGet( \WP_REST_Request $request ) {
		$options = get_option( 'seopress_xml_sitemap_option_name' );

		if ( empty( $options ) ) {
			return new \WP_REST_Response( array() );
		}

		// Read the same shape the sitemap itself reads. The screen looks for
		// `list[type]['include']`, which a bare value does not have, so an
		// entry that is being served would otherwise render unchecked.
		$options = $this->normalizeIncludeLists( $options );

		$data = array();

		foreach ( $options as $key => $value ) {
			$data[ $key ] = $value;
		}

		return new \WP_REST_Response( $data );
	}
}
