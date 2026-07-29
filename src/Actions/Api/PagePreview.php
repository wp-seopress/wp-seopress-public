<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

/**
 * Page Preview
 */
class PagePreview implements ExecuteHooks {

	/**
	 * The Page Preview hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Page Preview register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/page-preview',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'preview' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);

		// Same handler over POST so the browser can hand us the rendered HTML
		// it captured (crawler view, WAF-proof) instead of us looping back.
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/page-preview/analyze',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'preview' ),
				'args'                => array(
					'id'     => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
					// The HTTP status the browser observed for the captured
					// page, when its transport could read one. 0 means unknown.
					'status' => array(
						'default'           => 0,
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
						'sanitize_callback' => 'absint',
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);
	}

	/**
	 * The Page Preview process preview.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function preview( \WP_REST_Request $request ) {
		$api_header = new ApiHeader();
		$api_header->hooks();

		$id = (int) $request->get_param( 'id' );

		// Prefer the browser-captured HTML; fall back to the server loop-back.
		$html = $request->get_param( 'html' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- parsed read-only by DOMDocument, never echoed.

		if ( ! empty( $html ) ) {
			// The transport probed the page's real HTTP status: authoritative,
			// so honor it before looking at the markup at all. Yields no message
			// when the status is unknown (0) or fine — the body-class backstop
			// below then decides.
			$status_message = $this->capturedHtmlStatusMessage( $request->get_param( 'status' ) );

			if ( null !== $status_message ) {
				return $this->previewMessage( $status_message );
			}

			// Backstop for the captures that carry no status: the iframe
			// transport cannot read one, so a draft previewed over a URL that
			// 404s reaches us as a fully rendered "Page not found" page. Two
			// markers catch it, both keyed on the body classes WordPress prints:
			// the theme's standard 404 page carries `error404`, while a
			// page-builder or redirect "custom 404" renders a different post
			// whose `postid-N` / `page-id-N` does not match this one. Either
			// way, surface the same "publish your post!" message as the
			// loop-back 404 path rather than analyzing the wrong <title>.
			$body_classes = $this->capturedBodyClasses( (string) $html );

			if (
				$this->capturedHtmlIs404( $body_classes )
				|| $this->capturedHtmlIsForeignPost( $body_classes, (string) $html, $id )
			) {
				return $this->previewMessage( __( 'To get your Google snippet preview, publish your post!', 'wp-seopress' ) );
			}

			$str = (string) $html;
		} else {
			$dom_result = seopress_get_service( 'RequestPreview' )->getDomById( $id );

			if ( ! $dom_result['success'] ) {
				$message = '';

				switch ( $dom_result['code'] ) {
					case 404:
						$message = __( 'To get your Google snippet preview, publish your post!', 'wp-seopress' );
						break;
					case 401:
						$message = __( 'Your site is protected by an authentication.', 'wp-seopress' );
						break;
				}

				return $this->previewMessage( $message );
			}

			$str = $dom_result['body'];
		}

		$data = seopress_get_service( 'DomFilterContent' )->getData( $str, $id );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$data['analyzed_content_id'] = $id;
		}

		$data['analysis_target_kw'] = array(
			'value' => array_filter( explode( ',', strtolower( (string) get_post_meta( $id, '_seopress_analysis_target_kw', true ) ) ) ),
		);

		return new \WP_REST_Response( $data );
	}

	/**
	 * Build the preview response carrying a user-facing message instead of
	 * analyzed content.
	 *
	 * Matches the nested { value } shape returned on success (DomFilterContent)
	 * so consumers reading `title.value` / `description.value` (GooglePreview,
	 * field placeholders) surface the message instead of a blank preview.
	 *
	 * @param string $message The message to surface in the title slot.
	 *
	 * @return \WP_REST_Response
	 */
	private function previewMessage( $message ) {
		return new \WP_REST_Response(
			array(
				'title'       => array( 'value' => $message ),
				'description' => array( 'value' => '' ),
			)
		);
	}

	/**
	 * Map the HTTP status the browser transport observed for the captured page
	 * to the same user-facing message as the server loop-back path.
	 *
	 * The status is authoritative when we have it — no markup heuristic needed.
	 * It is only known for the fetch transports and for the iframe navigation's
	 * status probe; a capture with no status reports 0 and the caller falls back
	 * to the body-class markers.
	 *
	 * Deliberately limited to "this page does not exist". A WAF answers the
	 * probe with 401/403 while happily serving the top-level navigation the
	 * iframe made, so treating those as fatal would throw away markup we did
	 * capture. Authentication walls are already reported by the loop-back path.
	 *
	 * @param mixed $status The status reported by the client, 0 when unknown.
	 *
	 * @return string|null The message to surface, or null to keep analyzing.
	 */
	private function capturedHtmlStatusMessage( $status ) {
		$status = (int) $status;

		if ( 404 === $status || 410 === $status ) {
			return __( 'To get your Google snippet preview, publish your post!', 'wp-seopress' );
		}

		return null;
	}

	/**
	 * The class tokens carried by the captured document's <body> tag.
	 *
	 * Parsed with DOMDocument rather than matched with a regex: the markup comes
	 * from an arbitrary theme, and only a real parse reliably resolves attribute
	 * quoting, ordering and stray "<body" strings appearing earlier in the page.
	 *
	 * @param string $html The captured HTML.
	 *
	 * @return string[] The body class tokens, empty when the document has no
	 *                  readable <body>.
	 */
	private function capturedBodyClasses( $html ) {
		if ( '' === $html || false === stripos( $html, '<body' ) ) {
			return array();
		}

		$internal_errors = libxml_use_internal_errors( true );

		$dom = new \DOMDocument();
		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );

		libxml_clear_errors();
		libxml_use_internal_errors( $internal_errors );

		$body = $dom->getElementsByTagName( 'body' )->item( 0 );

		if ( null === $body ) {
			return array();
		}

		$class = trim( $body->getAttribute( 'class' ) );

		if ( '' === $class ) {
			return array();
		}

		return preg_split( '/\s+/', $class );
	}

	/**
	 * Whether browser-captured HTML is actually the theme's 404 page.
	 *
	 * WordPress prints the `error404` body class on its 404 template via
	 * body_class(): present on virtually every theme and absent from any real
	 * singular/term view. Read from the <body> tag's own class list, so a post
	 * that merely mentions "error404" in its content is not mistaken for a 404
	 * page.
	 *
	 * @param string[] $body_classes The captured document's body classes.
	 *
	 * @return bool
	 */
	private function capturedHtmlIs404( $body_classes ) {
		return in_array( 'error404', $body_classes, true );
	}

	/**
	 * Whether captured HTML is positively a *different* post than the one being
	 * previewed (a page-builder/redirect "custom 404" that renders another page,
	 * a wrong vhost, etc.).
	 *
	 * Fail-open by construction: it fires only when the <body> uses WordPress's
	 * standard singular body classes (`postid-N` / `page-id-N`) — proof the theme
	 * emits identity markers — yet none match $id and the permalink is absent. A
	 * theme that prints no such classes yields no verdict, so the HTML is analyzed
	 * as before and a genuine preview is never suppressed. The standard WP 404
	 * page carries no `postid-`/`page-id-` class and is handled by capturedHtmlIs404().
	 *
	 * @param string[] $body_classes The captured document's body classes.
	 * @param string   $html         The captured HTML.
	 * @param int      $id           The post being previewed.
	 *
	 * @return bool
	 */
	private function capturedHtmlIsForeignPost( $body_classes, $html, $id ) {
		$id    = (int) $id;
		$found = false;

		foreach ( $body_classes as $class ) {
			if ( ! preg_match( '/^(?:postid|page-id)-(\d+)$/', $class, $matches ) ) {
				continue;
			}

			// The body advertises *this* post: it really is the previewed content.
			if ( $id === (int) $matches[1] ) {
				return false;
			}

			$found = true;
		}

		// No standard identity machinery in the <body> tag: we cannot judge, so
		// fail open and let the analyzer run.
		if ( ! $found ) {
			return false;
		}

		// The permalink is present somewhere (canonical, og:url...): treat as this post.
		$permalink = get_permalink( $id );
		if ( ! empty( $permalink ) && false !== strpos( $html, $permalink ) ) {
			return false;
		}

		// Standard body classes present, but for a different object: foreign page.
		return true;
	}
}
