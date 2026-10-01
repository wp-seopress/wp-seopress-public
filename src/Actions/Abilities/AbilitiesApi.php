<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Actions\Api\ContentAnalysis;
use SEOPress\Actions\Api\Metas\RobotSettings;
use SEOPress\Actions\Api\Metas\SocialSettings;
use SEOPress\Actions\Api\Options\IndexingSettings;
use SEOPress\Actions\Api\Options\SitemapsSettings;
use SEOPress\Actions\Api\Options\SocialSettings as GlobalSocialSettings;
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
	 * Hard ceiling on how many posts one site-wide listing call may return.
	 */
	const LISTING_MAX_PER_PAGE = 100;

	/**
	 * Hard ceiling on the page number of a site-wide listing call.
	 */
	const LISTING_MAX_PAGE = 1000;

	/**
	 * Placeholder post type used when every requested post type is unknown,
	 * so the query matches nothing instead of widening to the whole site.
	 */
	const NO_SUCH_POST_TYPE = 'seopress_no_such_post_type';

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
		// These methods are public callbacks: guard the WP 6.9+ API here too, not
		// only in hooks(), so a direct call on an older WordPress cannot fatal.
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		// Use the registry's is_registered() rather than wp_get_ability_category():
		// the latter calls get_registered(), which emits a "_doing_it_wrong" notice
		// when the category is not registered yet, which is exactly the case the
		// first time this guard runs.
		if ( class_exists( 'WP_Ability_Categories_Registry' )
			&& \WP_Ability_Categories_Registry::get_instance()->is_registered( self::CATEGORY ) ) {
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
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

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
						'post_id'     => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the post that was read.', 'wp-seopress' ),
						),
						'post_title'  => array(
							'type'        => 'string',
							'description' => __( 'The title of the post itself, which is not the SEO title.', 'wp-seopress' ),
						),
						'post_type'   => array(
							'type'        => 'string',
							'description' => __( 'The post type of the post that was read.', 'wp-seopress' ),
						),
						'status'      => array(
							'type'        => 'string',
							'description' => __( 'The publication status of the post that was read.', 'wp-seopress' ),
						),
					),
				),
				'permission_callback' => array( $this, 'canEditPost' ),
				'execute_callback'    => function ( $input ) {
					$post_id = (int) $input['post_id'];
					$read    = $this->runController(
						new TitleDescriptionMeta(),
						'processGet',
						'GET',
						$post_id
					);

					return is_wp_error( $read ) ? $read : $this->withPostIdentity( $read, $post_id );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
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
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					)
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
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
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
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					)
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
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
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
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					)
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
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly' => true,
					)
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
					return current_user_can( seopress_capability( 'manage_options', 'titles_metas' ) );
				},
				'execute_callback'    => function () {
					$options = get_option( 'seopress_titles_option_name' );

					return is_array( $options ) ? $options : array();
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
				),
			)
		);

		// 9. Find posts whose SEO title and/or meta description is missing.
		wp_register_ability(
			'seopress/list-posts-missing-metadata',
			array(
				'label'               => __( 'List posts missing SEO metadata', 'wp-seopress' ),
				'description'         => __( 'List the posts, pages and custom post type entries that have no custom SEO title and/or no custom meta description, so the site-wide gaps can be found without opening each post. Only posts the current user may read are returned.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->listingInputSchema(
					array(
						'missing' => array(
							'type'        => 'string',
							'enum'        => array( 'title', 'description', 'any', 'both' ),
							'description' => __( 'Which gap to look for: "title", "description", "any" of the two (default) or "both" at once.', 'wp-seopress' ),
						),
					)
				),
				'output_schema'       => $this->listingOutputSchema(),
				'permission_callback' => array( $this, 'canListPosts' ),
				'execute_callback'    => function ( $input ) {
					$missing = isset( $input['missing'] ) ? (string) $input['missing'] : 'any';

					$result = $this->queryPosts( $input, array(), $missing );
					$items  = array();

					foreach ( $result['posts'] as $post ) {
						$items[] = $this->postPayload( $post );
					}

					return $this->listingResponse( $result, $items );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
				),
			)
		);

		// 10. List posts together with their stored content analysis score.
		wp_register_ability(
			'seopress/list-posts-by-content-score',
			array(
				'label'               => __( 'List posts with their content analysis score', 'wp-seopress' ),
				'description'         => __( 'List posts with the verdict of their last SEOPress content analysis: "good", "needs_improvement", or "not_analyzed" when the analysis has never run on that post. The score is read from the stored analysis, no post is re-analyzed, and only posts the current user may read are returned.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->listingInputSchema(),
				'output_schema'       => $this->listingOutputSchema(
					array(
						'score'   => array(
							'type'        => 'string',
							'enum'        => array( 'good', 'needs_improvement', 'not_analyzed' ),
							'description' => __( 'The verdict of the last content analysis of this post.', 'wp-seopress' ),
						),
						'impacts' => array(
							'type'        => 'array',
							'items'       => array( 'type' => 'string' ),
							'description' => __( 'The distinct impact levels recorded by the last analysis (empty when never analyzed).', 'wp-seopress' ),
						),
					)
				),
				'permission_callback' => array( $this, 'canListPosts' ),
				'execute_callback'    => function ( $input ) {
					$result = $this->queryPosts( $input );

					$post_ids = array();
					foreach ( $result['posts'] as $post ) {
						$post_ids[] = (int) $post->ID;
					}

					$scores = seopress_get_service( 'ContentAnalysisRepository' )->getScoresForPostIds( $post_ids );
					$items  = array();

					foreach ( $result['posts'] as $post ) {
						$raw     = isset( $scores[ (int) $post->ID ] ) ? $scores[ (int) $post->ID ] : null;
						$impacts = is_array( $raw ) ? array_values( array_map( 'strval', $raw ) ) : array();

						$items[] = array_merge(
							$this->postPayload( $post ),
							array(
								'score'   => $this->scoreBucket( $raw ),
								'impacts' => $impacts,
							)
						);
					}

					return $this->listingResponse( $result, $items );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
				),
			)
		);

		// 11. Find the posts that are explicitly excluded from search engines.
		wp_register_ability(
			'seopress/list-posts-noindexed',
			array(
				'label'               => __( 'List posts excluded from search engines', 'wp-seopress' ),
				'description'         => __( 'List the posts whose SEOPress robots settings carry an explicit noindex directive, so pages hidden from search engines by mistake can be found site-wide. Only posts the current user may read are returned.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->listingInputSchema(),
				'output_schema'       => $this->listingOutputSchema(),
				'permission_callback' => array( $this, 'canListPosts' ),
				'execute_callback'    => function ( $input ) {
					$result = $this->queryPosts(
						$input,
						array(
							array(
								'key'     => '_seopress_robots_index',
								'value'   => 'yes',
								'compare' => '=',
							),
						)
					);

					$items = array();
					foreach ( $result['posts'] as $post ) {
						$items[] = $this->postPayload( $post );
					}

					return $this->listingResponse( $result, $items );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
				),
			)
		);

		// 12. Read the XML / HTML sitemap settings.
		wp_register_ability(
			'seopress/get-sitemap-settings',
			array(
				'label'               => __( 'Get sitemap settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the XML and HTML sitemap configuration: whether the sitemaps are enabled, which post types and taxonomies are included, and the related display options.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->emptyInputSchema(),
				'output_schema'       => array(
					'type'        => 'object',
					'description' => __( 'The seopress_xml_sitemap_option_name option contents.', 'wp-seopress' ),
				),
				'permission_callback' => function () {
					return ( new SitemapsSettings() )->permissionCheck( new \WP_REST_Request() );
				},
				'execute_callback'    => function () {
					return $this->runController( new SitemapsSettings(), 'processGet', 'GET' );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
				),
			)
		);

		// 13. Read the Instant Indexing settings, credentials excluded.
		wp_register_ability(
			'seopress/get-instant-indexing-settings',
			array(
				'label'               => __( 'Get Instant Indexing settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the Instant Indexing (IndexNow and Google Indexing API) configuration and the submission log. API keys are never returned: only whether each credential is configured.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->emptyInputSchema(),
				'output_schema'       => array(
					'type'        => 'object',
					'description' => __( 'The Instant Indexing settings and log, with every credential replaced by a boolean "configured" flag.', 'wp-seopress' ),
				),
				'permission_callback' => function () {
					return ( new IndexingSettings() )->permissionCheck( new \WP_REST_Request() );
				},
				'execute_callback'    => function () {
					$data = $this->runController( new IndexingSettings(), 'processGet', 'GET' );

					if ( is_wp_error( $data ) ) {
						return $data;
					}

					return $this->redactIndexingCredentials( $data );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
				),
			)
		);

		// 14. Read the global social settings, including the Knowledge Graph.
		wp_register_ability(
			'seopress/get-global-social-settings',
			array(
				'label'               => __( 'Get global social and Knowledge Graph settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the site-wide social settings: the Knowledge Graph site identity (person or organization, name, logo, social profiles) and the default Open Graph and X/Twitter card configuration.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->emptyInputSchema(),
				'output_schema'       => array(
					'type'        => 'object',
					'description' => __( 'The seopress_social_option_name option contents.', 'wp-seopress' ),
				),
				'permission_callback' => function () {
					return ( new GlobalSocialSettings() )->permissionCheck( new \WP_REST_Request() );
				},
				'execute_callback'    => function () {
					return $this->runController( new GlobalSocialSettings(), 'processGet', 'GET' );
				},
				'meta'                => seopress_abilities_api_meta(
					array(
						'readonly'   => true,
						'idempotent' => true,
					)
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
	 * @param int|null $post_id  The post ID, or null for a controller that takes none.
	 * @param array  $params     Extra request parameters.
	 *
	 * @return mixed|\WP_Error
	 */
	protected function runController( $controller, $method, $http_method, $post_id = null, $params = array() ) {
		$request = new \WP_REST_Request( $http_method );

		// Option controllers act on the whole site, so they take no post id.
		if ( null !== $post_id ) {
			$request->set_param( 'id', $post_id );
		}

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
	 * Permission callback for the site-wide listing abilities.
	 *
	 * These abilities answer questions about the site's content, so the gate
	 * is the capability that says "this user works with content". It is not
	 * manage_options: an editor must be able to ask which posts are missing a
	 * meta description. Each row is then checked individually in queryPosts(),
	 * so a contributor never sees another author's unpublished work.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public function canListPosts() {
		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Shared input schema of the site-wide listing abilities.
	 *
	 * Response sizes and page offsets are bounded, and unknown properties are
	 * rejected. Computing an exact total can still examine all matching posts.
	 *
	 * @since 10.3.0
	 *
	 * @param array $extra Additional properties merged into the schema.
	 *
	 * @return array
	 */
	protected function listingInputSchema( $extra = array() ) {
		$properties = array(
			'post_type' => array(
				'type'        => 'array',
				'items'       => array( 'type' => 'string' ),
				'maxItems'    => 20,
				'uniqueItems' => true,
				'description' => __( 'Post types to scan. Defaults to every public post type. Unknown or non-public post types are ignored.', 'wp-seopress' ),
			),
			'per_page'  => array(
				'type'        => 'integer',
				'minimum'     => 1,
				'maximum'     => self::LISTING_MAX_PER_PAGE,
				'description' => __( 'How many posts to return per page. Defaults to 20.', 'wp-seopress' ),
			),
			'page'      => array(
				'type'        => 'integer',
				'minimum'     => 1,
				'maximum'     => self::LISTING_MAX_PAGE,
				'description' => __( 'Which page of results to return. Defaults to 1.', 'wp-seopress' ),
			),
		);

		return array(
			'type'                 => 'object',
			'properties'           => array_merge( $properties, $extra ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Shared output schema of the site-wide listing abilities.
	 *
	 * @since 10.3.0
	 *
	 * @param array $item_extra Additional per-item properties.
	 *
	 * @return array
	 */
	protected function listingOutputSchema( $item_extra = array() ) {
		$item_properties = array(
			'id'               => array(
				'type'        => 'integer',
				'description' => __( 'The post ID.', 'wp-seopress' ),
			),
			'title'            => array(
				'type'        => 'string',
				'description' => __( 'The post title.', 'wp-seopress' ),
			),
			'post_type'        => array( 'type' => 'string' ),
			'status'           => array( 'type' => 'string' ),
			'permalink'        => array( 'type' => 'string' ),
			'seo_title'        => array(
				'type'        => 'string',
				'description' => __( 'The custom SEO title, empty when the global template applies.', 'wp-seopress' ),
			),
			'meta_description' => array(
				'type'        => 'string',
				'description' => __( 'The custom meta description, empty when the global template applies.', 'wp-seopress' ),
			),
		);

		return array(
			'type'       => 'object',
			'properties' => array(
				'items'       => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array_merge( $item_properties, $item_extra ),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'How many posts match across every page, counting only the statuses this user may read.', 'wp-seopress' ),
				),
				'total_pages' => array( 'type' => 'integer' ),
				'page'        => array( 'type' => 'integer' ),
				'per_page'    => array( 'type' => 'integer' ),
			),
		);
	}

	/**
	 * The input schema of an ability that takes no input.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected function emptyInputSchema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(),
			'additionalProperties' => false,
		);
	}

	/**
	 * The post statuses the current user may read for one post type.
	 *
	 * WP_Query's own `perm => readable` cannot be used here: reading core
	 * shows it only ever restricts the `private` status, never a draft or a
	 * pending post, so a contributor would still be counted in on another
	 * author's unpublished work. Scoping the statuses by capability instead
	 * keeps the paging counts exact, because every row the query returns is a
	 * row this user may read.
	 *
	 * The trade-off is deliberate: a user who cannot edit other people's posts
	 * sees only published ones; their own drafts remain excluded, preserving
	 * the listing's existing policy. Statuses are applied per type in SQL,
	 * before pagination and counting.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected function readableStatuses( $post_type ) {
		if ( ! $post_type->map_meta_cap ) {
			return current_user_can( $post_type->cap->read_post )
				? array( 'publish', 'future', 'draft', 'pending', 'private' )
				: array();
		}
		$statuses = current_user_can( $post_type->cap->read ) ? array( 'publish' ) : array();

		if ( current_user_can( $post_type->cap->edit_others_posts ) ) {
			if ( current_user_can( $post_type->cap->edit_published_posts ) ) {
				$statuses[] = 'future';
			}
			$statuses[] = 'draft';
			$statuses[] = 'pending';
		}

		if ( current_user_can( $post_type->cap->read_private_posts ) ) {
			$statuses[] = 'private';
		}

		return $statuses;
	}

	/**
	 * Run the query shared by every site-wide listing ability.
	 *
	 * @since 10.3.0
	 *
	 * @param array $input      The ability input.
	 * @param array $meta_query An optional WP_Meta_Query definition.
	 *
	 * @return array { posts, total, total_pages, page, per_page }
	 */
	protected function queryPosts( $input, $meta_query = array(), $missing = null ) {
		global $wpdb;
		$post_types = $this->resolvePostTypes( $input );
		$status_clauses = array();
		foreach ( $post_types as $name ) {
			$post_type = get_post_type_object( $name );
			if ( ! $post_type ) {
				continue;
			}
			$statuses = $this->readableStatuses( $post_type );
			if ( empty( $statuses ) ) {
				continue;
			}
			$placeholders = implode( ', ', array_fill( 0, count( $statuses ), '%s' ) );
			$status_clauses[] = $wpdb->prepare(
				"({$wpdb->posts}.post_type = %s AND {$wpdb->posts}.post_status IN ($placeholders))",
				array_merge( array( $name ), $statuses )
			);
		}
		$where = empty( $status_clauses ) ? ' AND 1=0' : ' AND (' . implode( ' OR ', $status_clauses ) . ')';
		if ( null !== $missing ) {
			$where .= ' AND ' . $this->missingMetadataQuery( $missing );
		}
		$per_page = isset( $input['per_page'] ) ? (int) $input['per_page'] : 20;
		$per_page = max( 1, min( self::LISTING_MAX_PER_PAGE, $per_page ) );

		$page = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$page = max( 1, min( self::LISTING_MAX_PAGE, $page ) );

		$args = array(
			'post_type'              => $post_types,
			'post_status'            => array( 'publish', 'future', 'draft', 'pending', 'private' ),
			'posts_per_page'         => $per_page,
			'paged'                  => $page,
			'orderby'                => array( 'date' => 'DESC', 'ID' => 'DESC' ),
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
		);

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- The noindex listing filters one exact metadata key and value.
		}

		// Scope the predicate to this exact query and remove it even if a hook throws.
		// Filtering before LIMIT and FOUND_ROWS keeps totals and pages consistent.
		$query = new \WP_Query();
		$filter = function ( $sql, $candidate ) use ( $query, $where ) {
			return $candidate === $query ? $sql . $where : $sql;
		};
		add_filter( 'posts_where', $filter, 10, 2 );
		try {
			$query->query( $args );
		} finally {
			remove_filter( 'posts_where', $filter, 10 );
		}

		$posts = array();
		foreach ( $query->posts as $post ) {
			// Second gate. An ability is a remote control handed to an AI
			// client, so a row is only returned when the current user may
			// actually read that post.
			if ( ! current_user_can( 'read_post', $post->ID ) ) {
				continue;
			}

			$posts[] = $post;
		}

		return array(
			'posts'       => $posts,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => $page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Turn a queryPosts() result and its formatted rows into the ability output.
	 *
	 * @since 10.3.0
	 *
	 * @param array $result The queryPosts() result.
	 * @param array $items  The formatted rows.
	 *
	 * @return array
	 */
	protected function listingResponse( $result, $items ) {
		return array(
			'items'       => $items,
			'total'       => $result['total'],
			'total_pages' => $result['total_pages'],
			'page'        => $result['page'],
			'per_page'    => $result['per_page'],
		);
	}

	/**
	 * Resolve the requested post types against the public ones.
	 *
	 * @since 10.3.0
	 *
	 * @param array $input The ability input.
	 *
	 * @return array
	 */
	protected function resolvePostTypes( $input ) {
		$available = array_values( get_post_types( array( 'public' => true ), 'names' ) );

		if ( empty( $input['post_type'] ) || ! is_array( $input['post_type'] ) ) {
			return $available;
		}

		$requested = array_intersect( array_map( 'sanitize_key', $input['post_type'] ), $available );

		// A list of nothing but unknown post types must not silently widen
		// back to the whole site: query a post type that matches nothing.
		return empty( $requested ) ? array( self::NO_SUCH_POST_TYPE ) : array_values( $requested );
	}

	/**
	 * Format one post for a listing ability.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function postPayload( $post ) {
		return array(
			'id'               => (int) $post->ID,
			'title'            => $this->postTitle( $post ),
			'post_type'        => (string) $post->post_type,
			'status'           => (string) $post->post_status,
			'permalink'        => (string) get_permalink( $post ),
			'seo_title'        => (string) get_post_meta( $post->ID, '_seopress_titles_title', true ),
			'meta_description' => (string) get_post_meta( $post->ID, '_seopress_titles_desc', true ),
		);
	}

	/**
	 * Add the identity of the post a reading ability just answered about.
	 *
	 * Fields named for SEO alone leave the caller holding two strings and no
	 * idea which post they belong to, so an agent has to call again to show a
	 * result or decide anything. The post's own title is not the SEO title, so
	 * both are named for what they are.
	 *
	 * @since 10.3.0
	 *
	 * @param array $payload The ability payload so far.
	 * @param int   $post_id The post that was read.
	 *
	 * @return array
	 */
	protected function withPostIdentity( $payload, $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post || ! is_array( $payload ) ) {
			return $payload;
		}

		return array_merge(
			$payload,
			array(
				'post_id'    => (int) $post->ID,
				'post_title' => $this->postTitle( $post ),
				'post_type'  => (string) $post->post_type,
				'status'     => (string) $post->post_status,
			)
		);
	}

	/**
	 * The title of a post as its author typed it.
	 *
	 * get_the_title() is a front end function: it runs the title through
	 * wptexturize, which encodes quotes and ampersands as HTML entities, and
	 * it wraps a private or password protected title in the site's
	 * "Private: %s" format. An agent reading a listing needs the characters
	 * behind both, so take the stored column and only undo an encoding the
	 * editor may have left in it.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Post $post The post.
	 *
	 * @return string
	 */
	protected function postTitle( $post ) {
		return (string) wp_specialchars_decode( (string) $post->post_title, ENT_QUOTES );
	}

	/**
	 * Build the meta query that finds a missing SEO title and/or description.
	 *
	 * A value is "missing" when the meta row does not exist at all, which is
	 * what the title/description controller leaves behind when a field is
	 * cleared, or when it exists but is empty.
	 *
	 * @since 10.3.0
	 *
	 * @param string $missing One of title, description, any, both.
	 *
	 * @return string Prepared SQL predicate.
	 */
	protected function missingMetadataQuery( $missing ) {
		global $wpdb;
		$empty = function ( $key ) use ( $wpdb ) {
			// Correlated existence checks stop at the first matching metadata row.
			// Unlike unrestricted joins, unrelated metadata cannot multiply rows.
			return $wpdb->prepare(
				"(NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} seopress_missing WHERE seopress_missing.post_id = {$wpdb->posts}.ID AND seopress_missing.meta_key = %s)
				OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} seopress_empty WHERE seopress_empty.post_id = {$wpdb->posts}.ID AND seopress_empty.meta_key = %s AND seopress_empty.meta_value = %s))",
				$key, $key, ''
			);
		};
		if ( 'title' === $missing ) {
			return $empty( '_seopress_titles_title' );
		}
		if ( 'description' === $missing ) {
			return $empty( '_seopress_titles_desc' );
		}
		return '(' . $empty( '_seopress_titles_title' ) . ( 'both' === $missing ? ' AND ' : ' OR ' ) . $empty( '_seopress_titles_desc' ) . ')';
	}

	/**
	 * Turn a stored analysis score into a single verdict.
	 *
	 * The analysis stores the distinct impact levels of its checks. The admin
	 * posts column calls a post good only when no check reported a medium or
	 * high impact, and this reports the same verdict rather than a second one.
	 *
	 * @since 10.3.0
	 *
	 * @param mixed $score The stored score.
	 *
	 * @return string good|needs_improvement|not_analyzed
	 */
	protected function scoreBucket( $score ) {
		if ( ! is_array( $score ) || empty( $score ) ) {
			return 'not_analyzed';
		}

		if ( in_array( 'medium', $score, true ) || in_array( 'high', $score, true ) ) {
			return 'needs_improvement';
		}

		return 'good';
	}

	/**
	 * Replace every Instant Indexing credential by a "configured" flag.
	 *
	 * The option group holds a Google service account private key and the
	 * IndexNow key. These abilities are exposed to MCP clients, so the values
	 * never leave the site: only whether they are set.
	 *
	 * @since 10.3.0
	 *
	 * @param mixed $data The controller payload.
	 *
	 * @return array
	 */
	protected function redactIndexingCredentials( $data ) {
		if ( ! is_array( $data ) ) {
			return array();
		}

		$credentials = array(
			'seopress_instant_indexing_google_api_key' => 'google_api_key_configured',
			'seopress_instant_indexing_api_key_txt'    => 'indexnow_api_key_configured',
			'seopress_instant_indexing_bing_api_key'   => 'indexnow_api_key_configured',
		);

		foreach ( $credentials as $key => $flag ) {
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}

			$configured = '' !== trim( (string) $data[ $key ] );

			unset( $data[ $key ] );

			// Two option keys feed the same IndexNow flag: either one being
			// set must leave the flag true.
			$data[ $flag ] = ! empty( $data[ $flag ] ) || $configured;
		}

		return $data;
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
