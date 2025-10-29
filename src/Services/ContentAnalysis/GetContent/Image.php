<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image
 */
class Image {

	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();

		$items = $xpath->query( '//img[not(ancestor::noscript)]' );

		foreach ( $items as $key => $img ) {
			// Get the actual image source, handling lazy loading and caching.
			$img_src = $this->get_image_source( $img );
			if ( empty( $img_src ) ) {
				continue;
			}

			$result = preg_match_all( '#\b(avatar)\b#iu', $img->getAttribute( 'class' ), $matches );

			// Exclude avatars from analysis.
			if ( $result ) {
				continue;
			}

			// Exclude images inferior to 1px.
			if ( $img->hasAttribute( 'width' ) || $img->hasAttribute( 'height' ) ) {
				if ( $img->getAttribute( 'width' ) <= 1 || $img->getAttribute( 'height' ) <= 1 ) {
					continue;
				}
			}

			// Exclude images inferior to 100 bytes.
			if ( ! function_exists( 'download_url' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$downloaded_img = download_url( $img_src );
			if ( false === is_wp_error( $downloaded_img ) ) {
				if ( filesize( $downloaded_img ) < 100 ) {
					continue;
				}
				wp_delete_file( $downloaded_img );
			}

			$data[ $key ]['src'] = $img_src;
			$data[ $key ]['alt'] = $img->getAttribute( 'alt' );
		}

		return array_values( $data );
	}

	/**
	 * Get the actual image source, handling lazy loading and caching attributes.
	 *
	 * @param object $img The img DOM element.
	 *
	 * @return string The image source URL.
	 */
	private function get_image_source( $img ) {
		// Priority order for image source attributes.
		$source_attributes = array(
			'src',              // Standard src attribute.
			'data-src',         // Common lazy loading attribute.
			'data-lazy-src',    // Another common lazy loading attribute.
			'data-original',    // Used by some lazy loading libraries.
			'data-lazy',        // Alternative lazy loading attribute.
			'data-defer-src',   // Deferred loading attribute.
			'data-delayed-src', // Delayed loading attribute.
			'data-srcset',      // Sometimes used for lazy loading.
			'data-cache-src',   // Cache-specific attribute.
			'data-cdn-src',     // CDN-specific attribute.
			'data-retina-src',  // Retina image source.
			'data-fallback-src', // Fallback source.
		);

		foreach ( $source_attributes as $attr ) {
			if ( $img->hasAttribute( $attr ) ) {
				$src = $img->getAttribute( $attr );

				// Handle srcset attributes (take the first URL).
				if ( ( 'data-srcset' === $attr || 'srcset' === $attr ) && false !== strpos( $src, ',' ) ) {
					$srcset_parts = explode( ',', $src );
					$src          = trim( explode( ' ', $srcset_parts[0] )[0] );
				}

				// Clean and validate URL.
				$src = $this->clean_image_url( $src );
				if ( ! empty( $src ) && $this->is_valid_image_url( $src ) ) {
					return $src;
				}
			}
		}

		return '';
	}

	/**
	 * Clean and normalize image URL.
	 *
	 * @param string $url The image URL to clean.
	 *
	 * @return string The cleaned URL.
	 */
	private function clean_image_url( $url ) {
		// Remove whitespace.
		$url = trim( $url );

		// Handle data URLs (base64 encoded images).
		if ( 0 === strpos( $url, 'data:' ) ) {
			return $url;
		}

		// Handle protocol-relative URLs.
		if ( 0 === strpos( $url, '//' ) ) {
			$url = 'https:' . $url;
		}

		// Handle relative URLs (convert to absolute if possible).
		if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
			$home_url = home_url();
			$url      = $home_url . $url;
		}

		return $url;
	}

	/**
	 * Validate if URL is a valid image URL.
	 *
	 * @param string $url The URL to validate.
	 *
	 * @return bool True if valid image URL.
	 */
	private function is_valid_image_url( $url ) {
		// Check if it's a valid URL.
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		// Allow data URLs for base64 encoded images.
		if ( 0 === strpos( $url, 'data:' ) ) {
			return true;
		}

		// Check for common image file extensions.
		$image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico' );
		$path_info        = pathinfo( wp_parse_url( $url, PHP_URL_PATH ) );
		$extension        = isset( $path_info['extension'] ) ? strtolower( $path_info['extension'] ) : '';

		// If no extension, it might still be a valid image URL (some CDNs don't use extensions).
		if ( empty( $extension ) ) {
			return true;
		}

		return in_array( $extension, $image_extensions, true );
	}
}
