<?php
/**
 * SEOPress Admin Bar functions.
 *
 * @package SEOPress
 * @subpackage Admin_Bar
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Admin bar customization.
 */
function seopress_admin_bar_links() {
	if ( ! current_user_can( seopress_capability( 'manage_options', 'admin_bar' ) ) ) {
		return;
	}

	global $wp_admin_bar;

	$hide_admin_bar = '1' === seopress_get_service( 'AdvancedOption' )->getAppearanceAdminBar();

	// Critical noindex warning. Built first so it can still be displayed when the
	// user removed SEOPress from the admin bar, to avoid leaving a site noindexed
	// without any visible alert.
	$noindex = '';

	if ( '1' !== seopress_get_service( 'AdvancedOption' )->getAppearanceAdminBarNoIndex() ) {
		$metarobots = false;

		// Get the object ID.
		$current_id = get_queried_object_id() ? get_queried_object_id() : null;

		if ( get_post_meta( $current_id, '_seopress_robots_index', true ) && is_singular() ) {
			$metarobots = true;
		} elseif ( isset( $current_id ) && (
			seopress_get_service( 'TitleOption' )->getSingleCptNoIndex( $current_id ) ||
			seopress_get_service( 'TitleOption' )->getTitleNoIndex() ||
			true === post_password_required( $current_id )
		) ) {
			$metarobots = true;
		} elseif (
			'1' === seopress_get_service( 'TitleOption' )->getTitleNoIndex() ||
			'1' !== get_option( 'blog_public' )
		) {
			$metarobots = true;
		}

		if ( true === $metarobots ) {
			$noindex  = '<a class="wrap-seopress-noindex" href="' . admin_url( 'admin.php?page=seopress-titles#tab=tab_seopress_titles_advanced' ) . '">';
			$noindex .= '<span class="ab-icon dashicons dashicons-hidden"></span>';
			$noindex .= __( 'noindex is on!', 'wp-seopress' );
			$noindex .= '</a>';
		}

		$noindex = apply_filters( 'seopress_adminbar_noindex', $noindex ?? '' );
	}

	// When SEOPress is removed from the admin bar, still surface the noindex
	// warning on its own as it is a critical alert (e.g. a site accidentally
	// left noindexed). Nothing is displayed when there is no warning, so the
	// admin bar stays clean. Users who also want to hide this warning can enable
	// "Hide noindex warning in admin bar".
	if ( $hide_admin_bar ) {
		if ( '' === $noindex ) {
			return;
		}

		// Render the warning as a self-contained admin bar item (own markup +
		// alert styling). The in-menu badge ($noindex) assumes the full SEO menu
		// context and, used alone, would nest anchors and break the layout.
		$wp_admin_bar->add_menu(
			array(
				'parent' => false,
				'id'     => 'seopress',
				'title'  => '<span class="ab-icon dashicons dashicons-hidden"></span>' . __( 'noindex is on!', 'wp-seopress' ),
				'href'   => admin_url( 'admin.php?page=seopress-titles#tab=tab_seopress_titles_advanced' ),
				'meta'   => array(
					'class' => 'seopress-adminbar-noindex-alert',
				),
			)
		);

		return;
	}

	$notifications = seopress_get_service( 'Notifications' )->getSeverityNotification( 'all' );
	$total         = 0;

	if ( ! empty( $notifications['total'] ) ) {
		$total = $notifications['total'];
	}

	$title = '<div id="seopress-ab-icon" class="ab-item svg seopress-logo" style="background-image: url(data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSItMjQgLTI0IDI0MiAyNDIiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTEyMC42MjYgMTI5LjY4NEMxMjIuODMzIDEyOS43NjMgMTI0LjkyOSAxMzAuNjc1IDEyNi40OTEgMTMyLjIzN0MxMjguMDUzIDEzMy43OTkgMTI4Ljk2NSAxMzUuODk1IDEyOS4wNDQgMTM4LjEwMkMxMjkuMTIyIDE0MC4zMSAxMjguMzYyIDE0Mi40NjUgMTI2LjkxNSAxNDQuMTM0TDEyNi44NTUgMTQ0LjIwMkwxMjYuNzkyIDE0NC4yNjVMODEuOTg1MSAxODkuMDA4TDgxLjkyMDcgMTg5LjA3NEw4MS44NTEzIDE4OS4xMzNDODAuMTgxMSAxOTAuNTczIDc4LjAyODIgMTkxLjMyNyA3NS44MjUgMTkxLjI0NkM3My42MjE1IDE5MS4xNjQgNzEuNTI5OCAxOTAuMjUzIDY5Ljk3MDUgMTg4LjY5NEM2OC40MTExIDE4Ny4xMzUgNjcuNDk4OCAxODUuMDQ0IDY3LjQxNjcgMTgyLjg0QzY3LjMzNDcgMTgwLjYzNyA2OC4wODkgMTc4LjQ4NCA2OS41MjgxIDE3Ni44MTNMNjkuNTg5NiAxNzYuNzQzTDY5LjY1NiAxNzYuNjc2TDExNC40NjUgMTMxLjkzM0wxMTQuNTI3IDEzMS44NzFMMTE0LjU5NCAxMzEuODEzQzExNi4yNjMgMTMwLjM2NiAxMTguNDE4IDEyOS42MDUgMTIwLjYyNiAxMjkuNjg0WiIgZmlsbD0iI2E3YWFhZCIvPjxwYXRoIGQ9Ik01NS43MjY2IDY0Ljc4NTZDNTcuOTM0MSA2NC44NjQzIDYwLjAyOTggNjUuNzc2NCA2MS41OTE4IDY3LjMzODRDNjMuMTUzNyA2OC45MDAzIDY0LjA2NTkgNzAuOTk2MSA2NC4xNDQ1IDczLjIwMzZDNjQuMjIzMSA3NS40MTExIDYzLjQ2MjUgNzcuNTY2OCA2Mi4wMTU2IDc5LjIzNThMNjEuOTU3IDc5LjMwMzJMNjEuODkyNiA3OS4zNjY3TDE3LjgxODQgMTIzLjM2N1YxMjMuNDgzTDE2Ljk1NjEgMTI0LjIzQzE1LjI4NyAxMjUuNjc3IDEzLjEzMTMgMTI2LjQzNyAxMC45MjM4IDEyNi4zNTlDOC43MTYzMSAxMjYuMjggNi42MjA1NiAxMjUuMzY4IDUuMDU4NTkgMTIzLjgwNkMzLjQ5NjYzIDEyMi4yNDQgMi41ODQ0OSAxMjAuMTQ4IDIuNTA1ODYgMTE3Ljk0MUMyLjQyNzI2IDExNS43MzMgMy4xODc5MSAxMTMuNTc4IDQuNjM0NzcgMTExLjkwOUw0LjY5MzM2IDExMS44NDFMNC43NTY4NCAxMTEuNzc4TDQ5LjU2NTQgNjcuMDM0N0w0OS42Mjc5IDY2Ljk3MjJMNDkuNjk0MyA2Ni45MTQ2QzUxLjM2MzUgNjUuNDY3NyA1My41MTkgNjQuNzA3IDU1LjcyNjYgNjQuNzg1NloiIGZpbGw9IiNhN2FhYWQiLz48cGF0aCBkPSJNMTMzLjQ2NiAyLjU5NzY2QzE0Ni44MTEgMS43OTcyMiAxNTkuOTg2IDUuOTE3OTkgMTcwLjQ5NyAxNC4xNzk3QzE5NC4yMTggMzIuODI0NiAxOTguMzMzIDY3LjE2ODcgMTc5LjY4OCA5MC44ODk2QzE2MS41MDQgMTE0LjAyNSAxMjguMzg1IDExOC41MTEgMTA0Ljc1NCAxMDEuNDJMNDkuNTgyIDE1Ni41OTRMNDkuNTE2NiAxNTYuNjU5TDQ5LjQ0NjMgMTU2LjcyQzQ3Ljc3NiAxNTguMTU5IDQ1LjYyMjQgMTU4LjkxNSA0My40MTg5IDE1OC44MzNDNDEuMjE1NiAxNTguNzUxIDM5LjEyNDYgMTU3LjgzOSAzNy41NjU0IDE1Ni4yOEMzNi4wMDYxIDE1NC43MjEgMzUuMDkzOCAxNTIuNjMgMzUuMDExNyAxNTAuNDI3QzM0LjkyOTcgMTQ4LjIyNCAzNS42ODQyIDE0Ni4wNzEgMzcuMTIzIDE0NC40TDM3LjE4MzYgMTQ0LjMyOUwzNy4yNDkgMTQ0LjI2NEw5Mi40Mjc3IDg5LjA4NjlDODUuMDc3OSA3OC44OTY0IDgxLjQ0OTIgNjYuNDYyMyA4Mi4yMDEyIDUzLjg3NUM4Mi45OTc0IDQwLjU0NjIgODguNjQ3MyAyNy45NzAxIDk4LjA4NCAxOC41MjM0QzEwNy41MzIgOS4wNjQ0NiAxMjAuMTIxIDMuMzk4MiAxMzMuNDY2IDIuNTk3NjZaTTE2Mi43NjkgMzEuMDg5OEMxNDguMzc2IDE2LjY5OTIgMTI1LjA0MyAxNi42OTcxIDExMC42NTIgMzEuMDg4OUM5Ni4yNjE2IDQ1LjQ4MTYgOTYuMjU5NSA2OC44MTU5IDExMC42NTEgODMuMjA2MUMxMjUuMDQ0IDk3LjU5NjYgMTQ4LjM3NiA5Ny41OTY1IDE2Mi43NjkgODMuMjA2MUwxNjMuNTgyIDgyLjM5MTZDMTc3LjE3MiA2Ny45Mzk3IDE3Ni44OTQgNDUuMjEzNSAxNjIuNzY5IDMxLjA4OThaIiBmaWxsPSIjYTdhYWFkIi8+PC9zdmc+) !important"></div> ' . __( 'SEO', 'wp-seopress' );

	$title = apply_filters( 'seopress_adminbar_icon', $title );

	$counter = '';
	if ( '1' !== seopress_get_service( 'AdvancedOption' )->getAppearanceAdminBarCounter() && $total > 0 ) {
		$counter = '<div class="wp-core-ui wp-ui-notification seopress-menu-notification-counter">' . $total . '</div>';

		$counter = apply_filters( 'seopress_adminbar_counter', $counter, $total );
	}

	// Adds a new top level admin bar link and a submenu to it.
	$wp_admin_bar->add_menu(
		array(
			'parent' => false,
			'id'     => 'seopress',
			'title'  => $title . $counter . $noindex,
			'href'   => admin_url( 'admin.php?page=seopress-option' ),
		)
	);

	// noindex/nofollow per CPT.
	if ( function_exists( 'get_current_screen' ) && null !== get_current_screen() ) {
		if ( get_current_screen()->post_type || get_current_screen()->taxonomy ) {
			$robots = '';

			$options = get_option( 'seopress_titles_option_name' );

			if ( get_current_screen()->taxonomy ) {
				$noindex  = isset( $options['seopress_titles_single_titles'][ get_current_screen()->taxonomy ]['noindex'] );
				$nofollow = isset( $options['seopress_titles_single_titles'][ get_current_screen()->taxonomy ]['nofollow'] );
			} else {
				$noindex  = isset( $options['seopress_titles_single_titles'][ get_current_screen()->post_type ]['noindex'] );
				$nofollow = isset( $options['seopress_titles_single_titles'][ get_current_screen()->post_type ]['nofollow'] );
			}

			if ( get_current_screen()->taxonomy ) {
				/* translators: %s taxonomy name */
				$robots .= '<span class="wrap-seopress-cpt-seo">' . sprintf( __( 'SEO for "%s"', 'wp-seopress' ), get_current_screen()->taxonomy ) . '</span>';
			} else {
				/* translators: %s custom post type name */
				$robots .= '<span class="wrap-seopress-cpt-seo">' . sprintf( __( 'SEO for "%s"', 'wp-seopress' ), get_current_screen()->post_type ) . '</span>';
			}
			$robots .= '<span class="wrap-seopress-cpt-noindex">';

			if ( true === $noindex ) {
				$robots .= '<span class="ab-icon dashicons dashicons-marker on"></span>';
				$robots .= __( 'noindex is on!', 'wp-seopress' );
			} else {
				$robots .= '<span class="ab-icon dashicons dashicons-marker off"></span>';
				$robots .= __( 'noindex is off.', 'wp-seopress' );
			}

			$robots .= '</span>';

			$robots .= '<span class="wrap-seopress-cpt-nofollow">';

			if ( true === $nofollow ) {
				$robots .= '<span class="ab-icon dashicons dashicons-marker on"></span>';
				$robots .= __( 'nofollow is on!', 'wp-seopress' );
			} else {
				$robots .= '<span class="ab-icon dashicons dashicons-marker off"></span>';
				$robots .= __( 'nofollow is off.', 'wp-seopress' );
			}

			$robots .= '</span>';

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'seopress',
					'id'     => 'seopress_custom_sub_menu_meta_robots',
					'title'  => $robots,
					'href'   => admin_url( 'admin.php?page=seopress-titles' ),
				)
			);
		}
	}

	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_titles',
			'title'  => __( 'Titles & Metas', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-titles' ),
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_xml_sitemap',
			'title'  => __( 'XML - HTML Sitemap', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-xml-sitemap' ),
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_social',
			'title'  => __( 'Social Networks', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-social' ),
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_google_analytics',
			'title'  => __( 'Analytics', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-google-analytics' ),
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_instant_indexing',
			'title'  => __( 'Instant Indexing', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-instant-indexing' ),
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_advanced',
			'title'  => __( 'Advanced', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-advanced' ),
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_import_export',
			'title'  => __( 'Tools', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-import-export' ),
		)
	);

	do_action( 'seopress_admin_bar_items' );

	$wp_admin_bar->add_menu(
		array(
			'parent' => 'seopress',
			'id'     => 'seopress_custom_sub_menu_wizard',
			'title'  => __( 'Configuration wizard', 'wp-seopress' ),
			'href'   => admin_url( 'admin.php?page=seopress-setup' ),
		)
	);
}
add_action( 'admin_bar_menu', 'seopress_admin_bar_links', 99 );
