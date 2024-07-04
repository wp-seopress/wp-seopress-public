<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_import_export_option_name');

$docs = seopress_get_docs_links();

if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<div class="seopress-option">
    <?php
        echo $this->seopress_feature_title(null);
        $current_tab = '';
    ?>
    <div id="seopress-tabs" class="wrap">
        <?php
            $plugin_settings_tabs = [
                'tab_seopress_tool_settings'       => esc_html__('Settings', 'wp-seopress'),
                'tab_seopress_tool_plugins'        => esc_html__('Plugins', 'wp-seopress'),
                'tab_seopress_tool_reset'          => esc_html__('Reset', 'wp-seopress'),
            ];

            $plugin_settings_tabs = apply_filters('seopress_tools_tabs', $plugin_settings_tabs);
        ?>
        <div class="nav-tab-wrapper">
            <?php foreach ($plugin_settings_tabs as $tab_key => $tab_caption) { ?>
                <a id="<?php echo esc_attr($tab_key); ?>-tab" class="nav-tab" href="?page=seopress-import-export#tab=<?php echo esc_attr($tab_key); ?>">
                    <?php echo esc_html($tab_caption); ?>
                </a>
                <?php
            } ?>
        </div>

    <?php do_action('seopress_tools_before', $current_tab, $docs); ?>
        <div class="seopress-tab <?php if ('tab_seopress_tool_settings' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_settings">
            <div class="postbox section-tool">
                <div class="sp-section-header">
                    <h2>
                        <?php esc_html_e('Settings', 'wp-seopress'); ?>
                    </h2>
                </div>
                <div class="inside">
                    <h3>
                        <span><?php esc_html_e('Export plugin settings', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php esc_html_e('Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-seopress'); ?></p>

                    <form method="post">
                        <input type="hidden" name="seopress_action" value="export_settings" />
                        <?php wp_nonce_field('seopress_export_nonce', 'seopress_export_nonce'); ?>

                        <button id="seopress-export" type="submit" class="btn btnTertiary">
                            <?php esc_html_e('Export', 'wp-seopress'); ?>
                        </button>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox section-tool">
                <div class="inside">
                    <h3>
                        <span><?php esc_html_e('Import plugin settings', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php esc_html_e('Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-seopress'); ?></p>

                    <form method="post" enctype="multipart/form-data">
                        <p><input type="file" name="import_file" /></p>

                        <input type="hidden" name="seopress_action" value="import_settings" />

                        <?php wp_nonce_field('seopress_import_nonce', 'seopress_import_nonce'); ?>

                        <button id="seopress-import-settings" type="submit" class="btn btnTertiary">
                            <?php esc_html_e('Import', 'wp-seopress'); ?>
                        </button>

                        <?php if (! empty($_GET['success']) && 'true' == htmlspecialchars($_GET['success'])) {
        echo '<div class="log" style="display:block"><div class="seopress-notice is-success"><p>' . esc_html__('Import completed!', 'wp-seopress') . '</p></div></div>';
    } ?>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_tool_plugins' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_plugins">
            <div class="sp-section-header">
                <h2>
                    <?php esc_html_e('Plugins', 'wp-seopress'); ?>
                </h2>
            </div>
            <h3>
                <span><?php esc_html_e('Import posts and terms metadata from', 'wp-seopress'); ?></span>
            </h3>

            <?php
                $plugins = [
                    'yoast'            => 'Yoast SEO',
                    'aio'              => 'All In One SEO',
                    'seo-framework'    => 'The SEO Framework',
                    'rk'               => 'Rank Math',
                    'squirrly'         => 'Squirrly SEO',
                    'seo-ultimate'     => 'SEO Ultimate',
                    'wp-meta-seo'      => 'WP Meta SEO',
                    'premium-seo-pack' => 'Premium SEO Pack',
                    'wpseo'            => 'wpSEO',
                    'platinum-seo'     => 'Platinum SEO Pack',
                    'smart-crawl'      => 'SmartCrawl',
                    'seopressor'       => 'SEOPressor',
                    'slim-seo'         => 'Slim SEO'
                ];

    echo '<p><select id="select-wizard-import" name="select-wizard-import"><option value="none">' . esc_html__('Select an option', 'wp-seopress') . '</option>';

    foreach ($plugins as $plugin => $name) {
        echo '<option value="' . esc_attr($plugin) . '-migration-tool">' . esc_html($name) . '</option>';
    }
    echo '</select></p><p class="description">' . esc_html__('You don\'t have to enable the selected SEO plugin to run the import.', 'wp-seopress') . '</p>';

    foreach ($plugins as $plugin => $name) {
        echo seopress_migration_tool($plugin, $name);
    } ?>
        </div>
       <?php do_action('seopress_tools_migration', $current_tab); ?>
        <div class="seopress-tab <?php if ('tab_seopress_tool_reset' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_reset">
            <div class="postbox section-tool">
                <div class="sp-section-header">
                    <h2>
                        <?php esc_html_e('Cleaning', 'wp-seopress'); ?>
                    </h2>
                </div>
                <div class="inside">
                    <h3>
                        <span><?php esc_html_e('Clean content scans', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php esc_html_e('By clicking Delete content scans, all content analysis will be deleted from your database.', 'wp-seopress'); ?></p>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="seopress_action" value="clean_content_scans" />
                        <?php wp_nonce_field('seopress_clean_content_scans_nonce', 'seopress_clean_content_scans_nonce'); ?>
                        <?php sp_submit_button(esc_html__('Delete content scans', 'wp-seopress'), 'btn btnTertiary'); ?>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox section-tool">
                <div class="sp-section-header">
                    <h2>
                        <?php esc_html_e('Reset', 'wp-seopress'); ?>
                    </h2>
                </div>
                <div class="inside">
                    <h3>
                        <span><?php esc_html_e('Reset All Notices From Notifications Center', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php esc_html_e('By clicking Reset Notices, all notices in the notifications center will be set to their initial status.', 'wp-seopress'); ?></p>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="seopress_action" value="reset_notices_settings" />
                        <?php wp_nonce_field('seopress_reset_notices_nonce', 'seopress_reset_notices_nonce'); ?>
                        <?php sp_submit_button(esc_html__('Reset notices', 'wp-seopress'), 'btn btnTertiary'); ?>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox section-tool">
                <div class="inside">
                    <h3><?php esc_html_e('Reset All Settings', 'wp-seopress'); ?></h3>

                    <div class="seopress-notice is-warning">
                        <p><?php echo wp_kses_post(__('<strong>WARNING:</strong> Delete all options related to this plugin in your database.', 'wp-seopress')); ?></p>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="seopress_action" value="reset_settings" />
                        <?php wp_nonce_field('seopress_reset_nonce', 'seopress_reset_nonce'); ?>
                        <?php sp_submit_button(esc_html__('Reset settings', 'wp-seopress'), 'btn btnTertiary is-deletable'); ?>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->
        </div>
    </div>
</div>
<?php
