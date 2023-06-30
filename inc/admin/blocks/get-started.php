<?php
    // To prevent calling the plugin directly
    if ( ! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }
    $docs = seopress_get_docs_links();
    $class = '1' !== seopress_get_service('NoticeOption')->getNoticeGetStarted() ? 'is-active' : '';

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {
?>

    <div id="notice-get-started-alert" class="seopress-get-started seopress-alert deleteable <?php echo $class; ?>" style="display: none">
        <div class="seopress-block-wizard">
            <h3><?php _e('Configure your SEO', 'wp-seopress'); ?></h3>

            <p><?php _e('Launch our installation wizard to quickly and easily configure the basic SEO settings for your site.', 'wp-seopress'); ?></p>

            <p class="seopress-card-actions">
                <a href="<?php echo admin_url('admin.php?page=seopress-setup'); ?>" class="seopress-btn seopress-btn-primary">
                    <?php _e('Get started', 'wp-seopress'); ?>
                </a>
            </p>
        </div>

        <div class="seopress-block-new-features">
            <div class="seopress-new-feature">
                <img src="<?php echo SEOPRESS_URL_ASSETS . '/img/ico-universal-seo-metabox.svg'; ?>" alt="" width="100" height="56"/>

                <h3><?php _e('Universal SEO Metabox', 'wp-seopress'); ?></h3>

                <p><?php _e('Edit your SEO metadata (title, meta description, social tags, schemas, meta robots…) directly from your favorite theme or page builder.', 'wp-seopress'); ?></p>

                <p class="seopress-card-actions">
                    <a href="<?php echo $docs['universal']['introduction']; ?>" target="_blank" title="<?php _e('Universal SEO Metabox - new window', 'wp-seopress'); ?>">
                        <?php _e('Learn more', 'wp-seopress'); ?>
                    </a>
                </p>
            </div>

            <div class="seopress-new-feature">
                <img src="<?php echo SEOPRESS_URL_ASSETS . '/img/ico-open-ai.svg'; ?>" alt="" width="100" height="56"/>

                <h3><?php _e('Open AI', 'wp-seopress'); ?></h3>

                <p><?php _e('Generate automagically meta title and description with artificial intelligence (AI).', 'wp-seopress'); ?></p>

                <p class="seopress-card-actions">
                    <a href="<?php echo $docs['ai']['introduction']; ?>" target="_blank" title="<?php _e('Open AI - new window', 'wp-seopress'); ?>">
                        <?php _e('Learn more', 'wp-seopress'); ?>
                    </a>
                </p>
            </div>

            <div class="seopress-new-feature">
                <img src="<?php echo SEOPRESS_URL_ASSETS . '/img/ico-stars.svg'; ?>" alt="" width="56" height="56"/>

                <h3><?php _e('You like SEOPress?', 'wp-seopress'); ?></h3>

                <p><?php _e('Please help us by rating us ★★★★★ on WordPress.org. Thank you!', 'wp-seopress'); ?></p>

                <p class="seopress-card-actions">
                    <a href="https://wordpress.org/support/plugin/wp-seopress/reviews/#new-post" target="_blank" title="<?php _e('SEOPress on WordPress plugins repository - new window', 'wp-seopress'); ?>">
                        <?php _e('Yes sure!', 'wp-seopress'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>

<?php }
