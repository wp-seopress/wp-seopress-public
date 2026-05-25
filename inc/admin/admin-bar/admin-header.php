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
