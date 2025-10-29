<?php // phpcs:ignore

namespace SEOPress\Services\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\Metas\SocialSettings;

/**
 * SocialMeta
 */
class SocialMeta {

	/**
	 * The getTypeSocial function.
	 *
	 * @param string $meta The meta.
	 *
	 * @return string
	 */
	protected function getTypeSocial( $meta ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $meta ) {
			case '_seopress_social_fb_title':
			case '_seopress_social_fb_desc':
			case '_seopress_social_fb_img':
			case '_seopress_social_fb_img_attachment_id':
			case '_seopress_social_fb_img_width':
			case '_seopress_social_fb_img_height':
				return 'og';

			case '_seopress_social_twitter_title':
			case '_seopress_social_twitter_desc':
			case '_seopress_social_twitter_img':
			case '_seopress_social_twitter_img_attachment_id':
			case '_seopress_social_twitter_img_width':
			case '_seopress_social_twitter_img_height':
				return 'twitter';
		}
	}

	/**
	 * The getKeySocial function.
	 *
	 * @param string $meta The meta.
	 *
	 * @return string
	 */
	public function getKeySocial( $meta ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $meta ) {
			case '_seopress_social_fb_title':
			case '_seopress_social_twitter_title':
				return 'title';
			case '_seopress_social_fb_desc':
			case '_seopress_social_twitter_desc':
				return 'description';

			case '_seopress_social_fb_img':
			case '_seopress_social_twitter_img':
				return 'image';
			case '_seopress_social_fb_img_attachment_id':
			case '_seopress_social_twitter_img_attachment_id':
				return 'attachment_id';
			case '_seopress_social_fb_img_width':
			case '_seopress_social_twitter_img_width':
				return 'image_width';
			case '_seopress_social_fb_img_height':
			case '_seopress_social_twitter_img_height':
				return 'image_height';
		}
	}

	/**
	 * The getFacebookHomeDescription function.
	 *
	 * @return string
	 */
	public function getFacebookHomeDescription() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$page_id = get_option( 'page_for_posts' );
		$value   = get_post_meta( $page_id, '_seopress_social_fb_desc', true );
		if ( empty( $value ) ) {
			return;
		}

		return $value;
	}

	/**
	 * The getFacebookTaxonomyDescription function.
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getFacebookTaxonomyDescription( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$value = get_term_meta( $id, '_seopress_social_fb_desc', true );
		if ( empty( $value ) ) {
			return;
		}

		return $value;
	}

	/**
	 * The getValue function.
	 *
	 * @param array $context The context.
	 *
	 * @return string|null
	 */
	public function getValue( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array(
			'og'      => array(),
			'twitter' => array(),
		);

		$callback = 'get_post_meta';
		$id       = null;
		if ( isset( $context['post'] ) ) {
			$id = $context['post']->ID;
		} elseif ( isset( $context['term_id'] ) ) {
			$id       = $context['term_id'];
			$callback = 'get_term_meta';
		}

		if ( null === $id ) {
			return $data;
		}

		$metas = SocialSettings::getMetaKeys( $id );

		foreach ( $metas as $key => $value ) {
			$type       = $this->getTypeSocial( $value['key'] );
			$result     = $callback( $id, $value['key'], true );
			$key_social = $this->getKeySocial( $value['key'] );

			$data[ $type ][ $key_social ] = $result;
		}

		return $data;
	}
}
