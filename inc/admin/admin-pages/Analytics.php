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
if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
    $plugin_settings_tabs = [
                    'tab_seopress_google_analytics_enable'              => __('General', 'wp-seopress'),
                    'tab_seopress_google_analytics_features'            => __('Tracking', 'wp-seopress'),
                    'tab_seopress_google_analytics_ecommerce'           => __('Ecommerce', 'wp-seopress'),
                    'tab_seopress_google_analytics_events'              => __('Events', 'wp-seopress'),
                    'tab_seopress_google_analytics_custom_dimensions'   => __('Custom Dimensions', 'wp-seopress'),
                    'tab_seopress_google_analytics_dashboard'           => __('Stats in Dashboard', 'wp-seopress'),
                    'tab_seopress_google_analytics_gdpr'                => __('Cookie bar / GDPR', 'wp-seopress'),
                    'tab_seopress_google_analytics_matomo'              => __('Matomo', 'wp-seopress'),
                ];
} else {
    $plugin_settings_tabs = [
                    'tab_seopress_google_analytics_enable'              => __('General', 'wp-seopress'),
                    'tab_seopress_google_analytics_features'            => __('Tracking', 'wp-seopress'),
                    'tab_seopress_google_analytics_events'              => __('Events', 'wp-seopress'),
                    'tab_seopress_google_analytics_custom_dimensions'   => __('Custom Dimensions', 'wp-seopress'),
                    'tab_seopress_google_analytics_gdpr'                => __('Cookie bar / GDPR', 'wp-seopress'),
                    'tab_seopress_google_analytics_matomo'              => __('Matomo', 'wp-seopress'),
                ];
}

echo '<div class="nav-tab-wrapper">';
foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
    echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-google-analytics#tab=' . $tab_key . '">' . $tab_caption . '</a>';
}
echo '</div>'; ?>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_enable' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_enable"><?php do_settings_sections('seopress-settings-admin-google-analytics-enable'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_features' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_features"><?php do_settings_sections('seopress-settings-admin-google-analytics-features'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_events' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_events"><?php do_settings_sections('seopress-settings-admin-google-analytics-events'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_custom_dimensions' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_custom_dimensions"><?php do_settings_sections('seopress-settings-admin-google-analytics-custom-dimensions'); ?>
        </div>
        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_dashboard' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_dashboard"><?php do_settings_sections('seopress-settings-admin-google-analytics-dashboard'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_ecommerce' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_ecommerce"><?php do_settings_sections('seopress-settings-admin-google-analytics-ecommerce'); ?>
        </div>
        <?php } ?>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_gdpr' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_gdpr"><?php do_settings_sections('seopress-settings-admin-google-analytics-gdpr'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_google_analytics_matomo' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_google_analytics_matomo"><?php do_settings_sections('seopress-settings-admin-google-analytics-matomo'); ?>
        </div>
    </div>

    <?php sp_submit_button(__('Save changes', 'wp-seopress')); ?>
</form>
<?php
