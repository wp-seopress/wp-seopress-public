<?php // phpcs:ignore

namespace SEOPress\ManualHooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ApiHeader
 */
class ApiHeader {

	/**
	 * The ApiHeader hooks.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'http_request_args', array( $this, 'addHeaderRequest' ) );
	}

	/**
	 * The ApiHeader add header request.
	 *
	 * @since 4.4.0
	 *
	 * @param array $arguments The arguments.
	 *
	 * @return array
	 */
	public function addHeaderRequest( $arguments ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$body = $arguments['body'];

		if ( is_array( $body ) ) {
			$body = implode( '', $body );
		}

		$arguments['headers']['expect'] = ! empty( $body ) && strlen( $body ) > 1048576 ? '100-Continue' : '';

		return $arguments;
	}
}
