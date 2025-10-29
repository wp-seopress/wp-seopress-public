<?php // phpcs:ignore

namespace SEOPress\Services\Context;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ContextPage
 */
class ContextPage {

	/**
	 * The context.
	 *
	 * @var array
	 */
	protected $context;

	/**
	 * The getEmptyContext function.
	 *
	 * @return array
	 */
	protected function getEmptyContext() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return array(
			'post'                 => null,
			'product'              => null,
			'term_id'              => null,
			'is_singular'          => false,
			'is_single'            => false,
			'is_post_type_archive' => false,
			'is_home'              => false,
			'is_front_page'        => false,
			'is_product'           => false,
			'is_archive'           => false,
			'is_category'          => false,
			'is_author'            => false,
			'is_tax'               => false,
			'is_tag'               => false,
			'is_search'            => false,
			'is_date'              => false,
			'is_404'               => false,
			'has_category'         => false,
			'has_tag'              => false,
			'paged'                => null,
			'schemas_manual'       => array(),
		);
	}

	/**
	 * The buildTerm function.
	 *
	 * @param int   $id The id.
	 * @param array $options The options.
	 */
	protected function buildTerm( $id, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$taxonomy = isset( $options['taxonomy'] ) ? $options['taxonomy'] : 'category';
		$term     = get_term_by( 'id', $id, $taxonomy );

		if ( $term ) {
			$this->setIsCategory( true );
			$this->setTermId( $id );
			$this->context['term'] = $term;
		}
	}

	/**
	 * The buildIsHome function.
	 */
	protected function buildIsHome() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! ( $this->context['is_404'] || $this->context['is_search'] || $this->context['is_front_page'] || $this->context['is_archive'] || $this->context['is_singular'] ) ) {
			$this->context['is_home'] = true;
		}
	}

	/**
	 * The buildIsFrontPage function.
	 *
	 * @param int $id The id.
	 */
	protected function buildIsFrontPage( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$show_on_front = get_option( 'show_on_front' );
		$page_on_front = get_option( 'page_on_front' );

		if ( 'posts' === $show_on_front && $id === $page_on_front ) {
			$this->setIsFrontPage( true );
			return;
		} elseif ( 'page' === $show_on_front && $id === $page_on_front
		) {
			$this->setIsFrontPage( true );
			return;
		}

		$this->setIsFrontPage( false );
	}

	/**
	 * The buildPost function.
	 *
	 * @param int $id The id.
	 */
	protected function buildPost( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$is_post_type = get_post_type( $id );

		if ( $is_post_type ) {
			$this->setPostById( (int) $id );
			$terms = get_the_terms( $id, 'post_tag' );

			if ( ! empty( $terms ) ) {
				$this->setHasTag( true );
			}

			$categories = get_the_terms( $id, 'category' );
			if ( ! empty( $categories ) ) {
				$this->setHasCategory( true );
			}

			$this->setIsPostType( $is_post_type, true );

			// TODO : Need better verification.
			$this->setIsSingle( true );
			$this->context['is_singular'] = true;
		}

		$this->buildIsFrontPage( $id );

		$term = term_exists( $id );
		if ( null !== $term ) {
			$this->setIsCategory( true );
			$this->setTermId( $id );
		}

		$this->buildIsHome();
	}

	/**
	 * The buildContextWithCurrentId function.
	 *
	 * @since 4.4.0
	 *
	 * @param int   $id The id.
	 * @param array $options The options.
	 *
	 * @return $this
	 */
	public function buildContextWithCurrentId( $id, $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$type_build = isset( $options['type'] ) ? $options['type'] : 'post';

		$this->buildContextDefault();

		switch ( $type_build ) {
			case 'post':
				$this->buildPost( $id );
				break;
			case 'term':
				$this->buildTerm( $id, $options );
				break;
		}

		return $this;
	}

	/**
	 * The buildContextDefault function.
	 *
	 * @since 4.4.0
	 *
	 * @return array
	 */
	public function buildContextDefault() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $post;
		global $product;

		$context = $this->getEmptyContext();

		$context['post']    = $post;
		$context['product'] = $product;
		$context['paged']   = get_query_var( 'paged' );

		if ( is_singular() ) {
			$schemas_manual = get_post_meta( $context['post']->ID, '_seopress_pro_schemas_manual', true );
			if ( ! $schemas_manual ) {
				$schemas_manual = array();
			}
			$context = array_replace(
				$context,
				array(
					'is_single'      => true,
					'schemas_manual' => $schemas_manual,
				)
			);
		}
		if ( is_home() ) {
			$context = array_replace( $context, array( 'is_home' => true ) );
		}
		if ( is_front_page() ) {
			$context = array_replace( $context, array( 'is_front_page' => true ) );
		}
		if ( is_singular() ) {
			$context = array_replace( $context, array( 'is_singular' => true ) );
		}
		if ( is_post_type_archive() ) {
			$context = array_replace(
				$context,
				array(
					'is_archive'           => true,
					'is_post_type_archive' => true,
				)
			);
		}
		if ( is_tax() || is_category() || is_tag() ) {
			$object = get_queried_object();

			if ( null !== $object && property_exists( $object, 'term_id' ) ) {
				$context = array_replace(
					$context,
					array(
						'term_id' => $object->term_id,
						'term'    => $object,
					)
				);
			}
			$context = array_replace( $context, array( 'is_category' => true ) );
		}
		if ( is_tax() ) {
			$context = array_replace( $context, array( 'is_tax' => true ) );
		}
		if ( is_tag() ) {
			$context = array_replace( $context, array( 'is_tag' => true ) );
		}
		if ( is_author() ) {
			$context = array_replace( $context, array( 'is_author' => true ) );
		}
		if ( is_404() ) {
			$context = array_replace( $context, array( 'is_404' => true ) );
		}
		if ( is_search() ) {
			$context = array_replace( $context, array( 'is_search' => true ) );
		}
		if ( has_category() ) {
			$context = array_replace( $context, array( 'has_category' => true ) );
		}
		if ( has_tag() ) {
			$context = array_replace( $context, array( 'has_tag' => true ) );
		}

		$this->context = $context;

		return $this;
	}

	/**
	 * The getContext function.
	 *
	 * @since 4.4.0
	 *
	 * @return array
	 */
	public function getContext() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( $this->context ) {
			return $this->context;
		}

		$this->buildContextDefault();

		return $this->context;
	}

	/**
	 * The setContextBooleanByKey function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $key The key.
	 * @param bool   $value The value.
	 */
	protected function setContextBooleanByKey( $key, $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->context[ $key ] = $value;

		return $this;
	}



	/**
	 * The setTermId function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $value The value.
	 */
	public function setTermId( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->context['term_id'] = $value;

		return $this;
	}

	/**
	 * The setIsSingle function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $value The value.
	 */
	public function setIsSingle( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( 'is_single', $value );

		return $this;
	}

	/**
	 * The setHasTag function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $value The value.
	 */
	public function setHasTag( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( 'has_tag', $value );

		return $this;
	}

	/**
	 * The setHasCategory function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $value The value.
	 */
	public function setHasCategory( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( 'has_category', $value );

		return $this;
	}

	/**
	 * The setIsHome function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $value The value.
	 */
	public function setIsHome( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( 'is_home', $value );

		return $this;
	}

	/**
	 * The setIsFrontPage function.
	 *
	 * @param string $value The value.
	 */
	public function setIsFrontPage( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( 'is_front_page', $value );

		return $this;
	}

	/**
	 * The setIsCategory function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $value The value.
	 */
	public function setIsCategory( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( 'is_category', $value );

		return $this;
	}

	/**
	 * The setPostById function.
	 *
	 * @since 4.4.0
	 *
	 * @param int $id The id.
	 */
	public function setPostById( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$post                  = get_post( $id );
		$this->context['post'] = $post;

		return $this;
	}

	/**
	 * The setIsPostType function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $post_type The post type.
	 * @param int    $value The value.
	 */
	public function setIsPostType( $post_type, $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->setContextBooleanByKey( sprintf( 'is_%s', $post_type ), $value );

		return $this;
	}
}
