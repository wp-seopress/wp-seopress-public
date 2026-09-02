<?php
/**
 * SEOPress Admin Header functions.
 *
 * @package SEOPress
 * @subpackage Admin_Bar
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Admin header.
 *
 * The breadcrumb and activity panel (Help / Display / Notifications) are
 * now rendered by the React bundle enqueued by ModuleAdminHeader on every
 * SEOPress admin page. The function keeps the same signature so existing
 * callers do not change; it simply outputs the React mount point and the
 * top banner promotion (still PHP for now — will move into React in a
 * follow-up commit).
 *
 * @param string $context The context. Unused — kept for backward compat.
 * @return void
 */
function seopress_admin_header( $context = '' ) {
	?>

<div id="seopress-header-wrapper">
	<div id="seopress-admin-header-root"></div>
</div><!-- #seopress-header-wrapper -->
	<?php
}

/**
 * Settings page loading indicator.
 *
 * Outputs the WordPress spinner shown inside the React mount point
 * while JavaScript initialises.
 *
 * @return void
 */
function seopress_settings_skeleton() {
	?>
	<div style="display:flex;justify-content:center;align-items:center;min-height:200px;padding:40px"><span class="spinner is-active" style="float:none"></span></div>
	<?php
}

/**
 * Anchor telling WordPress where the admin notices belong on a SEOPress screen.
 *
 * WordPress prints admin notices at the very top of #wpbody-content, before the
 * page callback has echoed anything. On SEOPress screens that spot is covered by
 * #seopress-header-wrapper, which is fixed, so a notice ends up hidden behind the
 * header bar (only its last line sticks out) and its height still pushes the rest
 * of the page down, leaving a gap.
 *
 * wp-admin/js/common.js already solves this for core screens: it moves every
 * `div.notice`, `div.updated` and `div.error` after `.wp-header-end`, falling
 * back to the first `.wrap h1, .wrap h2`. Our screens have neither, so the move
 * was a no-op and the notices stayed behind the header.
 *
 * Output this right after the page title block — never inside .seopress-php-header
 * (the settings bundle empties it before portaling the React header into it) nor
 * inside #seopress-admin-settings-root (React owns that subtree).
 *
 * @since 10.2.1
 *
 * @return void
 */
function seopress_admin_notices_anchor() {
	echo '<hr class="wp-header-end seopress-notices-anchor">';
}
