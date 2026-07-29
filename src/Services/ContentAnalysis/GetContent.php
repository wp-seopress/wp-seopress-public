<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\ContentAnalysis;
use SEOPressPro\Services\Audit;

/**
 * GetContent
 */
class GetContent {

	const NAME_SERVICE = 'GetContentAnalysis';

	/**
	 * The seo issues repository.
	 *
	 * @var SEOIssuesRepository
	 */
	private $seo_issues_repository;

	/**
	 * The seo issues database.
	 *
	 * @var SEOIssuesDatabase
	 */
	private $seo_issues_database;

	/**
	 * Request-scoped memo of the post_content-derived heading outline, keyed by
	 * "post ID:content hash". Both heading checks resolve the same outline, so
	 * this avoids running do_blocks() + DOM parsing twice on the fallback path.
	 *
	 * @var array
	 */
	private $heading_outline_cache = array();

	/**
	 * Request-scoped memo of the page-builder detection, keyed by post ID.
	 *
	 * @var array
	 */
	private $builder_driven_cache = array();

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->seo_issues_repository = function_exists( 'seopress_pro_get_service' ) ? seopress_pro_get_service( 'SEOIssuesRepository' ) : null;

		$this->seo_issues_database = function_exists( 'seopress_pro_get_service' ) ? seopress_pro_get_service( 'SEOIssuesDatabase' ) : null;
	}

	/**
	 * Persist a single issue row through the Pro database service and
	 * record its issue_name so cleanupResolvedIssues() can later spare
	 * it from the orphan sweep.
	 *
	 * Issues whose priority resolves to 'good' or 0 are intentionally
	 * not added to the emitted list: the Pro saveData() short-circuits
	 * on them, so leaving the legacy row would be a false positive and
	 * the orphan sweep is the right place to remove it.
	 *
	 * @param int   $post_id Post id.
	 * @param array $issue Issue payload.
	 * @param array $emitted_names Accumulator passed by reference.
	 */
	private function saveIssue( $post_id, $issue, array &$emitted_names ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! isset( $issue['issue_name'] ) ) {
			return;
		}

		$priority = isset( $issue['issue_priority'] ) ? $issue['issue_priority'] : 0;
		if ( 0 === $priority || 'good' === $priority ) {
			return;
		}

		if ( $this->seo_issues_database && method_exists( $this->seo_issues_database, 'saveData' ) ) {
			$this->seo_issues_database->saveData( $post_id, $issue );
		}

		$emitted_names[] = $issue['issue_name'];
	}

	/**
	 * Remove the seopress_seo_issues rows for ($post_id, $issue_type)
	 * whose issue_name was not emitted during this analysis pass. Rows
	 * that survive keep their id (and therefore their issue_ignore flag)
	 * which is what makes the editor-side ignore stick across re-saves.
	 *
	 * Falls back to the legacy wipe when running against an older Pro
	 * version that doesn't ship deleteOrphans yet — in that case the
	 * ignore flag is not preserved, matching the pre-9.9 behaviour.
	 *
	 * @param int      $post_id       Post id.
	 * @param string   $issue_type    Issue type bucket.
	 * @param string[] $emitted_names issue_name values to keep.
	 */
	private function cleanupResolvedIssues( $post_id, $issue_type, array $emitted_names ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! $this->seo_issues_repository ) {
			return;
		}

		if ( method_exists( $this->seo_issues_repository, 'deleteOrphans' ) ) {
			$this->seo_issues_repository->deleteOrphans( $post_id, $issue_type, $emitted_names );
			return;
		}

		if ( empty( $emitted_names ) && method_exists( $this->seo_issues_repository, 'deleteSEOIssue' ) ) {
			$this->seo_issues_repository->deleteSEOIssue( $post_id, $issue_type );
		}
	}

	/**
	 * The getMatches function.
	 *
	 * @param string $content The content.
	 * @param array  $target_keywords The target keywords.
	 *
	 * @return array
	 */
	public function getMatches( $content, $target_keywords ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();

		if ( empty( $target_keywords ) ) {
			return null;
		}

		foreach ( $target_keywords as $kw ) {
			$kw = remove_accents( wp_specialchars_decode( $kw ) );

			if ( preg_match_all( '@(?<![\w-])' . preg_quote( $kw, '@' ) . '(?![\w-])@is', remove_accents( $content ), $matches ) ) {
				$data[ $kw ][] = $matches[0];
			}
		}

		if ( empty( $data ) ) {
			return null;
		}

		return $data;
	}


	/**
	 * The analyzeSchemas function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeSchemas( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'json_schemas';
		$emitted_names       = array();

		if ( isset( $data['json_schemas'] ) && is_array( $data['json_schemas'] ) && ( ! empty( $data['json_schemas'] ) || isset( $data['json_schemas'] ) ) ) {
			$desc = '<p>' . __( 'We found these schemas in the source code of this page:', 'wp-seopress' ) . '</p>';

			$desc .= '<ul>';

			$issue_desc = array();

			foreach ( array_count_values( $data['json_schemas'] ) as $key => $value ) {
				$html = null;
				if ( $value > 1 ) {
					if ( 'Review' !== $key ) {
						$html                          = '<span class="impact high">' . __( 'duplicated schema - x', 'wp-seopress' ) . $value . '</span>';
						$analyzes['schemas']['impact'] = 'high';
					} else {
						$html = ' <span class="impact">' . __( 'x', 'wp-seopress' ) . $value . '</span>';
					}

					$issue_desc[] = array( $key, $value );
				}
				$desc .= '<li><span class="dashicons dashicons-minus"></span>' . $key . $html . '</li>';
			}
			$desc                       .= '</ul>';
			$analyzes['schemas']['desc'] = $desc;

			// If duplicated schema.
			if ( ! empty( $issue_desc ) ) {
				$issue['issue_name'] = 'json_schemas_duplicated';
				$issue['issue_desc'] = $issue_desc;
			}
		} else {
			$docs                          = seopress_get_docs_links();
			$analyzes['schemas']['impact'] = 'low';
			$analyzes['schemas']['desc']   = '<p>' . __( 'No schemas found in the source code of this page. Get rich snippets in Google Search results and improve your visibility by adding structured data types (schemas) to your page.', 'wp-seopress' ) . '</p>';

			if ( ! is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
				$analyzes['schemas']['desc'] .= '<p><a class="seopress-help" href="' . esc_url( $docs['schemas']['feature'] ) . '" target="_blank" class="components-button is-link">' . __( 'Get SEOPress PRO to add schemas now', 'wp-seopress' ) . '</a></p>';
			} else {
				$analyzes['schemas']['desc'] .= '<p><a class="seopress-help" href="' . esc_url( $docs['schemas']['ebook'] ) . '" target="_blank" class="components-button is-link">' . __( 'Learn more', 'wp-seopress' ) . '</a></p>';
			}

			$issue['issue_name'] = 'json_schemas_not_found';
		}

		$issue['issue_priority'] = $analyzes['schemas']['impact'] ? $analyzes['schemas']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'json_schemas', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeOldPost function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeOldPost( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'old_post';
		$emitted_names       = array();

		$modified = get_post_datetime( $post, 'modified' );

		$desc = null;
		if ( $modified->getTimestamp() < strtotime( '-365 days' ) ) {
			$analyzes['old_post']['impact'] = 'medium';
			$desc                           = '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'This post is a little old!', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'old_post';
			$issue['issue_desc'] = $modified->getTimestamp();
		} else {
			$desc = '<p><span class="dashicons dashicons-yes"></span>' . __( 'The last modified date of this article is less than 1 year. Cool!', 'wp-seopress' ) . '</p>';
		}
		$desc                        .= '<p>' . __( 'Search engines love fresh content. Update regularly your articles without entirely rewriting your content and give them a boost in search rankings. SEOPress takes care of the technical part.', 'wp-seopress' ) . '</p>';
		$analyzes['old_post']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['old_post']['impact'] ? $analyzes['old_post']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'old_post', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeKeywordsPermalink function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeKeywordsPermalink( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'permalink';
		$emitted_names       = array();

		$permalink = ! empty( $data['permalink'] ) && is_array( $data['permalink'] ) ? $data['permalink']['value'] : '';
		$permalink = str_replace( '-', ' ', $permalink );
		$keywords  = isset( $data['keywords'] ) ? $data['keywords'] : array();
		$matches   = $this->getMatches( $permalink, $keywords );

		// Fallback: try matching transliterated keywords against the permalink slug.
		// Handles Cyrillic/non-Latin keywords with Latin slugs (e.g. via Cyr-To-Lat plugin).
		if ( empty( $matches ) && ! empty( $keywords ) ) {
			$transliterated_kw = array();
			foreach ( $keywords as $kw ) {
				$sanitized = sanitize_title( $kw );
				if ( $sanitized !== $kw ) {
					$transliterated_kw[] = $sanitized;
				}
			}
			if ( ! empty( $transliterated_kw ) ) {
				$matches = $this->getMatches( $permalink, $transliterated_kw );
			}
		}

		if ( ! empty( $matches ) ) {
			$desc  = '<p><span class="dashicons dashicons-yes"></span>' . __( 'Cool, one of your target keyword is used in your permalink.', 'wp-seopress' ) . '</p>';
			$desc .= '<ul>';
			foreach ( $matches as $key => $value ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span>' . $key . '</li>';
			}

			$desc                                    .= '</ul>';
			$analyzes['keywords_permalink']['desc']   = $desc;
			$analyzes['keywords_permalink']['impact'] = 'good';
		} elseif ( get_option( 'page_on_front' ) == $post->ID ) {
				$analyzes['keywords_permalink']['desc']   = '<p><span class="dashicons dashicons-yes"></span>' . __( 'This is your homepage. This check doesn\'t apply here because there is no slug.', 'wp-seopress' ) . '</p>';
				$analyzes['keywords_permalink']['impact'] = 'good';
		} else {
			$analyzes['keywords_permalink']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'You should add one of your target keyword in your permalink.', 'wp-seopress' ) . '</p>';
			$analyzes['keywords_permalink']['impact'] = 'medium';

			$issue['issue_name'] = 'keywords_permalink';
		}

		$issue['issue_priority'] = $analyzes['keywords_permalink']['impact'] ? $analyzes['keywords_permalink']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'permalink', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeHeadings function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeHeadings( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$emitted_names = array();

		// H1.
		$issue               = array();
		$issue['issue_type'] = 'headings';
		$desc                = '<h4>' . __( 'H1 (Heading 1)', 'wp-seopress' ) . '</h4>';

		// No headings found.
		if ( empty( $data['h1'] ) && empty( $data['h2'] ) && empty( $data['h3'] ) ) {
			$analyzes['headings']['impact'] = 'high';

			$issue['issue_name'] = 'headings_not_found';
		}

		$h1_matches = array();
		if ( ! empty( $data['h1'] ) ) {
			foreach ( $data['h1'] as $key => $value ) {
				$matches = $this->getMatches( $value, isset( $data['keywords'] ) ? $data['keywords'] : array() );

				if ( ! $matches ) {
					continue;
				}

				foreach ( $matches as $key_for_keyword => $value ) {
					$h1_matches[ $key_for_keyword ] = isset( $h1_matches[ $key_for_keyword ] ) ? $h1_matches[ $key_for_keyword ] + count( $value ) : count( $value );
				}
			}
		}

		if ( isset( $data['h1'] ) && is_array( $data['h1'] ) && ! empty( $h1_matches ) ) {
			$total_h1 = count( $data['h1'] );

			$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'Target keywords were found in Heading 1 (H1).', 'wp-seopress' ) . '</p>';

			$desc .= '<ul>';

			foreach ( $h1_matches as $key => $matches ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s target keyword, %2$d number of times the keyword was found */ sprintf( esc_html__( '%1$s was found %2$d times.', 'wp-seopress' ), $key, $matches ) . '</li>';
			}

			$desc .= '</ul>';
			if ( $total_h1 > 1 ) {
				$issue_desc = array();

				$desc                          .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of headings 1 */ sprintf( esc_html__( 'We found %d Heading 1 (H1) in your content.', 'wp-seopress' ), $total_h1 ) . '</p>';
				$desc                          .= '<p>' . __( 'You should not use more than one H1 heading in your post content. The rule is simple: only one H1 for each web page. It is better for both SEO and accessibility. Below, the list:', 'wp-seopress' ) . '</p>';
				$analyzes['headings']['impact'] = 'high';

				$desc .= '<ul>';
				foreach ( $data['h1'] as $h1 ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html( $h1 ) . '</li>';

					$issue_desc[] = sanitize_text_field( $h1 );
				}
				$desc .= '</ul>';

				$issue['issue_name'] = 'headings_h1_duplicated';
				$issue['issue_desc'] = $issue_desc;
			}
		} elseif ( isset( $data['h1'] ) && is_array( $data['h1'] ) && count( $data['h1'] ) === 0 ) {
			$desc                          .= '<p><span class="dashicons dashicons-no-alt"></span><strong>' . __( 'No Heading 1 (H1) found in your content. This is required for both SEO and Accessibility!', 'wp-seopress' ) . '</strong></p>';
			$analyzes['headings']['impact'] = 'high';

			$issue['issue_name'] = 'headings_h1_not_found';
		} else {
			$desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'None of your target keywords were found in Heading 1 (H1).', 'wp-seopress' ) . '</p>';
			if ( 'high' !== $analyzes['headings']['impact'] ) {
				$analyzes['headings']['impact'] = 'high';
			}

			$issue['issue_name'] = 'headings_h1_without_target_kw';
		}

		$issue['issue_priority'] = $analyzes['headings']['impact'] ? $analyzes['headings']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );

		// H2.
		$issue               = array();
		$issue['issue_type'] = 'headings';
		$desc               .= '<h4>' . __( 'H2 (Heading 2)', 'wp-seopress' ) . '</h4>';
		$h2_matches          = array();

		if ( ! empty( $data['h2'] ) ) {
			foreach ( $data['h2'] as $key => $value ) {
				$matches = $this->getMatches( $value, isset( $data['keywords'] ) ? $data['keywords'] : array() );
				if ( ! $matches ) {
					continue;
				}

				foreach ( $matches as $key_for_keyword => $value ) {
					$h2_matches[ $key_for_keyword ] = isset( $h2_matches[ $key_for_keyword ] ) ? $h2_matches[ $key_for_keyword ] + count( $value ) : count( $value );
				}
			}
		}

		if ( ! empty( $h2_matches ) ) {
			$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'Target keywords were found in Heading 2 (H2).', 'wp-seopress' ) . '</p>';
			$desc .= '<ul>';

			foreach ( $h2_matches as $key => $matches ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s heading 2, %2$d number of times the heading 2 was found */ sprintf( esc_html__( '%1$s was found %2$d times.', 'wp-seopress' ), $key, $matches ) . '</li>';
			}
			$desc .= '</ul>';
		} else {
			$desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'None of your target keywords were found in Heading 2 (H2).', 'wp-seopress' ) . '</p>';
			if ( 'high' !== $analyzes['headings']['impact'] ) {
				$analyzes['headings']['impact'] = 'medium';
			}

			$issue['issue_name'] = 'headings_h2_without_target_kw';
		}

		$issue['issue_priority'] = $analyzes['headings']['impact'] ? $analyzes['headings']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );

		// H3.
		$issue               = array();
		$issue['issue_type'] = 'headings';
		$desc               .= '<h4>' . __( 'H3 (Heading 3)', 'wp-seopress' ) . '</h4>';

		$h3_matches = array();
		if ( ! empty( $data['h3'] ) ) {
			foreach ( $data['h3'] as $key => $value ) {
				$matches = $this->getMatches( $value, isset( $data['keywords'] ) ? $data['keywords'] : array() );
				if ( ! $matches ) {
					continue;
				}

				foreach ( $matches as $key_for_keyword => $value ) {
					$h3_matches[ $key_for_keyword ] = isset( $h3_matches[ $key_for_keyword ] ) ? $h3_matches[ $key_for_keyword ] + count( $value ) : count( $value );
				}
			}
		}

		if ( ! empty( $h3_matches ) ) {
			$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'Target keywords were found in Heading 3 (H3).', 'wp-seopress' ) . '</p>';
			$desc .= '<ul>';

			foreach ( $h3_matches as $key => $matches ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s heading 3, %2$d number of times the heading 3 was found */ sprintf( esc_html__( '%1$s was found %2$d times.', 'wp-seopress' ), $key, $matches ) . '</li>';
			}
			$desc .= '</ul>';
		} else {
			$desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'None of your target keywords were found in Heading 3 (H3).', 'wp-seopress' ) . '</p>';
			if ( 'high' !== $analyzes['headings']['impact'] && 'medium' !== $analyzes['headings']['impact'] ) {
				$analyzes['headings']['impact'] = 'low';
			}

			$issue['issue_name'] = 'headings_h3_without_target_kw';
		}
		$analyzes['headings']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['headings']['impact'] ? $analyzes['headings']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'headings', $emitted_names );

		return $analyzes;
	}

	/**
	 * Resolve the effective meta title or description SEOPress outputs for a
	 * post: the global template with custom-field and dynamic variables
	 * resolved, computed without the loop-back page fetch. Uses the same
	 * TitleMeta / DescriptionMeta services that feed the editor metabox
	 * placeholder, so the analysis stays in sync with what is actually
	 * rendered even when the page could not be fetched (CDN, WAF, draft...).
	 *
	 * @since 10.0.0
	 *
	 * @param WP_Post $post The post.
	 * @param string  $type Either 'title' or 'description'.
	 *
	 * @return string The resolved value, or '' when it cannot be resolved.
	 */
	protected function getEffectiveMeta( $post, $type ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		$context_service = seopress_get_service( 'ContextPage' );
		$meta_service    = seopress_get_service( 'description' === $type ? 'DescriptionMeta' : 'TitleMeta' );

		if ( ! is_object( $context_service ) || ! is_object( $meta_service ) ) {
			return '';
		}

		$context = $context_service->buildContextWithCurrentId( $post->ID )->getContext();
		$value   = $meta_service->getValue( $context );

		return is_string( $value ) ? $value : '';
	}

	/**
	 * The analyzeMetaTitle function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeMetaTitle( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issues        = array();
		$emitted_names = array();

		$seopress_titles_title = ! empty( $data['title'] ) ? $data['title'] : get_post_meta( $post->ID, '_seopress_titles_title', true );

		// When neither the analyzed <title> nor a per-post custom title is
		// available, fall back to the effective title SEOPress outputs (global
		// template with custom-field / dynamic variables resolved). This keeps
		// the analysis aligned with the rendered title (and the metabox
		// placeholder) even when the loop-back page fetch could not capture it,
		// so a post whose title comes from a global or custom-field template is
		// analyzed instead of being flagged as having no title.
		if ( empty( $seopress_titles_title ) ) {
			$seopress_titles_title = $this->getEffectiveMeta( $post, 'title' );
		}

		$title_length = mb_strlen( $seopress_titles_title );

		if ( ! empty( $seopress_titles_title ) ) {
			$desc = null;

			$matches = $this->getMatches( $seopress_titles_title, isset( $data['keywords'] ) ? $data['keywords'] : array() );

			if ( ! empty( $matches ) ) {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'Target keywords were found in the Meta Title.', 'wp-seopress' ) . '</p>';
				$desc .= '<ul>';
				foreach ( $matches as $key => $value ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s target keyword, %2$d number of times the target keyword was found */ sprintf( esc_html__( '%1$s was found %2$d times.', 'wp-seopress' ), $key, count( $value ) ) . '</li>';
				}
				$desc                            .= '</ul>';
				$analyzes['meta_title']['impact'] = 'good';
			} else {
				$analyzes['meta_title']['impact'] = 'medium';
				$desc                            .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'None of your target keywords were found in the Meta Title.', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'title_without_target_kw';
				$issues[0]['issue_priority'] = $analyzes['meta_title']['impact'];
			}

			if ( $title_length > 60 ) {
				$analyzes['meta_title']['impact'] = 'medium';
				$desc                            .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your custom title is too long.', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'title_too_long';
				$issues[1]['issue_desc']     = $title_length;
				$issues[1]['issue_priority'] = $analyzes['meta_title']['impact'];
			} else {
				if ( ! empty( $analyzes['meta_title']['impact'] ) && 'medium' !== $analyzes['meta_title']['impact'] ) {
					$analyzes['meta_title']['impact'] = 'good';
				}
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'The length of your title is correct', 'wp-seopress' ) . '</p>';
			}
			$analyzes['meta_title']['desc'] = $desc;
		} else {
			$analyzes['meta_title']['impact'] = 'medium';
			$analyzes['meta_title']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'No custom title is set for this post. If the global meta title suits you, you can ignore this recommendation.', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'title_not_custom';
			$issues[2]['issue_priority'] = $analyzes['meta_title']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'title';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}
		$this->cleanupResolvedIssues( $post->ID, 'title', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeMetaDescription function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeMetaDescription( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issues        = array();
		$emitted_names = array();

		$seopress_titles_desc = ! empty( $data['description'] ) ? $data['description'] : get_post_meta( $post->ID, '_seopress_titles_desc', true );

		// Same effective-value fallback as the meta title: resolve the
		// description SEOPress outputs (global template, custom-field / dynamic
		// variables) when no analyzed or per-post description is available.
		if ( empty( $seopress_titles_desc ) ) {
			$seopress_titles_desc = $this->getEffectiveMeta( $post, 'description' );
		}

		$desc_length = mb_strlen( $seopress_titles_desc );

		if ( ! empty( $seopress_titles_desc ) ) {
			$desc = null;

			$matches = $this->getMatches( $seopress_titles_desc, isset( $data['keywords'] ) ? $data['keywords'] : array() );
			if ( ! empty( $matches ) ) {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'Target keywords were found in the Meta description.', 'wp-seopress' ) . '</p>';
				$desc .= '<ul>';

				foreach ( $matches as $key => $value ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s target keyword, %2$d number of times the target keyword was found */ sprintf( esc_html__( '%1$s was found %2$d times.', 'wp-seopress' ), $key, count( $value ) ) . '</li>';
				}
				$desc                           .= '</ul>';
				$analyzes['meta_desc']['impact'] = 'good';
			} else {
				$analyzes['meta_desc']['impact'] = 'medium';
				$desc                           .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'None of your target keywords were found in the Meta description.', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'description_without_target_kw';
				$issues[0]['issue_priority'] = $analyzes['meta_desc']['impact'];
			}

			if ( $desc_length > 160 ) {
				$analyzes['meta_desc']['impact'] = 'medium';
				$desc                           .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'You custom meta description is too long.', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'description_too_long';
				$issues[1]['issue_desc']     = $desc_length;
				$issues[1]['issue_priority'] = $analyzes['meta_desc']['impact'];
			} else {
				if ( ! empty( $analyzes['meta_desc']['impact'] ) && 'medium' !== $analyzes['meta_desc']['impact'] ) {
					$analyzes['meta_desc']['impact'] = 'good';
				}
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'The length of your meta description is correct', 'wp-seopress' ) . '</p>';
			}
			$analyzes['meta_desc']['desc'] = $desc;
		} else {
			$analyzes['meta_desc']['impact'] = 'medium';
			$analyzes['meta_desc']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'No custom meta description is set for this post. If the global meta description suits you, you can ignore this recommendation.', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'description_not_custom';
			$issues[2]['issue_priority'] = $analyzes['meta_desc']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'description';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}
		$this->cleanupResolvedIssues( $post->ID, 'description', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeSocialTags function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeSocialTags( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$emitted_names = array();

		// og:title.
		$issues = array();
		$desc   = null;
		$desc  .= '<h4>' . __( 'Open Graph Title', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['og_title'] ) && is_array( $data['og_title'] ) && ! empty( $data['og_title'] ) ) {
			$count = count( $data['og_title'] );

			$all_og_title = $data['og_title'];

			if ( $count > 1 ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:TITLE tags */ sprintf( esc_html__( 'We found %d og:title in your content.', 'wp-seopress' ), $count ) . '</p>';
				$desc                        .= '<p>' . __( 'You should not use more than one og:title in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:title tag from your source code. Below, the list:', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'og_title_duplicated';
				$issues[0]['issue_priority'] = $analyzes['social']['impact'];
			} elseif ( empty( $all_og_title[0] ) ) { // If og:title empty.
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Title tag is empty!', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'og_title_empty';
				$issues[1]['issue_priority'] = $analyzes['social']['impact'];
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found an Open Graph Title tag in your source code.', 'wp-seopress' ) . '</p>';
			}

			if ( ! empty( $all_og_title ) ) {
				$issue_desc = array();

				$desc .= '<ul>';
				foreach ( $all_og_title as $og_title ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html( $og_title ) . '</li>';

					$issue_desc[] = sanitize_text_field( $og_title );
				}
				$desc .= '</ul>';

				$issues[0]['issue_desc'] = $issue_desc;
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Title is missing!', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'og_title_missing';
			$issues[2]['issue_priority'] = $analyzes['social']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'social';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}

		// og:description.
		$issues = array();
		$desc  .= '<h4>' . __( 'Open Graph Description', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['og_description'] ) && is_array( $data['og_description'] ) && ! empty( $data['og_description'] ) ) {
			$count = count( $data['og_description'] );

			$all_og_desc = $data['og_description'];

			if ( $count > 1 ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:DESCRIPTION tags */ sprintf( esc_html__( 'We found %d og:description in your content.', 'wp-seopress' ), $count ) . '</p>';
				$desc                        .= '<p>' . __( 'You should not use more than one og:description in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:description tag from your source code. Below, the list:', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'og_desc_duplicated';
				$issues[0]['issue_priority'] = $analyzes['social']['impact'];
			} elseif ( empty( $all_og_desc[0] ) ) { // If og:description empty.
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Description tag is empty!', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'og_desc_empty';
				$issues[1]['issue_priority'] = $analyzes['social']['impact'];
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found an Open Graph Description tag in your source code.', 'wp-seopress' ) . '</p>';
			}

			if ( ! empty( $all_og_desc ) ) {
				$issue_desc = array();

				$desc .= '<ul>';
				foreach ( $all_og_desc as $og_desc ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html( $og_desc ) . '</li>';

					$issue_desc[] = sanitize_text_field( $og_desc );
				}
				$desc .= '</ul>';

				$issues[0]['issue_desc'] = $issue_desc;
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Description is missing!', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'og_desc_missing';
			$issues[2]['issue_priority'] = $analyzes['social']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'social';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}

		// og:image.
		$issue               = array();
		$issue['issue_type'] = 'social';
		$desc               .= '<h4>' . __( 'Open Graph Image', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['og_image'] ) && is_array( $data['og_image'] ) && ! empty( $data['og_image'] ) ) {
			$count = count( $data['og_image'] );

			$all_og_img = $data['og_image'];

			if ( $count > 0 && ! empty( $all_og_img[0] ) ) {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . /* translators: %d number of OG:IMAGE tags */ sprintf( esc_html__( 'We found %d og:image in your content.', 'wp-seopress' ), $count ) . '</p>';
			}

			// If og:image empty.
			if ( $count > 0 && empty( $all_og_img[0] ) ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Image tag is empty!', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'og_img_empty';
			}

			if ( ! empty( $all_og_img ) ) {
				$desc .= '<ul>';
				foreach ( $all_og_img as $og_img ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url( $og_img ) . '</li>';
				}
				$desc .= '</ul>';
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Image is missing!', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'og_img_missing';
		}

		$issue['issue_priority'] = $analyzes['social']['impact'] ? $analyzes['social']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );

		// og:url.
		$issues = array();
		$desc  .= '<h4>' . __( 'Open Graph URL', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['og_url'] ) && is_array( $data['og_url'] ) && ! empty( $data['og_url'] ) ) {
			$count = count( $data['og_url'] );

			$all_og_url = $data['og_url'];

			if ( $count > 1 ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:URL tags */ sprintf( esc_html__( 'We found %d og:url in your content.', 'wp-seopress' ), $count ) . '</p>';
				$desc                        .= '<p>' . __( 'You should not use more than one og:url in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:url tag from your source code. Below, the list:', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'og_url_duplicated';
				$issues[0]['issue_priority'] = $analyzes['social']['impact'];
			} elseif ( empty( $all_og_url[0] ) ) { // If og:url empty.
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph URL tag is empty!', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'og_url_empty';
				$issues[1]['issue_priority'] = $analyzes['social']['impact'];
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found an Open Graph URL tag in your source code.', 'wp-seopress' ) . '</p>';
			}

			if ( ! empty( $all_og_url ) ) {
				$issue_desc = array();

				$desc .= '<ul>';
				foreach ( $all_og_url as $og_url ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url( $og_url ) . '</li>';

					$issue_desc[] = sanitize_url( $og_url );
				}
				$desc .= '</ul>';

				$issues[0]['issue_desc'] = $issue_desc;
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph URL is missing!', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'og_url_missing';
			$issues[2]['issue_priority'] = $analyzes['social']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'social';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}

		// og:site_name.
		$issues = array();
		$desc  .= '<h4>' . __( 'Open Graph Site Name', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['og_site_name'] ) && is_array( $data['og_site_name'] ) && ! empty( $data['og_site_name'] ) ) {
			$count = count( $data['og_site_name'] );

			$all_og_site_name = $data['og_site_name'];

			if ( $count > 1 ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:SITE_NAME tags */ sprintf( esc_html__( 'We found %d og:site_name in your content.', 'wp-seopress' ), $count ) . '</p>';
				$desc                        .= '<p>' . __( 'You should not use more than one og:site_name in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:site_name tag from your source code. Below, the list:', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'og_sitename_duplicated';
				$issues[0]['issue_priority'] = $analyzes['social']['impact'];
			} elseif ( empty( $all_og_site_name[0] ) ) { // If og:site_name empty.
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Site Name tag is empty!', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'og_sitename_empty';
				$issues[1]['issue_priority'] = $analyzes['social']['impact'];
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found an Open Graph Site Name tag in your source code.', 'wp-seopress' ) . '</p>';
			}

			if ( ! empty( $all_og_site_name ) ) {
				$issue_desc = array();

				$desc .= '<ul>';
				foreach ( $all_og_site_name as $og_site_name ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html( $og_site_name ) . '</li>';

					$issue_desc[] = sanitize_text_field( $og_site_name );
				}
				$desc .= '</ul>';

				$issues[0]['issue_desc'] = $issue_desc;
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your Open Graph Site Name is missing!', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'og_sitename_missing';
			$issues[2]['issue_priority'] = $analyzes['social']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'social';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}

		// twitter:title.
		$issues = array();
		$desc  .= '<h4>' . __( 'X Title', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['twitter_title'] ) && is_array( $data['twitter_title'] ) && ! empty( $data['twitter_title'] ) ) {
			$count = count( $data['twitter_title'] );

			$all_tw_title = $data['twitter_title'];

			if ( $count > 1 ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of times a twitter:tile tag is found */ sprintf( esc_html__( 'We found %d twitter:title in your content.', 'wp-seopress' ), $count ) . '</p>';
				$desc                        .= '<p>' . /* translators: %d number of TWITTER:TITLE tags */ __( 'You should not use more than one twitter:title in your post content to avoid conflicts when sharing on social networks. X will take the last twitter:title tag from your source code. Below, the list:', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'x_title_duplicated';
				$issues[0]['issue_priority'] = $analyzes['social']['impact'];
			} elseif ( empty( $all_tw_title[0] ) ) { // If twitter:title empty.
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your X Title tag is empty!', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'x_title_empty';
				$issues[1]['issue_priority'] = $analyzes['social']['impact'];
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found a X Title tag in your source code.', 'wp-seopress' ) . '</p>';
			}

			if ( ! empty( $all_tw_title ) ) {
				$issue_desc = array();

				$desc .= '<ul>';
				foreach ( $all_tw_title as $tw_title ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html( $tw_title ) . '</li>';

					$issue_desc[] = sanitize_text_field( $tw_title );
				}
				$desc .= '</ul>';

				$issues[0]['issue_desc'] = $issue_desc;
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your X Title is missing!', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'x_title_missing';
			$issues[2]['issue_priority'] = $analyzes['social']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'social';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}

		// twitter:description.
		$issues = array();
		$desc  .= '<h4>' . __( 'X Description', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['twitter_description'] ) && is_array( $data['twitter_description'] ) && ! empty( $data['twitter_description'] ) ) {
			$count = count( $data['twitter_description'] );

			$all_tw_desc = $data['twitter_description'];

			if ( $count > 1 ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of TWITTER:DESCRIPTION tags */ sprintf( esc_html__( 'We found %d twitter:description in your content.', 'wp-seopress' ), $count ) . '</p>';
				$desc                        .= '<p>' . __( 'You should not use more than one twitter:description in your post content to avoid conflicts when sharing on social networks. X will take the last twitter:description tag from your source code. Below, the list:', 'wp-seopress' ) . '</p>';

				$issues[0]['issue_name']     = 'x_desc_duplicated';
				$issues[0]['issue_priority'] = $analyzes['social']['impact'];
			} elseif ( empty( $all_tw_desc[0] ) ) { // If twitter:description empty.
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your X Description tag is empty!', 'wp-seopress' ) . '</p>';

				$issues[1]['issue_name']     = 'x_desc_empty';
				$issues[1]['issue_priority'] = $analyzes['social']['impact'];
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found a X Description tag in your source code.', 'wp-seopress' ) . '</p>';
			}

			if ( ! empty( $all_tw_desc ) ) {
				$issue_desc = array();

				$desc .= '<ul>';
				foreach ( $all_tw_desc as $tw_desc ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html( $tw_desc ) . '</li>';

					$issue_desc[] = sanitize_text_field( $tw_desc );
				}
				$desc .= '</ul>';

				$issues[0]['issue_desc'] = $issue_desc;
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your X Description is missing!', 'wp-seopress' ) . '</p>';

			$issues[2]['issue_name']     = 'x_desc_missing';
			$issues[2]['issue_priority'] = $analyzes['social']['impact'];
		}

		if ( ! empty( $issues ) ) {
			foreach ( $issues as $issue ) {
				$issue['issue_type'] = 'social';
				$this->saveIssue( $post->ID, $issue, $emitted_names );
			}
		}

		// twitter:image.
		$issue               = array();
		$issue['issue_type'] = 'social';
		$desc               .= '<h4>' . __( 'X Image', 'wp-seopress' ) . '</h4>';

		if ( isset( $data['twitter_image'] ) && is_array( $data['twitter_image'] ) && ! empty( $data['twitter_image'] ) ) {
			$count = count( $data['twitter_image'] );

			$all_tw_img = $data['twitter_image'];

			if ( $count > 0 && ! empty( $all_tw_img[0] ) ) {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . /* translators: %d number of TWITTER:IMAGE tags */ sprintf( esc_html__( 'We found %d twitter:image in your content.', 'wp-seopress' ), $count ) . '</p>';
			}

			// If twitter:image:src empty.
			if ( $count > 0 && empty( $all_tw_img[0] ) ) {
				$analyzes['social']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your X Image tag is empty!', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'x_img_empty';
			}

			if ( ! empty( $all_tw_img ) ) {
				$desc .= '<ul>';
				foreach ( $all_tw_img as $tw_img ) {
					$desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url( $tw_img ) . '</li>';
				}
				$desc .= '</ul>';
			}
		} else {
			$analyzes['social']['impact'] = 'high';
			$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Your X Image is missing!', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'x_img_missing';
		}
		$analyzes['social']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['social']['impact'] ? $analyzes['social']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'social', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeRobots function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeRobots( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'robots';
		$emitted_names       = array();
		$desc = null;
		if ( isset( $data['meta_robots'] ) && is_array( $data['meta_robots'] ) && ! empty( $data['meta_robots'] ) ) {
			$meta_robots = $data['meta_robots'];

			if ( count( $data['meta_robots'] ) > 1 ) {
				$analyzes['robots']['impact'] = 'high';

				$count_meta_robots = count( $data['meta_robots'] );

				$desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %s number of meta robots tags */ sprintf( esc_html__( 'We found %s meta robots in your page. There is probably something wrong with your theme!', 'wp-seopress' ), $count_meta_robots ) . '</p>';

				$issue['issue_name'] = 'meta_robots_duplicated';
				$issue['issue_desc'] = absint( $count_meta_robots );
			}

			$encoded = wp_json_encode( $meta_robots );

			if ( preg_match( '/noindex/', $encoded ) ) {
				$analyzes['robots']['impact'] = 'high';
				$desc                        .= '<p data-robots="noindex"><span class="dashicons dashicons-no-alt"></span>' . __( '<strong>noindex</strong> is on! Search engines can\'t index this page.', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'meta_robots_noindex';
			} else {
				$desc .= '<p data-robots="index"><span class="dashicons dashicons-yes"></span>' . __( '<strong>noindex</strong> is off. Search engines will index this page.', 'wp-seopress' ) . '</p>';
			}

			if ( preg_match( '/nofollow/', $encoded ) ) {
				$analyzes['robots']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( '<strong>nofollow</strong> is on! Search engines can\'t follow your links on this page.', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'meta_robots_nofollow';
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( '<strong>nofollow</strong> is off. Search engines will follow links on this page.', 'wp-seopress' ) . '</p>';
			}

			if ( preg_match( '/noimageindex/', $encoded ) ) {
				$analyzes['robots']['impact'] = 'high';
				$desc                        .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( '<strong>noimageindex</strong> is on! Google will not index your images on this page (but if someone makes a direct link to one of your image in this page, it will be indexed).', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'meta_robots_noimageindex';
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( '<strong>noimageindex</strong> is off. Google will index the images on this page.', 'wp-seopress' ) . '</p>';
			}

			if ( preg_match( '/nosnippet/', $encoded ) ) {
				if ( 'high' !== $analyzes['robots']['impact'] ) {
					$analyzes['robots']['impact'] = 'medium';
				}
				$desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( '<strong>nosnippet</strong> is on! Search engines will not display a snippet of this page in search results.', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'meta_robots_nosnippet';
			} else {
				$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( '<strong>nosnippet</strong> is off. Search engines will display a snippet of this page in search results.', 'wp-seopress' ) . '</p>';
			}
		} else {
			$desc .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'We found no meta robots on this page. It means, your page is index,follow. Search engines will index it, and follow links. ', 'wp-seopress' ) . '</p>';
		}

		$analyzes['robots']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['robots']['impact'] ? $analyzes['robots']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'robots', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeImgAlt function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeImgAlt( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'img_alt';
		$emitted_names       = array();

		$with_alt    = array();
		$without_alt = array();
		if ( ! empty( $data['images'] ) ) {
			foreach ( $data['images'] as $image ) {
				if ( ! empty( $image['alt'] ) ) {
					$with_alt[] = $image['src'];
				} else {
					$without_alt[] = $image['src'];
				}
			}
		}

		if ( ! empty( $without_alt ) ) {
			$desc = '<div class="wrap-analysis-img">';

			if ( ! empty( $without_alt ) ) {
				$issue_desc = array();

				$analyzes['img_alt']['impact'] = 'high';
				$desc                         .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'No alternative text found for these images. Alt tags are important for both SEO and accessibility. Edit your images using the media library or your favorite page builder and fill in alternative text fields.', 'wp-seopress' ) . '</p>';

				// Standard images & galleries.
				if ( ! empty( $without_alt ) ) {
					$desc .= '<ul class="attachments">';
					foreach ( $without_alt as $img ) {
				$img_url = esc_url( $img );
				$desc    .= '<li class="attachment"><figure>';

				// Enhanced image preview with lazy loading support.
				$desc .= '<img src="' . $img_url . '" ';
				$desc .= 'loading="lazy" ';
				$desc .= 'onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" ';
				$desc .= '/>';

				$desc .= '<figcaption><a href="' . $img_url . '" target="_blank">' . esc_html__( 'Open image (new tab)', 'wp-seopress' ) . '</a></figcaption></figure></li>';

						$issue_desc[] = sanitize_url( $img );
					}
					$desc .= '</ul>';
				}

				$desc .= '<p>' . __( 'Note that we scan all your source code, it means, some missing alternative texts of images might be located in your header, sidebar or footer.', 'wp-seopress' ) . '</p>';
			}
			$desc .= '</div>';

			$analyzes['img_alt']['desc'] = $desc;

			$issue['issue_name'] = 'img_alt_missing';
			$issue['issue_desc'] = $issue_desc;
		} elseif ( ! empty( $with_alt ) && empty( $without_alt ) ) {
			$analyzes['img_alt']['impact'] = 'good';
			$analyzes['img_alt']['desc']   = '<p><span class="dashicons dashicons-yes"></span>' . __( 'All alternative tags are filled in. Good work!', 'wp-seopress' ) . '</p>';
		} elseif ( empty( $with_alt ) && empty( $without_alt ) ) {
			$analyzes['img_alt']['impact'] = 'medium';
			$analyzes['img_alt']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'We could not find any image in your content. Content with media is a plus for your SEO.', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'img_alt_no_media';
		}

		$issue['issue_priority'] = $analyzes['img_alt']['impact'] ? $analyzes['img_alt']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'img_alt', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeNoFollowLinks function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeNoFollowLinks( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'nofollow_links';
		$emitted_names       = array();

		if ( isset( $data['links_no_follow'] ) && is_array( $data['links_no_follow'] ) && ! empty( $data['links_no_follow'] ) ) {
			$issue_desc = array();
			$count      = count( $data['links_no_follow'] );

			$desc  = '<p>' . /* translators: %d number of nofollow links */ sprintf( esc_html__( 'We found %d links with nofollow attribute in your page. Do not overuse nofollow attribute in links. Below, the list:', 'wp-seopress' ), $count ) . '</p>';
			$desc .= '<ul>';
			foreach ( $data['links_no_follow'] as $link ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url( $link['url'] ) . '" target="_blank" class="components-button is-link">' . esc_url( $link['value'] ) . '</a><span class="dashicons dashicons-external"></span></li>';

				$issue_desc[] = sanitize_url( $link['url'] );
			}
			$desc                                .= '</ul>';
			$analyzes['nofollow_links']['impact'] = 'good';
			if ( $count > 3 ) {
				$analyzes['nofollow_links']['impact'] = 'low';
			}
			$analyzes['nofollow_links']['desc'] = $desc;

			$issue['issue_name'] = 'nofollow_links_too_many';
			$issue['issue_desc'] = $issue_desc;
		} else {
			$analyzes['nofollow_links']['desc'] = '<p><span class="dashicons dashicons-yes"></span>' . __( 'This page doesn\'t have any nofollow links.', 'wp-seopress' ) . '</p>';
		}

		$issue['issue_priority'] = $analyzes['nofollow_links']['impact'] ? $analyzes['nofollow_links']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'nofollow_links', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeOutboundLinks function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeOutboundLinks( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'outbound_links';
		$emitted_names       = array();

		$desc = '<p>' . __( 'Internet is built on the principle of hyperlink. It is therefore perfectly normal to make links between different websites. However, avoid making links to low quality sites, SPAM... If you are not sure about the quality of a site, add the attribute "nofollow" to your link.', 'wp-seopress' ) . '</p>';
		if ( isset( $data['outbound_links'] ) && is_array( $data['outbound_links'] ) && ! empty( $data['outbound_links'] ) ) {
			$count = count( $data['outbound_links'] );

			$desc .= '<p>' . /* translators: %d number of outbound links */ sprintf( __( 'We found %s outbound links in your page. Below, the list:', 'wp-seopress' ), $count ) . '</p>';
			$desc .= '<ul>';
			foreach ( $data['outbound_links'] as $link ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url( $link['url'] ) . '" target="_blank" class="components-button is-link">' . esc_url( $link['value'] ) . '</a><span class="dashicons dashicons-external"></span></li>';
			}
			$desc .= '</ul>';
		} else {
			$analyzes['outbound_links']['impact'] = 'medium';
			$desc                                .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'This page doesn\'t have any outbound links.', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'outbound_links_missing';
		}
		$analyzes['outbound_links']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['outbound_links']['impact'] ? $analyzes['outbound_links']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'outbound_links', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeInternalLinks function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeInternalLinks( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'internal_links';
		$emitted_names       = array();

		$desc = '<p>' . __( 'Internal links are important for SEO and user experience. Always try to link your content together, with quality link anchors.', 'wp-seopress' ) . '</p>';

		// Bricks compatibility.
		$theme = wp_get_theme();
		if ( defined( 'BRICKS_DB_EDITOR_MODE' ) && ( 'bricks' === $theme->template || 'Bricks' === $theme->parent_theme ) ) {
			$analyzes['internal_links']['impact'] = 'good';
			$desc                                .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'Unfortunately, this analysis can‘t work with Bricks Builder because of the way your content is stored in your database.', 'wp-seopress' ) . '</p>';
		} elseif ( isset( $data['internal_links'] ) && is_array( $data['internal_links'] ) && ! empty( $data['internal_links'] ) ) {
			$count = count( $data['internal_links'] );

			$desc .= '<p>' . /* translators: %s internal links */ sprintf( __( 'We found %s internal links to this page.', 'wp-seopress' ), $count ) . '</p>';

			$desc .= '<ul>';
			foreach ( $data['internal_links'] as $link ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url( $link['url'] ) . '" target="_blank" class="components-button is-link">' . esc_html( $link['value'] ) . '</a>
                <a class="nounderline" href="' . esc_url( get_edit_post_link( $link['id'] ) ) . '" title="' . /* translators: %s link to edit the post */ sprintf( __( 'edit %s', 'wp-seopress' ), esc_html( get_the_title( $link['id'] ) ) ) . '"><span class="dashicons dashicons-edit-large"></span></a></li>';
			}
			$desc .= '</ul>';
		} else {
			$analyzes['internal_links']['impact'] = 'medium';
			$desc                                .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'This page doesn\'t have any internal links from other content. Links from archive pages are not considered internal links due to lack of context.', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'internal_links_missing';
		}
		$analyzes['internal_links']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['internal_links']['impact'] ? $analyzes['internal_links']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'internal_links', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeCanonical function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeCanonical( $analyzes, $data, $post ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$issue               = array();
		$issue['issue_type'] = 'all_canonical';
		$emitted_names       = array();

		$desc = '<p>' . __( 'A canonical URL is required by search engines to handle duplicate content.', 'wp-seopress' ) . '</p>';

		if ( isset( $data['canonical'] ) && is_array( $data['canonical'] ) && ! empty( $data['canonical'] ) ) {
			$count = count( $data['canonical'] );

			$desc .= '<p>' . /* translators: %s number of canonical tags */ sprintf( _n( 'We found %s canonical URL in your source code. Below, the list:', 'We found %s canonical URLs in your source code. Below, the list:', $count, 'wp-seopress' ), number_format_i18n( $count ) ) . '</p>';

			$desc .= '<ul>';
			foreach ( $data['canonical'] as $link ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url( $link ) . '" target="_blank" class="components-button is-link">' . esc_url( $link ) . '</a><span class="dashicons dashicons-external"></span></li>';
			}
			$desc .= '</ul>';

			if ( $count > 1 ) {
				$analyzes['all_canonical']['impact'] = 'high';
				$desc                               .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'You must fix this. Canonical URL duplication is bad for SEO.', 'wp-seopress' ) . '</p>';

				$issue['issue_name'] = 'canonical_duplicated';
			}
		} elseif ( 'yes' === get_post_meta( $post->ID, '_seopress_robots_index', true ) ) {
				$analyzes['all_canonical']['impact'] = 'good';
				$desc                               .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'This page doesn\'t have any canonical URL because your post is set to <strong>noindex</strong>. This is normal.', 'wp-seopress' ) . '</p>';
		} elseif ( seopress_get_service( 'TitleOption' )->getSingleCptNoIndex() || seopress_get_service( 'TitleOption' )->getTitleNoIndex() || true === post_password_required( $post->ID ) ) {
			$analyzes['all_canonical']['impact'] = 'good';
			$desc                               .= '<p><span class="dashicons dashicons-yes"></span>' . __( 'This page doesn\'t have any canonical URL because your post is set to <strong>noindex</strong>. This is normal.', 'wp-seopress' ) . '</p>';
		} else {
			$analyzes['all_canonical']['impact'] = 'high';
			$desc                               .= '<p><span class="dashicons dashicons-no-alt"></span>' . __( 'This page doesn\'t have any canonical URL.', 'wp-seopress' ) . '</p>';

			$issue['issue_name'] = 'canonical_missing';
		}
		$analyzes['all_canonical']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['all_canonical']['impact'] ? $analyzes['all_canonical']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'all_canonical', $emitted_names );

		return $analyzes;
	}

	/**
	 * Count the words of the rendered post content (multibyte aware).
	 * Scanning the post object rather than the full source code keeps
	 * the header/footer/sidebar out of the depth metrics.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return int
	 */
	private function getContentWordCount( $post ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( null === $post || empty( $post->post_content ) ) {
			return 0;
		}

		$content = $post->post_content;

		if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$content = do_blocks( $content );
		}

		$content = wp_strip_all_tags( strip_shortcodes( $content ), true );
		$content = html_entity_decode( $content, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		if ( '' === trim( $content ) ) {
			return 0;
		}

		$words = preg_split( '/[\p{Z}\s]+/u', trim( $content ), -1, PREG_SPLIT_NO_EMPTY );

		return is_array( $words ) ? count( $words ) : 0;
	}

	/**
	 * Resolve the heading outline used by the heading-structure and readability
	 * checks.
	 *
	 * The primary outline is parsed from the fetched rendered page
	 * (\SEOPress\Services\ContentAnalysis\GetContent\ContentStructure). When that
	 * capture fails — a WAF/CDN challenge interstitial, an unrendered draft /
	 * preview, a stale full-page cache, a cross-origin fetch — the fetched body
	 * is empty and the outline comes back empty even though the post is full of
	 * headings. Falling back to the server-rendered post_content (the same source
	 * getContentWordCount() already uses) keeps these checks accurate regardless
	 * of the front-end capture, and consistent with the word count they compare
	 * against.
	 *
	 * @param array   $data The analysis data.
	 * @param WP_Post $post The post.
	 *
	 * @return array Ordered list of heading levels (e.g. array( 2, 3, 3, 2 )).
	 */
	private function resolveHeadingOutline( $data, $post ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$outline = isset( $data['content_structure']['outline'] ) && is_array( $data['content_structure']['outline'] )
			? $data['content_structure']['outline']
			: array();

		if ( ! empty( $outline ) ) {
			return $outline;
		}

		return $this->getHeadingOutlineFromContent( $post );
	}

	/**
	 * Extract the heading outline from the server-rendered post_content.
	 *
	 * Renders the block content the same way getContentWordCount() does, then
	 * reads the heading levels straight from the markup. post_content holds only
	 * the article body — no theme header/footer/sidebar — so no scoping is
	 * needed. Headings are matched by tag name, so builder headings that output a
	 * real <hN> (Kadence Advanced Heading, etc.) are counted like core ones.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return array Ordered list of heading levels.
	 */
	private function getHeadingOutlineFromContent( $post ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( null === $post || empty( $post->post_content ) || ! class_exists( 'DOMDocument' ) ) {
			return array();
		}

		// Both heading checks resolve the same outline within one analysis run;
		// memoize on the content hash so do_blocks() + DOM parsing runs once and
		// the cache is never served stale after an edit.
		$cache_key = $post->ID . ':' . md5( $post->post_content );
		if ( isset( $this->heading_outline_cache[ $cache_key ] ) ) {
			return $this->heading_outline_cache[ $cache_key ];
		}

		$content = $post->post_content;

		if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$content = do_blocks( $content );
		}

		if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
			$this->heading_outline_cache[ $cache_key ] = array();
			return array();
		}

		$dom             = new \DOMDocument();
		$internal_errors = libxml_use_internal_errors( true );

		// The XML prolog forces UTF-8 decoding; the wrapper div gives loadHTML a
		// single root so it does not inject its own <p> around loose text.
		$dom->loadHTML( '<?xml encoding="utf-8" ?><div>' . $content . '</div>' );

		libxml_clear_errors();
		libxml_use_internal_errors( $internal_errors );

		$xpath    = new \DOMXPath( $dom );
		$headings = $xpath->query( '//h1|//h2|//h3|//h4|//h5|//h6' );

		$outline = array();
		if ( $headings ) {
			foreach ( $headings as $heading ) {
				if ( '' === trim( (string) $heading->nodeValue ) ) { // phpcs:ignore -- DOM property.
					continue;
				}

				$outline[] = (int) substr( $heading->nodeName, 1 ); // phpcs:ignore -- DOM property.
			}
		}

		$this->heading_outline_cache[ $cache_key ] = $outline;

		return $outline;
	}

	/**
	 * Whether the post is managed by a known page builder (Elementor, Bricks,
	 * Beaver Builder, Oxygen, Zion, Breakdance, Divi, Avada, WPBakery,
	 * Cornerstone...).
	 *
	 * The builder set mirrors \SEOPress\Services\EnqueueModuleMetabox, but is
	 * detected here by storage signal (meta / post_content marker) rather than
	 * by edit-context query args, since analysis runs on the saved post. Whether
	 * the content is actually readable is decided separately by
	 * aiContentChecksUnreliable() via the word-count gate, so both meta-based
	 * builders (empty post_content) and shortcode-based ones (Divi, WPBakery)
	 * are handled: they degrade only when post_content is genuinely too thin.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return bool
	 */
	private function isBuilderDriven( $post ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( null === $post || empty( $post->ID ) ) {
			return false;
		}

		if ( isset( $this->builder_driven_cache[ $post->ID ] ) ) {
			return $this->builder_driven_cache[ $post->ID ];
		}

		$is_builder = false;

		// Known page builders, aligned with the edit-context detection in
		// \SEOPress\Services\EnqueueModuleMetabox::canEnqueue() (which keys off
		// the builder preview query args). Content analysis runs on the saved
		// post (REST / cron), outside any builder iframe, so we need a *storage*
		// signal instead: a meta the builder writes, or a marker it leaves in
		// post_content. A non-empty meta is enough; value semantics differ per
		// builder (Elementor stores 'builder', Beaver '1', others a structure).
		//
		// It is safe to over-match here: aiContentChecksUnreliable() only
		// degrades when post_content is *also* too thin to analyse, so a builder
		// post that still exposes readable text in post_content is evaluated
		// normally.
		$meta_flags = array(
			'_elementor_edit_mode',       // Elementor.
			'_bricks_page_content_2',     // Bricks.
			'_fl_builder_enabled',        // Beaver Builder.
			'ct_builder_shortcodes',      // Oxygen.
			'_zionbuilder_page_elements', // Zion Builder.
			'_breakdance_data',           // Breakdance.
			'_et_pb_use_builder',         // Divi.
			'_cornerstone_data',          // Themeco Cornerstone / Pro.
		);

		foreach ( $meta_flags as $key ) {
			if ( ! empty( get_post_meta( $post->ID, $key, true ) ) ) {
				$is_builder = true;
				break;
			}
		}

		// Builders that leave a placeholder block or a wrapper shortcode in
		// post_content while keeping the real layout elsewhere. These markers are
		// reliable and survive meta-key changes across builder versions.
		if ( ! $is_builder && ! empty( $post->post_content ) ) {
			$content_markers = array(
				'wp:breakdance/',            // Breakdance launcher block.
				'[et_pb_section',            // Divi.
				'[fusion_builder_container', // Avada / Fusion Builder.
				'[vc_row',                   // WPBakery Page Builder.
				'[cs_content',               // Themeco Cornerstone / Pro.
			);

			foreach ( $content_markers as $marker ) {
				if ( false !== strpos( $post->post_content, $marker ) ) {
					$is_builder = true;
					break;
				}
			}
		}

		/**
		 * Filter whether a post is considered managed by a page builder that
		 * stores its content outside post_content. Return true to make the AI
		 * content checks degrade to a "not evaluated" state instead of
		 * analysing the (empty) post_content.
		 *
		 * @since 10.1
		 *
		 * @param bool    $is_builder Detected builder state.
		 * @param WP_Post $post       The analysed post.
		 */
		$is_builder = (bool) apply_filters( 'seopress_content_analysis_builder_driven', $is_builder, $post );

		$this->builder_driven_cache[ $post->ID ] = $is_builder;

		return $is_builder;
	}

	/**
	 * Whether the post_content-based AI content checks cannot be trusted for
	 * this post.
	 *
	 * True only when a page builder holds the content outside post_content AND
	 * post_content is itself too thin to analyse — so a hybrid post that still
	 * keeps real text in post_content is evaluated normally.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return bool
	 */
	private function aiContentChecksUnreliable( $post ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! $this->isBuilderDriven( $post ) ) {
			return false;
		}

		$min_words = (int) apply_filters( 'seopress_content_analysis_min_words', 300, $post );

		return $this->getContentWordCount( $post ) < $min_words;
	}

	/**
	 * Put an AI content check into a neutral "not evaluated" state: no alert, no
	 * unearned green pass, an explanatory note, and any stale issue for that
	 * check cleared. Used when the content lives in a page builder and cannot be
	 * read from post_content.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param string  $key      Analyze key, which is also the issue-type bucket
	 *                          (content_depth, heading_hierarchy...).
	 * @param WP_Post $post     The post.
	 *
	 * @return array
	 */
	private function markCheckNotEvaluated( $analyzes, $key, $post ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// 'good' keeps the check out of the alert buckets and the aggregate
		// score; the note makes clear it was skipped rather than passed.
		$analyzes[ $key ]['impact'] = 'good';
		$analyzes[ $key ]['desc']   = '<p>' . __( 'This content is built with a page builder that stores its layout outside the post content, so these checks could not read it and were skipped. Please verify these aspects directly on the published page.', 'wp-seopress' ) . '</p>';

		// Drop any verdict emitted on a previous pass so a stale alert clears.
		$this->cleanupResolvedIssues( $post->ID, $key, array() );

		return $analyzes;
	}

	/**
	 * The analyzeContentDepth function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeContentDepth( $analyzes, $data, $post ) {
		if ( $this->aiContentChecksUnreliable( $post ) ) {
			return $this->markCheckNotEvaluated( $analyzes, 'content_depth', $post );
		}

		$issue               = array();
		$issue['issue_type'] = 'content_depth';
		$emitted_names       = array();

		$word_count = $this->getContentWordCount( $post );
		$min_words  = (int) apply_filters( 'seopress_content_analysis_min_words', 300, $post );

		$desc = '<p>' . __( 'Google\'s AI features and search rank in-depth content with a unique point of view higher than thin, commodity content. Make sure your content covers the topic thoroughly.', 'wp-seopress' ) . '</p>';

		if ( $word_count < $min_words ) {
			$analyzes['content_depth']['impact'] = 'medium';
			$desc                               .= '<p><span class="dashicons dashicons-no-alt sp-dashicons-not"></span>' . /* translators: %1$d current word count, %2$d recommended minimum word count */ sprintf( esc_html__( 'Your content is quite thin (%1$d words). Aim for at least %2$d words to cover the topic in depth.', 'wp-seopress' ), $word_count, $min_words ) . '</p>';

			$issue['issue_name'] = 'content_too_thin';
			$issue['issue_desc'] = array( $word_count );
		} else {
			$desc .= '<p><span class="dashicons dashicons-yes sp-dashicons-ok"></span>' . /* translators: %d word count */ sprintf( esc_html__( 'Your content has %d words. Good job!', 'wp-seopress' ), $word_count ) . '</p>';
		}

		$analyzes['content_depth']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['content_depth']['impact'] ? $analyzes['content_depth']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'content_depth', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeHeadingHierarchy function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeHeadingHierarchy( $analyzes, $data, $post ) {
		if ( $this->aiContentChecksUnreliable( $post ) ) {
			return $this->markCheckNotEvaluated( $analyzes, 'heading_hierarchy', $post );
		}

		$issue               = array();
		$issue['issue_type'] = 'heading_hierarchy';
		$emitted_names       = array();

		$outline = $this->resolveHeadingOutline( $data, $post );

		$desc = '<p>' . __( 'Organize your content into clear sections with a coherent heading hierarchy. This helps both readers and AI systems understand the structure of your page.', 'wp-seopress' ) . '</p>';

		$skips = array();
		$prev  = 0;
		foreach ( $outline as $level ) {
			if ( $prev > 0 && $level > $prev + 1 ) {
				$skips[] = sprintf( 'H%1$d &rarr; H%2$d', $prev, $level );
			}
			$prev = $level;
		}

		$subheadings = 0;
		foreach ( $outline as $level ) {
			if ( $level >= 2 ) {
				$subheadings++;
			}
		}

		$word_count       = $this->getContentWordCount( $post );
		$long_content     = (int) apply_filters( 'seopress_content_analysis_long_content_words', 900, $post );
		$min_subheadings  = (int) apply_filters( 'seopress_content_analysis_min_subheadings', 2, $post );
		$is_long          = $word_count >= $long_content;

		if ( ! empty( $skips ) ) {
			$analyzes['heading_hierarchy']['impact'] = 'low';
			$desc                                   .= '<p><span class="dashicons dashicons-no-alt sp-dashicons-not"></span>' . __( 'Your heading levels are not sequential (a level is skipped). Don\'t jump from H2 to H4 without an H3 in between.', 'wp-seopress' ) . '</p>';
			$desc                                   .= '<ul>';
			foreach ( $skips as $skip ) {
				$desc .= '<li><span class="dashicons dashicons-minus"></span>' . wp_kses_post( $skip ) . '</li>';
			}
			$desc .= '</ul>';

			$issue['issue_name'] = 'heading_hierarchy_skipped';
			$issue['issue_desc'] = array_map( 'wp_strip_all_tags', $skips );

			$issue['issue_priority'] = $analyzes['heading_hierarchy']['impact'];
			$this->saveIssue( $post->ID, $issue, $emitted_names );
		}

		if ( $is_long && $subheadings < $min_subheadings ) {
			$issue                                   = array();
			$issue['issue_type']                     = 'heading_hierarchy';
			$analyzes['heading_hierarchy']['impact'] = 'medium';
			$desc                                   .= '<p><span class="dashicons dashicons-no-alt sp-dashicons-not"></span>' . /* translators: %1$d word count, %2$d recommended minimum number of subheadings */ sprintf( esc_html__( 'Your content is long (%1$d words) but uses only %2$d subheadings. Break it into more sections with H2/H3 headings.', 'wp-seopress' ), $word_count, $subheadings ) . '</p>';

			$issue['issue_name']     = 'heading_hierarchy_too_few';
			$issue['issue_desc']     = array( $subheadings );
			$issue['issue_priority'] = $analyzes['heading_hierarchy']['impact'];
			$this->saveIssue( $post->ID, $issue, $emitted_names );
		}

		if ( 'good' === $analyzes['heading_hierarchy']['impact'] ) {
			$desc .= '<p><span class="dashicons dashicons-yes sp-dashicons-ok"></span>' . __( 'Your heading structure looks coherent. Good job!', 'wp-seopress' ) . '</p>';
		}

		$analyzes['heading_hierarchy']['desc'] = $desc;

		$this->cleanupResolvedIssues( $post->ID, 'heading_hierarchy', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeContentMedia function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeContentMedia( $analyzes, $data, $post ) {
		if ( $this->aiContentChecksUnreliable( $post ) ) {
			return $this->markCheckNotEvaluated( $analyzes, 'content_media', $post );
		}

		$issue               = array();
		$issue['issue_type'] = 'content_media';
		$emitted_names       = array();

		$images = isset( $data['images'] ) && is_array( $data['images'] ) ? count( $data['images'] ) : 0;
		$videos = isset( $data['content_structure']['videos'] ) ? (int) $data['content_structure']['videos'] : 0;
		$media  = $images + $videos;

		$word_count   = $this->getContentWordCount( $post );
		$long_content = (int) apply_filters( 'seopress_content_analysis_long_content_words', 900, $post );

		$desc = '<p>' . __( 'Google recommends including relevant, high-quality images and videos in your content. Rich media improves engagement and eligibility for AI experiences.', 'wp-seopress' ) . '</p>';

		if ( $word_count >= $long_content && 0 === $media ) {
			$analyzes['content_media']['impact'] = 'medium';
			$desc                               .= '<p><span class="dashicons dashicons-no-alt sp-dashicons-not"></span>' . /* translators: %d word count */ sprintf( esc_html__( 'Your content is long (%d words) but does not contain any image or video. Add relevant media to enrich it.', 'wp-seopress' ), $word_count ) . '</p>';

			$issue['issue_name'] = 'content_media_missing';
			$issue['issue_desc'] = array( $word_count );
		} else {
			$desc .= '<p><span class="dashicons dashicons-yes sp-dashicons-ok"></span>' . /* translators: %1$d number of images, %2$d number of videos */ sprintf( esc_html__( 'We found %1$d image(s) and %2$d video(s) in your content.', 'wp-seopress' ), $images, $videos ) . '</p>';
		}

		$analyzes['content_media']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['content_media']['impact'] ? $analyzes['content_media']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'content_media', $emitted_names );

		return $analyzes;
	}

	/**
	 * The analyzeContentStructure function.
	 *
	 * @param array   $analyzes The analyzes.
	 * @param array   $data The data.
	 * @param WP_Post $post The post.
	 *
	 * @return array
	 */
	protected function analyzeContentStructure( $analyzes, $data, $post ) {
		if ( $this->aiContentChecksUnreliable( $post ) ) {
			return $this->markCheckNotEvaluated( $analyzes, 'content_structure', $post );
		}

		$issue               = array();
		$issue['issue_type'] = 'content_structure';
		$emitted_names       = array();

		$outline = $this->resolveHeadingOutline( $data, $post );

		$subheadings = 0;
		foreach ( $outline as $level ) {
			if ( $level >= 2 ) {
				$subheadings++;
			}
		}

		$word_count        = $this->getContentWordCount( $post );
		$long_content      = (int) apply_filters( 'seopress_content_analysis_long_content_words', 900, $post );
		$words_per_section = (int) apply_filters( 'seopress_content_analysis_words_per_section', 300, $post );
		$sections          = $subheadings + 1;
		$ratio             = $sections > 0 ? (int) round( $word_count / $sections ) : $word_count;

		$desc = '<p>' . __( 'Avoid walls of text. Splitting long content into well-titled sections makes it easier to read and to surface in AI answers.', 'wp-seopress' ) . '</p>';

		if ( $word_count >= $long_content && $ratio > $words_per_section ) {
			$analyzes['content_structure']['impact'] = 'low';
			$desc                                   .= '<p><span class="dashicons dashicons-no-alt sp-dashicons-not"></span>' . /* translators: %1$d average words per section, %2$d recommended maximum words per section */ sprintf( esc_html__( 'Your sections average %1$d words. Add more subheadings to keep sections under ~%2$d words.', 'wp-seopress' ), $ratio, $words_per_section ) . '</p>';

			$issue['issue_name'] = 'content_wall_of_text';
			$issue['issue_desc'] = array( $ratio );
		} else {
			$desc .= '<p><span class="dashicons dashicons-yes sp-dashicons-ok"></span>' . __( 'Your content is well structured into sections. Good job!', 'wp-seopress' ) . '</p>';
		}

		$analyzes['content_structure']['desc'] = $desc;

		$issue['issue_priority'] = $analyzes['content_structure']['impact'] ? $analyzes['content_structure']['impact'] : 0;

		$this->saveIssue( $post->ID, $issue, $emitted_names );
		$this->cleanupResolvedIssues( $post->ID, 'content_structure', $emitted_names );

		return $analyzes;
	}

	/**
	 * The getAnalyzes function.
	 *
	 * @param WP_Post $post The post.
	 * @param string  $type The type.
	 *
	 * @return array
	 */
	public function getAnalyzes( $post, $type = '' ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( null === $post ) {
			return;
		}

		$data     = seopress_get_service( 'ContentAnalysisDatabase' )->getData( $post->ID );
		$analyzes = ContentAnalysis::getData();

		switch ( $type ) {
			case 'schemas':
				$analyzes = $this->analyzeSchemas( $analyzes, $data, $post );
				break;
			case 'old_post':
				$analyzes = $this->analyzeOldPost( $analyzes, $data, $post );
				break;
			case 'keywords_permalink':
				$analyzes = $this->analyzeKeywordsPermalink( $analyzes, $data, $post );
				break;
			case 'headings':
				$analyzes = $this->analyzeHeadings( $analyzes, $data, $post );
				break;
			case 'meta_title':
				$analyzes = $this->analyzeMetaTitle( $analyzes, $data, $post );
				break;
			case 'meta_description':
				$analyzes = $this->analyzeMetaDescription( $analyzes, $data, $post );
				break;
			case 'social_tags':
				$analyzes = $this->analyzeSocialTags( $analyzes, $data, $post );
				break;
			case 'robots':
				$analyzes = $this->analyzeRobots( $analyzes, $data, $post );
				break;
			case 'img_alt':
				$analyzes = $this->analyzeImgAlt( $analyzes, $data, $post );
				break;
			case 'nofollow_links':
				$analyzes = $this->analyzeNoFollowLinks( $analyzes, $data, $post );
				break;
			case 'outbound_links':
				$analyzes = $this->analyzeOutboundLinks( $analyzes, $data, $post );
				break;
			case 'internal_links':
				$analyzes = $this->analyzeInternalLinks( $analyzes, $data, $post );
				break;
			case 'canonical_url':
				$analyzes = $this->analyzeCanonical( $analyzes, $data, $post );
				break;
			case 'content_depth':
				$analyzes = $this->analyzeContentDepth( $analyzes, $data, $post );
				break;
			case 'heading_hierarchy':
				$analyzes = $this->analyzeHeadingHierarchy( $analyzes, $data, $post );
				break;
			case 'content_media':
				$analyzes = $this->analyzeContentMedia( $analyzes, $data, $post );
				break;
			case 'content_structure':
				$analyzes = $this->analyzeContentStructure( $analyzes, $data, $post );
				break;
			default:
				$analyzes = $this->analyzeSchemas( $analyzes, $data, $post );
				$analyzes = $this->analyzeOldPost( $analyzes, $data, $post );
				$analyzes = $this->analyzeKeywordsPermalink( $analyzes, $data, $post );
				$analyzes = $this->analyzeHeadings( $analyzes, $data, $post );
				$analyzes = $this->analyzeMetaTitle( $analyzes, $data, $post );
				$analyzes = $this->analyzeMetaDescription( $analyzes, $data, $post );
				$analyzes = $this->analyzeSocialTags( $analyzes, $data, $post );
				$analyzes = $this->analyzeRobots( $analyzes, $data, $post );
				$analyzes = $this->analyzeImgAlt( $analyzes, $data, $post );
				$analyzes = $this->analyzeNoFollowLinks( $analyzes, $data, $post );
				$analyzes = $this->analyzeOutboundLinks( $analyzes, $data, $post );
				$analyzes = $this->analyzeInternalLinks( $analyzes, $data, $post );
				$analyzes = $this->analyzeCanonical( $analyzes, $data, $post );
				$analyzes = $this->analyzeContentDepth( $analyzes, $data, $post );
				$analyzes = $this->analyzeHeadingHierarchy( $analyzes, $data, $post );
				$analyzes = $this->analyzeContentMedia( $analyzes, $data, $post );
				$analyzes = $this->analyzeContentStructure( $analyzes, $data, $post );
				break;
		}

		return $analyzes;
	}
}
