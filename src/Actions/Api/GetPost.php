<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Get Post
 */
class GetPost implements ExecuteHooks {

	/**
	 * The Get Post hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Get Post register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'seopress/v1',
			'/posts/by-url',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGetByUrl' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * The Get Post process get.
	 *
	 * @since 5.0.0
	 * @param int $id The post ID.
	 * @return array
	 */
	protected function getData( $id ) {
		$post_status = get_post_status( $id ) ?? null;

		if ( 'publish' !== $post_status ) {
			return null;
		}

		if ( true === post_password_required( $id ) ) {
			return null;
		}

		$context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $id )->getContext();

		$title           = seopress_get_service( 'TitleMeta' )->getValue( $context );
		$description     = seopress_get_service( 'DescriptionMeta' )->getValue( $context );
		$social_facebook = seopress_get_service( 'SocialFacebookMeta' )->getValue( $context );
		$social_twitter  = seopress_get_service( 'SocialTwitterMeta' )->getValue( $context );
		$social          = seopress_get_service( 'SocialMeta' )->getValue( $context );
		$robots          = seopress_get_service( 'RobotMeta' )->getValue( $context, false );
		$redirections    = seopress_get_service( 'RedirectionMeta' )->getValue( $context, false );

		$canonical = '';
		if ( isset( $robots['canonical'] ) ) {
			$canonical = $robots['canonical'];
			unset( $robots['canonical'] );
		}

		$primarycat = '';
		if ( isset( $robots['primarycat'] ) ) {
			$primarycat = $robots['primarycat'];
			unset( $robots['primarycat'] );
		}

		$breadcrumbs = '';
		if ( isset( $robots['breadcrumbs'] ) ) {
			$breadcrumbs = $robots['breadcrumbs'];
			unset( $robots['breadcrumbs'] );
		}

		$data = array(
			'title'        => $title,
			'description'  => $description,
			'canonical'    => $canonical,
			'og'           => $social_facebook,
			'twitter'      => $social_twitter,
			'robots'       => $robots,
			'primarycat'   => $primarycat,
			'breadcrumbs'  => $breadcrumbs,
			'redirections' => $redirections,
		);

		return apply_filters( 'seopress_headless_get_post', $data, $id, $context );
	}

	/**
	 * The Get Post process get by url.
	 *
	 * @since 5.0.0
	 *
	 * @param \WP_REST_Request $request The request.
	 */
	public function processGet( \WP_REST_Request $request ) {
		$id   = $request->get_param( 'id' );
		$data = $this->getData( $id );

		wp_send_json_success( $data );
		return; // phpcs:ignore -- TODO: check if this is needed
	}

	/**
	 * The Get Post process get by url.
	 *
	 * @since 5.0.0
	 *
	 * @param \WP_REST_Request $request The request.
	 */
	public function processGetByUrl( \WP_REST_Request $request ) {
		$url = $request->get_param( 'url' );

		if ( empty( $url ) || ! $url ) {
			return new \WP_Error( 'missing_parameters', 'Need an URL' );
		}

		try {
			$id = apply_filters( 'seopress_headless_url_to_postid', url_to_postid( $url ), $request );
			if ( ! $id ) {
				return new \WP_Error( 'not_found', 'ID for URL not found' );
			}

			$data = $this->getData( $id );

			wp_send_json_success( $data );
			return;
		} catch ( \Exception $e ) {
			return new \WP_Error( 'unknow' );
		}
	}
}
