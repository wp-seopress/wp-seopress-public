<?php // phpcs:ignore

namespace SEOPress\Services\Context;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CheckContextPage
 */
class CheckContextPage {

	/**
	 * The hasSchemaManualValues function.
	 *
	 * @since 4.6.0
	 *
	 * @param array $context The context.
	 *
	 * @return bool
	 */
	public function hasSchemaManualValues( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! isset( $context['schemas_manual'] ) || ! isset( $context['key_get_json_schema'] ) ) {
			return false;
		}

		if ( ! isset( $context['schemas_manual'][ $context['key_get_json_schema'] ] ) ) {
			return false;
		}

		return true;
	}
}
