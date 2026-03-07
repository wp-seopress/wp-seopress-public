<?php
/**
 * Get started block.
 *
 * @package SEOPress
 * @subpackage Blocks
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$docs          = seopress_get_docs_links();
$class         = '1' !== seopress_get_service( 'NoticeOption' )->getNoticeGetStarted() ? 'is-active' : '';
$notice_wizard = seopress_get_service( 'NoticeOption' )->getNoticeWizard() ? true : false;


if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && SEOPRESS_WL_ADMIN_HEADER === false ) {
	// White label mode - do nothing.
} elseif ( $notice_wizard === true ) {
	// Wizard completed - show SEO Checklist card instead.
	$checklist_class = '1' !== seopress_get_service( 'NoticeOption' )->getNoticeGetStarted() ? 'is-active' : '';
	?>

	<div id="notice-get-started-alert" class="seopress-get-started seopress-checklist <?php echo esc_attr( $checklist_class ); ?>" style="display: none">
		<div class="seopress-block-checklist">
			<div class="checklist-badge"><?php esc_html_e( 'SEO Tips', 'wp-seopress' ); ?></div>
			<h2><?php esc_html_e( 'SEO Checklist', 'wp-seopress' ); ?></h2>
			<p class="checklist-subtitle"><?php esc_html_e( 'Quick reminders to boost your rankings.', 'wp-seopress' ); ?></p>

			<ul class="seopress-checklist-items">
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<span><?php esc_html_e( 'Add meta descriptions to your posts', 'wp-seopress' ); ?></span>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<span><?php esc_html_e( 'Optimize images with alt text', 'wp-seopress' ); ?></span>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<span><?php esc_html_e( 'Submit your sitemap to Google', 'wp-seopress' ); ?></span>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<span><?php esc_html_e( 'Check for broken links', 'wp-seopress' ); ?></span>
				</li>
			</ul>

			<p class="seopress-card-actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=seopress-setup&step=welcome&parent=welcome' ) ); ?>" class="seopress-btn seopress-btn-primary">
					<span class="dashicons dashicons-update"></span>
					<?php esc_html_e( 'Run Setup Wizard', 'wp-seopress' ); ?>
				</a>
			</p>
		</div>
	</div>

	<?php
} else {
	// Show Wizard card.
	?>

	<div id="notice-get-started-alert" class="seopress-get-started seopress-alert deleteable <?php echo esc_attr( $class ); ?>" style="display: none">
		<div class="seopress-block-wizard seopress-card-title">
			<div class="wizard-badge"><?php esc_html_e( 'Quick Setup', 'wp-seopress' ); ?></div>
			<h2><?php esc_html_e( 'Rank Higher on Google', 'wp-seopress' ); ?></h2>
			<p class="wizard-subtitle"><?php esc_html_e( 'Configure your SEO in minutes with our guided wizard.', 'wp-seopress' ); ?></p>

			<ul class="wizard-benefits">
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Optimize titles & meta', 'wp-seopress' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Generate XML sitemaps', 'wp-seopress' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Connect to Google', 'wp-seopress' ); ?>
				</li>
			</ul>

			<p class="seopress-card-actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=seopress-setup&step=welcome&parent=welcome' ) ); ?>" class="seopress-btn seopress-btn-primary">
					<span class="dashicons dashicons-controls-play"></span>
					<?php esc_html_e( 'Start Setup Wizard', 'wp-seopress' ); ?>
				</a>
				<span class="wizard-time">
					<span class="dashicons dashicons-clock"></span>
					<?php esc_html_e( 'Takes only 2 minutes', 'wp-seopress' ); ?>
				</span>
			</p>
		</div>
	</div>

	<?php
}
