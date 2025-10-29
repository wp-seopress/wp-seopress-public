<?php // phpcs:ignore

namespace SEOPress\Actions\Front\Schemas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

/**
 * Print Head Json Schema
 */
class PrintHeadJsonSchema implements ExecuteHooksFrontend {
	/**
	 * The Print Head Json Schema hooks.
	 *
	 * @since 5.3
	 */
	public function hooks() {
		add_action( 'wp_head', array( $this, 'render' ), 2 );
	}

	/**
	 * The Print Head Json Schema render.
	 *
	 * @since 5.3
	 */
	public function render() {
		/**
		 * Check if Social toggle is ON
		 *
		 * @since 5.3
		 * @author Benjamin
		 */
		if ( seopress_get_toggle_option( 'social' ) !== '1' ) {
			return;
		}

		/**
		 * Check if is homepage
		 *
		 * @since 5.3
		 * @author Benjamin
		 */
		if ( ! is_front_page() ) {
			return;
		}

		if ( 'none' === seopress_get_service( 'SocialOption' )->getSocialKnowledgeType() ) {
			return;
		}

		$jsons = seopress_get_service( 'JsonSchemaGenerator' )->getJsonsEncoded(
			array(
				'organization',
			)
		);
		?><script type="application/ld+json"><?php echo apply_filters( 'seopress_schemas_organization_html', $jsons[0] ); // phpcs:ignore -- TODO: escape properly. ?></script>
		<?php
	}
}
