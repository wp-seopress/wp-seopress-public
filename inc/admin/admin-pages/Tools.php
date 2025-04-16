<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_import_export_option_name');

$docs = seopress_get_docs_links();

if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<div class="seopress-option">
    <?php
        echo $this->feature_title(null);
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
                'yoast'            => [
                    'slug' => [
                        'wordpress-seo/wp-seo.php',
                        'wordpress-seo-premium/wp-seo-premium.php',
                    ],
                    'name' => 'Yoast SEO',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/yoast.png',
                ],
                'aio'              => [
                    'slug' => [
                        'all-in-one-seo-pack/all_in_one_seo_pack.php',
                    ],
                    'name' => 'All In One SEO',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/aio.svg',
                ],
                'seo-framework'    => [
                    'slug' => [
                        'autodescription/autodescription.php',
                    ],
                    'name' => 'The SEO Framework',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/seo-framework.svg',
                ],
                'rk'               => [
                    'slug' => [
                        'seo-by-rank-math/rank-math.php',
                    ],
                    'name' => 'Rank Math',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/rk.svg',
                ],
                'squirrly'         => [
                    'slug' => [
                        'squirrly-seo/squirrly.php',
                    ],
                    'name' => 'Squirrly SEO',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/squirrly.png',
                ],
                'seo-ultimate'     => [
                    'slug' => [
                        'seo-ultimate/seo-ultimate.php',
                    ],
                    'name' => 'SEO Ultimate',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/seo-ultimate.svg',
                ],
                'wp-meta-seo'      => [
                    'slug' => [
                        'wp-meta-seo/wp-meta-seo.php',
                    ],
                    'name' => 'WP Meta SEO',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/wp-meta-seo.png',
                ],
                'premium-seo-pack' => [
                    'slug' => [
                        'premium-seo-pack/plugin.php',
                    ],
                    'name' => 'Premium SEO Pack',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/premium-seo-pack.png',
                ],
                'smart-crawl'      => [
                    'slug' => [
                        'smartcrawl-seo/wpmu-dev-seo.php',
                    ],
                    'name' => 'SmartCrawl',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/smart-crawl.png',
                ],
                'slim-seo'         => [
                    'slug' => [
                        'slim-seo/slim-seo.php',
                    ],
                    'name' => 'Slim SEO',
                    'img' => SEOPRESS_URL_ASSETS . '/img/import/slim-seo.svg',
                ],
            ];

            $active_seo_plugins = [];

            foreach ($plugins as $plugin => $detail) {
                foreach($detail['slug'] as $key => $slug) {
                    if (is_plugin_active($slug)) {
                        $active_seo_plugins['name'][] = $detail['name'];
                        $active_seo_plugins['slug'][] = $detail['slug'];
                    }
                }
            }

            ?>
            <fieldset class="seopress-import-tools-wrapper" role="group" aria-labelledby="import-tools-legend">
                <div class="seopress-notice">
                    <legend id="import-tools-legend"><?php esc_attr_e('Select an SEO plugin to migrate from (you don\'t have to enable the selected one to run the import):', 'wp-seopress'); ?></legend>
                </div>
                <div class="seopress-import-tools" role="radiogroup" aria-labelledby="import-tools-legend">
                    <?php
                        foreach ($plugins as $plugin => $detail) {
                            ?>
                            <div class="seopress-import-tool <?php if (!empty($active_seo_plugins) && in_array($detail['slug'], $active_seo_plugins['slug'])) { echo 'active'; } ?>">
                                <label for="<?php echo esc_attr($plugin); ?>-migration-tool" tabindex="0">
                                    <input type="radio" id="<?php echo esc_attr($plugin); ?>-migration-tool" name="select-wizard-import" value="<?php echo esc_attr($plugin); ?>-migration-tool"
                                    aria-describedby="<?php echo esc_attr($plugin); ?>-description"
                                    aria-label="<?php echo /* translators: %s: "SEO plugin name" */ esc_attr(sprintf(__('Select %s for migration', 'wp-seopress'), $detail['name'])); ?>"
                                    <?php
                                        if (!empty($active_seo_plugins) && in_array($detail['slug'], $active_seo_plugins['slug'])) {
                                            echo 'checked';
                                        }
                                    ?>
                                    />
                                    <?php if (!empty($detail['img'])): ?>
                                        <img src="<?php echo esc_url($detail['img']); ?>" alt="<?php echo esc_attr($detail['name']); ?> logo">
                                    <?php endif; ?>
                                    <span><?php echo esc_html($detail['name']); ?></span>
                                </label>
                                <p id="<?php echo esc_attr($plugin); ?>-description" class="screen-reader-text"><?php echo /* translators: %s: "SEO plugin name" */ wp_kses_post(sprintf(__('Import metadata from %s, including titles and meta descriptions.', 'wp-seopress'), esc_html($detail['name']))); ?></p>
                            </div>
                        <?php } 
                    ?>
                </div>

                <div class="seopress-import-tools-details" aria-live="polite">
                    <?php
                        foreach ($plugins as $plugin => $detail) {
                            $checked = false;
                            if (!empty($active_seo_plugins) && in_array($detail['slug'], $active_seo_plugins['slug'])) {
                                $checked = true;
                            } 
                            echo wp_kses_post(seopress_migration_tool($plugin, $detail, $checked));
                        }
                    ?>
                </div>
            </fieldset>
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

            <?php do_action('seopress_tools_reset_seo_issues'); ?>

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
