<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schema
 */
class Schema {
	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();
		$seen = array();

		$items = $xpath->query( '//script[@type="application/ld+json"]' );
		foreach ( $items as $node ) {
			$this->collect_types( json_decode( $node->nodeValue, true ), $data, $seen );
		}

		return $data;
	}

	/**
	 * Collect scalar types once per explicit node identity across JSON-LD scripts.
	 *
	 * @param mixed $node Decoded node, graph, or top-level node list.
	 * @param array $data Collected types; anonymous nodes keep separate counts.
	 * @param array $seen Types already collected for each explicit @id.
	 * @return void
	 */
	private function collect_types( $node, &$data, &$seen ) {
		if ( ! is_array( $node ) ) {
			return;
		}

		$id = isset( $node['@id'] ) && is_string( $node['@id'] ) && '' !== $node['@id'] ? $node['@id'] : null;
		$types = isset( $node['@type'] ) ? (array) $node['@type'] : array();
		$node_types = array();
		foreach ( $types as $type ) {
			if ( ! is_string( $type ) || '' === $type || isset( $node_types[ $type ] ) ) {
				continue;
			}
			$node_types[ $type ] = true;
			if ( null !== $id ) {
				if ( isset( $seen[ $id ][ $type ] ) ) {
					continue;
				}
				$seen[ $id ][ $type ] = true;
			}
			$data[] = $type;
		}

		if ( isset( $node['@graph'] ) && is_array( $node['@graph'] ) ) {
			$this->collect_types( $node['@graph'], $data, $seen );
		}
		if ( isset( $node[0] ) ) {
			foreach ( $node as $item ) {
				$this->collect_types( $item, $data, $seen );
			}
		}
	}
}
