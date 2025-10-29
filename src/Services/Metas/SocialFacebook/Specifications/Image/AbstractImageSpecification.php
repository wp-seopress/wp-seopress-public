<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Image;

/**
 * AbstractImageSpecification
 */
abstract class AbstractImageSpecification {

	/**
	 * The applyFilter function.
	 *
	 * @param array $value The value.
	 * @param array $params The params.
	 *
	 * @return array
	 */
	public function applyFilter( $value, $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( has_filter( 'seopress_social_og_thumb' ) ) {
			$value['url'] = apply_filters( 'seopress_social_og_thumb', $value['url'] );
			if ( preg_match( '/content="([^"]+)"/', $value['url'], $matches ) ) {
				$value['url'] = $matches[1];
			}
		}

		$stop_attachment_url_to_postid = apply_filters( 'seopress_stop_attachment_url_to_postid', false );
		$context                       = $params['context'];
		$post_id                       = null;

		if ( isset( $context['post'] ) && ! empty( $context['post'] ) ) {
			$post_id = get_post_thumbnail_id( $context['post']->ID );
		}

		if ( 0 === $post_id && false === $stop_attachment_url_to_postid ) {
			$post_id = attachment_url_to_postid( $value['url'] );

			// If cropped image.
			if ( 0 !== $post_id ) {
				$dir  = wp_upload_dir();
				$path = $value['url'];
				if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
					$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
				}

				if ( preg_match( '/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches ) ) {
					$value['url'] = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
					$post_id      = attachment_url_to_postid( $value['url'] );
				}
			}
		}

		if ( 0 !== $post_id ) {
			$image_src = wp_get_attachment_image_src( $post_id, 'full' );

			$value['attachment_id'] = $post_id;

			if ( ! empty( $image_src ) ) {
				$value['image_width']  = $image_src[1];
				$value['image_height'] = $image_src[2];
			}

			if ( ! empty( get_post_meta( $post_id, '_wp_attachment_image_alt', true ) ) ) {
				$value['alt'] = get_post_meta( $post_id, '_wp_attachment_image_alt', true );
			}
		}

		return $value;
	}
}
