<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\PagesAdmin;

/**
 * Instant Indexing history: the History tab and the post metabox.
 *
 * @since 10.4.0
 */
class InstantIndexingHistory implements ExecuteHooks {

	/**
	 * Rows shown in the metabox.
	 *
	 * @var int
	 */
	const POST_LIMIT = 20;

	/**
	 * The hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
		add_action( 'admin_init', array( $this, 'migrate' ) );
	}

	/**
	 * Copy the last request logged before the history existed, once.
	 *
	 * @return void
	 */
	public function migrate() {
		seopress_get_service( 'IndexingHistory' )->maybe_migrate();
	}

	/**
	 * Whether the current user manages Instant Indexing.
	 *
	 * @return bool
	 */
	public function can_manage() {
		return current_user_can( seopress_capability( 'manage_options', PagesAdmin::INSTANT_INDEXING ) );
	}

	/**
	 * Clean the URL search term.
	 *
	 * @param mixed $value Search term.
	 *
	 * @return string
	 */
	public function sanitize_search( $value ) {
		return is_string( $value ) ? trim( wp_strip_all_tags( $value ) ) : '';
	}

	/**
	 * Register the routes.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/instant-indexing/history',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_history' ),
					'permission_callback' => array( $this, 'can_manage' ),
					'args'                => array(
						'page'     => array(
							'type'              => 'integer',
							'default'           => 1,
							'minimum'           => 1,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
						),
						'per_page' => array(
							'type'              => 'integer',
							'default'           => 20,
							'minimum'           => 1,
							'maximum'           => 100,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
						),
						'search'   => array(
							'type'              => 'string',
							'default'           => '',
							// Not sanitize_text_field(): it strips percent-encoded
							// octets, and non-Latin URLs are stored encoded. The
							// value only reaches a prepared LIKE.
							'sanitize_callback' => array( $this, 'sanitize_search' ),
							'validate_callback' => 'rest_validate_request_arg',
						),
						'source'   => array(
							'type'              => 'string',
							'default'           => '',
							'enum'              => array( '', 'manual', 'auto' ),
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => 'rest_validate_request_arg',
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_history' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/indexing-history',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_post_history' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);
	}

	/**
	 * A page of the whole history.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_history( \WP_REST_Request $request ) {
		$per_page = (int) $request->get_param( 'per_page' );
		$result   = seopress_get_service( 'IndexingHistory' )->query(
			array(
				'page'     => (int) $request->get_param( 'page' ),
				'per_page' => $per_page,
				'search'   => (string) $request->get_param( 'search' ),
				'source'   => (string) $request->get_param( 'source' ),
			)
		);

		return new \WP_REST_Response(
			array(
				'items'       => $result['items'],
				'total'       => $result['total'],
				'total_pages' => (int) ceil( $result['total'] / max( 1, $per_page ) ),
				'max_rows'    => seopress_get_service( 'IndexingHistory' )->get_max_rows(),
			)
		);
	}

	/**
	 * Delete the whole history.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_history() {
		if ( ! seopress_get_service( 'IndexingHistory' )->clear() ) {
			return new \WP_Error( 'seopress_history_not_cleared', __( 'The history could not be cleared.', 'wp-seopress' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( array( 'success' => true ) );
	}

	/**
	 * The latest submissions of one post, for the metabox.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_post_history( \WP_REST_Request $request ) {
		$post_id = (int) $request['id'];
		$options = get_option( 'seopress_instant_indexing_option_name' );
		$manage  = $this->can_manage();

		return new \WP_REST_Response(
			array(
				'items'       => seopress_get_service( 'IndexingHistory' )->get_for_post( $post_id, self::POST_LIMIT ),
				'automatic'   => is_array( $options ) && isset( $options['seopress_instant_indexing_automate_submission'] ),
				// Only for users who can open it.
				'history_url' => $manage ? admin_url( 'admin.php?page=seopress-instant-indexing#tab=tab_seopress_instant_indexing_history' ) : '',
			)
		);
	}
}
