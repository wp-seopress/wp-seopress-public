<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Services\TagsToString;

/**
 * Manage column
 */
class ManageColumn implements ExecuteHooksBackend {

	/**
	 * The TagsToString service.
	 *
	 * @var TagsToString
	 */
	protected $tags_to_string_service;

	/**
	 * The ManageColumn constructor.
	 *
	 * @since 4.4.0
	 */
	public function __construct() {
		$this->tags_to_string_service = seopress_get_service( 'TagsToString' );
	}

	/**
	 * The ManageColumn hooks.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function hooks() {
		global $pagenow;

		$is_edit_page        = in_array( $pagenow, array( 'edit.php', 'upload.php' ), true );
		$is_advanced_enabled = '1' === seopress_get_toggle_option( 'advanced' );

		if ( ( $is_edit_page && $is_advanced_enabled ) || wp_doing_ajax() ) {
			// Priority is important for plugins compatibility like Toolset.
			add_action( 'init', array( $this, 'setup' ), 20 );
		}
	}

	/**
	 * Setup the ManageColumn.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function setup() {
		$list_post_types = seopress_get_service( 'WordPressData' )->getPostTypes();

		if ( empty( $list_post_types ) ) {
			return;
		}

		foreach ( $list_post_types as $key => $value ) {
			if ( null === seopress_get_service( 'TitleOption' )->getSingleCptEnable( $key ) && '' !== $key ) {
				add_filter( 'manage_' . $key . '_posts_columns', array( $this, 'addColumn' ) );
				add_action( 'manage_' . $key . '_posts_custom_column', array( $this, 'displayColumn' ), 10, 2 );
				add_filter( 'manage_edit-' . $key . '_sortable_columns', array( $this, 'sortableColumn' ) );
				add_filter( 'pre_get_posts', array( $this, 'sortColumnsBy' ) );
			}
		}

		add_filter( 'manage_media_columns', array( $this, 'addMediaColumn' ) );
		add_action( 'manage_media_custom_column', array( $this, 'displayMediaColumn' ), 10, 2 );
		add_filter( 'manage_upload_sortable_columns', array( $this, 'sortableMediaColumn' ) );
		add_filter( 'pre_get_posts', array( $this, 'sortMediaColumnsBy' ) );

		add_filter( 'manage_edit-download_columns', array( $this, 'addColumn' ), 10, 2 );
	}

	/**
	 * Add column.
	 *
	 * @since 4.4.0
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function addColumn( $columns ) {
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceTitleCol() === '1' ) {
			$columns['seopress_title'] = __( 'Title tag', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceMetaDescriptionCol() === '1' ) {
			$columns['seopress_desc'] = __( 'Meta Desc.', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceRedirectEnableCol() === '1' ) {
			$columns['seopress_redirect_enable'] = __( 'Redirect?', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceRedirectUrlCol() === '1' ) {
			$columns['seopress_redirect_url'] = __( 'Redirect URL', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceCanonical() === '1' ) {
			$columns['seopress_canonical'] = __( 'Canonical', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceTargetKwCol() === '1' ) {
			$columns['seopress_tkw'] = __( 'Target Kw', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceNoIndexCol() === '1' ) {
			$columns['seopress_noindex'] = __( 'noindex?', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceNoFollowCol() === '1' ) {
			$columns['seopress_nofollow'] = __( 'nofollow?', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceInboundCol() === '1' ) {
			$columns['seopress_inbound'] = __( 'Inbound links', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceOutboundCol() === '1' ) {
			$columns['seopress_outbound'] = __( 'Outbound links', 'wp-seopress' );
		}
		if ( seopress_get_service( 'AdvancedOption' )->getAppearanceScoreCol() === '1' ) {
			$columns['seopress_score'] = __( 'Score', 'wp-seopress' );
		}

		return $columns;
	}

	/**
	 * Add media column.
	 *
	 * @since 7.2.0
	 * @see manage_media_columns
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function addMediaColumn( $columns ) {
		$columns['seopress_alt_text'] = __( 'Alt text', 'wp-seopress' );

		return $columns;
	}

	/**
	 * Display column.
	 *
	 * @since 4.4.0
	 * @see manage_' . $postType . '_posts_custom_column
	 *
	 * @param string $column   The column.
	 * @param int    $post_id  The post ID.
	 *
	 * @return void
	 */
	public function displayColumn( $column, $post_id ) {
		switch ( $column ) {
			case 'seopress_title':
				$meta_post_title = get_post_meta( $post_id, '_seopress_titles_title', true );

				$context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $post_id )->getContext();
				$title   = $this->tags_to_string_service->replace( $meta_post_title, $context );
				if ( empty( $title ) ) {
					$title = $meta_post_title;
				}
				printf( '<div id="seopress_title-%s">%s</div>', esc_attr( $post_id ), esc_html( $title ) );
				printf( '<div id="seopress_title_raw-%s" class="hidden">%s</div>', esc_attr( $post_id ), esc_html( $meta_post_title ) );
				break;

			case 'seopress_desc':
				$meta_description = get_post_meta( $post_id, '_seopress_titles_desc', true );
				$context          = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $post_id )->getContext();
				$description      = $this->tags_to_string_service->replace( $meta_description, $context );
				if ( empty( $description ) ) {
					$description = $meta_description;
				}
				printf( '<div id="seopress_desc-%s">%s</div>', esc_attr( $post_id ), esc_html( $description ) );
				printf( '<div id="seopress_desc_raw-%s" class="hidden">%s</div>', esc_attr( $post_id ), esc_html( $meta_description ) );
				break;

			case 'seopress_redirect_enable':
				if ( 'yes' === get_post_meta( $post_id, '_seopress_redirections_enabled', true ) ) {
					echo '<span class="dashicons dashicons-yes-alt"></span>';
				}
				break;
			case 'seopress_redirect_url':
				echo '<div id="seopress_redirect_url-' . esc_attr( $post_id ) . '">' . esc_html( get_post_meta( $post_id, '_seopress_redirections_value', true ) ) . '</div>';
				break;

			case 'seopress_canonical':
				echo '<div id="seopress_canonical-' . esc_attr( $post_id ) . '">' . esc_html( get_post_meta( $post_id, '_seopress_robots_canonical', true ) ) . '</div>';
				break;

			case 'seopress_tkw':
				echo '<div id="seopress_tkw-' . esc_attr( $post_id ) . '">' . esc_html( get_post_meta( $post_id, '_seopress_analysis_target_kw', true ) ) . '</div>';
				break;

			case 'seopress_noindex':
				if ( 'yes' === get_post_meta( $post_id, '_seopress_robots_index', true ) ) {
					echo '<span class="dashicons dashicons-hidden"></span><span class="screen-reader-text">' . esc_html__( 'noindex is on!', 'wp-seopress' ) . '</span>';
				}
				break;

			case 'seopress_nofollow':
				if ( 'yes' === get_post_meta( $post_id, '_seopress_robots_follow', true ) ) {
					echo '<span class="dashicons dashicons-yes"></span><span class="screen-reader-text">' . esc_html__( 'nofollow is on!', 'wp-seopress' ) . '</span>';
				}
				break;

			case 'seopress_inbound':
				$internal_links = seopress_get_service( 'ContentAnalysisDatabase' )->getData( $post_id, array( 'internal_links' ) );

				if ( ! empty( $internal_links ) ) {
					$count = count( $internal_links );
					echo '<div id="seopress_inbound-' . esc_attr( $post_id ) . '">' . esc_html( $count ) . '</div>';
					return;
				}

				/**
				 * This is deprecated
				 *
				 * @deprecated
				 * @since 7.3.0
				 * We don't use this anymore because we use the new table to get the data
				 */
				$data_api_analysis = get_post_meta( $post_id, '_seopress_content_analysis_api', true );

				if ( isset( $data_api_analysis['internal_links'] ) && null !== $data_api_analysis['internal_links'] ) {
					$count = $data_api_analysis['internal_links'];
					echo '<div id="seopress_inbound-' . esc_attr( $post_id ) . '">' . esc_html( $count ) . '</div>';
				} elseif ( get_post_meta( $post_id, '_seopress_analysis_data' ) ) {
					$data = get_post_meta( $post_id, '_seopress_analysis_data', true );

					if ( ! empty( $data['internal_links'] ) ) {
						$count = $data['internal_links']['count'];
						echo '<div id="seopress_inbound-' . esc_attr( $post_id ) . '">' . esc_html( $count ) . '</div>';
					}
				}
				break;

			case 'seopress_outbound':
				$internal_links = seopress_get_service( 'ContentAnalysisDatabase' )->getData( $post_id, array( 'outbound_links' ) );

				if ( ! empty( $internal_links ) ) {
					$count = count( $internal_links );
					echo '<div id="seopress_inbound-' . esc_attr( $post_id ) . '">' . esc_html( $count ) . '</div>';
					return;
				}

				/**
				 * This is deprecated
				 *
				 * @deprecated
				 * @since 7.3.0
				 * We don't use this anymore because we use the new table to get the data
				 */
				$data_api_analysis = get_post_meta( $post_id, '_seopress_content_analysis_api', true );

				if ( isset( $data_api_analysis['outbound_links'] ) && null !== $data_api_analysis['outbound_links'] ) {
					$count = $data_api_analysis['outbound_links'];
					echo '<div id="seopress_outbound-' . esc_attr( $post_id ) . '">' . esc_html( $count ) . '</div>';
				} elseif ( get_post_meta( $post_id, '_seopress_analysis_data' ) ) {
					$data = get_post_meta( $post_id, '_seopress_analysis_data', true );

					if ( ! empty( $data['outbound_links'] ) ) {
						$count = count( $data['outbound_links'] );
						echo '<div id="seopress_outbound-' . esc_attr( $post_id ) . '">' . esc_html( $count ) . '</div>';
					}
				}
				break;

			case 'seopress_score':
				$score = seopress_get_service( 'ContentAnalysisDatabase' )->getData( $post_id, array( 'score' ) );

				if ( ! isset( $score ) ) {
					return;
				}

				if ( ! empty( $score ) && is_array( $score ) ) {
					$score = array_shift( $score );

					// Validate that $score contains the expected values.
					if ( ! is_array( $score ) ) {
						return;
					}

					echo '<div class="analysis-score">';
					if ( in_array( 'medium', $score, true ) || in_array( 'high', $score, true ) ) {
						echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                        <circle id="bar" class="notgood" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
                    </svg><span class="screen-reader-text">' . esc_html__( 'Should be improved', 'wp-seopress' ) . '</span></p>';
					} else {
						echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                        <circle id="bar" class="good" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                    </svg><span class="screen-reader-text">' . esc_html__( 'Good', 'wp-seopress' ) . '</span></p>';
					}
					echo '</div>';
					return;
				}

				/**
				 * This is deprecated
				 *
				 * @deprecated
				 * @since 7.3.0
				 * We don't use this anymore because we use the new table to get the data
				 */
				$data_api_analysis = get_post_meta( $post_id, '_seopress_content_analysis_api', true );
				if ( isset( $data_api_analysis['score'] ) && null !== $data_api_analysis['score'] ) {
					echo '<div class="analysis-score">';
					if ( 'good' === $data_api_analysis['score'] ) {
						echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                        <circle id="bar" class="good" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                    </svg><span class="screen-reader-text">' . esc_html__( 'Good', 'wp-seopress' ) . '</span></p>';
					} else {
						echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                        <circle id="bar" class="notgood" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
                    </svg><span class="screen-reader-text">' . esc_html__( 'Should be improved', 'wp-seopress' ) . '</span></p>';
					}
					echo '</div>';
				} elseif ( get_post_meta( $post_id, '_seopress_analysis_data' ) ) {
						$ca = get_post_meta( $post_id, '_seopress_analysis_data' );
						echo '<div class="analysis-score">';
					if ( isset( $ca[0]['score'] ) && 1 === $ca[0]['score'] ) {
						echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
							<circle id="bar" class="good" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
						</svg><span class="screen-reader-text">' . esc_html__( 'Good', 'wp-seopress' ) . '</span></p>';
					} elseif ( isset( $ca[0]['score'] ) && '' === $ca[0]['score'] ) {
						echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
							<circle id="bar" class="notgood" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
						</svg><span class="screen-reader-text">' . esc_html__( 'Should be improved', 'wp-seopress' ) . '</span></p>';
					}
						echo '</div>';
				}
				break;
		}
	}

		/**
		 * Display media column.
		 *
		 * @since 7.2.0
		 * @see manage_media_custom_column
		 *
		 * @param string $column   The column.
		 * @param int    $post_id  The post ID.
		 *
		 * @return void
		 */
	public function displayMediaColumn( $column, $post_id ) {
		switch ( $column ) {
			case 'seopress_alt_text':
				echo esc_html( get_post_meta( $post_id, '_wp_attachment_image_alt', true ) );
		}
	}

	/**
	 * Sortable column.
	 *
	 * @since 6.5.0
	 * @see manage_edit' . $postType . '_sortable_columns
	 *
	 * @param string $columns The columns.
	 *
	 * @return array $columns
	 */
	public function sortableColumn( $columns ) {
		$columns['seopress_noindex']  = 'seopress_noindex';
		$columns['seopress_nofollow'] = 'seopress_nofollow';

		return $columns;
	}

	/**
	 * Sortable media column.
	 *
	 * @since 7.2.0
	 * @see manage_edit-media_sortable_columns
	 *
	 * @param string $columns The columns.
	 *
	 * @return array $columns
	 */
	public function sortableMediaColumn( $columns ) {
		$columns['seopress_alt_text'] = 'seopress_alt_text';

		return $columns;
	}

	/**
	 * Sort columns by.
	 *
	 * @since 6.5.0
	 * @see pre_get_posts
	 *
	 * @param string $query The query.
	 *
	 * @return void
	 */
	public function sortColumnsBy( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( 'seopress_noindex' === $orderby ) {
			$query->set( 'meta_key', '_seopress_robots_index' );
			$query->set( 'orderby', 'meta_value' );
		}
		if ( 'seopress_nofollow' === $orderby ) {
			$query->set( 'meta_key', '_seopress_robots_follow' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/**
	 * Sort media columns by.
	 *
	 * @since 7.2.0
	 * @see pre_get_posts
	 *
	 * @param string $query The query.
	 *
	 * @return void
	 */
	public function sortMediaColumnsBy( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( 'seopress_alt_text' === $orderby ) {
			$query->set( 'meta_key', '_wp_attachment_image_alt' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
