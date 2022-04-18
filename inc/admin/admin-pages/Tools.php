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
                    'tab_seopress_tool_data'           => __('Data', 'wp-seopress'),
                    'tab_seopress_tool_settings'       => __('Settings', 'wp-seopress'),
                    'tab_seopress_tool_plugins'        => __('Plugins', 'wp-seopress'),
                    'tab_seopress_tool_redirects'      => __('Redirections', 'wp-seopress'),
                    'tab_seopress_tool_reset'          => __('Reset', 'wp-seopress'),
                ];

    if (! is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        unset($plugin_settings_tabs['tab_seopress_tool_data']);
        unset($plugin_settings_tabs['tab_seopress_tool_redirects']);
    }

    echo '<div class="nav-tab-wrapper">';
    foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
        echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-import-export#tab=' . $tab_key . '">' . $tab_caption . '</a>';
    }
    echo '</div>'; ?>
        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
        <div class="seopress-tab <?php if ('tab_seopress_tool_data' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_data">
            <div class="postbox section-tool">
                <div class="sp-section-header">
                    <h2>
                        <?php _e('Data', 'wp-seopress'); ?>
                    </h2>
                </div>
                <div class="inside">
                    <h3>
                        <?php _e('Import data from a CSV', 'wp-seopress'); ?>
                    </h3>
                    <p>
                        <?php _e('Upload a CSV file to quickly import post (post, page, single post type) and term metadata.', 'wp-seopress'); ?>
                        <?php echo seopress_tooltip_link($docs['tools']['csv_import'], __('Learn how to import SEO metadata from a CSV file', 'wp-seopress')); ?>
                    </p>
                    <ul>
                        <li>
                            <?php _e('Meta title', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Meta description', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Meta robots (noindex, nofollow...)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Facebook Open Graph tags (title, description, image)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Twitter cards tags (title, description, image)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Redirection (enable, type, URL)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Primary category', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Canonical URL', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Target keywords', 'wp-seopress'); ?>
                        </li>
                    </ul>
                    <p>
                        <a class="btn btnSecondary"
                            href="<?php echo admin_url('admin.php?page=seopress_csv_importer'); ?>">
                            <?php _e('Run the importer', 'wp-seopress'); ?>
                        </a>
                    </p>
                </div><!-- .inside -->
            </div><!-- .postbox -->
            <div id="metadata-migration-tool" class="postbox section-tool">
                <div class="inside">
                    <h3>
                        <?php _e('Export metadata to a CSV', 'wp-seopress'); ?>
                    </h3>
                    <p>
                        <?php _e('Export your post (post, page, single post type) and term metadata for this site as a .csv file.', 'wp-seopress'); ?>
                        <?php echo seopress_tooltip_link($docs['tools']['csv_export'], __('Learn how to export SEO metadata to a CSV file', 'wp-seopress')); ?>
                    </p>
                    <ul>
                        <li>
                            <?php _e('Meta title', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Meta description', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Meta robots (noindex, nofollow...)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Facebook Open Graph tags (title, description, image)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Twitter cards tags (title, description, image)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Redirection (enable, type, URL)', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Primary category', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Canonical URL', 'wp-seopress'); ?>
                        </li>
                        <li>
                            <?php _e('Target keywords', 'wp-seopress'); ?>
                        </li>
                    </ul>
                    <form method="post">
                        <input type="hidden" name="seopress_action" value="export_csv_metadata" />
                        <?php wp_nonce_field('seopress_export_csv_metadata_nonce', 'seopress_export_csv_metadata_nonce'); ?>

                        <button id="seopress-metadata-migrate" type="button" class="btn btnSecondary">
                            <?php _e('Export', 'wp-seopress'); ?>
                        </button>

                        <span class="spinner"></span>

                        <div class="log"></div>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->
        </div>
        <?php } ?>
        <div class="seopress-tab <?php if ('tab_seopress_tool_settings' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_settings">
            <div class="postbox section-tool">
                <div class="sp-section-header">
                    <h2>
                        <?php _e('Settings', 'wp-seopress'); ?>
                    </h2>
                </div>
                <div class="inside">
                    <h3><span><?php _e('Export plugin settings', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php _e('Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-seopress'); ?>
                    </p>

                    <form method="post">
                        <input type="hidden" name="seopress_action" value="export_settings" />
                        <?php wp_nonce_field('seopress_export_nonce', 'seopress_export_nonce'); ?>

                        <button id="seopress-export" type="submit" class="btn btnSecondary">
                            <?php _e('Export', 'wp-seopress'); ?>
                        </button>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox section-tool">
                <div class="inside">
                    <h3><span><?php _e('Import plugin settings', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php _e('Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-seopress'); ?>
                    </p>

                    <form method="post" enctype="multipart/form-data">
                        <p>
                            <input type="file" name="import_file" />
                        </p>
                        <input type="hidden" name="seopress_action" value="import_settings" />

                        <?php wp_nonce_field('seopress_import_nonce', 'seopress_import_nonce'); ?>

                        <button id="seopress-import-settings" type="submit" class="btn btnSecondary">
                            <?php _e('Import', 'wp-seopress'); ?>
                        </button>

                        <?php if (! empty($_GET['success']) && 'true' == htmlspecialchars($_GET['success'])) {
        echo '<div class="log" style="display:block"><div class="seopress-notice is-success"><p>' . __('Import completed!', 'wp-seopress') . '</p></div></div>';
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
                    <?php _e('Plugins', 'wp-seopress'); ?>
                </h2>
            </div>
            <h3><span><?php _e('Import posts and terms metadata from', 'wp-seopress'); ?></span>
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
                    ];

    echo '<p>
                            <select id="select-wizard-import" name="select-wizard-import">
                                <option value="none">' . __('Select an option', 'wp-seopress') . '</option>';

    foreach ($plugins as $plugin => $name) {
        echo '<option value="' . $plugin . '-migration-tool">' . $name . '</option>';
    }
    echo '</select>
                        </p>

                    <p class="description">' . __('You don\'t have to enable the selected SEO plugin to run the import.', 'wp-seopress') . '</p>';

    foreach ($plugins as $plugin => $name) {
        echo seopress_migration_tool($plugin, $name);
    } ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_tool_redirects' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_redirects">
            <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
            <?php if ('1' == seopress_get_toggle_option('404') && function_exists('seopress_get_redirection_pro_html')) {
        seopress_get_redirection_pro_html();
    } else { ?>
            <div class="seopress-notice is-warning">
                <p><?php _e('Redirections feature is disabled. Please activate it from the <strong>PRO page</strong>.', 'wp-seopress'); ?>
                </p>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=seopress-pro-page'); ?>"
                        class="btn btnSecondary">
                        <?php _e('Activate Redirections', 'wp-seopress'); ?>
                    </a>
                </p>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
        <div class="seopress-tab <?php if ('tab_seopress_tool_reset' == $current_tab) {
        echo 'active';
    } ?>" id="tab_seopress_tool_reset">
            <div class="postbox section-tool">
                <div class="sp-section-header">
                    <h2>
                        <?php _e('Reset', 'wp-seopress'); ?>
                    </h2>
                </div>
                <div class="inside">
                    <h3><span><?php _e('Reset All Notices From Notifications Center', 'wp-seopress'); ?></span>
                    </h3>

                    <p><?php _e('By clicking Reset Notices, all notices in the notifications center will be set to their initial status.', 'wp-seopress'); ?>
                    </p>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="seopress_action" value="reset_notices_settings" />
                        <?php wp_nonce_field('seopress_reset_notices_nonce', 'seopress_reset_notices_nonce'); ?>
                        <?php sp_submit_button(__('Reset notices', 'wp-seopress'), 'btn btnSecondary'); ?>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox section-tool">
                <div class="inside">
                    <h3><?php _e('Reset All Settings', 'wp-seopress'); ?>
                    </h3>

                    <div class="seopress-notice is-warning">
                        <p><?php _e('<strong>WARNING:</strong> Delete all options related to this plugin in your database.', 'wp-seopress'); ?>
                        </p>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="seopress_action" value="reset_settings" />
                        <?php wp_nonce_field('seopress_reset_nonce', 'seopress_reset_nonce'); ?>
                        <?php sp_submit_button(__('Reset settings', 'wp-seopress'), 'btn btnSecondary is-deletable'); ?>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->
        </div>
    </div>
</div>
<?php
