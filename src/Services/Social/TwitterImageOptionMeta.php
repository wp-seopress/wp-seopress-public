<?php // phpcs:ignore

namespace SEOPress\Services\Social;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TwitterImageOptionMeta
 */
class TwitterImageOptionMeta {
	/**
	 * The getUrl function.
	 *
	 * @return string
	 */
	public function getUrl() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$value = get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_twitter_img', true );
		} else {
			$value = get_post_meta( get_the_ID(), '_seopress_social_twitter_img', true );
		}

		return $value;
	}

	/**
	 * The getAttachmentId function.
	 *
	 * @return string
	 */
	public function getAttachmentId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$value = get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_twitter_img_attachment_id', true );
		} else {
			$value = get_post_meta( get_the_ID(), '_seopress_social_twitter_img_attachment_id', true );
		}

		return $value;
	}
}
