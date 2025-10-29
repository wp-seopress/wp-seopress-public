<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RequestPreview
 */
class RequestPreview {

	/**
	 * The getLinkRequest function.
	 *
	 * @param int    $id The id.
	 * @param string $taxname The taxname.
	 *
	 * @return string
	 */
	public function getLinkRequest( $id, $taxname = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$args = array( 'no_admin_bar' => 1 );

		// Useful for Page / Theme builders.
		$args = apply_filters( 'seopress_real_preview_custom_args', $args );

		// Post type.
		if ( empty( $taxname ) ) {
			$theme = wp_get_theme();
			// Oxygen / beTheme compatibility.
			$oxygen_metabox_enabled = get_option( 'oxygen_vsb_ignore_post_type_' . get_post_type( $id ) ) ? false : true;
			if (
				( is_plugin_active( 'oxygen/functions.php' ) && function_exists( 'ct_template_output' ) && true === $oxygen_metabox_enabled )
				||
				( 'betheme' === $theme->template || 'Betheme' === $theme->parent_theme )
			) {
				$link = get_permalink( (int) $id );
				$link = add_query_arg( 'no_admin_bar', 1, $link );
			} else {
				$link = add_query_arg( 'no_admin_bar', 1, get_preview_post_link( (int) $id, $args ) );
			}
		} else {
			// Taxonomy.
			$link = get_term_link( (int) $id, $taxname );
			$link = add_query_arg( 'no_admin_bar', 1, $link );
		}

		$link = apply_filters( 'seopress_get_dom_link', $link, $id );

		return $link;
	}

	/**
	 * The getDomById function.
	 *
	 * @param int    $id The id.
	 * @param string $taxname The taxname.
	 *
	 * @return string
	 */
	public function getDomById( $id, $taxname = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$args = array(
			'redirection' => 2,
			'timeout'     => 30,
			'sslverify'   => false,
		);

		// Get cookies.
		$cookies = array();
		if ( isset( $_COOKIE ) ) {
			foreach ( $_COOKIE as $name => $value ) {
				if ( 'PHPSESSID' !== $name ) {
					if ( is_array( $value ) ) {
						$value = implode( '|', $value );
					}
					$cookies[] = new \WP_Http_Cookie(
						array(
							'name'  => $name,
							'value' => $value,
						)
					);
				}
			}
		}

		if ( ! empty( $cookies ) ) {
			$args['cookies'] = $cookies;
		}

		$args = apply_filters( 'seopress_real_preview_remote', $args );

		$link = $this->getLinkRequest( $id, $taxname );

		try {
			$response = wp_remote_get( $link, $args );

			$code_response = wp_remote_retrieve_response_code( $response );
			if ( is_wp_error( $response ) || in_array( $code_response, array( 404, 401 ), true ) ) {
				return array(
					'success' => false,
					'code'    => $code_response,
				);
			}

			$body = wp_remote_retrieve_body( $response );

			return array(
				'success' => true,
				'body'    => $body,
			);
		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'code'    => '',
			);
		}
	}
}
