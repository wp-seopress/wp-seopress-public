<?php
/**
 * SEOPress Tools functions.
 *
 * @package SEOPress
 * @subpackage Admin_Pages
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$this->options = get_option( 'seopress_import_export_option_name' );

$docs = seopress_get_docs_links();

if ( function_exists( 'seopress_admin_header' ) ) {
	echo seopress_admin_header();
}
?>
<div class="seopress-option seopress-php-header">
	<?php echo $this->feature_title( null ); ?>
</div>

<div id="seopress-admin-settings-root" class="seopress-option">
	<?php seopress_settings_skeleton(); ?>
</div>
