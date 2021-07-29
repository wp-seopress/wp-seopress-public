<?php
    // To prevent calling the plugin directly
    if ( ! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }
    $docs = seopress_get_docs_links();
?>

<div id="notice-get-started-alert" class="seopress-get-started seopress-alert deleteable">
	<div class="inside">
        <img src="<?php echo SEOPRESS_ASSETS_DIR . '/img/logo-seopress-square-alt.svg'; ?>" width="40" height="40" alt=""/>

        <p class="preheader"><?php _e('How-to get started', 'wp-seopress'); ?></p>

        <h2><?php _e('Welcome to SEOPress!', 'wp-seopress'); ?></h2>

        <p><?php _e('Launch our installation wizard to quickly and easily configure the basic SEO settings for your site. Browse our video guides to go further. Can\'t find the answers to your questions? Open a ticket from your customer area. A happiness engineer will be happy to help you.', 'wp-seopress'); ?></p>

        <p class="seopress-card-actions">
            <a href="<?php echo admin_url('admin.php?page=seopress-setup'); ?>" class="btn btnPrimary">
                <?php _e('Get started', 'wp-seopress'); ?>
            </a>
		    <button type="button" name="notice-get-started" id="notice-get-started" class="btn btnTertiary" data-notice="notice-get-started">
                <?php _e('Dismiss','wp-seopress'); ?>
            </button>
        </p>

	</div>
</div>
