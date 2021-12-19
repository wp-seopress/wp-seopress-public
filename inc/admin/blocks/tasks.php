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
            <ul class="seopress-list-items" role="menu">
                <?php $done = '';
                if ('valid' === get_option('seopress_pro_license_status') && is_plugin_active('wp-seopress-pro/seopress-pro.php') && ! is_multisite()) {
                    $done = 'done'; ?>
                <li class="seopress-item has-action seopress-item-inner">
                    <a href="<?php echo admin_url('admin.php?page=seopress-license'); ?>"
                        class="seopress-item-inner check <?php echo $done; ?>">
                        <?php _e('Activate your license key', 'wp-seopress'); ?>
                    </a>
                </li>
                <?php
                }
                $done    = '';
                $options       = get_option('seopress_xml_sitemap_option_name');
                    $check     = isset($options['seopress_xml_sitemap_general_enable']);
                    if ('1' == $check) {
                        $done = 'done';
                    }
                ?>
                <li class="seopress-item has-action seopress-item-inner">
                    <a href="<?php echo admin_url('admin.php?page=seopress-xml-sitemap'); ?>"
                        class="seopress-item-inner check <?php echo $done; ?>">
                        <?php _e('Generate XML sitemaps', 'wp-seopress'); ?>
                    </a>
                </li>
                <?php $done    = '';
                $options       = get_option('seopress_social_option_name');
                    $check     = isset($options['seopress_social_facebook_og']);
                    if ('1' == $check) {
                        $done = 'done';
                    }
                ?>
                <li class="seopress-item has-action seopress-item-inner">
                    <a href="<?php echo admin_url('admin.php?page=seopress-social'); ?>"
                        class="seopress-item-inner check <?php echo $done; ?>">
                        <?php _e('Be social', 'wp-seopress'); ?>
                    </a>
                </li>
                <?php
                $done = '';
                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    if ('1' === seopress_get_toggle_option('local-business')) {
                        $done = 'done';
                    } ?>
                <li class="seopress-item has-action seopress-item-inner">
                    <a href="<?php echo admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_local_business'); ?>"
                        class="seopress-item-inner check <?php echo $done; ?>">
                        <?php _e('Improve Local SEO', 'wp-seopress'); ?>
                    </a>
                </li>
                <?php
                } ?>
                <?php
                $done    = '';
                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    $options = get_option('seopress_pro_option_name');
                    $check   = isset($options['seopress_rich_snippets_enable']);
                    if ('1' === seopress_get_toggle_option('rich-snippets') && '1' == $check) {
                        $done = 'done';
                    } ?>
                <li class="seopress-item has-action seopress-item-inner">
                    <a href="<?php echo admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets'); ?>"
                        class="seopress-item-inner check <?php echo $done; ?>">
                        <?php _e('Add Structured Data Types to increase visibility in SERPs', 'wp-seopress'); ?>
                    </a>
                </li>
                <?php
                } ?>
            </ul>
        </div>
    </div>

<?php }
}
