<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_titles_option_name');
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<form method="post"
    action="<?php echo admin_url('options.php'); ?>"
    class="seopress-option">
    <?php
        echo $this->seopress_feature_title('titles');
settings_fields('seopress_titles_option_group'); ?>

    <div id="seopress-tabs" class="wrap">
        <?php
            $current_tab = '';
$plugin_settings_tabs    = [
                'tab_seopress_titles_home'     => __('Home', 'wp-seopress'),
                'tab_seopress_titles_single'   => __('Post Types', 'wp-seopress'),
                'tab_seopress_titles_archives' => __('Archives', 'wp-seopress'),
                'tab_seopress_titles_tax'      => __('Taxonomies', 'wp-seopress'),
                'tab_seopress_titles_advanced' => __('Advanced', 'wp-seopress'),
            ];

echo '<div class="nav-tab-wrapper">';
foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
    echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-titles#tab=' . $tab_key . '">' . $tab_caption . '</a>';
}
echo '</div>'; ?>
        <div class="seopress-tab <?php if ('tab_seopress_titles_home' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_titles_home"><?php do_settings_sections('seopress-settings-admin-titles-home'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_titles_single' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_titles_single"><?php do_settings_sections('seopress-settings-admin-titles-single'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_titles_archives' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_titles_archives"><?php do_settings_sections('seopress-settings-admin-titles-archives'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_titles_tax' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_titles_tax"><?php do_settings_sections('seopress-settings-admin-titles-tax'); ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_titles_advanced' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_titles_advanced"><?php do_settings_sections('seopress-settings-admin-titles-advanced'); ?>
        </div>
    </div>

    <?php sp_submit_button(__('Save changes', 'wp-seopress')); ?>
</form>
<?php
