<?php
/**
 * Options advanced admin
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

// MANDATORY for using is_plugin_active.
require_once ABSPATH . 'wp-admin/includes/plugin.php';

add_filter( 'sanitize_file_name', 'seopress_image_seo_cleaning_filename', 10 );
/**
 * Cleaning attachments filename
 *
 * @param string $filename Filename.
 *
 * @return string $filename
 */
function seopress_image_seo_cleaning_filename( $filename ) {
	if ( seopress_get_service( 'AdvancedOption' )->getAdvancedCleaningFileName() === '1' ) {
		$filename = apply_filters( 'seopress_image_seo_before_cleaning', $filename );

		/* Force the file name in UTF-8 (encoding Windows / OS X / Linux) */
		$filename = wp_check_invalid_utf8( $filename, true );

		$char_not_clean = array( '/•/', '/·/', '/À/', '/Á/', '/Â/', '/Ã/', '/Ä/', '/Å/', '/Ç/', '/È/', '/É/', '/Ê/', '/Ë/', '/Ì/', '/Í/', '/Î/', '/Ï/', '/Ò/', '/Ó/', '/Ô/', '/Õ/', '/Ö/', '/Ù/', '/Ú/', '/Û/', '/Ü/', '/Ý/', '/à/', '/á/', '/â/', '/ã/', '/ä/', '/å/', '/ç/', '/è/', '/é/', '/ê/', '/ë/', '/ì/', '/í/', '/î/', '/ï/', '/ð/', '/ò/', '/ó/', '/ô/', '/õ/', '/ö/', '/ù/', '/ú/', '/û/', '/ü/', '/ý/', '/ÿ/', '/©/' );

		$char_not_clean = apply_filters( 'seopress_image_seo_clean_input', $char_not_clean );

		$clean = array( '-', '-', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'copy' );

		$clean = apply_filters( 'seopress_image_seo_clean_output', $clean );

		$friendly_filename = preg_replace( $char_not_clean, $clean, $filename );

		// After replacement, we destroy the last residues.
		$friendly_filename = preg_replace( '/\?/', '', $friendly_filename );

		// Remove uppercase.
		$friendly_filename = strtolower( $friendly_filename );

		$friendly_filename = apply_filters( 'seopress_image_seo_after_cleaning', $friendly_filename );

		$filename = $friendly_filename;
	}

	return $filename;
}

add_action( 'add_attachment', 'seopress_auto_image_attr' );
/**
 * Automatic image attributes
 *
 * @param string  $post_ID  Post ID.
 * @param boolean $bulk     Bulk.
 */
function seopress_auto_image_attr( $post_ID, $bulk = false ) {
	if ( '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoTitleEditor() ||
	'1' === seopress_get_service( 'AdvancedOption' )->getImageAutoAltEditor() ||
	'1' === seopress_get_service( 'AdvancedOption' )->getImageAutoCaptionEditor() ||
	'1' === seopress_get_service( 'AdvancedOption' )->getImageAutoDescriptionEditor() ||
	true === $bulk ) {
		if ( wp_attachment_is_image( $post_ID ) ) {
			$parent = get_post( $post_ID )->post_parent ? get_post( $post_ID )->post_parent : null;
			$cpt    = get_post_type( $parent ) ? get_post_type( $parent ) : null;

			$img_attr = '';
			if ( isset( $cpt ) && isset( $parent ) && in_array( $cpt, array( 'product', 'product_variation' ), true ) ) {
				$img_attr = get_post( $parent )->post_title; // Use the product title for WooCommerce products.
			} else {
				$img_attr = get_post( $post_ID )->post_title;
			}

			// Sanitize the title: remove hyphens, underscores & extra spaces.
			$img_attr = preg_replace( '%\s*[-_\s]+\s*%', ' ', $img_attr );

			// Lowercase attributes.
			$img_attr = strtolower( $img_attr );

			$img_attr = apply_filters( 'seopress_auto_image_title', $img_attr, $cpt, $parent, $post_ID );

			// Create an array with the image meta (Title, Caption, Description) to be updated.
			$img_attr_array = array( 'ID' => $post_ID ); // Image (ID) to be updated.

			if ( '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoTitleEditor() ) {
				$img_attr_array['post_title'] = $img_attr; // Set image Title.
			}

			if ( '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoCaptionEditor() ) {
				$img_attr_array['post_excerpt'] = $img_attr; // Set image Caption.
			}

			if ( '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoDescriptionEditor() ) {
				$img_attr_array['post_content'] = $img_attr; // Set image Desc.
			}

			$img_attr_array = apply_filters( 'seopress_auto_image_attr', $img_attr_array );

			// Set the image Alt-Text.
			if ( '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoAltEditor() || true === $bulk ) {
				update_post_meta( $post_ID, '_wp_attachment_image_alt', $img_attr );
			}

			// Set the image meta (e.g. Title, Excerpt, Content).
			if ( '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoTitleEditor() || '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoCaptionEditor() || '1' === seopress_get_service( 'AdvancedOption' )->getImageAutoDescriptionEditor() ) {
				wp_update_post( $img_attr_array );
			}
		}
	}
}

// Remove Content Analysis Metaboxe.
if ( seopress_get_service( 'AdvancedOption' )->getAppearanceCaMetaboxe() === '1' ) {
	/**
	 * Function to remove content analysis metaboxe.
	 *
	 * @return void
	 */
	function seopress_advanced_appearance_ca_metaboxe_hook() {
		add_filter( 'seopress_metaboxe_content_analysis', '__return_false' );
	}
	add_action( 'init', 'seopress_advanced_appearance_ca_metaboxe_hook', 999 );
}

// Bulk actions.
global $pagenow;
if ( 'edit.php' === $pagenow || 'edit-tags.php' === $pagenow ) {
	/**
	 * Function to add bulk action filters.
	 *
	 * @param string $key Key.
	 * @param array  $actions Actions.
	 *
	 * @return void
	 */
	function add_bulk_action_filters( $key, $actions ) {
		foreach ( $actions as $action => $handler ) {
			add_filter( 'bulk_actions-edit-' . $key, $action );
			add_filter( 'handle_bulk_actions-edit-' . $key, $handler, 10, 3 );
		}
	}

	// Define common actions and handlers.
	$common_actions = array(
		'seopress_bulk_actions_noindex'  => 'seopress_bulk_action_noindex_handler',
		'seopress_bulk_actions_index'    => 'seopress_bulk_action_index_handler',
		'seopress_bulk_actions_nofollow' => 'seopress_bulk_action_nofollow_handler',
		'seopress_bulk_actions_follow'   => 'seopress_bulk_action_follow_handler',
	);

	// Bulk actions for post types.
	$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
	if ( ! empty( $post_types ) ) {
		foreach ( $post_types as $key => $value ) {
			if ( null === seopress_get_service( 'TitleOption' )->getSingleCptEnable( $key ) && '' !== $key ) {
				$post_type_actions = $common_actions + array(
					'seopress_bulk_actions_redirect_enable' => 'seopress_bulk_action_redirect_enable_handler',
					'seopress_bulk_actions_redirect_disable' => 'seopress_bulk_action_redirect_disable_handler',
					'seopress_bulk_actions_add_instant_indexing' => 'seopress_bulk_action_add_instant_indexing_handler',
				);
				add_bulk_action_filters( $key, $post_type_actions );
			}
		}
	}

	// Bulk actions for taxonomies.
	$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();
	if ( ! empty( $taxonomies ) ) {
		foreach ( $taxonomies as $key => $value ) {
			if ( null === seopress_get_service( 'TitleOption' )->getTaxEnable( $key ) && '' !== $key ) {
				add_bulk_action_filters( $key, $common_actions );
			}
		}
	}

	// Bulk actions for WooCommerce products.
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		add_bulk_action_filters( 'product', $common_actions );
	}

	/**
	 * No index bulk actions
	 *
	 * @param array $bulk_actions No index bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_noindex( $bulk_actions ) {
		$bulk_actions['seopress_noindex'] = __( 'Enable noindex', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * No index bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_noindex_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_noindex' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			// Perform action for each post/term.
			update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
			update_term_meta( $post_id, '_seopress_robots_index', 'yes' );
		}
		$redirect_to = add_query_arg( 'bulk_noindex_posts', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_noindex_admin_notice' );

	/**
	 * No index bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_noindex_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_noindex_posts'] ) ) {
			$count = intval( $_REQUEST['bulk_noindex_posts'] );
			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of posts set to noindex */
					_n(
						'%s post to noindex.',
						'%s posts to noindex.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * Index bulk actions
	 *
	 * @param array $bulk_actions Index bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_index( $bulk_actions ) {
		$bulk_actions['seopress_index'] = __( 'Enable index', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * Index bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_index_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_index' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			// Perform action for each post.
			delete_post_meta( $post_id, '_seopress_robots_index', '' );
			delete_term_meta( $post_id, '_seopress_robots_index', '' );
		}
		$redirect_to = add_query_arg( 'bulk_index_posts', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_index_admin_notice' );

	/**
	 * Index bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_index_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_index_posts'] ) ) {
			$count = intval( $_REQUEST['bulk_index_posts'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of posts set to index */
					_n(
						'%s post to index.',
						'%s posts to index.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * No follow bulk actions
	 *
	 * @param array $bulk_actions No follow bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_nofollow( $bulk_actions ) {
		$bulk_actions['seopress_nofollow'] = __( 'Enable nofollow', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * No follow bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_nofollow_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_nofollow' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			// Perform action for each post/term.
			update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
			update_term_meta( $post_id, '_seopress_robots_follow', 'yes' );
		}
		$redirect_to = add_query_arg( 'bulk_nofollow_posts', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_nofollow_admin_notice' );

	/**
	 * No follow bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_nofollow_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_nofollow_posts'] ) ) {
			$count = intval( $_REQUEST['bulk_nofollow_posts'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of posts set to nofollow */
					_n(
						'%s post to nofollow.',
						'%s posts to nofollow.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * Follow bulk actions
	 *
	 * @param array $bulk_actions Follow bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_follow( $bulk_actions ) {
		$bulk_actions['seopress_follow'] = __( 'Enable follow', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * Follow bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_follow_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_follow' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			// Perform action for each post/term.
			delete_post_meta( $post_id, '_seopress_robots_follow' );
			delete_term_meta( $post_id, '_seopress_robots_follow' );
		}
		$redirect_to = add_query_arg( 'bulk_follow_posts', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_follow_admin_notice' );

	/**
	 * Follow bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_follow_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_follow_posts'] ) ) {
			$count = intval( $_REQUEST['bulk_follow_posts'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of posts set to follow */
					_n(
						'%s post to follow.',
						'%s posts to follow.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * Enable 301 bulk actions
	 *
	 * @param array $bulk_actions Enable 301 bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_redirect_enable( $bulk_actions ) {
		$bulk_actions['seopress_enable'] = __( 'Enable redirection', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * Enable 301 bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_redirect_enable_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_enable' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			// Perform action for each post/term.
			update_post_meta( $post_id, '_seopress_redirections_enabled', 'yes' );
		}
		$redirect_to = add_query_arg( 'bulk_enable_redirects_posts', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_redirect_enable_admin_notice' );

	/**
	 * Enable 301 bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_redirect_enable_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_enable_redirects_posts'] ) ) {
			$count = intval( $_REQUEST['bulk_enable_redirects_posts'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of redirections set to enabled */
					_n(
						'%s redirection enabled.',
						'%s redirections enabled.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * Disable 301 bulk actions
	 *
	 * @param array $bulk_actions Disable 301 bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_redirect_disable( $bulk_actions ) {
		$bulk_actions['seopress_disable'] = __( 'Disable redirection', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * Disable 301 bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_redirect_disable_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_disable' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			// Perform action for each post/term.
			update_post_meta( $post_id, '_seopress_redirections_enabled', '' );
		}
		$redirect_to = add_query_arg( 'bulk_disable_redirects_posts', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_redirect_disable_admin_notice' );
	/**
	 * Disable 301 bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_redirect_disable_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_disable_redirects_posts'] ) ) {
			$count = intval( $_REQUEST['bulk_disable_redirects_posts'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of redirections set to disabled */
					_n(
						'%s redirection disabled.',
						'%s redirections disabled.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * Add to instant indexing bulk actions
	 *
	 * @param array $bulk_actions Add to instant indexing bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_add_instant_indexing( $bulk_actions ) {
		$bulk_actions['seopress_instant_indexing'] = __( 'Add to instant indexing queue', 'wp-seopress' );

		return $bulk_actions;
	}

	/**
	 * Add to instant indexing bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_action_add_instant_indexing_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_instant_indexing' !== $doaction ) {
			return $redirect_to;
		}

		if ( ! empty( $post_ids ) ) {
			$urls    = '';
			$options = get_option( 'seopress_instant_indexing_option_name' );
			$check   = isset( $options['seopress_instant_indexing_manual_batch'] ) ? esc_attr( $options['seopress_instant_indexing_manual_batch'] ) : null;

			foreach ( $post_ids as $post_id ) {
				// Perform action for each post/term.
				$urls .= esc_url( get_the_permalink( $post_id ) ) . "\n";
			}

			$urls = $check . "\n" . $urls;

			$urls = implode( "\n", array_unique( explode( "\n", $urls ) ) );
			$options['seopress_instant_indexing_manual_batch'] = $urls;

			update_option( 'seopress_instant_indexing_option_name', $options );
		}
		$redirect_to = add_query_arg( 'bulk_add_instant_indexing', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_action_add_instant_indexing_admin_notice' );

	/**
	 * Add to instant indexing bulk action admin notice
	 *
	 * @return void
	 */
	function seopress_bulk_action_add_instant_indexing_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_add_instant_indexing'] ) ) {
			$count = intval( $_REQUEST['bulk_add_instant_indexing'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of posts added to instant indexing queue */
					_n(
						'%s post added to instant indexing queue.',
						'%s posts added to instant indexing queue.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}
}

if ( 'upload.php' === $pagenow ) {
	add_filter( 'bulk_actions-upload', 'seopress_bulk_actions_alt_text' );

	/**
	 * Bulk action to generate alt text for images
	 *
	 * @param array $bulk_actions Bulk actions.
	 *
	 * @return array $bulk_actions
	 */
	function seopress_bulk_actions_alt_text( $bulk_actions ) {
		$bulk_actions['seopress_alt_text'] = __( 'Generate alt text from filename', 'wp-seopress' );

		return $bulk_actions;
	}

	add_filter( 'handle_bulk_actions-upload', 'seopress_bulk_actions_alt_text_handler', 10, 3 );

	/**
	 * Bulk action to generate alt text for images
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction Do action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string $redirect_to
	 */
	function seopress_bulk_actions_alt_text_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'seopress_alt_text' !== $doaction ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			seopress_auto_image_attr( $post_id, true );
		}
		$redirect_to = add_query_arg( 'bulk_alt_text', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	add_action( 'admin_notices', 'seopress_bulk_actions_alt_text_notice' );

	/**
	 * Bulk action to generate alt text for images
	 *
	 * @return void
	 */
	function seopress_bulk_actions_alt_text_notice() {
		if ( ! empty( $_REQUEST['bulk_alt_text'] ) ) {
			$count = intval( $_REQUEST['bulk_alt_text'] );

			echo '<div id="message" class="updated fade"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s number of media */
					_n(
						'%s alternative text generated from filename.',
						'%s alternative texts generated from filename.',
						$count,
						'wp-seopress'
					),
					number_format_i18n( $count )
				)
			);
			echo '</p></div>';
		}
	}
}

// Quick Edit.
if ( 'edit.php' === $pagenow ) {
	add_action( 'quick_edit_custom_box', 'seopress_bulk_quick_edit_custom_box', 10, 2 );

	/**
	 * Quick edit custom box
	 *
	 * @param string $column_name Column name.
	 *
	 * @return void
	 */
	function seopress_bulk_quick_edit_custom_box( $column_name ) {
		if ( is_plugin_active( 'admin-columns-pro/admin-columns-pro.php' ) ) {
			return;
		}

		static $print_nonce = true;
		if ( $print_nonce ) {
			$print_nonce = false;
			wp_nonce_field( plugin_basename( __FILE__ ), 'seopress_title_edit_nonce' );
		} ?>
	<div class="wp-clearfix"></div>
	<fieldset class="inline-edit-col-left">
		<div class="inline-edit-col column-<?php echo esc_html( $column_name ); ?>">

			<?php
			switch ( $column_name ) {
				case 'seopress_title':
					?>
			<h4><?php esc_html_e( 'SEO', 'wp-seopress' ); ?>
			</h4>
			<label class="inline-edit-group">
				<span class="title"><?php esc_html_e( 'Title tag', 'wp-seopress' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="seopress_title" /></span>
			</label>
					<?php
					break;
				case 'seopress_desc':
					?>
			<label class="inline-edit-group">
				<span class="title"><?php esc_html_e( 'Meta description', 'wp-seopress' ); ?></span>
				<span class="input-text-wrap"><textarea cols="18" rows="1" name="seopress_desc" autocomplete="off"
						role="combobox" aria-autocomplete="list" aria-expanded="false"></textarea></span>
			</label>
					<?php
					break;
				case 'seopress_tkw':
					?>
			<label class="inline-edit-group">
				<span class="title"><?php esc_html_e( 'Target keywords', 'wp-seopress' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="seopress_tkw" /></span>
			</label>
					<?php
					break;
				case 'seopress_canonical':
					?>
			<label class="inline-edit-group">
				<span class="title"><?php esc_html_e( 'Canonical', 'wp-seopress' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="seopress_canonical" /></span>
			</label>
					<?php
					break;
				case 'seopress_noindex':
					?>
			<label class="alignleft">
				<input type="checkbox" name="seopress_noindex" value="yes">
				<span class="checkbox-title"><?php echo wp_kses_post( __( 'Do not display this page in search engine results / XML - HTML sitemaps <strong>(noindex)</strong>', 'wp-seopress' ) ); ?></span>
			</label>
					<?php
					break;
				case 'seopress_nofollow':
					?>
			<label class="alignleft">
				<input type="checkbox" name="seopress_nofollow" value="yes">
				<span class="checkbox-title"><?php echo wp_kses_post( __( 'Do not follow links for this page <strong>(nofollow)</strong>', 'wp-seopress' ) ); ?></span>
			</label>
					<?php
					break;
				case 'seopress_redirect_enable':
					?>
			<label class="alignleft">
				<input type="checkbox" name="seopress_redirections_enabled" value="yes">
				<span class="checkbox-title"><?php echo wp_kses_post( __( 'Enable redirection?', 'wp-seopress' ) ); ?></span>
			</label>
					<?php
					break;
				case 'seopress_redirect_url':
					?>
			<label class="inline-edit-group">
				<span class="title"><?php echo wp_kses_post( __( 'New URL', 'wp-seopress' ) ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="seopress_redirections_value" />
				</span>
			</label>
					<?php
					break;
				default:
					break;
			}
			?>
		</div>
	</fieldset>
		<?php
	}
}

add_action( 'save_post', 'seopress_bulk_quick_edit_save_post', 10, 2 );

/**
 * Quick edit save post
 *
 * @param string $post_id  Post ID.
 */
function seopress_bulk_quick_edit_save_post( $post_id ) {
	// don't save if Elementor library.
	if ( isset( $_REQUEST['post_type'] ) && 'elementor_library' === $_REQUEST['post_type'] ) {
		return $post_id;
	}

	// don't save for autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// dont save for revisions.
	if ( isset( $_REQUEST['post_type'] ) && 'revision' === $_REQUEST['post_type'] ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_posts', $post_id ) ) {
		return;
	}

	$_REQUEST += array( 'seopress_title_edit_nonce' => '' );

	if ( ! isset( $_REQUEST['seopress_title_edit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['seopress_title_edit_nonce'] ) ), plugin_basename( __FILE__ ) ) ) {
		return;
	}
	if ( isset( $_REQUEST['seopress_title'] ) ) {
		update_post_meta( $post_id, '_seopress_titles_title', sanitize_text_field( wp_unslash( $_REQUEST['seopress_title'] ) ) );
	}
	if ( isset( $_REQUEST['seopress_desc'] ) ) {
		update_post_meta( $post_id, '_seopress_titles_desc', sanitize_textarea_field( wp_unslash( $_REQUEST['seopress_desc'] ) ) );
	}
	if ( isset( $_REQUEST['seopress_tkw'] ) ) {
		update_post_meta( $post_id, '_seopress_analysis_target_kw', sanitize_text_field( wp_unslash( $_REQUEST['seopress_tkw'] ) ) );
	}
	if ( isset( $_REQUEST['seopress_canonical'] ) ) {
		update_post_meta( $post_id, '_seopress_robots_canonical', sanitize_url( wp_unslash( $_REQUEST['seopress_canonical'] ) ) );
	}
	if ( seopress_get_service( 'AdvancedOption' )->getAppearanceNoIndexCol() === '1' ) {
		if ( isset( $_REQUEST['seopress_noindex'] ) ) {
			update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
		} else {
			delete_post_meta( $post_id, '_seopress_robots_index' );
		}
	}
	if ( seopress_get_service( 'AdvancedOption' )->getAppearanceNoFollowCol() === '1' ) {
		if ( isset( $_REQUEST['seopress_nofollow'] ) ) {
			update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
		} else {
			delete_post_meta( $post_id, '_seopress_robots_follow' );
		}
	}

	// Elementor sync.
	if ( did_action( 'elementor/loaded' ) ) {
		$elementor = get_post_meta( $post_id, '_elementor_page_settings', true );

		if ( ! empty( $elementor ) ) {
			if ( isset( $_REQUEST['seopress_title'] ) ) {
				$elementor['_seopress_titles_title'] = sanitize_text_field( wp_unslash( $_REQUEST['seopress_title'] ) );
			}
			if ( isset( $_REQUEST['seopress_desc'] ) ) {
				$elementor['_seopress_titles_desc'] = sanitize_textarea_field( wp_unslash( $_REQUEST['seopress_desc'] ) );
			}
			if ( isset( $_REQUEST['seopress_noindex'] ) ) {
				$elementor['_seopress_robots_index'] = 'yes';
			} else {
				$elementor['_seopress_robots_index'] = '';
			}
			if ( isset( $_REQUEST['seopress_nofollow'] ) ) {
				$elementor['_seopress_robots_follow'] = 'yes';
			} else {
				$elementor['_seopress_robots_follow'] = '';
			}
			if ( isset( $_REQUEST['seopress_canonical'] ) ) {
				$elementor['_seopress_robots_canonical'] = sanitize_url( wp_unslash( $_REQUEST['seopress_canonical'] ) );
			}
			if ( isset( $_REQUEST['seopress_tkw'] ) ) {
				$elementor['_seopress_analysis_target_kw'] = sanitize_text_field( wp_unslash( $_REQUEST['seopress_tkw'] ) );
			}
			update_post_meta( $post_id, '_elementor_page_settings', $elementor );
		}
	}
}

// WP Editor on taxonomy description field.
if ( seopress_get_service( 'AdvancedOption' )->getAdvancedTaxDescEditor() === '1' && current_user_can( 'publish_posts' ) ) {

	/**
	 * Taxonomy description WP editor init
	 *
	 * @return void
	 */
	function seopress_tax_desc_wp_editor_init() {
		global $pagenow;

		if ( 'term.php' === $pagenow || 'edit-tags.php' === $pagenow ) {
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
			remove_filter( 'term_description', 'wp_kses_data' );

			// Disallow HTML Tags.
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				add_filter( 'pre_term_description', 'wp_kses_post' );
				add_filter( 'term_description', 'wp_kses_post' );
			}

			// Allow HTML Tags.
			add_filter( 'term_description', 'wptexturize' );
			add_filter( 'term_description', 'convert_smilies' );
			add_filter( 'term_description', 'convert_chars' );
			add_filter( 'term_description', 'wpautop' );
		}
	}
	add_action( 'init', 'seopress_tax_desc_wp_editor_init', 100 );

	/**
	 * Taxonomy description WP editor.
	 *
	 * @param string $tag  Tag.
	 *
	 * @return void
	 */
	function seopress_tax_desc_wp_editor( $tag ) {
		global $pagenow;
		if ( 'term.php' === $pagenow || 'edit-tags.php' === $pagenow ) {
			$content = '';

			if ( 'term.php' === $pagenow ) {
				$editor_id = 'description';
			} elseif ( 'edit-tags.php' === $pagenow ) {
				$editor_id = 'tag-description';
			}
			?>

<tr class="form-field term-description-wrap">
	<th scope="row"><label for="description"><?php esc_html_e( 'Description', 'wp-seopress' ); ?></label></th>
	<td>
			<?php
			$settings = array(
				'textarea_name' => 'description',
				'textarea_rows' => 10,
			);
			wp_editor( htmlspecialchars_decode( $tag->description ), 'html-tag-description', $settings );
			?>
		<p class="description"><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.', 'wp-seopress' ); ?>
		</p>
	</td>
	<script type="text/javascript">
		// Remove default description field.
		jQuery('textarea#description').closest('.form-field').remove();
	</script>
</tr>

			<?php
		}
	}
	$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();
	if ( ! empty( $taxonomies ) ) {
		foreach ( $taxonomies as $key => $value ) {
			add_action( $key . '_edit_form_fields', 'seopress_tax_desc_wp_editor', 9, 1 );
		}
	}
}
