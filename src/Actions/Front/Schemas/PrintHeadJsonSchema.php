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

		/**
		 * Check if a Knowledge Graph type is set.
		 *
		 * The settings page stores an empty string for "None", while the setup
		 * wizard and older installs use the literal "none". Treat both (and an
		 * unset option) as "feature disabled" so no schema is printed.
		 *
		 * @since 5.3
		 */
		$knowledge_type = seopress_get_service( 'SocialOption' )->getSocialKnowledgeType();
		if ( empty( $knowledge_type ) || 'none' === $knowledge_type ) {
			return;
		}

		// Pass the real page context: the Knowledge Graph fields can hold tags,
		// and without a context they would resolve to nothing. This runs on the
		// front page only, so the context describes it (the static page set as
		// front page, or the posts page).
		$context = seopress_get_service( 'ContextPage' )->getContext();

		$jsons = seopress_get_service( 'JsonSchemaGenerator' )->getJsonsEncoded(
			array(
				'organization',
			),
			$context
		);
		$json = apply_filters( 'seopress_schemas_organization_html', $jsons[0] );
		// Escape after the filter: literal less-than signs can close the script
		// or enter HTML comment parsing states. JSON decoding restores the value.
		$json = str_replace( '<', '\\u003C', (string) $json );
		?><script type="application/ld+json"><?php echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Less-than signs are JSON-escaped above. ?></script>
		<?php
	}
}
