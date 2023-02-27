<?php
    // To prevent calling the plugin directly
    if (! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }
?>
<div id="seopress-page-list" class="seopress-page-list seopress-card">
    <div class="seopress-card-title">
        <h2><?php _e('SEO management', 'wp-seopress'); ?>
        </h2>
        <span class="dashicons dashicons-sort"></span>
    </div>

    <?php
        $features = [
            'titles' => [
                'title'         => __('Titles & Metas', 'wp-seopress'),
                'desc'          => __('Manage all your titles & metas for post types, taxonomies, archives...', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-titles'),
                'filter'        => 'seopress_remove_feature_titles',
            ],
            'xml-sitemap' => [
                'title'         => __('XML & HTML Sitemaps', 'wp-seopress'),
                'desc'          => __('Manage your XML - Image - Video - HTML Sitemap.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-xml-sitemap'),
                'filter'        => 'seopress_remove_feature_xml_sitemap',
            ],
            'social' => [
                'title'         => __('Social Networks', 'wp-seopress'),
                'desc'          => __('Open Graph, Twitter Card, Google Knowledge Graph and more...', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-social'),
                'filter'        => 'seopress_remove_feature_social',
            ],
            'google-analytics' => [
                'title'         => __('Analytics', 'wp-seopress'),
                'desc'          => __('Track everything about your visitors with Google Analytics / Matomo / Microsoft Clarity.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-google-analytics'),
                'filter'        => 'seopress_remove_feature_google_analytics',
            ],
            'instant-indexing' => [
                'title'         => __('Instant Indexing', 'wp-seopress'),
                'desc'          => __('Ping Google & Bing to quickly index your content.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-instant-indexing'),
                'filter'        => 'seopress_remove_feature_instant_indexing',
            ],
            'advanced' => [
                'title'         => __('Image SEO & Advanced settings', 'wp-seopress'),
                'desc'          => __('Optimize your images for SEO. Configure advanced settings.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-advanced'),
                'filter'        => 'seopress_remove_feature_advanced',
            ],
        ];

        $features = apply_filters('seopress_features_list_before_tools', $features);

        $features['tools'] = [
            'title'         => __('Tools', 'wp-seopress'),
            'desc'          => __('Import/Export plugin settings from site to site.', 'wp-seopress'),
            'btn_primary'   => admin_url('admin.php?page=seopress-import-export'),
            'filter'        => 'seopress_remove_feature_tools',
            'toggle'        => false,
        ];

        $features = apply_filters('seopress_features_list_after_tools', $features);

        if (! empty($features)) { ?>
    <div class="seopress-card-content">

        <?php foreach ($features as $key => $value) {
            if (isset($value['filter'])) {
                $seopress_feature = apply_filters($value['filter'], true);
            }
            ?>

            <div class="seopress-cart-list inner">

                <?php
                if (true === $seopress_feature) {
                    $title            = isset($value['title']) ? $value['title'] : null;
                    $desc             = isset($value['desc']) ? $value['desc'] : null;
                    $btn_primary      = isset($value['btn_primary']) ? $value['btn_primary'] : '';
                    $toggle           = isset($value['toggle']) ? $value['toggle'] : true;

                    if (true === $toggle) {
                        if ('1' == seopress_get_toggle_option($key)) {
                            $seopress_get_toggle_option = '1';
                        } else {
                            $seopress_get_toggle_option = '0';
                        } ?>
                        <span class="screen-reader-text"><?php printf(__('Toggle %s','wp-seopress'), $title); ?></span>
                        <input type="checkbox" name="toggle-<?php echo $key; ?>" id="toggle-<?php echo $key; ?>" class="toggle" data-toggle="<?php echo $seopress_get_toggle_option; ?>">
                        <label for="toggle-<?php echo $key; ?>"></label>
                    <?php } ?>

                    <a href="<?php echo $btn_primary; ?>">
                        <div class="seopress-card-item">
                            <h3><?php echo $title; ?></h3>
                            <p><?php echo $desc; ?></p>
                        </div>
                    </a>
                <?php
                }
                ?>

            </div>

            <?php
        } ?>
    </div>
    <?php }
    ?>
</div>
