<?php

namespace SEOPress\Services\Context;

if ( ! defined('ABSPATH')) {
	exit;
}

class ContextPage
{
	protected $context;

	protected function getEmptyContext(){
		return  [
			'post'           => null,
			'product'        => null,
			'term_id'        => null,
			'is_singular'    => false,
			'is_single'      => false,
			'is_post_type_archive' => false,
			'is_singular'      => false,
			'is_home'        => false,
			'is_front_page'  => false,
			'is_product'     => false,
			'is_archive'     => false,
			'is_category'    => false,
			'is_author'      => false,
			'is_tax'         => false,
			'is_tag'         => false,
			'is_search'      => false,
			'is_date'        => false,
			'is_404'         => false,
			'has_category'   => false,
			'has_tag'        => false,
			'paged'          => null,
			'schemas_manual' => [],
		];
	}

	protected function buildTerm($id, $options){
		$taxonomy = isset($options['taxonomy']) ? $options['taxonomy'] : 'category';
		$term = get_term_by('id', $id, $taxonomy);

		if ($term) {
			$this->setIsCategory(true);
			$this->setTermId($id);
			$this->context['term'] = $term;
		}
	}

	protected function buildIsHome(){
		if( ! ( $this->context['is_404'] || $this->context['is_search'] || $this->context['is_front_page']  || $this->context['is_archive'] || $this->context['is_singular'] )){
			$this->context['is_home'] = true;
		}
	}

	protected function buildIsFrontPage($id){
		$showOnFront = get_option( 'show_on_front' );
		$pageOnFront = get_option( 'page_on_front' );

		if ( 'posts' === $showOnFront && $id === $pageOnFront ) {
			$this->setIsFrontPage(true);
			return;
		} elseif ( 'page' === $showOnFront && $id === $pageOnFront
		) {
			$this->setIsFrontPage(true);
			return;
		}

		$this->setIsFrontPage(false);

	}

	protected function buildPost($id){

		$isPostType = get_post_type($id);

		if ($isPostType) {
			$this->setPostById((int) $id);
			$terms = get_the_terms($id, 'post_tag');

			if ( ! empty($terms)) {
				$this->setHasTag(true);
			}

			$categories = get_the_terms($id, 'category');
			if ( ! empty($categories)) {
				$this->setHasCategory(true);
			}

			$this->setIsPostType($isPostType, true);

			// TODO : Need better verification
			$this->setIsSingle(true);
			$this->context['is_singular'] = true;
		}

		$this->buildIsFrontPage($id);


		$term = term_exists($id);
		if (null !== $term) {
			$this->setIsCategory(true);
			$this->setTermId($id);
		}


		$this->buildIsHome();
	}

	/**
	 * @since 4.4.0
	 *
	 * @param int   $id
	 * @param array $options
	 *
	 * @return void
	 */
	public function buildContextWithCurrentId($id, $options = []) {
		$typeBuild = isset($options['type']) ? $options['type'] : 'post';

		$this->buildContextDefault();

		switch($typeBuild) {
			case 'post':
				$this->buildPost($id);
				break;
			case 'term':
				$this->buildTerm($id, $options);
				break;
		}


		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @return array
	 */
	public function buildContextDefault() {
		global $post;
		global $product;

		$context = $this->getEmptyContext();

		$context['post'] = $post;
		$context['product'] = $product;
		$context['paged'] = get_query_var('paged');

		if (is_singular()) {
			$schemasManual = get_post_meta($context['post']->ID, '_seopress_pro_schemas_manual', true);
			if ( ! $schemasManual) {
				$schemasManual = [];
			}
			$context       = array_replace($context, ['is_single' => true, 'schemas_manual' => $schemasManual]);
		}
		if (is_home()) {
			$context = array_replace($context, ['is_home' => true]);
		}
		if (is_front_page()) {
			$context = array_replace($context, ['is_front_page' => true]);
		}
		if (is_singular()) {
			$context = array_replace($context, ['is_singular' => true]);
		}
		if (is_post_type_archive()) {
			$context = array_replace($context, ['is_archive' => true, 'is_post_type_archive' => true]);
		}
		if (is_tax() || is_category() || is_tag()) {
			$object = get_queried_object();

			if($object !== null && property_exists($object, 'term_id')){
				$context = array_replace($context, ['term_id' => $object->term_id, 'term' => $object ]);
			}
			$context = array_replace($context, ['is_category' => true]);
		}
		if (is_tax()) {
			$context = array_replace($context, ['is_tax' => true]);
		}
		if (is_tag()) {
			$context = array_replace($context, ['is_tag' => true]);
		}
		if (is_author()) {
			$context = array_replace($context, ['is_author' => true]);
		}
		if (is_404()) {
			$context = array_replace($context, ['is_404' => true]);
		}
		if (is_search()) {
			$context = array_replace($context, ['is_search' => true]);
		}
		if (has_category()) {
			$context = array_replace($context, ['has_category' => true]);
		}
		if (has_tag()) {
			$context = array_replace($context, ['has_tag' => true]);
		}

		$this->context = $context;

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @return array
	 */
	public function getContext() {
		if ($this->context) {
			return $this->context;
		}

		$this->buildContextDefault();

		return $this->context;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $key
	 * @param bool   $value
	 */
	protected function setContextBooleanByKey($key, $value) {
		$this->context[$key] = $value;

		return $this;
	}



	/**
	 * @since 4.4.0
	 *
	 * @param string $value
	 */
	public function setTermId($value) {
		$this->context['term_id'] = $value;

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $value
	 */
	public function setIsSingle($value) {
		$this->setContextBooleanByKey('is_single', $value);

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $value
	 */
	public function setHasTag($value) {
		$this->setContextBooleanByKey('has_tag', $value);

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $value
	 */
	public function setHasCategory($value) {
		$this->setContextBooleanByKey('has_category', $value);

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $value
	 */
	public function setIsHome($value) {
		$this->setContextBooleanByKey('is_home', $value);

		return $this;
	}

	/**
	 *
	 * @param string $value
	 */
	public function setIsFrontPage($value) {
		$this->setContextBooleanByKey('is_front_page', $value);

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $value
	 */
	public function setIsCategory($value) {
		$this->setContextBooleanByKey('is_category', $value);

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param int $id
	 */
	public function setPostById($id) {
		$post                  = get_post($id);
		$this->context['post'] = $post;

		return $this;
	}

	/**
	 * @since 4.4.0
	 *
	 * @param string $postType
	 * @param int    $value
	 */
	public function setIsPostType($postType, $value) {
		$this->setContextBooleanByKey(sprintf('is_%s', $postType), $value);

		return $this;
	}
}
