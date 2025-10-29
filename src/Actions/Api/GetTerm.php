<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Get Term
 */
class GetTerm implements ExecuteHooks {

	/**
	 * The Get Term hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Get Term register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/terms/(?P<id>\d+)',
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
	}

	/**
	 * The Get Term process get.
	 *
	 * @since 5.0.0
	 * @param int    $id The term ID.
	 * @param string $taxonomy The taxonomy.
	 * @return array
	 */
	protected function getData( $id, $taxonomy = 'category' ) {
		$context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId(
			$id,
			array(
				'type'     => 'term',
				'taxonomy' => $taxonomy,
			)
		)->getContext();

		$title = seopress_get_service( 'TitleMeta' )->getValue( $context );

		$description = seopress_get_service( 'DescriptionMeta' )->getValue( $context );

		$social       = seopress_get_service( 'SocialMeta' )->getValue( $context );
		$robots       = seopress_get_service( 'RobotMeta' )->getValue( $context );
		$redirections = seopress_get_service( 'RedirectionMeta' )->getValue( $context, false );

		$canonical = '';
		if ( isset( $robots['canonical'] ) ) {
			$canonical = $robots['canonical'];
			unset( $robots['canonical'] );
		}

		if ( isset( $robots['primarycat'] ) ) {
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
			'og'           => $social['og'],
			'twitter'      => $social['twitter'],
			'robots'       => $robots,
			'breadcrumbs'  => $breadcrumbs,
			'redirections' => $redirections,
		);

		return apply_filters( 'seopress_headless_get_post', $data, $id, $context );
	}

	/**
	 * The Get Term process get.
	 *
	 * @since 5.0.0
	 *
	 * @param \WP_REST_Request $request The request.
	 */
	public function processGet( \WP_REST_Request $request ) {
		$id       = $request->get_param( 'id' );
		$taxonomy = $request->get_param( 'taxonomy' );
		if ( null === $taxonomy ) {
			$taxonomy = 'category';
		}

		$data = $this->getData( $id, $taxonomy );

		wp_send_json_success( $data );
		return; // phpcs:ignore -- TODO: check if this is needed
	}
}
