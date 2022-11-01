<?php
    // To prevent calling the plugin directly
    if (! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }
    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {

        function seopress_get_hidden_notices_tasks_option()
        {
            $seopress_get_hidden_notices_tasks_option = get_option('seopress_notices');
            if (! empty($seopress_get_hidden_notices_tasks_option)) {
                foreach ($seopress_get_hidden_notices_tasks_option as $key => $seopress_get_hidden_notices_tasks_value) {
                    $options[$key] = $seopress_get_hidden_notices_tasks_value;
                }
                if (isset($seopress_get_hidden_notices_tasks_option['notice-tasks'])) {
                    return $seopress_get_hidden_notices_tasks_option['notice-tasks'];
                }
            }
        }

        if ('1' != seopress_get_hidden_notices_tasks_option()) {
    ?>

    <div id="notice-tasks-alert" class="seopress-card">
        <div class="seopress-card-title">
            <h2><?php _e('Get ready to improve your SEO', 'wp-seopress'); ?>
            </h2>

            <span class="seopress-item-toggle-options"></span>
            <div class="seopress-card-popover">
                <?php
                    $options = get_option('seopress_dashboard_option_name');
                    $value   = isset($options['hide_tasks']) ? esc_attr($options['hide_tasks']) : 5;
                ?>

                <button id="notice-tasks" name="notice-tasks" data-notice="notice-tasks" type="submit" class="btn btnSecondary">
                    <?php _e('Hide this', 'wp-seopress'); ?>
                </button>
            </div>
        </div>
        <div class="seopress-card-content">
            <?php
                /**
                 * Check if XML sitemaps feature is correctly enabled by the user
                 *
                 * @since 6.0
                 * @author Benjamin
                 *
                 */
                function seopress_tasks_sitemaps() {
                    $options = get_option('seopress_xml_sitemap_option_name');
                    if (isset($options['seopress_xml_sitemap_general_enable']) && ('1' === seopress_get_toggle_option('xml-sitemap'))) {
                        return 'done';
                    }

                    return;
                }

                /**
                 * Check if Social Networds feature is correctly enabled by the user
                 *
                 * @since 6.0
                 * @author Benjamin
                 *
                 */
                function seopress_tasks_social_networks() {
                    $options = get_option('seopress_social_option_name');
                    if (isset($options['seopress_social_facebook_og']) && ('1' === seopress_get_toggle_option('social'))) {
                        return 'done';
                    }

                    return;
                }

                /**
                 * Check if Schemas feature is correctly enabled by the user
                 *
                 * @since 6.0
                 * @author Benjamin
                 *
                 */
                function seopress_tasks_schemas() {
                    $options = get_option('seopress_pro_option_name');
                    if (is_plugin_active('wp-seopress-pro/seopress-pro.php') && isset($options['seopress_rich_snippets_enable']) && '1' === seopress_get_toggle_option('rich-snippets')) {
                        return 'done';
                    }

                    return;
                }
                $tasks = [
                    [
                        'done' => ('valid' === get_option('seopress_pro_license_status') && is_plugin_active('wp-seopress-pro/seopress-pro.php') && ! is_multisite()) ? 'done' : '',
                        'link' => admin_url('admin.php?page=seopress-license'),
                        'label' => __('Activate your license key', 'wp-seopress'),
                    ],
                    [
                        'done' => seopress_tasks_sitemaps(),
                        'link' => admin_url('admin.php?page=seopress-xml-sitemap'),
                        'label' => __('Generate XML sitemaps', 'wp-seopress'),
                    ],
                    [
                        'done' => seopress_tasks_social_networks(),
                        'link' => admin_url('admin.php?page=seopress-social'),
                        'label' => __('Be social', 'wp-seopress'),
                    ],
                    [
                        'done' => (is_plugin_active('wp-seopress-pro/seopress-pro.php') && seopress_get_toggle_option('local-business') === '1') ? 'done' : '',
                        'link' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_local_business'),
                        'label' => __('Improve Local SEO', 'wp-seopress'),
                    ],
                    [
                        'done' => seopress_tasks_schemas(),
                        'link' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets'),
                        'label' => __('Add Structured Data Types to increase visibility in SERPs', 'wp-seopress'),
                    ]
                ];

                if (!is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    unset($tasks[0]);
                    unset($tasks[3]);
                    unset($tasks[4]);
                    $tasks = array_values($tasks);
                }
            ?>

            <ul class="seopress-list-items" role="menu">
                <?php foreach($tasks as $key => $task) { ?>
                    <li class="seopress-item has-action seopress-item-inner <?php if (empty($task['done'])) { echo 'is-active'; }; ?>">
                        <a href="<?php echo $task['link']; ?>" class="seopress-item-inner check <?php echo $task['done']; ?>" data-index="<?php echo $key + 1; ?>">
                            <?php echo $task['label']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="seopress-card-footer">
            <a href="https://wordpress.org/support/view/plugin-reviews/wp-seopress?rate=5#postform" target="_blank">
                <?php _e('You like SEOPress? Please help us by rating us 5 stars!', 'wp-seopress'); ?>
            </a>
            <span class="dashicons dashicons-external"></span>
        </div>
    </div>

<?php }
}
