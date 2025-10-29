<?php // phpcs:ignore

namespace SEOPress\Services\HTMLSitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Services\Options\SitemapOption;

/**
 * HTMLSitemapService
 */
class HTMLSitemapService {
	/**
	 * The sitemap option.
	 *
	 * @var SitemapOption
	 */
	private $sitemap_option;

	/**
	 * The constructor.
	 *
	 * @param SitemapOption $sitemap_option The sitemap option.
	 */
	public function __construct( SitemapOption $sitemap_option ) {
		$this->sitemap_option = $sitemap_option;
	}

	/**
	 * The init function.
	 *
	 * @return void
	 */
	public function init() {
		if ( '1' === $this->sitemap_option->getHtmlEnable() ) {
			add_action( 'wp', array( $this, 'display' ) );
			add_shortcode( 'seopress_html_sitemap', array( $this, 'renderSitemap' ) );
		}
	}

	/**
	 * The display function.
	 *
	 * @return void
	 */
	public function display() {
		if ( '' !== $this->sitemap_option->getHtmlMapping() ) {
			if ( is_page( explode( ',', $this->sitemap_option->getHtmlMapping() ) ) ) {
				add_filter( 'the_content', array( $this, 'renderSitemap' ) );
			}
		}
	}

	/**
	 * The renderSitemap function.
	 *
	 * @param string $html The html.
	 *
	 * @return string
	 */
	public function renderSitemap( $html = '' ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$atts = shortcode_atts(
			array(
				'cpt'        => '',
				'terms_only' => false,
			),
			$html,
			'[seopress_html_sitemap]'
		);

		$product_cat_slug = apply_filters( 'seopress_sitemaps_html_product_cat_slug', 'product_cat' );

		$exclude_option = $this->sitemap_option->getHtmlExclude() ?: ''; // phpcs:ignore
		$order_option   = $this->sitemap_option->getHtmlOrder() ?: ''; // phpcs:ignore
		$orderby_option = $this->sitemap_option->getHtmlOrderBy() ?: ''; // phpcs:ignore

		$html = '';

		if ( ! empty( $this->sitemap_option->getPostTypesList() ) ) {
			$html .= '<div class="wrap-html-sitemap sp-html-sitemap">';

			$post_types_list = $this->sitemap_option->getPostTypesList();

			if ( isset( $post_types_list['page'] ) ) {
				$post_types_list = array( 'page' => $post_types_list['page'] ) + $post_types_list;
			}

			if ( ! empty( $atts['cpt'] ) ) {
				unset( $post_types_list );
				$cpt = explode( ',', $atts['cpt'] );
				foreach ( $cpt as $value ) {
					$post_types_list[ $value ] = array( 'include' => '1' );
				}
			}

			$post_types_list = apply_filters( 'seopress_sitemaps_html_cpt', $post_types_list );

			foreach ( $post_types_list as $cpt_key => $cpt_value ) {
				if ( ! empty( $cpt_value ) ) {
					$html .= '<div class="sp-wrap-cpt">';
				}

				$obj = get_post_type_object( $cpt_key );
				if ( $obj ) {
					$cpt_name = apply_filters( 'seopress_sitemaps_html_cpt_name', $obj->labels->name, $obj->name );
					$html    .= '<h2 class="sp-cpt-name">' . $cpt_name . '</h2>';

					// Add archive link if post type has archives enabled.
					if ( $this->hasPostTypeArchive( $cpt_key ) ) {
						$html .= $this->renderArchiveLink( $cpt_key, $obj );
					}
				}

				foreach ( $cpt_value as $_cpt_key => $_cpt_value ) {
					if ( '1' === $_cpt_value ) {
						$args           = $this->getQueryArgs( $cpt_key, $exclude_option, $order_option, $orderby_option );
						$args_cat_query = $this->getCategoryQueryArgs( $exclude_option );

						$cats = $this->getCategories( $cpt_key, $args_cat_query, $product_cat_slug );

						// Check if we should only display terms.
						$display_terms_only = apply_filters( 'seopress_sitemaps_html_display_terms_only', $atts['terms_only'], $cpt_key );

						if ( is_array( $cats ) && ! empty( $cats ) ) {
							if ( $display_terms_only ) {
								$html .= $this->renderTermsOnly( $cats, $cpt_key );
							} elseif ( '1' !== $this->sitemap_option->getHtmlNoHierarchy() ) {
								$html .= $this->renderHierarchicalSitemap( $cats, $cpt_key, $args, $product_cat_slug );
							} else {
								$html .= $this->renderFlatSitemap( $cpt_key, $args );
							}
						} else {
							$html .= $this->renderFlatSitemap( $cpt_key, $args );
						}
					}
				}

				if ( ! empty( $cpt_value ) ) {
					$html .= '</div>';
				}
			}

			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * The renderTermsOnly function.
	 *
	 * @param array  $terms The terms.
	 * @param string $cpt_key The cpt key.
	 *
	 * @return string
	 */
	private function renderTermsOnly( $terms, $cpt_key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$html  = '<div class="sp-wrap-terms">';
		$html .= '<ul class="sp-list-terms">';

		foreach ( $terms as $term ) {
			if ( ! is_wp_error( $term ) && is_object( $term ) ) {
				$term_name = apply_filters( 'seopress_sitemaps_html_term_name', $term->name, $term );
				$term_url  = get_term_link( $term );

				if ( ! is_wp_error( $term_url ) ) {
					$html .= sprintf(
						'<li class="sp-term-item"><a href="%s">%s</a></li>',
						esc_url( $term_url ),
						esc_html( $term_name )
					);
				}
			}
		}

		$html .= '</ul>';
		$html .= '</div>';

		return apply_filters( 'seopress_sitemaps_html_terms_output', $html, $terms, $cpt_key );
	}

	/**
	 * The hasPostTypeArchive function.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return bool
	 */
	private function hasPostTypeArchive( $post_type ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$post_type_obj = get_post_type_object( $post_type );
		return $post_type_obj && $post_type_obj->has_archive;
	}

	/**
	 * The renderArchiveLink function.
	 *
	 * @param string $post_type The post type.
	 * @param object $post_type_obj The post type object.
	 *
	 * @return string
	 */
	private function renderArchiveLink( $post_type, $post_type_obj ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( '1' === $this->sitemap_option->getHtmlPostTypeArchive() ) {
			return '';
		}

		$archive_url = get_post_type_archive_link( $post_type );
		if ( ! $archive_url ) {
			return '';
		}

		$archive_label = sprintf(
			/* translators: %s: post type archive label */
			__( 'View all %s', 'wp-seopress' ),
			strtolower( $post_type_obj->labels->name )
		);

		return sprintf(
			'<div class="sp-archive-link"><a href="%s">%s</a></div>',
			esc_url( $archive_url ),
			esc_html( $archive_label )
		);
	}

	/**
	 * The getQueryArgs function.
	 *
	 * @param string $cpt_key The cpt key.
	 * @param string $exclude_option The exclude option.
	 * @param string $order_option The order option.
	 * @param string $orderby_option The orderby option.
	 *
	 * @return array
	 */
	private function getQueryArgs( $cpt_key, $exclude_option, $order_option, $orderby_option ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return array(
			'posts_per_page'   => 1000,
			'order'            => $order_option,
			'orderby'          => $orderby_option,
			'post_type'        => $cpt_key,
			'post_status'      => 'publish',
			'meta_query'       => array(
				'relation' => 'OR',
				array(
					'key'     => '_seopress_robots_index',
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_seopress_robots_index',
					'value'   => 'yes',
					'compare' => '!=',
				),
			),
			'fields'           => 'ids',
			'exclude'          => $exclude_option,
			'suppress_filters' => false,
			'no_found_rows'    => true,
			'nopaging'         => true,
		);
	}

	/**
	 * The getCategoryQueryArgs function.
	 *
	 * @param string $exclude_option The exclude option.
	 *
	 * @return array
	 */
	private function getCategoryQueryArgs( $exclude_option ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return array(
			'orderby'          => 'name',
			'order'            => 'ASC',
			'meta_query'       => array(
				'relation' => 'OR',
				array(
					'key'     => '_seopress_robots_index',
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_seopress_robots_index',
					'value'   => 'yes',
					'compare' => '!=',
				),
			),
			'exclude'          => $exclude_option,
			'suppress_filters' => false,
		);
	}

	/**
	 * The getCategories function.
	 *
	 * @param string $cpt_key The cpt key.
	 * @param array  $args_cat_query The args cat query.
	 * @param string $product_cat_slug The product cat slug.
	 *
	 * @return array
	 */
	private function getCategories( $cpt_key, $args_cat_query, $product_cat_slug ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( 'post' === $cpt_key ) {
			$args_cat_query = apply_filters( 'seopress_sitemaps_html_cat_query', $args_cat_query );
			return get_categories( $args_cat_query );
		} elseif ( 'product' === $cpt_key ) {
			$args_cat_query = apply_filters( 'seopress_sitemaps_html_product_cat_query', $args_cat_query );
			return get_terms( $product_cat_slug, $args_cat_query );
		}

		return apply_filters( 'seopress_sitemaps_html_hierarchical_terms_query', $cpt_key, $args_cat_query );
	}

	/**
	 * The renderHierarchicalSitemap function.
	 *
	 * @param array  $cats The cats.
	 * @param string $cpt_key The cpt key.
	 * @param array  $args The args.
	 * @param string $product_cat_slug The product cat slug.
	 *
	 * @return string
	 */
	private function renderHierarchicalSitemap( $cats, $cpt_key, $args, $product_cat_slug ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$html = '<div class="sp-wrap-cats">';

		foreach ( $cats as $cat ) {
			if ( ! is_wp_error( $cat ) && is_object( $cat ) ) {
				$html .= '<div class="sp-wrap-cat">';
				$html .= '<h3 class="sp-cat-name"><a href="' . get_term_link( $cat->term_id ) . '">' . $cat->name . '</a></h3>';

				if ( 'post' === $cpt_key ) {
					unset( $args['cat'] );
					$args['cat'][] = $cat->term_id;
				} elseif ( 'product' === $cpt_key ) {
					unset( $args['tax_query'] );
					$args['tax_query'] = array(
						array(
							'taxonomy' => $product_cat_slug,
							'field'    => 'term_id',
							'terms'    => $cat->term_id,
						),
					);
				}

				if ( 'post' !== $cpt_key && 'product' !== $cpt_key ) {
					$args['tax_query'] = apply_filters( 'seopress_sitemaps_html_hierarchical_tax_query', $cpt_key, $cat, $args );
				}

				$html .= $this->renderFlatSitemap( $cpt_key, $args );
				$html .= '</div>';
			}
		}

		$html .= '</div>';
		return $html;
	}

	/**
	 * The renderFlatSitemap function.
	 *
	 * @param string $cpt_key The cpt key.
	 * @param array  $args The args.
	 *
	 * @return string
	 */
	private function renderFlatSitemap( $cpt_key, $args ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$template = new HTMLSitemapTemplate( $this->sitemap_option );
		return $template->render( $cpt_key, $args );
	}
}
