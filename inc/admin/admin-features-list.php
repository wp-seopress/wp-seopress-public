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
                'desc'          => __('Manage your XML - Image - Video - HTML Sitemap', 'wp-seopress'),
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
                'desc'          => __('Track everything about your visitors with Google Analytics / Matomo', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-google-analytics'),
                'filter'        => 'seopress_remove_feature_google_analytics',
            ],
            'instant-indexing' => [
                'title'         => __('Instant Indexing', 'wp-seopress'),
                'desc'          => __('Ping Google & Bing to quickly index your content', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-instant-indexing'),
                'filter'        => 'seopress_remove_feature_instant_indexing',
            ],
            'advanced' => [
                'title'         => __('Advanced', 'wp-seopress'),
                'desc'          => __('Advanced SEO options for advanced users!', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-advanced'),
                'filter'        => 'seopress_remove_feature_advanced',
            ],
        ];
        if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
            $features['insights'] = [
                'title'         => __('Insights', 'wp-seopress'),
                'desc'          => __('Track your keyword positions and backlinks directly in your WordPress.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-insights'),
                'toggle'        => false,
                'filter'        => 'seopress_remove_feature_insights',
            ];
        }
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $features['woocommerce'] = [
                    'title'         => __('WooCommerce', 'wp-seopress'),
                    'desc'          => __('Improve WooCommerce SEO', 'wp-seopress'),
                    'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_woocommerce'),
                    'filter'        => 'seopress_remove_feature_woocommerce',
                ];
            $features['edd'] = [
                    'title'         => __('Easy Digital Downloads', 'wp-seopress'),
                    'desc'          => __('Improve Easy Digital Downloads SEO', 'wp-seopress'),
                    'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_edd'),
                    'filter'        => 'seopress_remove_feature_edd',
                ];
            $features['local-business'] = [
                    'title'         => __('Local Business', 'wp-seopress'),
                    'desc'          => __('Add Google Local Business data type', 'wp-seopress'),
                    'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_local_business'),
                    'filter'        => 'seopress_remove_feature_local_business',
                ];
            $features['dublin-core'] = [
                'title'         => __('Dublin Core', 'wp-seopress'),
                'desc'          => __('Add Dublin Core meta tags', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_dublin_core'),
                'filter'        => 'seopress_remove_feature_dublin_core',
            ];
            $features['rich-snippets'] = [
                'title'         => __('Structured Data Types', 'wp-seopress'),
                'desc'          => __('Add data types to your content: articles, courses, recipes, videos, events, products and more.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets'),
                'filter'        => 'seopress_remove_feature_schemas',
            ];
            $features['breadcrumbs'] = [
                'title'         => __('Breadcrumbs', 'wp-seopress'),
                'desc'          => __('Enable Breadcrumbs for your theme and improve your SEO in SERPs', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_breadcrumbs'),
                'filter'        => 'seopress_remove_feature_breadcrumbs',
            ];
            $features['page-speed'] = [
                'title'         => __('Google Page Speed', 'wp-seopress'),
                'desc'          => __('Track your website performance to improve SEO with Google Page Speed', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed'),
                'filter'        => 'seopress_remove_feature_page_speed',
                'toggle'        => false,
            ];
            if (! is_multisite() || (is_multisite() && defined('SUBDOMAIN_INSTALL') && true === constant('SUBDOMAIN_INSTALL'))) {//subdomains or single site
                $features['robots'] = [
                    'title'       => __('robots.txt', 'wp-seopress'),
                    'desc'        => __('Edit your robots.txt file', 'wp-seopress'),
                    'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_robots'),
                    'filter'      => 'seopress_remove_feature_robots',
                ];
            }
            $features['news'] = [
                'title'         => __('Google News Sitemap', 'wp-seopress'),
                'desc'          => __('Optimize your site for Google News', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_news'),
                'filter'        => 'seopress_remove_feature_news',
            ];
            $features['rich-snippets'] = [
                'title'       => __('Schemas', 'wp-seopress'),
                'desc'        => __('Create / manage your schemas', 'wp-seopress'),
                'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets'),
                'filter'      => 'seopress_remove_feature_schemas',
            ];
            $features['404'] = [
                'title'       => __('Redirections', 'wp-seopress'),
                'desc'        => __('Monitor 404, create 301, 302 and 307 redirections', 'wp-seopress'),
                'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_404'),
                'filter'      => 'seopress_remove_feature_redirects',
            ];
            $features['bot'] = [
                'title'       => __('Broken links', 'wp-seopress'),
                'desc'        => __('Scan your site to find SEO problems.', 'wp-seopress'),
                'btn_primary' => admin_url('admin.php?page=seopress-bot-batch'),
                'filter'      => 'seopress_remove_feature_bot',
            ];
            $features['rewrite'] = [
                'title'         => __('URL Rewriting', 'wp-seopress'),
                'desc'          => __('Customize your permalinks.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rewrite'),
                'filter'        => 'seopress_remove_feature_rewrite',
            ];
            if (! is_multisite()) {
                $features['htaccess'] = [
                    'title'         => __('.htaccess', 'wp-seopress'),
                    'desc'          => __('Edit your htaccess file.', 'wp-seopress'),
                    'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_htaccess'),
                    'filter'        => 'seopress_remove_feature_htaccess',
                    'toggle'        => false,
                ];
            }
            $features['rss'] = [
                'title'         => __('RSS', 'wp-seopress'),
                'desc'          => __('Configure default WordPress RSS.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rss'),
                'filter'        => 'seopress_remove_feature_rss',
                'toggle'        => false,
            ];
        }
        $features['tools'] = [
            'title'         => __('Tools', 'wp-seopress'),
            'desc'          => __('Import/Export plugin settings from site to site.', 'wp-seopress'),
            'btn_primary'   => admin_url('admin.php?page=seopress-import-export'),
            'filter'        => 'seopress_remove_feature_tools',
            'toggle'        => false,
        ];
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $features['license'] = [
                'title'         => __('License', 'wp-seopress'),
                'desc'          => __('Edit your license key.', 'wp-seopress'),
                'btn_primary'   => admin_url('admin.php?page=seopress-license'),
                'filter'        => 'seopress_remove_feature_license',
                'toggle'        => false,
            ];
        }

        if (! empty($features)) { ?>
    <div class="seopress-card-content">

        <?php foreach ($features as $key => $value) {
            if (isset($value['filter'])) {
                $seopress_feature = apply_filters($value['filter'], true);
            }

            if (true === $seopress_feature) {
                $title            = isset($value['title']) ? $value['title'] : null;
                $desc             = isset($value['desc']) ? $value['desc'] : null;
                $btn_primary      = isset($value['btn_primary']) ? $value['btn_primary'] : '';
                $toggle           = isset($value['toggle']) ? $value['toggle'] : true; ?>
        <a href="<?php echo $btn_primary; ?>"
            class="seopress-cart-list inner">
            <?php
                            if (true === $toggle) {
                                if ('1' == seopress_get_toggle_option($key)) {
                                    $seopress_get_toggle_option = '1';
                                } else {
                                    $seopress_get_toggle_option = '0';
                                } ?>
            <input type="checkbox" name="toggle-<?php echo $key; ?>"
                id="toggle-<?php echo $key; ?>" class="toggle"
                data-toggle="<?php echo $seopress_get_toggle_option; ?>">
            <label for="toggle-<?php echo $key; ?>"></label>
            <?php
                            } ?>
            <div class="seopress-card-item">
                <h3><?php echo $title; ?>
                </h3>
                <p><?php echo $desc; ?>
                </p>
            </div>
        </a>
        <?php
            }
        } ?>
    </div>
    <?php }
    ?>
</div>
