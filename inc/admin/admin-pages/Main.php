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
	<!--Get started-->
	<?php
		require_once dirname( __DIR__ ) . '/blocks/intro.php';
		require_once dirname( __DIR__ ) . '/blocks/notifications.php';
	?>

	<div class="seopress-dashboard-columns">
		<div class="seopress-dashboard-column">
			<?php
				require_once dirname( __DIR__ ) . '/blocks/get-started.php';
				require_once dirname( __DIR__ ) . '/blocks/tasks.php';
			?>
		</div>
		<?php
			require_once dirname( __DIR__ ) . '/blocks/insights.php';
		?>
	</div>
	<?php
		require_once dirname( __DIR__ ) . '/blocks/features-list.php';
		require_once dirname( __DIR__ ) . '/blocks/ebooks.php';
		require_once dirname( __DIR__ ) . '/blocks/integrations.php';
		require_once dirname( __DIR__ ) . '/blocks/news.php';
		$this->feature_save();
	?>
</div>
<?php echo $this->feature_save(); ?>
<?php
