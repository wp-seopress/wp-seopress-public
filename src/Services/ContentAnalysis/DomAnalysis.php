<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\ContentAnalysis;

/**
 * DomAnalysis
 */
class DomAnalysis {

	/**
	 * The getMatches function.
	 *
	 * Both sides go through the same normalization before being compared:
	 * entities are resolved, the typographic characters WordPress substitutes
	 * at render time are folded back onto their ASCII form, then accents are
	 * stripped. Without it a keyword typed with a straight apostrophe could
	 * never match the typographic one wptexturize() puts on the page.
	 *
	 * @param string $content The content.
	 * @param array  $target_keywords The target keywords.
	 *
	 * @return array
	 */
	public function getMatches( $content, $target_keywords ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();

		if ( empty( $target_keywords ) ) {
			return null;
		}

		$normalized_content = remove_accents( ContentAnalysis::normalizeTypography( $content ) );

		foreach ( $target_keywords as $kw ) {
			$kw = remove_accents( ContentAnalysis::normalizeTypography( $kw ) );

			if ( '' === trim( $kw ) ) {
				continue;
			}

			if ( preg_match_all( '@(?<![\w-])' . preg_quote( $kw, '@' ) . '(?![\w-])@is', $normalized_content, $matches ) ) {
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
		// If target_keywords key exists in options, use it (even if empty).
		// This allows the API to explicitly pass empty keywords when user clears them.
		// Only fall back to database if the key doesn't exist at all.
		if ( array_key_exists( 'target_keywords', $options ) ) {
			$target_keywords = $options['target_keywords'];
		} else {
			$target_keywords = get_post_meta( $options['id'], '_seopress_analysis_target_kw', true );
		}

		// Handle null/empty values before processing.
		if ( empty( $target_keywords ) ) {
			$target_keywords = '';
		}

		// Rows stored before the meta was sanitized (10.0) can keep the space
		// typed after each comma.
		$target_keywords = array_filter( array_map( 'trim', explode( ',', remove_accents( ContentAnalysis::toLowercase( $target_keywords ) ) ) ) );

		return apply_filters( 'seopress_content_analysis_target_keywords', $target_keywords, $options['id'] );
	}

	/**
	 * The getScore function.
	 *
	 * Returns every check's impact, keyed by check. Up to 10.3.x the impacts
	 * were deduplicated and reindexed, which told the posts list column whether
	 * anything was wrong but not how much: a page failing one check and a page
	 * failing eight both came back as the same handful of values. Keeping the
	 * keys costs nothing and lets the column count the checks that passed.
	 *
	 * The set of values is unchanged, so the `in_array()` reads this value has
	 * always been subjected to behave exactly as before.
	 *
	 * @param object $post The post.
	 *
	 * @return array Check key => impact.
	 */
	public function getScore( $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$analyzes = seopress_get_service( 'GetContentAnalysis' )->getAnalyzes( $post );

		return \SEOPress\Helpers\ContentAnalysis::getImpactMap( $analyzes );
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
		$slug = ContentAnalysis::getPermalinkSlug( $post );

		$data['kws_permalink'] = array(
			'matches' => array(),
		);

		if ( ! empty( $target_keywords ) ) {
			$matches = $this->getMatches( $slug, $target_keywords );

			// Same fallback as the analysis report, so the metabox and the
			// report cannot disagree on whether a keyword is in the permalink:
			// try the slug readings of the keywords when the keywords
			// themselves did not line up.
			if ( null === $matches ) {
				$slug_variants = ContentAnalysis::getSlugVariants( $target_keywords );

				if ( ! empty( $slug_variants ) ) {
					$matches = $this->getMatches( $slug, $slug_variants );
				}
			}

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
