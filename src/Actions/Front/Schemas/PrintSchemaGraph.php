<?php
/**
 * Print the collected schema nodes as a single `@graph`.
 *
 * @package SEOPress
 */

namespace SEOPress\Actions\Front\Schemas;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

/**
 * PrintSchemaGraph
 */
class PrintSchemaGraph implements ExecuteHooksFrontend {

	/**
	 * When the graph is printed on `wp_head`.
	 *
	 * Every SEOPress schema printer, free and PRO, registers at priority 10 or
	 * earlier, so contributors have all run by the time this fires. A third
	 * party printing later has to register its node earlier than it would have
	 * printed it, which is what `SchemaGraph::add_node()` is for.
	 *
	 * @var int
	 */
	const RENDER_PRIORITY = 11;

	/**
	 * The PrintSchemaGraph hooks.
	 *
	 * @since 10.4.0
	 */
	public function hooks() {
		add_action( 'wp_head', array( $this, 'open' ), 0 );
		add_action( 'wp_head', array( $this, 'render' ), self::RENDER_PRIORITY );
	}

	/**
	 * A new head starts: nodes may be registered again.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	public function open() {
		$graph = seopress_get_service( 'SchemaGraph' );

		if ( is_object( $graph ) ) {
			$graph->open();
		}
	}

	/**
	 * Print the graph.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	public function render() {
		$graph = seopress_get_service( 'SchemaGraph' );

		if ( ! is_object( $graph ) ) {
			return;
		}

		if ( $graph->is_enabled() ) {
			$html = $graph->get_html();

			if ( '' !== $html ) {
				echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD document encoded with wp_json_encode() above, filterable by third parties.
			}
		}

		// Nothing must survive into another render of the same head, and a
		// node registered from here on would be accepted and then printed
		// nowhere, so the collector refuses it out loud instead.
		$graph->reset();
		$graph->mark_rendered();
	}
}
