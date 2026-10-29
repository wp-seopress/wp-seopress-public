<?php
/**
 * Collector for the optional single `@graph` structured data output.
 *
 * @package SEOPress
 */

namespace SEOPress\Services\Schemas;

defined( 'ABSPATH' ) || exit;

/**
 * SchemaGraph
 *
 * SEOPress prints each schema in its own `<script type="application/ld+json">`
 * element. That output is already connected: every node carries a stable `@id`
 * and Google resolves those references across separate blocks, so the merged
 * form below is a presentation choice, not a correctness one. It exists because
 * people compare our head to Yoast's and want one block.
 *
 * Renderers that would echo their node call `add_node()` instead while the
 * setting is on, and `PrintSchemaGraph` prints the collected list once, late
 * enough on `wp_head` for every SEOPress printer to have registered.
 *
 * The collector is public on purpose: PRO and third parties reach it through
 * `seopress_get_service( 'SchemaGraph' )` and join the same graph, and nothing
 * else in this class knows which plugin a node came from.
 *
 * @since 10.4.0
 */
class SchemaGraph {

	/**
	 * Nodes collected for the current request.
	 *
	 * @var array
	 */
	protected $nodes = array();

	/**
	 * Whether the graph of the current head has already been printed.
	 *
	 * @var bool
	 */
	protected $rendered = false;

	/**
	 * Is the single graph output turned on?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$option = null;

		$advanced = seopress_get_service( 'AdvancedOption' );
		if ( is_object( $advanced ) && method_exists( $advanced, 'getSchemaGraph' ) ) {
			$option = $advanced->getSchemaGraph();
		}

		// Settings store '1' or '0'; tolerate the boolean a filter or an
		// importer may write, and treat everything else as off.
		$enabled = ( '1' === $option || 1 === $option || true === $option );

		/**
		 * Turn the single `@graph` output on or off for the current request.
		 *
		 * @since 10.4.0
		 *
		 * @param bool $enabled Whether every node is merged into one block.
		 */
		return (bool) apply_filters( 'seopress_schemas_graph_enabled', $enabled );
	}

	/**
	 * Register a node for the current request.
	 *
	 * A node carries no `@context` of its own: the graph declares it once, and
	 * a per-node context inside a graph is redundant at best.
	 *
	 * @since 10.4.0
	 *
	 * @param array $node A schema node.
	 *
	 * @return bool True when the node was kept. False for a node without a
	 *              type, and for one registered after the graph of this head
	 *              was printed, which would otherwise be accepted and then
	 *              never appear anywhere.
	 */
	public function add_node( $node ) {
		if ( $this->rendered ) {
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'The single @graph of this page has already been printed. Register the node before wp_head priority 11.', 'wp-seopress' ),
				'10.4.0'
			);

			return false;
		}

		if ( ! is_array( $node ) || empty( $node['@type'] ) ) {
			return false;
		}

		unset( $node['@context'] );

		$this->nodes[] = $node;

		return true;
	}

	/**
	 * The nodes collected so far.
	 *
	 * @return array
	 */
	public function get_nodes() {
		return $this->nodes;
	}

	/**
	 * Drop every collected node.
	 *
	 * @return void
	 */
	public function reset() {
		$this->nodes = array();
	}

	/**
	 * Record that the graph of the current head has been printed.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	public function mark_rendered() {
		$this->rendered = true;
	}

	/**
	 * Open a new head: nodes may be registered again.
	 *
	 * Only the flag. Nodes registered before `wp_head`, on `wp` or
	 * `template_redirect`, are kept.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	public function open() {
		$this->rendered = false;
	}

	/**
	 * Whether the graph of the current head has already been printed.
	 *
	 * @since 10.4.0
	 *
	 * @return bool
	 */
	public function is_rendered() {
		return $this->rendered;
	}

	/**
	 * Run a per-block HTML filter on a node, the way it runs outside the graph.
	 *
	 * Those filters exist to change or suppress one block, and documentation
	 * tells integrators to return an empty string to remove one. In graph mode
	 * they used not to fire at all, so a site suppressing the Knowledge Graph
	 * through `seopress_schemas_organization_html` got it back the day someone
	 * turned this setting on.
	 *
	 * The filter is handed what it receives outside the graph, built by the
	 * caller. An empty answer drops the node. Anything else is decoded back
	 * into a node when it is the payload alone; when the filter receives a
	 * whole `<script>` element, as `seopress_schemas_website_html` does, only
	 * suppression can be honoured, since there is no block left to replace.
	 *
	 * @since 10.4.0
	 *
	 * @param string $hook     The per-block filter.
	 * @param array  $node     The node.
	 * @param string $original What the filter receives outside the graph.
	 * @param bool   $payload  Whether that is the JSON payload alone.
	 *
	 * @return array|null The node to register, or null to drop it.
	 */
	public function filter_block( $hook, $node, $original, $payload ) {
		if ( ! has_filter( $hook ) ) {
			return $node;
		}

		$filtered = apply_filters( $hook, $original );
		$filtered = is_scalar( $filtered ) ? (string) $filtered : '';

		if ( '' === trim( $filtered ) ) {
			return null;
		}

		if ( ! $payload || $filtered === $original ) {
			return $node;
		}

		$decoded = json_decode( $filtered, true );

		return is_array( $decoded ) && ! empty( $decoded['@type'] ) ? $decoded : $node;
	}

	/**
	 * The `<script>` element holding the graph, empty when there is nothing to print.
	 *
	 * @since 10.4.0
	 *
	 * @return string
	 */
	public function get_html() {
		/**
		 * Filter the assembled node list before it is printed.
		 *
		 * Fires even when SEOPress collected nothing, so a site can build the
		 * whole graph from here. Returning an empty array prints no block.
		 *
		 * @since 10.4.0
		 *
		 * @param array $nodes Nodes, in registration order.
		 */
		$nodes = apply_filters( 'seopress_schemas_graph', $this->nodes );

		if ( ! is_array( $nodes ) || empty( $nodes ) ) {
			return '';
		}

		$graph = array(
			'@context' => seopress_check_ssl() . 'schema.org',
			'@graph'   => array_values( $nodes ),
		);

		// Encoded with JSON_HEX_TAG, so no value can close the script element.
		$encoded = seopress_json_ld_encode( $graph );

		if ( false === $encoded ) {
			return '';
		}

		$html = '<script id="seopress-schema-graph" type="application/ld+json">' . $encoded . '</script>' . "\n";

		/**
		 * Filter the whole graph block.
		 *
		 * Replaces the per-block `*_html` filters, which have no single block
		 * to act on once the nodes are merged.
		 *
		 * @since 10.4.0
		 *
		 * @param string $html The `<script>` element.
		 * @param array  $nodes Nodes it holds.
		 */
		return (string) apply_filters( 'seopress_schemas_graph_html', $html, $nodes );
	}
}
