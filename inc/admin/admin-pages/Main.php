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
		// Unified Settings React shell. ModuleSettings enqueues
		// admin/settings.js on seopress-option and the bundle reads
		// PAGE_TYPE = 'dashboard' from the localized
		// SEOPRESS_SETTINGS_DATA to render the Dashboard as one of
		// its lazy sections.
	?>
	<div class="seopress-php-header"></div>
	<div id="seopress-admin-settings-root"></div>
	<?php
		// "Settings saved" snackbar — must stay inside #seopress-content
		// so it doesn't leave an orphan block in #wpbody-content after
		// the floated content (which collapses the body height and
		// drops the WP footer credits into the middle of the page).
		echo $this->feature_save(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built and escaped in feature_save().
	?>
</div>
<?php
