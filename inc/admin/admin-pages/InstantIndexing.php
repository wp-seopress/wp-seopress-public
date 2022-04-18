<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');
$this->options = get_option('seopress_pro_option_name');


if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
}
?>
<form method="post"
    action="<?php echo admin_url('options.php'); ?>"
    class="seopress-option">
    <?php
        $current_tab = '';

        echo $this->seopress_feature_title('instant-indexing');
        settings_fields('seopress_instant_indexing_option_group');
    ?>

    <div id="seopress-tabs" class="wrap">
        <?php
        $plugin_settings_tabs = [
            'tab_seopress_instant_indexing_general' => __('General', 'wp-seopress'),
            'tab_seopress_instant_indexing_settings'    => __('Settings', 'wp-seopress')
        ];

    echo '<div class="nav-tab-wrapper">';
    foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
        echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-instant-indexing-page#tab=' . $tab_key . '">' . $tab_caption . '</a>';
    }
    echo '</div>'; ?>

    <!-- General -->
    <div class="seopress-tab <?php if ('tab_seopress_instant_indexing_general' == $current_tab) {
    echo 'active';
    } ?>" id="tab_seopress_instant_indexing_general">
        <?php do_settings_sections('seopress-settings-admin-instant-indexing'); ?>
    </div>

    <!-- Settings -->
    <div class="seopress-tab <?php if ('tab_seopress_instant_indexing_settings' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_instant_indexing_settings">
        <?php do_settings_sections('seopress-settings-admin-instant-indexing-settings'); ?>
    </div>

    </div>
    <!--seopress-tabs-->
    <?php echo $this->seopress_feature_save(); ?>
    <?php sp_submit_button(__('Save changes', 'wp-seopress')); ?>
</form>
<?php
