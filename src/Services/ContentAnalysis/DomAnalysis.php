<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DomAnalysis
 */
class DomAnalysis {

	/**
	 * The getMatches function.
	 *
	 * @param string $content The content.
	 * @param array  $target_keywords The target keywords.
	 *
	 * @return array
	 */
	public function getMatches( $content, $target_keywords ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();
		foreach ( $target_keywords as $kw ) {
			$kw = remove_accents( wp_specialchars_decode( $kw ) );

			if ( preg_match_all( '@(?<![\w-])' . preg_quote( $kw, '@' ) . '(?![\w-])@is', remove_accents( $content ), $matches ) ) {
				$data[ $kw ][] = $matches[0];
			}
		}

		if ( empty( $data ) ) {
			return null;
		}

		return $data;
	}

	/**
	 * The getKeywords function.
	 *
	 * @param array $options The options.
	 *
	 * @return array
	 */
	public function getKeywords( $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$target_keywords = isset( $options['target_keywords'] ) && ! empty( $options['target_keywords'] ) ? $options['target_keywords'] : get_post_meta( $options['id'], '_seopress_analysis_target_kw', true );

		$target_keywords = array_filter( explode( ',', remove_accents( strtolower( $target_keywords ) ) ) );

		return apply_filters( 'seopress_content_analysis_target_keywords', $target_keywords, $options['id'] );
	}

	/**
	 * The getScore function.
	 *
	 * @param object $post The post.
	 *
	 * @return array
	 */
	public function getScore( $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$analyzes = seopress_get_service( 'GetContentAnalysis' )->getAnalyzes( $post );
		$impact   = array_unique( array_values( wp_list_pluck( $analyzes, 'impact' ) ) );
		return $impact;
	}

	/**
	 * The getDataAnalyze function.
	 *
	 * @param array $data The data.
	 * @param array $options The options.
	 *
	 * @return array
	 */
	public function getDataAnalyze( $data, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! isset( $options['id'] ) ) {
			return $data;
		}

		$post = get_post( $options['id'] );

		$target_keywords = $this->getKeywords( $options );

		// Manage keywords with special characters.
		foreach ( $target_keywords as $key => $kw ) {
			$target_keywords[ $key ] = trim( htmlspecialchars_decode( $kw, ENT_QUOTES ) );
		}

		// Remove duplicates.
		$target_keywords = array_unique( $target_keywords );

		$keys_analyze = array(
			'title',
			'description',
			'h1',
			'h2',
			'h3',
		);

		foreach ( $keys_analyze as $value ) {
			if ( ! isset( $data[ $value ] ) || ! isset( $data[ $value ]['value'] ) ) {
				continue;
			}
			$data[ $value ]['matches'] = array();

			$items = $data[ $value ]['value'];
			if ( is_string( $items ) ) {
				$matches = $this->getMatches( $items, $target_keywords );
				if ( null !== $matches ) {
					$keys = array_keys( $matches );

					foreach ( $keys as $key_match => $value_match ) {
						$data[ $value ]['matches'][] = array(
							'key'   => $value_match,
							'count' => count( $matches[ $value_match ][0] ),
						);
					}
				}
			} elseif ( is_array( $items ) ) {
				foreach ( $items as $key => $item ) {
					$matches = $this->getMatches( $item, $target_keywords );
					if ( null !== $matches ) {
						$keys = array_keys( $matches );
						foreach ( $keys as $key_match => $value_match ) {
							$data[ $value ]['matches'][] = array(
								'key'   => $value_match,
								'count' => count( $matches[ $value_match ][0] ),
							);
						}
					}
				}
			}
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$data['analyzed_content_id'] = $options['id'];
		}

		// Keywords in permalink.
		$slug = urldecode( $post->post_name );

		if ( is_plugin_active( 'permalink-manager-pro/permalink-manager.php' ) ) {
			global $permalink_manager_uris;
			if ( ! empty( $permalink_manager_uris ) && ! empty( $options ) && is_array( $options ) && array_key_exists( 'id', $options ) ) {
				$slug = isset( $permalink_manager_uris[ $options['id'] ] ) ? $permalink_manager_uris[ $options['id'] ] : '';
				$slug = urldecode( $slug );
			}
		}

		$slug = str_replace( '-', ' ', $slug );

		$data['kws_permalink'] = array(
			'matches' => array(),
		);

		if ( ! empty( $target_keywords ) ) {
			$matches = $this->getMatches( $slug, $target_keywords );
			if ( null !== $matches ) {
				$keys = array_keys( $matches );
				foreach ( $keys as $key => $value ) {
					$data['kws_permalink']['matches'][] = array(
						'key'   => $value,
						'count' => count( $matches[ $value ][0] ),
					);
				}
			}
		}

		// Old post.
		$data['old_post'] = array(
			'value' => isset( $post->post_modified ) && strtotime( $post->post_modified ) < strtotime( '-365 days' ),
		);

		return $data;
	}
}
