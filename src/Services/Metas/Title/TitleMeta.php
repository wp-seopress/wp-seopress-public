<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Title;

/**
 * TitleMeta
 */
class TitleMeta {

	/**
	 * The specifications property.
	 *
	 * @var array
	 */
	protected $specifications = array();

	/**
	 * The __construct function.
	 */
	public function __construct() {
		$this->createDefaultSpecifications();
	}

	/**
	 * The createDefaultSpecifications function.
	 */
	protected function createDefaultSpecifications() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->specifications = array(
			seopress_get_service( 'HomepageSpecification' ),
			seopress_get_service( 'StaticHomepageSpecification' ),
			seopress_get_service( 'SingularSpecification' ),
			seopress_get_service( 'PostTypeArchiveSpecification' ),
			seopress_get_service( 'TaxonomySpecification' ),
			seopress_get_service( 'AuthorSpecification' ),
			seopress_get_service( 'SearchSpecification' ),
			seopress_get_service( 'NotFound404Specification' ),
			seopress_get_service( 'DefaultTitleSpecification' ), // Don't move, it's the last one, always "yes" for isSatisfyBy.
		);
	}

	/**
	 * The getTitleForPost function.
	 *
	 * @param array $context The context.
	 *
	 * @return string
	 */
	protected function getTitleForPost( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$variables = apply_filters( 'seopress_dyn_variables_fn', array() );

		$post = $variables['post'] ?? $context['post'];

		$title_value = isset( $variables['seopress_titles_title_template'] ) ? $variables['seopress_titles_title_template'] : '';

		if ( empty( $title_value ) ) {
			$title_value = get_post_meta( $post->ID, '_seopress_titles_title', true );
		}

		foreach ( $this->specifications as $specification ) {
			if ( $specification->isSatisfyBy(
				array(
					'post'    => $post,
					'title'   => $title_value,
					'context' => $context,
				)
			) ) {
				$title_value = $specification->getValue(
					array(
						'post'    => $post,
						'title'   => $title_value,
						'context' => $context,
					)
				);
				break;
			}
		}

		if ( has_filter( 'seopress_titles_title' ) ) {
			$title_value = apply_filters( 'seopress_titles_title', $title_value, $context );
		}

		return $title_value;
	}

	/**
	 * The getTitleForTaxonomy function.
	 *
	 * @param array $context The context.
	 *
	 * @return string
	 */
	protected function getTitleForTaxonomy( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$variables = apply_filters( 'seopress_dyn_variables_fn', array() );

		$term = $context['term'] ?? get_term( $context['term_id'] );

		$title_value = isset( $variables['seopress_titles_title_template'] ) ? $variables['seopress_titles_title_template'] : '';

		if ( empty( $title_value ) ) {
			$title_value = get_term_meta( $term->term_id, '_seopress_titles_title', true );
		}

		foreach ( $this->specifications as $specification ) {
			if ( $specification->isSatisfyBy(
				array(
					'post'    => $term,
					'title'   => $title_value,
					'context' => $context,
				)
			) ) {
				$title_value = $specification->getValue(
					array(
						'post'    => $term,
						'title'   => $title_value,
						'context' => $context,
					)
				);
				break;
			}
		}

		if ( has_filter( 'seopress_titles_title' ) ) {
			$title_value = apply_filters( 'seopress_titles_title', $title_value, $context );
		}

		return $title_value;
	}

	/**
	 * The getValue function.
	 *
	 * @param array $context The context.
	 *
	 * @return string|null
	 */
	public function getValue( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$value = null;

		if ( isset( $context['post'] ) && null !== $context['post'] ) {
			$value = $this->getTitleForPost( $context );
		}
		if ( isset( $context['term_id'] ) ) {
			$value = $this->getTitleForTaxonomy( $context );
		}

		if ( null === $value ) {
			return $value;
		}

		return seopress_get_service( 'TagsToString' )->replace( $value, $context );
	}
}
