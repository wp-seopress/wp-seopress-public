<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DomFilterContent
 */
class DomFilterContent {

	/**
	 * The getData function.
	 *
	 * @param string $str The string.
	 * @param mixed  $id The id.
	 *
	 * @return array
	 */
	public function getData( $str, $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( empty( $str ) ) {
			return array(
				'code' => 'no_data',
			);
		}

		$dom                     = new \DOMDocument();
		$internal_errors         = libxml_use_internal_errors( true );
		$dom->preserveWhiteSpace = false; //phpcs:ignore

		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $str );

		// Disable wptexturize.
		add_filter( 'run_wptexturize', '__return_false' );

		$xpath = new \DOMXPath( $dom );

		$data = array(
			'title'               => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Title',
				'value' => '',
			),
			'description'         => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Description',
				'value' => '',
			),
			'og:title'            => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Title',
				'value' => '',
			),
			'og:description'      => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Description',
				'value' => '',
			),
			'og:image'            => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Image',
				'value' => '',
			),
			'og:url'              => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Url',
				'value' => '',
			),
			'og:site_name'        => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Sitename',
				'value' => '',
			),
			'twitter:title'       => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\Title',
				'value' => '',
			),
			'twitter:description' => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\Description',
				'value' => '',
			),
			'twitter:image'       => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\Image',
				'value' => '',
			),
			'twitter:image:src'   => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\ImageSrc',
				'value' => '',
			),
			'canonical'           => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Canonical',
				'value' => '',
			),
			'h1'                  => array(
				'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Hn',
				'value'   => '',
				'options' => array(
					'hn' => 'h1',
				),
			),
			'h2'                  => array(
				'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Hn',
				'value'   => '',
				'options' => array(
					'hn' => 'h2',
				),
			),
			'h3'                  => array(
				'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Hn',
				'value'   => '',
				'options' => array(
					'hn' => 'h3',
				),
			),
			'images'              => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Image',
				'value' => '',
			),
			'meta_robots'         => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Metas\Robot',
				'value' => '',
			),
			'meta_google'         => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Metas\Google',
				'value' => '',
			),
			'links_no_follow'     => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\LinkNoFollow',
				'value' => '',
			),
			'outbound_links'      => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OutboundLinks',
				'value' => '',
			),
			'internal_links'      => array(
				'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\InternalLinks',
				'value'   => '',
				'options' => array(
					'id' => $id,
				),
			),
			'schemas'             => array(
				'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Schema',
				'value' => '',
			),
		);

		$data = apply_filters( 'seopress_get_data_dom_filter_content', $data );

		foreach ( $data as $key => $item ) {
			$class = new $item['class']();

			$options = isset( $item['options'] ) ? $item['options'] : array();

			if ( method_exists( $class, 'getDataByXPath' ) ) {
				$data[ $key ]['value'] = $class->getDataByXPath( $xpath, $options );
			} elseif ( method_exists( $class, 'getDataByDom' ) ) {
				$data[ $key ]['value'] = $class->getDataByDom( $dom, $options );
			}
		}

		$data['id'] = array(
			'value' => $id,
		);

		$taxname = isset( $_GET['tax_name'] ) ? sanitize_text_field( wp_unslash( $_GET['tax_name'] ) ) : null;
		if ( ! empty( $taxname ) ) {
			$term              = get_term( $id, $taxname );
			$data['permalink'] = array(
				'value' => esc_url( get_term_link( $term->slug, $taxname ) ),
			);
		} else {
			$data['permalink'] = array(
				'value' => get_permalink( $id ),
			);
		}

		$data['id_homepage'] = array(
			'value' => get_option( 'page_on_front' ),
		);

		return $data;
	}
}
