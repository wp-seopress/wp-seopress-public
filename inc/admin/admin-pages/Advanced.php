<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_advanced_option_name');
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<form method="post"
    action="<?php echo esc_url(admin_url('options.php')); ?>"
    class="seopress-option">
    <?php
        echo $this->feature_title('advanced');
        settings_fields('seopress_advanced_option_group');
    ?>

    <div id="seopress-tabs" class="wrap">
        <?php
            $current_tab = '';
$plugin_settings_tabs    = [
                'tab_seopress_advanced_image'       => __('Image SEO', 'wp-seopress'),
                'tab_seopress_advanced_advanced'    => __('Advanced', 'wp-seopress'),
                'tab_seopress_advanced_appearance'  => __('Appearance', 'wp-seopress'),
                'tab_seopress_advanced_security'    => __('Security', 'wp-seopress'),
            ];
?>
        <div class="nav-tab-wrapper">

            <?php foreach ($plugin_settings_tabs as $tab_key => $tab_caption) { ?>
            <a id="<?php echo esc_attr($tab_key); ?>-tab" class="nav-tab"
                href="?page=seopress-advanced#tab=<?php echo esc_attr($tab_key); ?>"><?php echo esc_html($tab_caption); ?></a>
            <?php } ?>

        </div>
        <div class="seopress-tab<?php if ('tab_seopress_advanced_image' == $current_tab) {
    echo ' active';
} ?>" id="tab_seopress_advanced_image"><?php do_settings_sections('seopress-settings-admin-advanced-image'); ?>
        </div>
        <div class="seopress-tab<?php if ('tab_seopress_advanced_advanced' == $current_tab) {
    echo ' active';
} ?>" id="tab_seopress_advanced_advanced"><?php do_settings_sections('seopress-settings-admin-advanced-advanced'); ?>
        </div>
        <div class="seopress-tab<?php if ('tab_seopress_advanced_appearance' == $current_tab) {
    echo ' active';
} ?>" id="tab_seopress_advanced_appearance"><?php do_settings_sections('seopress-settings-admin-advanced-appearance'); ?>
        </div>
        <div class="seopress-tab<?php if ('tab_seopress_advanced_security' == $current_tab) {
    echo ' active';
} ?>" id="tab_seopress_advanced_security"><?php do_settings_sections('seopress-settings-admin-advanced-security'); ?>
        </div>
    </div>

    <?php sp_submit_button(esc_html__('Save changes', 'wp-seopress')); ?>
</form>
<?php
