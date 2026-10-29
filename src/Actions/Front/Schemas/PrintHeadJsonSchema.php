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

		/**
		 * Single `@graph` output: hand the node to the collector rather than
		 * printing it.
		 *
		 * Through the same chain as the block, so `seopress_get_json_data_organization`,
		 * `seopress_json_schema_generator_get_jsons_encoded` and
		 * `seopress_schemas_organization_html` all still fire. The last one is
		 * the documented way to suppress the Knowledge Graph: an empty answer
		 * drops the node here as it drops the block below, and a changed
		 * payload is decoded back into the node.
		 *
		 * @since 10.4.0
		 */
		$graph = seopress_get_service( 'SchemaGraph' );
		if ( is_object( $graph ) && $graph->is_enabled() ) {
			$encoded = isset( $jsons[0] ) && is_string( $jsons[0] ) ? $jsons[0] : '';
			$node    = '' !== $encoded ? json_decode( $encoded, true ) : null;

			if ( is_array( $node ) ) {
				$node = $graph->filter_block( 'seopress_schemas_organization_html', $node, $encoded, true );
			}

			if ( is_array( $node ) ) {
				$graph->add_node( $node );
			}

			return;
		}

		// Cast once, on the filtered value: an integrator returning an array
		// would otherwise raise the same notice twice.
		$json = (string) apply_filters( 'seopress_schemas_organization_html', $jsons[0] );

		// The filter receives the payload, not the whole block, unlike its
		// WebSite counterpart which is handed the <script> element itself. So
		// suppressing the node through it has to be handled here: anything
		// that is empty once trimmed prints nothing at all, rather than an
		// empty <script type="application/ld+json"></script> that validators
		// flag. `null` and `false`, which `__return_false` and friends return,
		// are covered by the cast above.
		if ( '' === trim( $json ) ) {
			return;
		}

		// Escape after the filter: literal less-than signs can close the script
		// or enter HTML comment parsing states. JSON decoding restores the value.
		$json = str_replace( '<', '\\u003C', $json );
		?><script type="application/ld+json"><?php echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Less-than signs are JSON-escaped above. ?></script>
		<?php
	}
}
