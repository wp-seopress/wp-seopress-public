<?php
	defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

	$docs = seopress_get_docs_links();
	$class = '1' !== seopress_get_service('NoticeOption')->getNoticeGetStarted() ? 'is-active' : '';

	if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
		//do nothing
	} else {
?>

	<div id="notice-get-started-alert" class="seopress-get-started seopress-alert deleteable <?php echo esc_attr($class); ?>" style="display: none">
		<div class="seopress-block-wizard seopress-card-title">
			<h2><?php esc_attr_e('Configure your SEO', 'wp-seopress'); ?></h2>

			<p><?php esc_attr_e('Launch our installation wizard to quickly and easily configure the basic SEO settings for your site.', 'wp-seopress'); ?></p>

			<p class="seopress-card-actions">
				<a href="<?php echo esc_url(admin_url('admin.php?page=seopress-setup&step=welcome&parent=welcome')); ?>" class="seopress-btn seopress-btn-primary">
					<?php esc_attr_e('Setup in 5 mins!', 'wp-seopress'); ?>
				</a>
			</p>
		</div>
	</div>

<?php }
