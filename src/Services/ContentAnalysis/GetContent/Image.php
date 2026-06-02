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
		$items = $xpath->query( '//img[not(ancestor::noscript)]' );

		// First pass — attribute-only filtering (no network). Build the list of
		// kept images and, separately, the remote URLs whose byte size still has
		// to be verified to drop tracking pixels / spacers.
		$data       = array();
		$to_measure = array();
		foreach ( $items as $key => $img ) {
			// Get the actual image source, handling lazy loading and caching.
			$img_src = $this->get_image_source( $img );
			if ( empty( $img_src ) ) {
				continue;
			}

			// Exclude avatars from analysis.
			if ( preg_match( '#\b(avatar)\b#iu', $img->getAttribute( 'class' ) ) ) {
				continue;
			}

			// Exclude images inferior to 1px.
			if ( $img->hasAttribute( 'width' ) || $img->hasAttribute( 'height' ) ) {
				if ( $img->getAttribute( 'width' ) <= 1 || $img->getAttribute( 'height' ) <= 1 ) {
					continue;
				}
			}

			// data: URIs carry their bytes inline — measure for free, no request.
			if ( 0 === strpos( $img_src, 'data:' ) ) {
				if ( strlen( $img_src ) < 100 ) {
					continue;
				}
			} elseif ( wp_http_validate_url( $img_src ) ) {
				// The source HTML is supplied by the client, so only probe URLs
				// that pass WordPress' SSRF guard (no private/reserved hosts, no
				// unexpected ports). Anything else is kept without a size check
				// rather than requested blindly.
				$to_measure[ $key ] = $img_src;
			}

			$data[ $key ] = array(
				'src' => $img_src,
				'alt' => $img->getAttribute( 'alt' ),
			);
		}

		// Second pass — drop sub-100-byte tracking pixels / spacers.
		//
		// Previously every image was downloaded in full (download_url) just to
		// read its size. On an image-heavy page that meant dozens of sequential
		// HTTP GETs — several seconds — and, behind a host WAF, the site fetching
		// its own images would hang or fail. Instead, measure all candidates at
		// once with parallel HEAD requests and read Content-Length. Static images
		// are served outside the PHP/WAF path, so this is fast and reliable, and
		// anything we cannot measure is kept (matching the old "download failed
		// -> keep" behaviour), so analysis results are preserved.
		//
		// Bound the number of outbound probes regardless of how many <img> tags
		// the (client-supplied) source carries, so the analysis can never be
		// turned into a request amplifier.
		$max_checks = (int) apply_filters( 'seopress_content_analysis_image_size_checks', 100 );
		if ( $max_checks > 0 && count( $to_measure ) > $max_checks ) {
			$to_measure = array_slice( $to_measure, 0, $max_checks, true );
		}

		foreach ( $this->find_tiny_images( $to_measure ) as $key ) {
			unset( $data[ $key ] );
		}

		return array_values( $data );
	}

	/**
	 * Return the keys of the images confirmed to be smaller than 100 bytes.
	 *
	 * Uses one batch of parallel HEAD requests (Content-Length) rather than a
	 * full download per image. Unknown / unreachable sizes are not reported, so
	 * the caller keeps those images.
	 *
	 * @param array $urls Map of key => image URL.
	 *
	 * @return array List of keys whose image is under 100 bytes.
	 */
	private function find_tiny_images( $urls ) {
		$tiny = array();
		if ( empty( $urls ) ) {
			return $tiny;
		}

		$requests_class = class_exists( '\WpOrg\Requests\Requests' )
			? '\WpOrg\Requests\Requests'
			: ( class_exists( '\Requests' ) ? '\Requests' : '' );

		// No parallel transport available: skip the byte check rather than fall
		// back to slow sequential downloads. 1px / noscript / data-URI tracking
		// pixels are already excluded above.
		if ( '' === $requests_class || ! method_exists( $requests_class, 'request_multiple' ) ) {
			return $tiny;
		}

		$requests = array();
		foreach ( $urls as $key => $url ) {
			$requests[ $key ] = array(
				'url'  => $url,
				'type' => 'HEAD',
			);
		}

		$request_options = array(
			'timeout'         => 3,
			'connect_timeout' => 3,
			'verify'          => false,
		);

		try {
			$responses = call_user_func( array( $requests_class, 'request_multiple' ), $requests, $request_options );
		} catch ( \Throwable $e ) {
			return $tiny;
		}

		if ( ! is_array( $responses ) ) {
			return $tiny;
		}

		foreach ( $responses as $key => $response ) {
			// A failed request (Exception object or unsuccessful response) means
			// "unknown size" -> keep the image.
			if ( ! is_object( $response ) || empty( $response->success ) ) {
				continue;
			}
			if ( ! isset( $response->headers['content-length'] ) ) {
				continue;
			}
			$length = (int) $response->headers['content-length'];
			if ( $length > 0 && $length < 100 ) {
				$tiny[] = $key;
			}
		}

		return $tiny;
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
		$path_info        = pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ) );
		$extension        = isset( $path_info['extension'] ) ? strtolower( $path_info['extension'] ) : '';

		// If no extension, it might still be a valid image URL (some CDNs don't use extensions).
		if ( empty( $extension ) ) {
			return true;
		}

		return in_array( $extension, $image_extensions, true );
	}
}
