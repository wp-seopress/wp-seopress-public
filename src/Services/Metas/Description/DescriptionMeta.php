<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DescriptionMeta
 */
class DescriptionMeta {


	/**
	 * The specifications.
	 *
	 * @var array
	 */
	protected $specifications = array();

	/**
	 * The DescriptionMeta constructor.
	 *
	 * @since 4.4.0
	 */
	public function __construct() {
		$this->createDefaultSpecifications();
	}

	/**
	 * The createDefaultSpecifications function.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	protected function createDefaultSpecifications() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->specifications = array(
			seopress_get_service( 'StaticHomepageDescriptionSpecification' ),
			seopress_get_service( 'HomepageDescriptionSpecification' ),
			seopress_get_service( 'BlogPageDescriptionSpecification' ),
			seopress_get_service( 'LatestPostsDescriptionSpecification' ),
			seopress_get_service( 'BuddyPressDescriptionSpecification' ),
			seopress_get_service( 'SingularDescriptionSpecification' ),
			seopress_get_service( 'PostTypeArchiveDescriptionSpecification' ),
			seopress_get_service( 'TaxonomyDescriptionSpecification' ),
			seopress_get_service( 'AuthorDescriptionSpecification' ),
			seopress_get_service( 'DateDescriptionSpecification' ),
			seopress_get_service( 'SearchDescriptionSpecification' ),
			seopress_get_service( 'NotFound404DescriptionSpecification' ),
			seopress_get_service( 'DefaultDescriptionSpecification' ),

		);
	}

	/**
	 * The getDescriptionForPost function.
	 *
	 * @since 4.4.0
	 *
	 * @param array $context The context.
	 *
	 * @return string
	 */
	protected function getDescriptionForPost( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$variables = apply_filters( 'seopress_dyn_variables_fn', array() );

		$post = $variables['post'] ?? $context['post'];

		$description_value = isset( $variables['seopress_titles_description_template'] ) ? $variables['seopress_titles_description_template'] : '';

		if ( empty( $description_value ) ) {
			$description_value = get_post_meta( $post->ID, '_seopress_titles_desc', true );
		}

		foreach ( $this->specifications as $specification ) {
			if ( $specification->isSatisfyBy(
				array(
					'post'        => $post,
					'description' => $description_value,
					'context'     => $context,
				)
			) ) {
				$description_value = $specification->getValue(
					array(
						'post'        => $post,
						'description' => $description_value,
						'context'     => $context,
					)
				);
				break;
			}
		}

		if ( has_filter( 'seopress_titles_desc' ) ) {
			$description_value = apply_filters( 'seopress_titles_desc', $description_value, $context );
		}

		return $description_value;
	}

	/**
	 * The getDescriptionForTaxonomy function.
	 *
	 * @since 4.4.0
	 *
	 * @param array $context The context.
	 *
	 * @return string
	 */
	protected function getDescriptionForTaxonomy( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$variables = apply_filters( 'seopress_dyn_variables_fn', array() );

		$term = $context['term'] ?? get_term( $context['term_id'] );

		$description_value = isset( $variables['seopress_titles_description_template'] ) ? $variables['seopress_titles_description_template'] : '';

		if ( empty( $description_value ) ) {
			$description_value = get_term_meta( $term->term_id, '_seopress_titles_desc', true );
		}

		foreach ( $this->specifications as $specification ) {
			if ( $specification->isSatisfyBy(
				array(
					'post'        => $term,
					'description' => $description_value,
					'context'     => $context,
				)
			) ) {
				$description_value = $specification->getValue(
					array(
						'post'        => $term,
						'description' => $description_value,
						'context'     => $context,
					)
				);
				break;
			}
		}

		if ( has_filter( 'seopress_titles_desc' ) ) {
			$description_value = apply_filters( 'seopress_titles_desc', $description_value, $context );
		}

		return $description_value;
	}

	/**
	 * The getValue function.
	 *
	 * @since 4.4.0
	 *
	 * @param array $context The context.
	 *
	 * @return string|null
	 */
	public function getValue( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$value = null;
		if ( isset( $context['post'] ) ) {
			return $this->getDescriptionForPost( $context );
		}

		if ( isset( $context['term_id'] ) ) {
			return $this->getDescriptionForTaxonomy( $context );
		}

		if ( null === $value ) {
			return $value;
		}

		return seopress_get_service( 'TagsToString' )->replace( $value, $context );
	}
}
