<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Actions\Api\ContentAnalysis;
use SEOPress\Actions\Api\Metas\RobotSettings;
use SEOPress\Actions\Api\Metas\SocialSettings;
use SEOPress\Actions\Api\TitleDescriptionMeta;
use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Register SEOPress abilities on the WordPress Abilities API (WP 6.9+).
 *
 * Each ability wraps an existing SEOPress REST controller so input
 * sanitization and storage logic are reused rather than duplicated.
 *
 * @since 9.9.0
 */
class AbilitiesApi implements ExecuteHooks {

	const CATEGORY = 'seopress';

	/**
	 * Register the hooks.
	 *
	 * @since 9.9.0
	 *
	 * @return void
	 */
	public function hooks() {
		// Feature-detect: silently no-op on WordPress < 6.9.
		if ( ! seopress_abilities_api_available() ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', array( $this, 'registerCategory' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'registerAbilities' ) );
	}

	/**
	 * Register the shared "seopress" ability category.
	 *
	 * Registered only if no other SEOPress plugin already created it.
	 *
	 * @since 9.9.0
	 *
	 * @return void
	 */
	public function registerCategory() {
		if ( function_exists( 'wp_get_ability_category' ) && wp_get_ability_category( self::CATEGORY ) ) {
			return;
		}

		wp_register_ability_category(
			self::CATEGORY,
			array(
				'label'       => __( 'SEO', 'wp-seopress' ),
				'description' => __( 'Read and manage SEO data (titles, meta descriptions, robots directives, social metadata, content analysis).', 'wp-seopress' ),
			)
		);
	}

	/**
	 * Register all free abilities.
	 *
	 * @since 9.9.0
	 *
	 * @return void
	 */
	public function registerAbilities() {
		$show_in_rest = seopress_abilities_api_rest_enabled();

		$post_id_schema = array(
			'type'        => 'object',
			'properties'  => array(
				'post_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The ID of the post, page or custom post type entry.', 'wp-seopress' ),
				),
			),
			'required'             => array( 'post_id' ),
			'additionalProperties' => false,
		);

		// 1. Read the SEO title and meta description of a post.
		wp_register_ability(
			'seopress/get-post-title-description',
			array(
				'label'               => __( 'Get post SEO title and meta description', 'wp-seopress' ),
				'description'         => __( 'Retrieve the custom SEO title and meta description set for a specific post, page or custom post type entry.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $post_id_schema,
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'title'       => array(
							'type'        => 'string',
							'description' => __( 'The custom SEO title (empty if the global template is used).', 'wp-seopress' ),
						),
						'description' => array(
							'type'        => 'string',
							'description' => __( 'The custom meta description (empty if the global template is used).', 'wp-seopress' ),
						),
					),
				),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					return $this->runController(
						new TitleDescriptionMeta(),
						'processGet',
						'GET',
						(int) $input['post_id']
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'  => true,
						'idempotent' => true,
					),
				),
			)
		);

		// 2. Update the SEO title and/or meta description of a post.
		wp_register_ability(
			'seopress/update-post-title-description',
			array(
				'label'               => __( 'Update post SEO title and meta description', 'wp-seopress' ),
				'description'         => __( 'Set or clear the custom SEO title and/or meta description of a specific post. An empty string clears the value and falls back to the global template.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'     => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'description' => __( 'The ID of the post to update.', 'wp-seopress' ),
						),
						'title'       => array(
							'type'        => 'string',
							'description' => __( 'New SEO title. Pass an empty string to clear it.', 'wp-seopress' ),
						),
						'description' => array(
							'type'        => 'string',
							'description' => __( 'New meta description. Pass an empty string to clear it.', 'wp-seopress' ),
						),
					),
					'required'             => array( 'post_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => $this->codeOutputSchema(),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					$params = array();
					if ( array_key_exists( 'title', $input ) ) {
						$params['title'] = (string) $input['title'];
					}
					if ( array_key_exists( 'description', $input ) ) {
						$params['description'] = (string) $input['description'];
					}

					return $this->runController(
						new TitleDescriptionMeta(),
						'processPut',
						'PUT',
						(int) $input['post_id'],
						$params
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'   => false,
						'destructive' => true,
						'idempotent' => true,
					),
				),
			)
		);

		// 3. Read robots/indexing directives of a post.
		wp_register_ability(
			'seopress/get-post-robots-settings',
			array(
				'label'               => __( 'Get post robots settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the robots/indexing directives of a post (noindex, nofollow, canonical URL, primary category, breadcrumbs, etc.).', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $post_id_schema,
				'output_schema'       => array(
					'type'        => 'array',
					'description' => __( 'List of robots settings as { key, value } entries.', 'wp-seopress' ),
				),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					return $this->runController(
						new RobotSettings(),
						'processGet',
						'GET',
						(int) $input['post_id']
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'   => true,
						'idempotent' => true,
					),
				),
			)
		);

		// 4. Update robots/indexing directives of a post.
		wp_register_ability(
			'seopress/update-post-robots-settings',
			array(
				'label'               => __( 'Update post robots settings', 'wp-seopress' ),
				'description'         => __( 'Update the robots/indexing directives of a post. Only known SEOPress robots meta keys are accepted; unknown keys are ignored.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'  => array(
							'type'    => 'integer',
							'minimum' => 1,
						),
						'settings' => array(
							'type'        => 'object',
							'description' => __( 'Map of SEOPress robots meta keys to values, e.g. { "_seopress_robots_index": "yes", "_seopress_robots_canonical": "https://example.com/" }.', 'wp-seopress' ),
						),
					),
					'required'             => array( 'post_id', 'settings' ),
					'additionalProperties' => false,
				),
				'output_schema'       => $this->codeOutputSchema(),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					$settings = is_array( $input['settings'] ) ? $input['settings'] : array();

					return $this->runController(
						new RobotSettings(),
						'processPut',
						'PUT',
						(int) $input['post_id'],
						$settings
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					),
				),
			)
		);

		// 5. Read social (Open Graph / Twitter) metadata of a post.
		wp_register_ability(
			'seopress/get-post-social-settings',
			array(
				'label'               => __( 'Get post social settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the social (Facebook/Open Graph and X/Twitter) title, description and image metadata of a post.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $post_id_schema,
				'output_schema'       => array(
					'type'        => 'array',
					'description' => __( 'List of social settings as { key, value } entries.', 'wp-seopress' ),
				),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					return $this->runController(
						new SocialSettings(),
						'processGet',
						'GET',
						(int) $input['post_id']
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'   => true,
						'idempotent' => true,
					),
				),
			)
		);

		// 6. Update social metadata of a post.
		wp_register_ability(
			'seopress/update-post-social-settings',
			array(
				'label'               => __( 'Update post social settings', 'wp-seopress' ),
				'description'         => __( 'Update the social (Facebook/Open Graph and X/Twitter) metadata of a post. Only known SEOPress social meta keys are accepted.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'  => array(
							'type'    => 'integer',
							'minimum' => 1,
						),
						'settings' => array(
							'type'        => 'object',
							'description' => __( 'Map of SEOPress social meta keys to values, e.g. { "_seopress_social_fb_title": "...", "_seopress_social_fb_desc": "..." }.', 'wp-seopress' ),
						),
					),
					'required'             => array( 'post_id', 'settings' ),
					'additionalProperties' => false,
				),
				'output_schema'       => $this->codeOutputSchema(),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					$settings = is_array( $input['settings'] ) ? $input['settings'] : array();

					return $this->runController(
						new SocialSettings(),
						'processPut',
						'PUT',
						(int) $input['post_id'],
						$settings
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					),
				),
			)
		);

		// 7. Run the SEOPress content analysis for a post.
		wp_register_ability(
			'seopress/analyze-post-content',
			array(
				'label'               => __( 'Analyze post content', 'wp-seopress' ),
				'description'         => __( 'Run the SEOPress content analysis on a published post and return the SEO score, checks and recommendations.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'         => array(
							'type'    => 'integer',
							'minimum' => 1,
						),
						'target_keywords' => array(
							'type'        => 'string',
							'description' => __( 'Optional comma-separated target keywords to analyze against (overrides the saved ones).', 'wp-seopress' ),
						),
					),
					'required'             => array( 'post_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'        => 'object',
					'description' => __( 'The content analysis payload (score, checks, links, keywords).', 'wp-seopress' ),
				),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					$params = array();
					if ( array_key_exists( 'target_keywords', $input ) ) {
						$params['target_keywords'] = (string) $input['target_keywords'];
					}

					return $this->runController(
						new ContentAnalysis(),
						'get',
						'GET',
						(int) $input['post_id'],
						$params
					);
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly' => true,
					),
				),
			)
		);

		// 8. Read the global title & meta description settings.
		wp_register_ability(
			'seopress/get-global-titles-settings',
			array(
				'label'               => __( 'Get global title & meta description settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the site-wide SEOPress title and meta description templates and separator configuration.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'        => 'object',
					'description' => __( 'The seopress_titles_option_name option contents.', 'wp-seopress' ),
				),
				'permission_callback' => function () {
					return current_user_can( seopress_capability( 'manage_options', 'titles' ) );
				},
				'execute_callback'    => function () {
					$options = get_option( 'seopress_titles_option_name' );

					return is_array( $options ) ? $options : array();
				},
				'meta'                => array(
					'show_in_rest' => $show_in_rest,
					'annotations'  => array(
						'readonly'   => true,
						'idempotent' => true,
					),
				),
			)
		);
	}

	/**
	 * Shared permission callback for per-post abilities.
	 *
	 * @since 9.9.0
	 *
	 * @param array $input The ability input.
	 *
	 * @return bool|\WP_Error
	 */
	public function canEditPost( $input ) {
		$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;

		if ( $post_id < 1 || ! get_post( $post_id ) ) {
			return new \WP_Error(
				'seopress_ability_invalid_post',
				__( 'The provided post does not exist.', 'wp-seopress' )
			);
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error(
				'seopress_ability_forbidden',
				__( 'You are not allowed to edit this post.', 'wp-seopress' )
			);
		}

		return true;
	}

	/**
	 * Wrap an existing SEOPress REST controller and adapt its response.
	 *
	 * @since 9.9.0
	 *
	 * @param object $controller The controller instance.
	 * @param string $method     The controller method to call.
	 * @param string $http_method The simulated HTTP method.
	 * @param int    $post_id    The post ID.
	 * @param array  $params     Extra request parameters.
	 *
	 * @return mixed|\WP_Error
	 */
	protected function runController( $controller, $method, $http_method, $post_id, $params = array() ) {
		$request = new \WP_REST_Request( $http_method );
		$request->set_param( 'id', $post_id );

		foreach ( $params as $key => $value ) {
			$request->set_param( $key, $value );
		}

		try {
			$response = $controller->$method( $request );
		} catch ( \Throwable $e ) {
			return new \WP_Error(
				'seopress_ability_execution_failed',
				__( 'The SEOPress action could not be completed.', 'wp-seopress' )
			);
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( $response instanceof \WP_REST_Response ) {
			$status = $response->get_status();
			if ( $status >= 400 ) {
				return new \WP_Error(
					'seopress_ability_execution_failed',
					__( 'The SEOPress action returned an error.', 'wp-seopress' ),
					array( 'status' => $status )
				);
			}

			return $response->get_data();
		}

		return $response;
	}

	/**
	 * Standard { code } output schema for write abilities.
	 *
	 * @since 9.9.0
	 *
	 * @return array
	 */
	protected function codeOutputSchema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'code' => array(
					'type'        => 'string',
					'description' => __( 'Result code ("success" on success).', 'wp-seopress' ),
				),
			),
		);
	}
}
