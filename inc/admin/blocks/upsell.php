<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
    //do nothing
} else {
    $class = '1' !== seopress_get_service('NoticeOption')->getNoticeGoInsights() ? 'is-active' : '';

    if (! is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
        $docs = seopress_get_docs_links();
        ?>
        <div id="notice-go-insights-alert" class="seopress-upsell seopress-upsell-insights seopress-card <?php echo esc_attr($class); ?>" style="display: none">
            <div class="seopress-card-title">
                <img src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/logo-seopress-insights-square-alt.svg'); ?>" width="56" height="56" alt=""/>
                <h2><?php esc_attr_e('Discover SEOPress Insights, off-site SEO plugin for WordPress!', 'wp-seopress'); ?></h2>
            </div>
            <div class="seopress-card-content">
                <p><?php esc_attr_e('Rank & Backlink Tracking directly in your WordPress admin with competition, Google Trends, email and Slack notifications.', 'wp-seopress'); ?></p>
                <a href="<?php echo esc_url($docs['addons']['insights']); ?>" target="_blank" class="seopress-btn seopress-btn-primary">
                    <?php esc_attr_e('Upgrade now!','wp-seopress'); ?>
                </a>
            </div>
        </div>
    <?php
    }

    $class = '1' !== seopress_get_service('NoticeOption')->getNoticeGoPro() ? 'is-active' : '';

    if (! is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
        <div id="notice-go-pro-alert" class="seopress-upsell seopress-upsell-pro seopress-card <?php echo esc_attr($class); ?>" style="display: none">
            <div class="seopress-card-title">
                <img src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/logo-seopress-pro-square-alt.svg'); ?>" width="56" height="56" alt=""/>
                <h2><?php esc_attr_e('Take your SEO to the next level with SEOPress PRO!', 'wp-seopress'); ?></h2>
            </div>
            <div class="seopress-card-content">
                <p><?php esc_attr_e('The PRO version of SEOPress allows you to easily manage your structured data (schemas), use the power of AI to optimize SEO metadata, add a breadcrumb optimized for SEO and accessibility, improve SEO for WooCommerce, gain productivity with our import / export tool from a CSV of your metadata and so much more.', 'wp-seopress'); ?></p>
                <a href="<?php echo esc_url($docs['addons']['pro']); ?>" target="_blank" class="seopress-btn seopress-btn-primary">
                    <?php esc_attr_e('Upgrade now!','wp-seopress'); ?>
                </a>
            </div>
        </div>
    <?php }
}
