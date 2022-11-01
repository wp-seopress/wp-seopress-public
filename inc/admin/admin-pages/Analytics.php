<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_google_analytics_option_name');
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<form method="post"
    action="<?php echo admin_url('options.php'); ?>"
    class="seopress-option">
    <?php
        echo $this->seopress_feature_title('google-analytics');
settings_fields('seopress_google_analytics_option_group'); ?>

    <div id="seopress-tabs" class="wrap">
        <?php
            $current_tab = '';

            $plugin_settings_tabs = [
                'tab_seopress_google_analytics_enable'              => __('Google Analytics', 'wp-seopress'),
                'tab_seopress_google_analytics_matomo'              => __('Matomo', 'wp-seopress'),
                'tab_seopress_google_analytics_clarity'             => __('Clarity', 'wp-seopress'),
                'tab_seopress_google_analytics_custom_dimensions'   => __('Advanced', 'wp-seopress'),
                'tab_seopress_google_analytics_gdpr'                => __('Cookie bar / GDPR', 'wp-seopress'),
                'tab_seopress_google_analytics_custom_tracking'     => __('Custom Tracking', 'wp-seopress'),
            ];

echo '<div class="nav-tab-wrapper">';
foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
    echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-google-analytics#tab=' . $tab_key . '">' . $tab_caption . '</a>';
}
echo '</div>'; ?>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_enable' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_enable">
            <?php do_settings_sections('seopress-settings-admin-google-analytics-enable'); ?>
            <?php do_settings_sections('seopress-settings-admin-google-analytics-features'); ?>
            <?php do_settings_sections('seopress-settings-admin-google-analytics-events'); ?>
            <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                do_settings_sections('seopress-settings-admin-google-analytics-ecommerce');
                do_settings_sections('seopress-settings-admin-google-analytics-dashboard');
            } ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_custom_tracking' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_custom_tracking"><?php do_settings_sections('seopress-settings-admin-google-analytics-custom-tracking'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_custom_dimensions' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_custom_dimensions">
            <?php do_settings_sections('seopress-settings-admin-google-analytics-custom-dimensions'); ?>
            <?php do_settings_sections('seopress-settings-admin-google-analytics-advanced'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_gdpr' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_gdpr"><?php do_settings_sections('seopress-settings-admin-google-analytics-gdpr'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_matomo' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_matomo"><?php do_settings_sections('seopress-settings-admin-google-analytics-matomo'); ?>
<?php do_settings_sections('seopress-settings-admin-google-analytics-matomo-widget'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_clarity' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_clarity"><?php do_settings_sections('seopress-settings-admin-google-analytics-clarity'); ?>
        </div>
    </div>

    <?php sp_submit_button(__('Save changes', 'wp-seopress')); ?>
</form>
<?php
