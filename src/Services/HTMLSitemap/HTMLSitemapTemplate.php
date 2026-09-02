<?php // phpcs:ignore

namespace SEOPress\Services\HTMLSitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Services\Options\SitemapOption;

/**
 * HTMLSitemapTemplate
 */
class HTMLSitemapTemplate {
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
	 * The render function.
	 *
	 * @param string $cpt_key The cpt key.
	 * @param array  $args The args.
	 *
	 * @return string
	 */
	public function render( $cpt_key, $args ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$args = apply_filters( 'seopress_sitemaps_html_query', $args, $cpt_key );
		$html = '';

		if ( is_post_type_hierarchical( $cpt_key ) ) {
			$postslist = get_posts( $args );

			$args2 = array(
				'post_type' => $cpt_key,
				'include'   => $postslist,
			);

			/*
			 * Only pass these through when they hold something. The option
			 * getters return null until the setting has been saved once, and
			 * get_pages() runs sort_column through wp_parse_list(), so a null
			 * reaches preg_split() as its subject: deprecated on PHP 8.1, a
			 * TypeError on a future release. Leaving the keys out lets
			 * get_pages() apply its own defaults, which is what the null was
			 * silently relying on anyway.
			 */
			$sort_order  = $this->sitemap_option->getHtmlOrder();
			$sort_column = $this->sitemap_option->getHtmlOrderBy();

			if ( ! empty( $sort_order ) ) {
				$args2['sort_order'] = $sort_order;
			}

			if ( ! empty( $sort_column ) ) {
				$args2['sort_column'] = $sort_column;
			}

			$args2     = apply_filters( 'seopress_sitemaps_html_pages_query', $args2, $cpt_key );
			$postslist = get_pages( $args2 );
		} else {
			$postslist = get_posts( $args );
		}

		if ( ! empty( $postslist ) ) {
			$date = true;
			if ( is_post_type_hierarchical( $cpt_key ) ) {
				$html .= $this->renderHierarchicalPosts( $postslist );
			} else {
				$html .= $this->renderFlatPosts( $postslist, $cpt_key );
			}
		}

		return $html;
	}

	/**
	 * The renderHierarchicalPosts function.
	 *
	 * @param array $posts_list The posts list.
	 *
	 * @return string
	 */
	private function renderHierarchicalPosts( $posts_list ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$walker_page = new \Walker_Page();
		$html        = '<ul class="sp-list-posts sp-cpt-hierarchical">';

		$depth = apply_filters( 'seopress_sitemaps_html_pages_depth_query', 0 );
		$html .= $walker_page->walk( $posts_list, $depth );
		$html .= '</ul>';

		return $html;
	}

	/**
	 * The renderFlatPosts function.
	 *
	 * @param array  $posts_list The posts list.
	 * @param string $cpt_key The cpt key.
	 *
	 * @return string
	 */
	private function renderFlatPosts( $posts_list, $cpt_key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$html = '<ul class="sp-list-posts">';

		foreach ( $posts_list as $post ) {
			setup_postdata( $post );

			if ( '1' !== $this->sitemap_option->getHtmlNoHierarchy() ) {
				if ( 'post' === $cpt_key || 'product' === $cpt_key ) {
					$tax = 'product' === $cpt_key ? 'product_cat' : 'category';
					if ( isset( $cat ) && ! has_term( $cat, $tax, $post ) ) {
						continue;
					}
				}
			}

			$post_title = apply_filters( 'seopress_sitemaps_html_post_title', get_the_title( $post ) );

			$html .= '<li>';
			$html .= '<a href="' . get_permalink( $post ) . '">' . $post_title . '</a>';

			if ( '1' !== $this->sitemap_option->getHtmlDate() ) {
				$date = apply_filters( 'seopress_sitemaps_html_post_date', true, $cpt_key );
				if ( true === $date ) {
					$date_format = apply_filters( 'seopress_sitemaps_html_post_date_format', 'j F Y' );
					$html       .= ' - ' . get_the_date( $date_format, $post );
				}
			}

			$html .= '</li>';
		}

		wp_reset_postdata();
		$html .= '</ul>';

		return $html;
	}
}
