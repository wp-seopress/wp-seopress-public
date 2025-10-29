<?php // phpcs:ignore

namespace SEOPress\Services\Social;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FacebookImageOptionMeta
 */
class FacebookImageOptionMeta {
	/**
	 * The getUrl function.
	 *
	 * @return string
	 */
	public function getUrl() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$value = get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_fb_img', true );
		} else {
			$value = get_post_meta( get_the_ID(), '_seopress_social_fb_img', true );
		}

		if ( empty( $value ) && '1' === seopress_get_service( 'SocialOption' )->getSocialFacebookImgDefault() ) {
			$options = get_option( 'seopress_social_option_name' );
			$value   = isset( $options['seopress_social_facebook_img'] ) ? $options['seopress_social_facebook_img'] : null;
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
			$value = get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_fb_img_attachment_id', true );
		} else {
			$value = get_post_meta( get_the_ID(), '_seopress_social_fb_img_attachment_id', true );
		}

		if ( empty( $value ) && '1' === seopress_get_service( 'SocialOption' )->getSocialFacebookImgDefault() && empty( get_post_meta( get_the_ID(), '_seopress_social_fb_img', true ) ) ) {
			$options = get_option( 'seopress_social_option_name' );
			$value   = isset( $options['seopress_social_facebook_img_attachment_id'] ) ? $options['seopress_social_facebook_img_attachment_id'] : null;
		}

		return $value;
	}


	/**
	 * The getMetasBy function.
	 *
	 * @param string $strategy The strategy.
	 *
	 * @return string
	 */
	public function getMetasBy( $strategy = 'url' ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( 'url' === $strategy ) {
			$url = $this->getUrl();

			if ( empty( $url ) ) {
				return '';
			}

			return $this->getMetasByUrl( $url );
		} elseif ( 'id' === $strategy ) {
			$id = $this->getAttachmentId();

			$stop_attachment_url_to_postid = apply_filters( 'seopress_stop_attachment_url_to_postid', false );

			if ( ( empty( $id ) || null === $id ) && ! $stop_attachment_url_to_postid ) {
				return $this->getMetasBy( 'url' );
			}

			return $this->getMetasStringByAttachmentId( $id );
		}

		return '';
	}

	/**
	 * The getMetasByUrl function.
	 *
	 * @param string $url The url.
	 *
	 * @return string
	 */
	public function getMetasByUrl( $url ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$str = '';
		if ( ! function_exists( 'attachment_url_to_postid' ) ) {
			return $str;
		}

		$post_id = attachment_url_to_postid( $url );

		if ( empty( $post_id ) && ! empty( $url ) ) {
			return $this->getMetasStringByUrl( $url );
		}

		return $this->getMetasStringByAttachmentId( $post_id );
	}


	/**
	 * The getMetasStringByUrl function.
	 *
	 * @param string $url The url.
	 *
	 * @return string
	 */
	public function getMetasStringByUrl( $url ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$str = '';

		// OG:IMAGE.
		$str  = '';
		$str .= '<meta property="og:image" content="' . esc_attr( $url ) . '">';
		$str .= "\n";

		// OG:IMAGE:SECURE_URL IF SSL.
		if ( is_ssl() ) {
			$str .= '<meta property="og:image:secure_url" content="' . esc_attr( $url ) . '">';
			$str .= "\n";
		}

		return $str;
	}

	/**
	 * The getOnlyImageUrlFromGlobals function.
	 *
	 * @return string
	 */
	public function getOnlyImageUrlFromGlobals() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$id = $this->getAttachmentId();

		if ( empty( $id ) ) {
			return '';
		}

		$image_src = wp_get_attachment_image_src( $id, 'full' );

		if ( empty( $image_src ) ) {
			return '';
		}

		return $image_src[0];
	}

	/**
	 * The getMetasStringByAttachmentId function.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return string
	 */
	public function getMetasStringByAttachmentId( $post_id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$str = '';

		$image_src = wp_get_attachment_image_src( $post_id, 'full' );

		if ( empty( $image_src ) ) {
			return $str;
		}

		$url = $image_src[0];

		// If cropped image.
		if ( 0 !== $post_id ) {
			$dir  = wp_upload_dir();
			$path = $url;
			if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
				$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
			}

			if ( preg_match( '/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches ) && function_exists( 'attachment_url_to_postid' ) ) {
				$url     = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
				$post_id = attachment_url_to_postid( $url );
			}
		}

		// OG:IMAGE.
		$str  = '';
		$str .= '<meta property="og:image" content="' . $url . '">';
		$str .= "\n";

		// OG:IMAGE:SECURE_URL IF SSL.
		if ( is_ssl() ) {
			$str .= '<meta property="og:image:secure_url" content="' . $url . '">';
			$str .= "\n";
		}

		// OG:IMAGE:WIDTH + OG:IMAGE:HEIGHT.
		if ( ! empty( $image_src ) ) {
			$str .= '<meta property="og:image:width" content="' . $image_src[1] . '">';
			$str .= "\n";
			$str .= '<meta property="og:image:height" content="' . $image_src[2] . '">';
			$str .= "\n";
		}

		// OG:IMAGE:ALT.
		$alt = get_post_meta( $post_id, '_wp_attachment_image_alt', true );
		if ( ! empty( $alt ) ) {
			$str .= '<meta property="og:image:alt" content="' . esc_attr( get_post_meta( $post_id, '_wp_attachment_image_alt', true ) ) . '">';
			$str .= "\n";
		}

		return $str;
	}
}
