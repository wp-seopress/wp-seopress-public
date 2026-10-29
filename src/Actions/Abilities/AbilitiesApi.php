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
use SEOPress\Helpers\Metas\SocialSettings as SocialSettingsHelper;
use SEOPress\Helpers\Metas\TermRobotSettings;

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
				'permission_callback' => array( $this, 'canWritePost' ),
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
				'permission_callback' => array( $this, 'canWritePost' ),
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
				'permission_callback' => array( $this, 'canWritePost' ),
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
						'score'         => array(
							'type'        => 'string',
							'enum'        => array( 'good', 'needs_improvement', 'not_analyzed' ),
							'description' => __( 'The verdict of the last content analysis of this post.', 'wp-seopress' ),
						),
						'impacts'       => array(
							'type'        => 'array',
							'items'       => array( 'type' => 'string' ),
							'description' => __( 'The distinct impact levels recorded by the last analysis, ignored checks left out (empty when never analyzed).', 'wp-seopress' ),
						),
						'checks_passed' => array(
							'type'        => array( 'integer', 'null' ),
							'description' => __( 'How many checks of the last analysis the post passes, or null when the stored analysis predates the per-check detail.', 'wp-seopress' ),
						),
						'checks_total'  => array(
							'type'        => array( 'integer', 'null' ),
							'description' => __( 'How many checks the last analysis ran, ignored ones left out, or null when the stored analysis predates the per-check detail.', 'wp-seopress' ),
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

					// One query for the ignored checks of the whole page.
					$ignored_by_post = \SEOPress\Helpers\ContentAnalysis::getIgnoredChecksForPostIds( $post_ids );

					foreach ( $result['posts'] as $post ) {
						$raw     = isset( $scores[ (int) $post->ID ] ) ? $scores[ (int) $post->ID ] : null;
						$ignored = isset( $ignored_by_post[ (int) $post->ID ] ) ? $ignored_by_post[ (int) $post->ID ] : array();

						// An ignored check counts nowhere else, so its level is
						// not reported either: `score: "good"` next to "high"
						// would contradict itself.
						$counted = is_array( $raw ) ? array_diff_key( $raw, array_flip( $ignored ) ) : null;

						// The stored score is one impact per check now, so the
						// schema's "distinct impact levels" has to be produced
						// here: without the dedup a caller got seventeen values
						// with repeats, and "" for a third-party check that
						// reported no impact or was not evaluated.
						$impacts = is_array( $counted )
							? array_values( array_filter( array_unique( array_map( 'strval', $counted ) ), 'strlen' ) )
							: array();

						$summary = is_array( $counted )
							? \SEOPress\Helpers\ContentAnalysis::getScoreSummary( $counted )
							: array( 'total' => 0 );

						// Every check ignored: nothing left to fail, as in the editor.
						$verdict = is_array( $raw ) && ! empty( $raw ) && empty( $counted )
							? 'good'
							: $this->scoreBucket( $counted, $summary );

						$items[] = array_merge(
							$this->postPayload( $post ),
							array(
								'score'         => $verdict,
								'impacts'       => $impacts,
								'checks_passed' => ! empty( $summary['total'] ) ? (int) $summary['passed'] : null,
								'checks_total'  => ! empty( $summary['total'] ) ? (int) $summary['total'] : null,
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

		$this->registerTermAbilities();
	}

	/**
	 * Register the abilities that act on taxonomy terms.
	 *
	 * The term counterparts of the post title & description abilities, for
	 * product categories, categories, tags and custom taxonomies. They read and
	 * write the same term metas as the term SEO metabox.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	protected function registerTermAbilities() {
		$term_id_schema = array(
			'type'                 => 'object',
			'properties'           => array(
				'term_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The ID of the term (category, tag, product category or custom taxonomy term).', 'wp-seopress' ),
				),
			),
			'required'             => array( 'term_id' ),
			'additionalProperties' => false,
		);

		// 15. Read the SEO title and meta description of a term.
		wp_register_ability(
			'seopress/get-term-title-description',
			array(
				'label'               => __( 'Get term SEO title and meta description', 'wp-seopress' ),
				'description'         => __( 'Retrieve the custom SEO title and meta description set for a taxonomy term, such as a product category, a category or a tag.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $term_id_schema,
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
						'term_id'     => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the term that was read.', 'wp-seopress' ),
						),
						'term_name'   => array(
							'type'        => 'string',
							'description' => __( 'The name of the term itself, which is not the SEO title.', 'wp-seopress' ),
						),
						'taxonomy'    => array(
							'type'        => 'string',
							'description' => __( 'The taxonomy of the term that was read.', 'wp-seopress' ),
						),
					),
				),
				'permission_callback' => array( $this, 'canEditTerm' ),
				'execute_callback'    => function ( $input ) {
					$term_id = (int) $input['term_id'];

					$title       = (string) get_term_meta( $term_id, '_seopress_titles_title', true );
					$description = (string) get_term_meta( $term_id, '_seopress_titles_desc', true );

					return $this->withTermIdentity(
						array(
							'title'       => html_entity_decode( $title, ENT_QUOTES | ENT_XML1, 'UTF-8' ),
							'description' => html_entity_decode( $description, ENT_QUOTES | ENT_XML1, 'UTF-8' ),
						),
						$term_id
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

		// 16. Update the SEO title and/or meta description of a term.
		wp_register_ability(
			'seopress/update-term-title-description',
			array(
				'label'               => __( 'Update term SEO title and meta description', 'wp-seopress' ),
				'description'         => __( 'Set or clear the custom SEO title and/or meta description of a taxonomy term, such as a product category, a category or a tag. An empty string clears the value and falls back to the global template.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'term_id'     => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'description' => __( 'The ID of the term to update.', 'wp-seopress' ),
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
					'required'             => array( 'term_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => $this->codeOutputSchema(),
				'permission_callback' => array( $this, 'canWriteTerm' ),
				'execute_callback'    => function ( $input ) {
					$term_id = (int) $input['term_id'];

					// The keys and sanitizers of the term metabox, see
					// seopress_tax_save_term(): a value written here reads back
					// in the metabox exactly as if it had been typed there.
					$fields = array(
						'title'       => '_seopress_titles_title',
						'description' => '_seopress_titles_desc',
					);

					foreach ( $fields as $key => $meta_key ) {
						if ( ! array_key_exists( $key, $input ) ) {
							continue;
						}

						$value = 'title' === $key
							? sanitize_text_field( (string) $input[ $key ] )
							: sanitize_textarea_field( (string) $input[ $key ] );

						if ( '' === $value ) {
							delete_term_meta( $term_id, $meta_key );
						} else {
							// Ability input is unslashed; metadata storage removes one slash layer.
							update_term_meta( $term_id, $meta_key, wp_slash( $value ) );
						}
					}

					return array( 'code' => 'success' );
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

		// 17. Find terms whose SEO title and/or meta description is missing.
		wp_register_ability(
			'seopress/list-terms-missing-metadata',
			array(
				'label'               => __( 'List terms missing SEO metadata', 'wp-seopress' ),
				'description'         => __( 'List the taxonomy terms (product categories, categories, tags, custom taxonomies) that have no custom SEO title and/or no custom meta description, so the gaps can be found without opening each term. Only terms of the taxonomies the current user may manage are returned.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'taxonomy' => array(
							'type'        => 'array',
							'items'       => array( 'type' => 'string' ),
							'maxItems'    => 20,
							'uniqueItems' => true,
							'description' => __( 'Taxonomies to scan, e.g. ["product_cat"]. Defaults to every taxonomy with an SEO metabox. Unknown or unsupported taxonomies are ignored.', 'wp-seopress' ),
						),
						'missing'  => array(
							'type'        => 'string',
							'enum'        => array( 'title', 'description', 'any', 'both' ),
							'description' => __( 'Which gap to look for: "title", "description", "any" of the two (default) or "both" at once.', 'wp-seopress' ),
						),
						'per_page' => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => self::LISTING_MAX_PER_PAGE,
							'description' => __( 'How many terms to return per page. Defaults to 20.', 'wp-seopress' ),
						),
						'page'     => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => self::LISTING_MAX_PAGE,
							'description' => __( 'Which page of results to return. Defaults to 1.', 'wp-seopress' ),
						),
					),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'items'       => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'id'               => array(
										'type'        => 'integer',
										'description' => __( 'The term ID.', 'wp-seopress' ),
									),
									'name'             => array(
										'type'        => 'string',
										'description' => __( 'The term name.', 'wp-seopress' ),
									),
									'taxonomy'         => array( 'type' => 'string' ),
									'slug'             => array( 'type' => 'string' ),
									'count'            => array(
										'type'        => 'integer',
										'description' => __( 'How many published objects the term is assigned to.', 'wp-seopress' ),
									),
									'permalink'        => array( 'type' => 'string' ),
									'seo_title'        => array(
										'type'        => 'string',
										'description' => __( 'The custom SEO title, empty when the global template applies.', 'wp-seopress' ),
									),
									'meta_description' => array(
										'type'        => 'string',
										'description' => __( 'The custom meta description, empty when the global template applies.', 'wp-seopress' ),
									),
								),
							),
						),
						'total'       => array(
							'type'        => 'integer',
							'description' => __( 'How many terms match across every page.', 'wp-seopress' ),
						),
						'total_pages' => array( 'type' => 'integer' ),
						'page'        => array( 'type' => 'integer' ),
						'per_page'    => array( 'type' => 'integer' ),
					),
				),
				'permission_callback' => array( $this, 'canListTerms' ),
				'execute_callback'    => function ( $input ) {
					$missing = isset( $input['missing'] ) ? (string) $input['missing'] : 'any';

					$result = $this->queryTerms( $input, $missing );
					$items  = array();

					foreach ( $result['terms'] as $term ) {
						$items[] = $this->termPayload( $term );
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

		$checkbox_schema = array(
			'type' => array( 'string', 'boolean' ),
			'enum' => array( 'yes', '', true, false ),
		);

		// 18. Read the robots directives of a term.
		wp_register_ability(
			'seopress/get-term-robots-settings',
			array(
				'label'               => __( 'Get term robots settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the robots/indexing directives of a taxonomy term (noindex, nofollow, nosnippet, noimageindex, canonical URL). A directive forced on by the global settings is reported with can_modify false and the setting that holds it.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $term_id_schema,
				'output_schema'       => $this->termSettingsOutputSchema( __( 'The robots settings of the term as { key, value } entries.', 'wp-seopress' ) ),
				'permission_callback' => array( $this, 'canEditTerm' ),
				'execute_callback'    => function ( $input ) {
					$term_id = (int) $input['term_id'];

					return $this->withTermIdentity(
						array( 'settings' => $this->readTermSettings( $term_id, TermRobotSettings::getMetaKeys( get_term( $term_id ) ) ) ),
						$term_id
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

		// 19. Update the robots directives of a term.
		wp_register_ability(
			'seopress/update-term-robots-settings',
			array(
				'label'               => __( 'Update term robots settings', 'wp-seopress' ),
				'description'         => __( 'Update the robots/indexing directives of a taxonomy term. A checkbox takes "yes" (or true) to set it and "" (or false) to clear it. A directive forced on by the global settings is not written and is returned in "skipped".', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->termSettingsInputSchema(
					__( 'Map of robots meta keys to values, e.g. { "_seopress_robots_index": "yes", "_seopress_robots_canonical": "https://example.com/" }. Only the keys listed here are accepted.', 'wp-seopress' ),
					array(
						'_seopress_robots_index'      => $checkbox_schema,
						'_seopress_robots_follow'     => $checkbox_schema,
						'_seopress_robots_snippet'    => $checkbox_schema,
						'_seopress_robots_imageindex' => $checkbox_schema,
						'_seopress_robots_canonical'  => array(
							'type'        => 'string',
							'description' => __( 'An absolute http(s) URL, or an empty string to remove it.', 'wp-seopress' ),
						),
					)
				),
				'output_schema'       => $this->termUpdateOutputSchema(),
				'permission_callback' => array( $this, 'canWriteTerm' ),
				'execute_callback'    => function ( $input ) {
					$term_id  = (int) $input['term_id'];
					$settings = is_array( $input['settings'] ) ? $input['settings'] : array();
					$skipped  = array();
					$writes   = array();

					foreach ( TermRobotSettings::getMetaKeys( get_term( $term_id ) ) as $field ) {
						$key = $field['key'];
						if ( ! array_key_exists( $key, $settings ) ) {
							continue;
						}

						// The metabox disables these checkboxes: the global
						// setting wins whatever the term meta says.
						if ( $field['use_default'] ) {
							$skipped[] = array(
								'key'    => $key,
								'reason' => $field['locked_reason'],
							);
							continue;
						}

						if ( 'checkbox' === $field['type'] ) {
							$writes[ $key ] = in_array( $settings[ $key ], array( 'yes', true ), true ) ? 'yes' : '';
							continue;
						}

						$url = $this->termSettingUrl( $settings[ $key ], $key );
						if ( is_wp_error( $url ) ) {
							return $url;
						}
						$writes[ $key ] = $url;
					}

					$this->writeTermMetas( $term_id, $writes );

					return array(
						'code'    => 'success',
						'skipped' => $skipped,
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

		// 20. Read the social metadata of a term.
		wp_register_ability(
			'seopress/get-term-social-settings',
			array(
				'label'               => __( 'Get term social settings', 'wp-seopress' ),
				'description'         => __( 'Retrieve the social (Facebook/Open Graph and X/Twitter) title, description and image of a taxonomy term. Each image comes with its media library attachment ID and its size when it is one.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $term_id_schema,
				'output_schema'       => $this->termSettingsOutputSchema( __( 'The social settings of the term as { key, value } entries.', 'wp-seopress' ) ),
				'permission_callback' => array( $this, 'canEditTerm' ),
				'execute_callback'    => function ( $input ) {
					$term_id = (int) $input['term_id'];

					return $this->withTermIdentity(
						array( 'settings' => $this->readTermSettings( $term_id, $this->termSocialFields() ) ),
						$term_id
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

		$social_properties = array();
		foreach ( array( 'fb', 'twitter' ) as $network ) {
			$prefix = '_seopress_social_' . $network;

			$social_properties[ $prefix . '_title' ]             = array( 'type' => 'string' );
			$social_properties[ $prefix . '_desc' ]              = array( 'type' => 'string' );
			$social_properties[ $prefix . '_img' ]               = array(
				'type'        => 'string',
				'description' => __( 'Image URL (absolute, http or https). When it is a media library image, its attachment ID and size are filled in from it. An empty string removes the image.', 'wp-seopress' ),
			);
			$social_properties[ $prefix . '_img_attachment_id' ] = array(
				'type'        => 'integer',
				'minimum'     => 0,
				'description' => __( 'Media library image to use: its URL and size are taken from it, and it wins over an image URL passed alongside. 0, with no image URL, removes the image.', 'wp-seopress' ),
			);
		}

		// 21. Update the social metadata of a term.
		wp_register_ability(
			'seopress/update-term-social-settings',
			array(
				'label'               => __( 'Update term social settings', 'wp-seopress' ),
				'description'         => __( 'Update the social (Facebook/Open Graph and X/Twitter) title, description and image of a taxonomy term. Set an image by media library attachment ID or by URL: the attachment ID, width and height are always worked out from the image, never written on their own. An empty string clears a value.', 'wp-seopress' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->termSettingsInputSchema(
					__( 'Map of social meta keys to values, e.g. { "_seopress_social_fb_title": "...", "_seopress_social_fb_img_attachment_id": 123 }. Only the keys listed here are accepted.', 'wp-seopress' ),
					$social_properties
				),
				'output_schema'       => $this->termUpdateOutputSchema(),
				'permission_callback' => array( $this, 'canWriteTerm' ),
				'execute_callback'    => function ( $input ) {
					$term_id  = (int) $input['term_id'];
					$settings = is_array( $input['settings'] ) ? $input['settings'] : array();
					$writes   = array();

					foreach ( array( 'fb', 'twitter' ) as $network ) {
						$prefix = '_seopress_social_' . $network;

						// The sanitizers of seopress_tax_save_term().
						if ( array_key_exists( $prefix . '_title', $settings ) ) {
							$writes[ $prefix . '_title' ] = sanitize_text_field( (string) $settings[ $prefix . '_title' ] );
						}
						if ( array_key_exists( $prefix . '_desc', $settings ) ) {
							$writes[ $prefix . '_desc' ] = sanitize_textarea_field( (string) $settings[ $prefix . '_desc' ] );
						}

						$image = $this->termSocialImage( $settings, $prefix . '_img' );
						if ( is_wp_error( $image ) ) {
							return $image;
						}
						$writes = array_merge( $writes, $image );
					}

					$this->writeTermMetas( $term_id, $writes );

					return array(
						'code'    => 'success',
						'skipped' => array(),
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
	}

	/**
	 * The social fields of the term metabox.
	 *
	 * The same twelve metas as on a post, see seopress_tax_save_term(), without
	 * the `seopress_api_meta_social_settings` filter, which is given a post ID.
	 *
	 * @since 10.4.0
	 *
	 * @return array[]
	 */
	protected function termSocialFields() {
		$fields = array_merge( SocialSettingsHelper::getMetaKeysFacebook(), SocialSettingsHelper::getMetaKeysTwitter() );

		foreach ( $fields as $index => $field ) {
			$is_size = 'hidden' === $field['type'] && '_attachment_id' !== substr( $field['key'], -14 );

			$fields[ $index ] = array_merge(
				array(
					'label'       => '',
					'use_default' => false,
				),
				$field,
				array(
					// Width and height are read from the image, never set.
					'locked_reason' => $is_size ? __( 'Read from the image: set the image or its attachment ID to change it.', 'wp-seopress' ) : '',
				)
			);
		}

		return $fields;
	}

	/**
	 * The current values of a term's fields, in the shape the post abilities use.
	 *
	 * @since 10.4.0
	 *
	 * @param int     $term_id The term.
	 * @param array[] $fields  From TermRobotSettings::getMetaKeys() or termSocialFields().
	 *
	 * @return array[]
	 */
	protected function readTermSettings( $term_id, $fields ) {
		$settings = array();

		foreach ( $fields as $field ) {
			if ( $field['use_default'] ) {
				$value = $field['default'];
			} else {
				$value = (string) get_term_meta( $term_id, $field['key'], true );

				if ( 'checkbox' === $field['type'] ) {
					$value = 'yes' === $value ? 'yes' : '';
				}
			}

			$entry = array(
				'key'        => $field['key'],
				'type'       => $field['type'],
				'label'      => (string) $field['label'],
				'value'      => $value,
				'can_modify' => '' === $field['locked_reason'],
			);

			if ( '' !== $field['locked_reason'] ) {
				$entry['locked_reason'] = $field['locked_reason'];
			}

			if ( ! empty( $field['description'] ) ) {
				$entry['description'] = $field['description'];
			}

			$settings[] = $entry;
		}

		return $settings;
	}

	/**
	 * Write term metas, an empty value deleting the meta as the metabox does.
	 *
	 * Called once every value has been checked, so a call refused over one
	 * value leaves the term as it was.
	 *
	 * @since 10.4.0
	 *
	 * @param int   $term_id The term.
	 * @param array $writes  Meta key => sanitized value.
	 *
	 * @return void
	 */
	protected function writeTermMetas( $term_id, $writes ) {
		foreach ( $writes as $meta_key => $value ) {
			if ( '' === (string) $value ) {
				delete_term_meta( $term_id, $meta_key );
			} else {
				// Ability input is unslashed; metadata storage removes one slash layer.
				update_term_meta( $term_id, $meta_key, wp_slash( (string) $value ) );
			}
		}
	}

	/**
	 * Sanitize a URL setting of a term.
	 *
	 * Uses sanitize_url() as the metabox does, kept to http(s). A value it reduces to
	 * nothing is refused: in the metabox an empty field removes the meta, so
	 * a mistyped URL would otherwise remove it without a word.
	 *
	 * @since 10.4.0
	 *
	 * @param mixed  $value The value passed.
	 * @param string $key   The meta key, for the error message.
	 *
	 * @return string|\WP_Error The URL, or '' to remove the meta.
	 */
	protected function termSettingUrl( $value, $key ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		// sanitize_url() keeps a relative URL and turns "example" into
		// "http://example", so the scheme is checked on the value passed.
		$url = preg_match( '#^https?://#i', $value ) ? sanitize_url( $value, array( 'http', 'https' ) ) : '';

		if ( '' === $url || ! wp_parse_url( $url, PHP_URL_HOST ) ) {
			return new \WP_Error(
				'seopress_ability_invalid_url',
				/* translators: %s: meta key, e.g. _seopress_robots_canonical */
				sprintf( __( '%s must be an absolute http or https URL, or an empty string to remove it.', 'wp-seopress' ), $key )
			);
		}

		return $url;
	}

	/**
	 * The four image metas of one social network, from the settings passed.
	 *
	 * The metabox stores the URL, attachment ID, width and height together:
	 * picking a media library image fills all four, pasting a URL clears the
	 * other three. Here an attachment ID fills all four from that image, a URL
	 * fills them from the media library image it points to, or stores the URL
	 * alone for any other image, so a size never describes another image.
	 *
	 * @since 10.4.0
	 *
	 * @param array  $settings The settings passed.
	 * @param string $url_key  The URL meta key, e.g. _seopress_social_fb_img.
	 *
	 * @return array|\WP_Error Meta key => value ('' deletes), empty when untouched.
	 */
	protected function termSocialImage( $settings, $url_key ) {
		$id_key  = $url_key . '_attachment_id';
		$has_url = array_key_exists( $url_key, $settings );
		$has_id  = array_key_exists( $id_key, $settings );

		if ( ! $has_url && ! $has_id ) {
			return array();
		}

		$image = array(
			$url_key             => '',
			$id_key              => '',
			$url_key . '_width'  => '',
			$url_key . '_height' => '',
		);

		$attachment_id = $has_id ? absint( $settings[ $id_key ] ) : 0;

		if ( $attachment_id > 0 ) {
			$src = wp_attachment_is_image( $attachment_id ) && current_user_can( 'read_post', $attachment_id )
				? wp_get_attachment_image_src( $attachment_id, 'full' )
				: false;

			if ( ! $src ) {
				return new \WP_Error(
					'seopress_ability_invalid_image',
					/* translators: 1: attachment ID, 2: meta key */
					sprintf( __( 'Attachment %1$d passed in %2$s is not an image of the media library you can use.', 'wp-seopress' ), $attachment_id, $id_key )
				);
			}

			return $this->termSocialImageFromAttachment( $image, $url_key, $attachment_id, $src );
		}

		$url = $this->termSettingUrl( $has_url ? $settings[ $url_key ] : '', $url_key );

		if ( is_wp_error( $url ) || '' === $url ) {
			// An empty URL or an attachment ID of 0 removes the image.
			return is_wp_error( $url ) ? $url : $image;
		}

		$image[ $url_key ] = $url;

		$found = attachment_url_to_postid( $url );
		$src   = $found && wp_attachment_is_image( $found ) ? wp_get_attachment_image_src( $found, 'full' ) : false;

		// Only the full size URL is that attachment's own size.
		if ( $src && $src[0] === $url ) {
			return $this->termSocialImageFromAttachment( $image, $url_key, $found, $src );
		}

		return $image;
	}

	/**
	 * Fill the four image metas from a media library image.
	 *
	 * @since 10.4.0
	 *
	 * @param array  $image         The four metas, emptied.
	 * @param string $url_key       The URL meta key.
	 * @param int    $attachment_id The attachment.
	 * @param array  $src           wp_get_attachment_image_src() at full size.
	 *
	 * @return array
	 */
	protected function termSocialImageFromAttachment( $image, $url_key, $attachment_id, $src ) {
		$image[ $url_key ]                    = sanitize_url( $src[0] );
		$image[ $url_key . '_attachment_id' ] = (string) $attachment_id;
		$image[ $url_key . '_width' ]         = $src[1] ? (string) (int) $src[1] : '';
		$image[ $url_key . '_height' ]        = $src[2] ? (string) (int) $src[2] : '';

		return $image;
	}

	/**
	 * Input schema of the term settings update abilities.
	 *
	 * The same { term_id, settings } map as the post abilities, but the map
	 * lists its keys: an unknown key is refused instead of ignored, so a
	 * client is told when a post-only key (a primary category, a frozen
	 * modified date) does not exist for terms.
	 *
	 * @since 10.4.0
	 *
	 * @param string $description What the map holds.
	 * @param array  $properties  Meta key => schema.
	 *
	 * @return array
	 */
	protected function termSettingsInputSchema( $description, $properties ) {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'term_id'  => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The ID of the term to update.', 'wp-seopress' ),
				),
				'settings' => array(
					'type'                 => 'object',
					'description'          => $description,
					'properties'           => $properties,
					'additionalProperties' => false,
					'minProperties'        => 1,
				),
			),
			'required'             => array( 'term_id', 'settings' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Output schema of the term settings read abilities.
	 *
	 * @since 10.4.0
	 *
	 * @param string $description What the list holds.
	 *
	 * @return array
	 */
	protected function termSettingsOutputSchema( $description ) {
		return array(
			'type'       => 'object',
			'properties' => array(
				'settings'  => array(
					'type'        => 'array',
					'description' => $description,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'key'           => array( 'type' => 'string' ),
							'type'          => array( 'type' => 'string' ),
							'label'         => array( 'type' => 'string' ),
							'description'   => array( 'type' => 'string' ),
							'value'         => array( 'type' => 'string' ),
							'can_modify'    => array(
								'type'        => 'boolean',
								'description' => __( 'False when the value cannot be set on this term, see locked_reason.', 'wp-seopress' ),
							),
							'locked_reason' => array(
								'type'        => 'string',
								'description' => __( 'Why the value cannot be set on this term, e.g. the global setting that forces it.', 'wp-seopress' ),
							),
						),
					),
				),
				'term_id'   => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the term that was read.', 'wp-seopress' ),
				),
				'term_name' => array(
					'type'        => 'string',
					'description' => __( 'The name of the term itself, which is not the SEO title.', 'wp-seopress' ),
				),
				'taxonomy'  => array(
					'type'        => 'string',
					'description' => __( 'The taxonomy of the term that was read.', 'wp-seopress' ),
				),
			),
		);
	}

	/**
	 * Output schema of the term settings update abilities.
	 *
	 * @since 10.4.0
	 *
	 * @return array
	 */
	protected function termUpdateOutputSchema() {
		$schema = $this->codeOutputSchema();

		$schema['properties']['skipped'] = array(
			'type'        => 'array',
			'description' => __( 'The keys passed that were not written, each with the reason.', 'wp-seopress' ),
			'items'       => array(
				'type'       => 'object',
				'properties' => array(
					'key'    => array( 'type' => 'string' ),
					'reason' => array( 'type' => 'string' ),
				),
			),
		);

		return $schema;
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
	 * Permission callback for the abilities that change a post's SEO settings.
	 *
	 * The REST routes these abilities wrap refuse a role blocked from the SEO
	 * metabox under Advanced > Security. The abilities call the controllers
	 * directly, past that permission_callback, so the block is checked here.
	 * Reading stays allowed, as it does over REST.
	 *
	 * @since 10.4.0
	 *
	 * @param array $input The ability input.
	 *
	 * @return bool|\WP_Error
	 */
	public function canWritePost( $input ) {
		$allowed = $this->canEditPost( $input );

		if ( true !== $allowed ) {
			return $allowed;
		}

		return $this->checkMetaboxRole();
	}

	/**
	 * Refuse a role blocked from the SEO metabox under Advanced > Security.
	 *
	 * @since 10.4.0
	 *
	 * @return true|\WP_Error
	 */
	protected function checkMetaboxRole() {
		if ( seopress_metabox_role_is_blocked( 'GLOBAL' ) ) {
			return new \WP_Error(
				'seopress_ability_role_blocked',
				__( 'Your role is not allowed to change SEO settings.', 'wp-seopress' )
			);
		}

		return true;
	}

	/**
	 * Shared permission callback for per-term abilities.
	 *
	 * The capability the term metabox's own handlers check (edit_term on that
	 * term), then the taxonomy: only one the term SEO metabox is offered on.
	 *
	 * @since 10.4.0
	 *
	 * @param array $input The ability input.
	 *
	 * @return bool|\WP_Error
	 */
	public function canEditTerm( $input ) {
		$term_id = isset( $input['term_id'] ) ? (int) $input['term_id'] : 0;
		$term    = $term_id > 0 ? get_term( $term_id ) : null;

		if ( ! $term instanceof \WP_Term ) {
			return new \WP_Error(
				'seopress_ability_invalid_term',
				__( 'The provided term does not exist.', 'wp-seopress' )
			);
		}

		if ( ! current_user_can( 'edit_term', $term->term_id ) ) {
			return new \WP_Error(
				'seopress_ability_forbidden',
				__( 'You are not allowed to edit this term.', 'wp-seopress' )
			);
		}

		if ( ! seopress_abilities_taxonomy_supported( $term->taxonomy ) ) {
			return new \WP_Error(
				'seopress_ability_unsupported_taxonomy',
				__( 'The SEO metadata of this taxonomy cannot be edited: it is not public, or its SEO metabox is disabled.', 'wp-seopress' )
			);
		}

		return true;
	}

	/**
	 * Permission callback for the abilities that change a term's SEO settings.
	 *
	 * The term counterpart of canWritePost(): a role blocked from the SEO
	 * metabox under Advanced > Security is not shown the term metabox either,
	 * so it may not write what that metabox writes. Unlike post meta, term meta
	 * has no write filter that would drop the value behind its back, so this is
	 * the only gate. Reading stays allowed, as for posts.
	 *
	 * @since 10.4.0
	 *
	 * @param array $input The ability input.
	 *
	 * @return bool|\WP_Error
	 */
	public function canWriteTerm( $input ) {
		$allowed = $this->canEditTerm( $input );

		if ( true !== $allowed ) {
			return $allowed;
		}

		return $this->checkMetaboxRole();
	}

	/**
	 * Permission callback for the term listing ability.
	 *
	 * Open to whoever may manage the terms of at least one supported taxonomy.
	 * The listing itself is then limited to those taxonomies.
	 *
	 * @since 10.4.0
	 *
	 * @return bool
	 */
	public function canListTerms() {
		return ! empty( $this->editableTaxonomies() );
	}

	/**
	 * The supported taxonomies whose terms the current user may edit.
	 *
	 * @since 10.4.0
	 *
	 * @return string[] Taxonomy names.
	 */
	protected function editableTaxonomies() {
		$names = array();

		foreach ( array_keys( seopress_abilities_supported_taxonomies() ) as $name ) {
			$taxonomy = get_taxonomy( $name );

			if ( $taxonomy && current_user_can( $taxonomy->cap->edit_terms ) ) {
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	 * Run the query of the term listing ability.
	 *
	 * The taxonomies are scoped to the ones the user may edit before the query
	 * runs, so the totals only count rows this user may see.
	 *
	 * @since 10.4.0
	 *
	 * @param array  $input   The ability input.
	 * @param string $missing One of title, description, any, both.
	 *
	 * @return array { terms, total, total_pages, page, per_page }
	 */
	protected function queryTerms( $input, $missing ) {
		$available  = $this->editableTaxonomies();
		$taxonomies = $available;

		if ( ! empty( $input['taxonomy'] ) && is_array( $input['taxonomy'] ) ) {
			// A list of nothing but unknown taxonomies matches nothing, it
			// never widens back to every taxonomy.
			$taxonomies = array_values( array_intersect( array_map( 'sanitize_key', $input['taxonomy'] ), $available ) );
		}

		$per_page = isset( $input['per_page'] ) ? (int) $input['per_page'] : 20;
		$per_page = max( 1, min( self::LISTING_MAX_PER_PAGE, $per_page ) );

		$page = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$page = max( 1, min( self::LISTING_MAX_PAGE, $page ) );

		$result = array(
			'terms'       => array(),
			'total'       => 0,
			'total_pages' => 0,
			'page'        => $page,
			'per_page'    => $per_page,
		);

		if ( empty( $taxonomies ) ) {
			return $result;
		}

		$args = array(
			'taxonomy'                 => $taxonomies,
			'hide_empty'               => false,
			'orderby'                  => 'term_id',
			'order'                    => 'ASC',
			// Marks this query for the clause filter below. The value is part
			// of the query vars, so the term query cache keys on it too.
			'seopress_ability_missing' => $missing,
		);

		// Scope the predicate to the queries carrying the marker and remove it
		// even if a hook throws. Filtering in SQL keeps totals and pages consistent.
		$filter = function ( $clauses, $query_taxonomies, $query_args ) {
			if ( ! empty( $query_args['seopress_ability_missing'] ) ) {
				$clauses['where'] .= ' AND ' . $this->missingMetadataQuery( (string) $query_args['seopress_ability_missing'], 'term' );
			}

			return $clauses;
		};

		add_filter( 'terms_clauses', $filter, 10, 3 );
		try {
			$total = ( new \WP_Term_Query() )->query( array_merge( $args, array( 'fields' => 'count' ) ) );
			$terms = ( new \WP_Term_Query() )->query(
				array_merge(
					$args,
					array(
						'number' => $per_page,
						'offset' => ( $page - 1 ) * $per_page,
					)
				)
			);
		} finally {
			remove_filter( 'terms_clauses', $filter, 10 );
		}

		foreach ( (array) $terms as $term ) {
			// Second gate, as for posts: a row is only returned when the
			// current user may actually edit that term.
			if ( $term instanceof \WP_Term && current_user_can( 'edit_term', $term->term_id ) ) {
				$result['terms'][] = $term;
			}
		}

		$result['total']       = is_numeric( $total ) ? (int) $total : 0;
		$result['total_pages'] = (int) ceil( $result['total'] / $per_page );

		return $result;
	}

	/**
	 * Format one term for the listing ability.
	 *
	 * @since 10.4.0
	 *
	 * @param \WP_Term $term The term.
	 *
	 * @return array
	 */
	protected function termPayload( $term ) {
		$permalink = get_term_link( $term );

		return array(
			'id'               => (int) $term->term_id,
			'name'             => $this->termName( $term ),
			'taxonomy'         => (string) $term->taxonomy,
			'slug'             => (string) $term->slug,
			'count'            => (int) $term->count,
			'permalink'        => is_wp_error( $permalink ) ? '' : (string) $permalink,
			'seo_title'        => (string) get_term_meta( $term->term_id, '_seopress_titles_title', true ),
			'meta_description' => (string) get_term_meta( $term->term_id, '_seopress_titles_desc', true ),
		);
	}

	/**
	 * Add the identity of the term a reading ability just answered about.
	 *
	 * See withPostIdentity(): the term's name is not its SEO title, so both
	 * are named for what they are.
	 *
	 * @since 10.4.0
	 *
	 * @param array $payload The ability payload so far.
	 * @param int   $term_id The term that was read.
	 *
	 * @return array
	 */
	protected function withTermIdentity( $payload, $term_id ) {
		$term = get_term( $term_id );

		if ( ! $term instanceof \WP_Term ) {
			return $payload;
		}

		return array_merge(
			$payload,
			array(
				'term_id'   => (int) $term->term_id,
				'term_name' => $this->termName( $term ),
				'taxonomy'  => (string) $term->taxonomy,
			)
		);
	}

	/**
	 * The name of a term as it was typed.
	 *
	 * WordPress stores term names HTML-encoded ("Fruits &amp; Vegetables"), so
	 * undo that encoding for the same reason postTitle() does.
	 *
	 * @since 10.4.0
	 *
	 * @param \WP_Term $term The term.
	 *
	 * @return string
	 */
	protected function termName( $term ) {
		return (string) wp_specialchars_decode( (string) $term->name, ENT_QUOTES );
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
	 * @since 10.4.0 The $object_type parameter, for terms.
	 *
	 * @param string $missing     One of title, description, any, both.
	 * @param string $object_type Optional. "post" (default), in a WP_Query, or
	 *                            "term", in a WP_Term_Query.
	 *
	 * @return string Prepared SQL predicate.
	 */
	protected function missingMetadataQuery( $missing, $object_type = 'post' ) {
		global $wpdb;

		// Table and column names only ever come from $wpdb, never from input.
		if ( 'term' === $object_type ) {
			$meta_table = $wpdb->termmeta;
			$id_column  = 'term_id';
			$object_id  = 't.term_id';
		} else {
			$meta_table = $wpdb->postmeta;
			$id_column  = 'post_id';
			$object_id  = "{$wpdb->posts}.ID";
		}

		$empty = function ( $key ) use ( $wpdb, $meta_table, $id_column, $object_id ) {
			// Correlated existence checks stop at the first matching metadata row.
			// Unlike unrestricted joins, unrelated metadata cannot multiply rows.
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table and column names are set above from $wpdb.
			return $wpdb->prepare(
				"(NOT EXISTS (SELECT 1 FROM {$meta_table} seopress_missing WHERE seopress_missing.{$id_column} = {$object_id} AND seopress_missing.meta_key = %s)
				OR EXISTS (SELECT 1 FROM {$meta_table} seopress_empty WHERE seopress_empty.{$id_column} = {$object_id} AND seopress_empty.meta_key = %s AND seopress_empty.meta_value = %s))",
				$key, $key, ''
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
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
	 * The analysis stores one impact per check (only the distinct levels up to
	 * 10.3.x). The admin posts column calls a post good when every check that
	 * counts passes, and this reports the same verdict rather than a second
	 * one.
	 *
	 * @since 10.3.0
	 *
	 * @param mixed $score   The stored score.
	 * @param array $summary Optional. The passed/total counts, see
	 *                       ContentAnalysis::getScoreSummary().
	 *
	 * @return string good|needs_improvement|not_analyzed
	 */
	protected function scoreBucket( $score, $summary = array() ) {
		if ( ! is_array( $score ) || empty( $score ) ) {
			return 'not_analyzed';
		}

		// With the per-check detail, the verdict is read from the same counts
		// the posts column reads its ring from, ignored checks left out.
		if ( is_array( $summary ) && ! empty( $summary['total'] ) ) {
			return $summary['passed'] === $summary['total'] ? 'good' : 'needs_improvement';
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
