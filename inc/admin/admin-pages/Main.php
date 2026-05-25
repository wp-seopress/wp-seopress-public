<?php
/**
 * SEOPress Main functions.
 *
 * @package SEOPress
 * @subpackage Admin_Pages
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Set class property
 */
$this->options = get_option( 'seopress_option_name' );
$current_tab   = '';
if ( function_exists( 'seopress_admin_header' ) ) {
	echo seopress_admin_header();
}
?>

<div id="seopress-content" class="seopress-option">
	<?php
		// React dashboard root. The bundle (admin/dashboard.js) renders the
		// full Intro / Notifications / Get Started / Tasks / Promotions /
		// FeaturesList / Integrations stack. It must render BEFORE the PRO
		// insights block below so the page header (Intro) and Notifications
		// banner sit at the top of the page, matching the Figma layout.
	?>
	<div id="seopress-admin-dashboard-root"></div>
	<?php
		// "Settings saved" snackbar — must stay inside #seopress-content
		// so it doesn't leave an orphan block in #wpbody-content after
		// the floated content (which collapses the body height and
		// drops the WP footer credits into the middle of the page).
		echo $this->feature_save(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built and escaped in feature_save().
	?>
</div>
<?php
