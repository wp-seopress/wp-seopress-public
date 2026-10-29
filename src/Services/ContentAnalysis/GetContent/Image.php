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
	 * Default number of image size requests running at the same time.
	 */
	const CONCURRENCY = 5;

	/**
	 * Seconds after which no further batch of image size requests is started.
	 */
	const TIME_BUDGET = 10;

	/**
	 * Base URLs of this site's files, mapped to their directories.
	 *
	 * @var array|null
	 */
	private $local_roots = null;

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
				'src'     => $img_src,
				'alt'     => $img->getAttribute( 'alt' ),
				'has_alt' => $img->hasAttribute( 'alt' ),
			);
		}

		// Second pass — drop sub-100-byte tracking pixels / spacers.
		//
		// Anything we cannot measure is kept (matching the old "download failed
		// -> keep" behaviour), so analysis results are preserved.

		/**
		 * Filter the maximum number of outbound requests made to measure images.
		 *
		 * Bounds the probes regardless of how many <img> tags the (client
		 * supplied) source carries, so the analysis can never be turned into a
		 * request amplifier. Images stored on this site are measured on disk and
		 * do not count.
		 *
		 * @since 10.0.0
		 * @since 10.4.0 Returning 0 or less skips the byte size check entirely,
		 *               instead of lifting the cap.
		 *
		 * @param int $max_checks Maximum number of distinct image URLs requested.
		 */
		$max_checks = (int) apply_filters( 'seopress_content_analysis_image_size_checks', 100 );
		if ( $max_checks <= 0 ) {
			return array_values( $data );
		}

		// A logo repeated in the header, the mobile menu and the footer is the
		// same file: measure each URL once, then apply the result to every tag.
		$keys_by_url = array();
		foreach ( $to_measure as $key => $url ) {
			$keys_by_url[ $url ][] = $key;
		}

		$tiny   = array();
		$remote = array();
		foreach ( array_keys( $keys_by_url ) as $url ) {
			// On a typical page nearly every image is the site's own. Asking
			// ourselves over HTTP for the size of a file on our own disk is
			// what made this check expensive.
			$size = $this->get_local_file_size( $url );
			if ( null === $size ) {
				$remote[] = $url;
			} elseif ( $size > 0 && $size < 100 ) {
				$tiny[] = $url;
			}
		}

		$remote = array_slice( $remote, 0, $max_checks );
		$tiny   = array_merge( $tiny, $this->find_tiny_images( $remote ) );

		foreach ( $tiny as $url ) {
			foreach ( $keys_by_url[ $url ] as $key ) {
				unset( $data[ $key ] );
			}
		}

		return array_values( $data );
	}

	/**
	 * Size in bytes of an image stored on this site, read from disk.
	 *
	 * Covers the uploads directory, wherever it lives, and wp-content (theme
	 * and plugin images). The resolved path must stay inside the directory it
	 * was mapped to, so a "../" in a client-supplied URL cannot reach out of it.
	 *
	 * @param string $url The image URL.
	 *
	 * @return int|null The size, or null when the URL is not a file of this site.
	 */
	private function get_local_file_size( $url ) {
		// Compared without the scheme: a page mixing http and https URLs for
		// its own uploads is common after a migration.
		$url = preg_replace( '#^https?:#i', '', preg_replace( '/[?#].*$/', '', $url ) );

		foreach ( $this->get_local_roots() as $root_url => $root_dir ) {
			if ( 0 !== stripos( $url, $root_url ) ) {
				continue;
			}

			$path = rawurldecode( substr( $url, strlen( $root_url ) ) );
			// A "%00" in the URL decodes to a NUL byte, which realpath() rejects
			// with a ValueError on PHP 8. No file of the site has one in its name.
			if ( false !== strpos( $path, "\0" ) ) {
				return null;
			}

			$file = realpath( $root_dir . $path );
			if ( false === $file || 0 !== strpos( $file, $root_dir ) || ! is_file( $file ) ) {
				// Not on disk (an image generated on the fly, for instance):
				// let the network check have it.
				return null;
			}

			$size = filesize( $file );

			return false === $size ? null : $size;
		}

		return null;
	}

	/**
	 * Map of scheme-less base URL => real directory path, both with a trailing slash.
	 *
	 * @return array
	 */
	private function get_local_roots() {
		if ( null !== $this->local_roots ) {
			return $this->local_roots;
		}

		$this->local_roots = array();

		$uploads = wp_get_upload_dir();
		$pairs   = array(
			array( isset( $uploads['baseurl'] ) ? $uploads['baseurl'] : '', isset( $uploads['basedir'] ) ? $uploads['basedir'] : '' ),
			array( content_url(), WP_CONTENT_DIR ),
		);

		foreach ( $pairs as $pair ) {
			$dir = '' !== $pair[1] ? realpath( $pair[1] ) : false;
			if ( '' === $pair[0] || false === $dir ) {
				continue;
			}

			$root_url = trailingslashit( preg_replace( '#^https?:#i', '', $pair[0] ) );
			if ( ! isset( $this->local_roots[ $root_url ] ) ) {
				$this->local_roots[ $root_url ] = trailingslashit( $dir );
			}
		}

		return $this->local_roots;
	}

	/**
	 * Return the URLs of the images confirmed to be smaller than 100 bytes.
	 *
	 * Uses HEAD requests (Content-Length) rather than a full download per
	 * image, a few at a time. All at once looks faster, but libcurl starts a
	 * resolver thread per concurrent transfer and each one can reserve its own
	 * malloc arena for the life of the PHP worker: on hosting that caps a
	 * worker's address space, a page with dozens of images took the worker
	 * down, and unrelated requests with it. Unknown / unreachable sizes are not
	 * reported, so the caller keeps those images.
	 *
	 * @param array $urls List of image URLs.
	 *
	 * @return array List of URLs whose image is under 100 bytes.
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
		foreach ( array_values( $urls ) as $index => $url ) {
			$requests[ $index ] = array(
				'url'  => $url,
				'type' => 'HEAD',
			);
		}

		$request_options = array(
			'timeout'          => 3,
			'connect_timeout'  => 3,
			'verify'           => true,
			'follow_redirects' => false,
			'redirects'        => 0,
		);

		/**
		 * Filter how many image size requests run at the same time.
		 *
		 * @since 10.4.0
		 *
		 * @param int $concurrency Number of concurrent HEAD requests.
		 */
		$concurrency = max( 1, (int) apply_filters( 'seopress_content_analysis_image_size_concurrency', self::CONCURRENCY ) );

		// Batches add up where a single call did not: stop measuring once the
		// budget is spent and keep the remaining images, as for any unknown size.
		$deadline = microtime( true ) + $this->get_time_budget();

		foreach ( array_chunk( $requests, $concurrency, true ) as $batch ) {
			if ( microtime( true ) >= $deadline ) {
				break;
			}

			try {
				$responses = $this->send_requests( $requests_class, $batch, $request_options );
			} catch ( \Throwable $e ) {
				continue;
			}

			if ( ! is_array( $responses ) ) {
				continue;
			}

			foreach ( $responses as $index => $response ) {
				// A failed request (Exception object or unsuccessful response)
				// means "unknown size" -> keep the image.
				if ( ! is_object( $response ) || empty( $response->success ) ) {
					continue;
				}
				if ( ! isset( $response->headers['content-length'] ) ) {
					continue;
				}
				$length = (int) $response->headers['content-length'];
				if ( $length > 0 && $length < 100 && isset( $batch[ $index ] ) ) {
					$tiny[] = $batch[ $index ]['url'];
				}
			}
		}

		return $tiny;
	}

	/**
	 * Seconds after which no further batch of image size requests is started.
	 *
	 * @return int
	 */
	protected function get_time_budget() {
		return self::TIME_BUDGET;
	}

	/**
	 * Send one batch of requests in parallel.
	 *
	 * @param string $requests_class The Requests class name.
	 * @param array  $requests       The requests, keyed by index.
	 * @param array  $options        The transfer options.
	 *
	 * @return array The responses, keyed like the requests.
	 */
	protected function send_requests( $requests_class, $requests, $options ) {
		return call_user_func( array( $requests_class, 'request_multiple' ), $requests, $options );
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
