<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_xml_sitemap_option_name');
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option" name="seopress-flush">
    <?php
        echo $this->seopress_feature_title('xml-sitemap');
settings_fields('seopress_xml_sitemap_option_group'); ?>

    <div id="seopress-tabs" class="wrap">
        <?php
            $current_tab = '';
$plugin_settings_tabs    = [
                'tab_seopress_xml_sitemap_general'    => __('General', 'wp-seopress'),
                'tab_seopress_xml_sitemap_post_types' => __('Post Types', 'wp-seopress'),
                'tab_seopress_xml_sitemap_taxonomies' => __('Taxonomies', 'wp-seopress'),
                'tab_seopress_html_sitemap'           => __('HTML Sitemap', 'wp-seopress'),
            ];

echo '<div class="nav-tab-wrapper">';
foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
    echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-xml-sitemap#tab=' . $tab_key . '">' . $tab_caption . '</a>';
}
echo '</div>'; ?>
                <div class="seopress-tab <?php if ('tab_seopress_xml_sitemap_general' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_xml_sitemap_general"><?php do_settings_sections('seopress-settings-admin-xml-sitemap-general'); ?></div>
                <div class="seopress-tab <?php if ('tab_seopress_xml_sitemap_post_types' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_xml_sitemap_post_types"><?php do_settings_sections('seopress-settings-admin-xml-sitemap-post-types'); ?></div>
                <div class="seopress-tab <?php if ('tab_seopress_xml_sitemap_taxonomies' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_xml_sitemap_taxonomies"><?php do_settings_sections('seopress-settings-admin-xml-sitemap-taxonomies'); ?></div>
                <div class="seopress-tab <?php if ('tab_seopress_html_sitemap' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_html_sitemap"><?php do_settings_sections('seopress-settings-admin-html-sitemap'); ?></div>
        </div>

        <?php sp_submit_button(__('Save changes', 'wp-seopress')); ?>
    </form>
<?php
