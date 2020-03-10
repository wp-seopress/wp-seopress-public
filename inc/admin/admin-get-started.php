<?php
// To prevent calling the plugin directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
	exit;
} ?>
<div id="notice-get-started-alert" class="seopress-get-started seopress-alert deleteable">
    <div class="inside">
        <span class="preheader"><?php _e('How-to get started','wp-seopress'); ?></span>
        <h2><?php _e('Welcome to SEOPress!','wp-seopress'); ?></h2>
        <p><?php _e('Launch our installation wizard to quickly and easily configure the basic SEO settings for your site. Browse our video guides to go further. Can\'t find the answers to your questions? Open a ticket from your customer area. A happiness engineer will be happy to help you.','wp-seopress'); ?></p>
        <a href="<?php echo admin_url('admin.php?page=seopress-setup'); ?>" class="button button-primary"><?php _e('Get started','wp-seopress'); ?> <span class="dashicons dashicons-arrow-right-alt"></span></a>
        <a class="btn-link" href="https://youtube.com/seopress" target="_blank"><span class="dashicons dashicons-video-alt3"></span><?php _e('Watch our video guides','wp-seopress'); ?></a>
        <?php
            if (function_exists('seopress_get_locale')) {
                if (seopress_get_locale() =='fr') {
                    $seopress_docs_link['support']['wizard'] = 'https://www.seopress.org/fr/support/';
                } else {
                    $seopress_docs_link['support']['wizard'] = 'https://www.seopress.org/support/';
                }
            }

            echo
                '<a class="btn-link" href="'.$seopress_docs_link['support']['wizard'].'" target="_blank">
                    <span class="dashicons dashicons-sos"></span>'.__('Our support center','wp-seopress').'
                </a>';
        ?>
        <span name="notice-get-started" id="notice-get-started" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-get-started"></span>
    </div>
</div>