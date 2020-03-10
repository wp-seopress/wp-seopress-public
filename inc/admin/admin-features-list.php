<?php
    // To prevent calling the plugin directly
    if ( !function_exists( 'add_action' ) ) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    } 
?>
<div class="seopress-page-list">
    <div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>
    <?php
        $features = [];
        $features = [
            'titles' => [
                'icon'  => 'dashicons-editor-table',
                'title' => __('Titles & metas','wp-seopress'),
                'desc'  => __('Manage all your titles & metas for post types, taxonomies, archives...', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-titles' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-metas/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to manage your titles and meta descriptions - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_titles'
            ],
            'xml-sitemap' => [
                'icon'  => 'dashicons-media-spreadsheet',
                'title' => __('XML / Image / Video / HTML Sitemap','wp-seopress'),
                'desc'  => __('Manage your XML / Image / Video / HTML Sitemap', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-xml-sitemap' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/enable-xml-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to enable your XML Sitemaps - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_xml_sitemap'
            ],
            'social' => [
                'icon'  => 'dashicons-share',
                'title' => __('Social Networks','wp-seopress'),
                'desc'  => __('Open Graph, Twitter Card, Google Knowledge Graph and more...', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-social' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/enable-google-knowledge-graph/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/activer-google-knowledge-graph/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to enable Google Knowledge Graph - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_social'
            ],
            'google-analytics' => [
                'icon'  => 'dashicons-chart-area',
                'title' => __('Google Analytics','wp-seopress'),
                'desc'  => __('Track everything about your visitors with Google Analytics', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-google-analytics' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/debutez-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to getting started with Google Analytics - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_google_analytics'
            ],
            'advanced' => [
                'icon'  => 'dashicons-admin-tools',
                'title' => __('Advanced','wp-seopress'),
                'desc'  => __('Advanced SEO options for advanced users!', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-advanced' ) => __('Manage','wp-seopress')
                ],
                'filter' => 'seopress_remove_feature_advanced'
            ]
        ];
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $features['woocommerce'] = [
                    'icon'  => 'dashicons-cart',
                    'title' => __('WooCommerce','wp-seopress'),
                    'desc'  => __('Improve WooCommerce SEO', 'wp-seopress'),
                    'btn_secondary' => [
                        admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_woocommerce$1' ) => __('Manage','wp-seopress')
                    ],
                    'help' => [
                        'en'        => 'https://www.seopress.org/blog/woocommerce-seo-seopress/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                        'fr'        => 'https://www.seopress.org/fr/blog/woocommerce-seo-seopress-le-guide/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                        'title'     => __('Read our guide','wp-seopress'),
                        'aria'      => __('Guide to optimize your WooCommerce SEO - new window','wp-seopress'),
                        'external'  => true
                    ],
                    'filter' => 'seopress_remove_feature_woocommerce'
                ];
            $features['edd'] = [
                    'icon'  => 'dashicons-cart',
                    'title' => __('Easy Digital Downloads','wp-seopress'),
                    'desc'  => __('Improve Easy Digital Downloads SEO', 'wp-seopress'),
                    'btn_secondary' => [
                        admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_edd$13' ) => __('Manage','wp-seopress')
                    ],
                    'filter' => 'seopress_remove_feature_edd'
                ];
            $features['local-business'] = [
                    'icon'  => 'dashicons-store',
                    'title' => __('Local Business','wp-seopress'),
                    'desc'  => __('Add Google Local Business data type', 'wp-seopress'),
                    'btn_secondary' => [
                        admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_local_business$10' ) => __('Manage','wp-seopress')
                    ],
                    'filter' => 'seopress_remove_feature_local_business'
                ];
            $features['dublin-core'] = [
                'icon'  => 'dashicons-welcome-learn-more',
                'title' => __('Dublin Core','wp-seopress'),
                'desc'  => __('Add Dublin Core meta tags', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_dublin_core$8' ) => __('Manage','wp-seopress')
                ],
                'filter' => 'seopress_remove_feature_dublin_core'
            ];
            $features['rich-snippets'] = [
                'icon'  => 'dashicons-media-spreadsheet',
                'title' => __('Structured Data Types','wp-seopress'),
                'desc'  => __('Add data types to your content: articles, courses, recipes, videos, events, products and more.', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets$9' ) => __('Manage','wp-seopress')
                ],
                'btn_primary' => [
                    admin_url( 'edit.php?post_type=seopress_schemas' ) => __('See schemas','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/#structured-data-types?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/#types-de-donnees-structurees?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to add schemas with SEOPress PRO - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_schemas'
            ];
            $features['breadcrumbs'] = [
                'icon'  => 'dashicons-feedback',
                'title' => __('Breadcrumbs','wp-seopress'),
                'desc'  => __('Enable Breadcrumbs for your theme and improve your SEO in SERPs', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_breadcrumbs$2' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/enable-breadcrumbs/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/activer-fil-dariane/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to enable Breadcrumbs - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_breadcrumbs'
            ];
            $features['page-speed'] = [
                'icon'  => 'dashicons-performance',
                'title' => __('Google Page Speed','wp-seopress'),
                'desc'  => __('Track your website performance to improve SEO with Google Page Speed', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed$3' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/analyse-site-google-page-speed/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/analyser-site-google-page-speed/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to analyze your site with Google Page Speed - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_page_speed',
                'toggle' => false
            ];
            if (!is_multisite()) {
                $features['robots'] = [
                    'icon'  => 'dashicons-media-text',
                    'title' => __('robots.txt','wp-seopress'),
                    'desc'  => __('Edit your robots.txt file', 'wp-seopress'),
                    'btn_primary' => [
                        get_home_url().'/robots.txt' => __('View your robots.txt','wp-seopress')
                    ],
                    'btn_secondary' => [
                        admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_robots$4' ) => __('Manage','wp-seopress')
                    ],
                    'help' => [
                        'en'        => 'https://www.seopress.org/support/guides/edit-robots-txt-file/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                        'fr'        => 'https://www.seopress.org/fr/support/guides/editer-fichier-robots-txt/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                        'title'     => __('Read our guide','wp-seopress'),
                        'aria'      => __('Guide to edit your robots.txt file - new window','wp-seopress'),
                        'external'  => true
                    ],
                    'filter' => 'seopress_remove_feature_robots'
                ];
            }
            $features['news'] = [
                'icon'  => 'dashicons-admin-post',
                'title' => __('Google News Sitemap','wp-seopress'),
                'desc'  => __('Optimize your site for Google News', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_news$5' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/enable-google-news-xml-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-xml-google-news/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to edit your robots.txt file - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_news'
            ];
            $features['rich-snippets'] = [
                'icon'  => 'dashicons-media-spreadsheet',
                'title' => __('Schemas','wp-seopress'),
                'desc'  => __('Create / manage your schemas', 'wp-seopress'),
                'btn_primary' => [
                    admin_url( 'edit.php?post_type=seopress_schemas' ) => __('See schemas','wp-seopress')
                ],
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_schemas$9' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/#structured-data-types?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/#types-de-donnees-structurees?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to add schemas with SEOPress PRO - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_schemas'
            ];
            $features['404'] = [
                'icon'  => 'dashicons-admin-links',
                'title' => __('Redirections','wp-seopress'),
                'desc'  => __('Monitor 404, create 301, 302 and 307 redirections', 'wp-seopress'),
                'btn_primary' => [
                    admin_url( 'edit.php?post_type=seopress_404' ) => __('See redirects','wp-seopress')
                ],
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_404$6' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/redirections/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/activer-redirections-301-surveillance-404/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to enable 301 redirections and 404 monitoring - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_redirects'
            ];
            $features['bot'] = [
                'icon'  => 'dashicons-admin-generic',
                'title' => __('Broken links','wp-seopress'),
                'desc'  => __('Scan your site to find SEO problems.', 'wp-seopress'),
                'btn_primary' => [
                    admin_url( 'edit.php?post_type=seopress_bot' ) => __('See broken links','wp-seopress')
                ],
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-bot-batch' ) => __('Scan','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/detect-broken-links/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/detecter-liens-casses/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to find broken links with SEOPress PRO - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_bot'
            ];
            $features['backlinks'] = [
                'icon'  => 'dashicons-admin-links',
                'title' => __('Backlinks','wp-seopress'),
                'desc'  => __('Check your backlinks with Majestic API.', 'wp-seopress'),
                'btn_primary' => [
                    admin_url( 'edit.php?post_type=seopress_backlinks' ) => __('See backlinks','wp-seopress')
                ],
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_backlinks$12' ) => __('Manage','wp-seopress')
                ],
                'filter' => 'seopress_remove_feature_backlinks',
                'toggle' => false
            ];
            $features['rewrite'] = [
                'icon'  => 'dashicons-admin-links',
                'title' => __('URL Rewriting','wp-seopress'),
                'desc'  => __('Customize your permalinks.', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rewrite$14' ) => __('Manage','wp-seopress')
                ],
                'filter' => 'seopress_remove_feature_rewrite'
            ];
            if (!is_multisite()) {
                $features['htaccess'] = [
                    'icon'  => 'dashicons-media-text',
                    'title' => __('.htaccess','wp-seopress'),
                    'desc'  => __('Edit your htaccess file.', 'wp-seopress'),
                    'btn_secondary' => [
                        admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_htaccess$7' ) => __('Manage','wp-seopress')
                    ],
                    'filter' => 'seopress_remove_feature_htaccess',
                    'toggle' => false
                ];
            }
            $features['rss'] = [
                'icon'  => 'dashicons-rss',
                'title' => __('RSS','wp-seopress'),
                'desc'  => __('Configure default WordPress RSS.', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rss$11' ) => __('Manage','wp-seopress')
                ],
                'filter' => 'seopress_remove_feature_rss',
                'toggle' => false
            ];
        }
        $features['tools'] = [
            'icon'  => 'dashicons-admin-settings',
            'title' => __('Tools','wp-seopress'),
            'desc'  => __('Import/Export plugin settings from site to site.', 'wp-seopress'),
            'btn_secondary' => [
                admin_url( 'admin.php?page=seopress-import-export' ) => __('Manage','wp-seopress')
            ],
            'help' => [
                'en'        => 'https://www.seopress.org/support/guides/export-import-reset-settings/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                'fr'        => 'https://www.seopress.org/fr/support/guides/exporter-importer-remise-a-niveau-parametres/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                'title'     => __('Read our guide','wp-seopress'),
                'aria'      => __('Guide to Export/Import/Reset settings - new window','wp-seopress'),
                'external'  => true
            ],
            'filter' => 'seopress_remove_feature_tools',
            'toggle' => false
        ];
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $features['license'] = [
                'icon'  => 'dashicons-admin-network',
                'title' => __('License','wp-seopress'),
                'desc'  => __('Edit your license key.', 'wp-seopress'),
                'btn_secondary' => [
                    admin_url( 'admin.php?page=seopress-license' ) => __('Manage','wp-seopress')
                ],
                'help' => [
                    'en'        => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'fr'        => 'https://www.seopress.org/fr/support/guides/activer-licence-seopress-pro/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                    'title'     => __('Read our guide','wp-seopress'),
                    'aria'      => __('Guide to activate SEOPress PRO - new window','wp-seopress'),
                    'external'  => true
                ],
                'filter' => 'seopress_remove_feature_license',
                'toggle' => false
            ];
        }

        if (!empty($features)) {
            foreach($features as $key => $value) {
                if (isset( $value['filter'] ) ) {
                    $seopress_feature = apply_filters($value['filter'], true);
                }

                if ($seopress_feature === true) { 
                    $icon             = isset( $value['icon'] ) ? $value['icon'] : NULL;
                    $title            = isset( $value['title'] ) ? $value['title'] : NULL;
                    $desc             = isset( $value['desc'] ) ? $value['desc'] : NULL;
                    $btn_primary      = isset( $value['btn_primary'] ) ? $value['btn_primary'] : NULL;
                    $btn_secondary    = isset( $value['btn_secondary'] ) ? $value['btn_secondary'] : NULL;
                    $help             = isset( $value['help'] ) ? $value['help'] : NULL;
                    $toggle           = isset( $value['toggle'] ) ? $value['toggle'] : true;
                    ?>
                    <div class="seopress-feature">
                        <span class="inner">
                            <div class="img-tool">
                                <span class="dashicons <?php echo $icon; ?>"></span>
                            </div>
                            <h3><?php echo $title; ?></h3>
                            <p><?php echo $desc; ?></p>
                            
                            <?php if ($btn_secondary || $btn_primary) { ?>
                                <div class="wrap-btn">

                                    <?php if ($btn_secondary) { ?>
                                        <a class="button-secondary" href="<?php echo key($btn_secondary); ?>"><?php echo reset($btn_secondary); ?></a>
                                    <?php } ?>

                                    <?php if ($btn_primary) { ?>
                                        <a class="button-secondary view-redirects" href="<?php echo key($btn_primary); ?>"><?php echo reset($btn_primary); ?></a>
                                    <?php } ?>

                                </div>
                            <?php }
                            if ($toggle === true) {
                                if(seopress_get_toggle_option($key) =='1') { 
                                    $seopress_get_toggle_option = '1';
                                } else { 
                                    $seopress_get_toggle_option = '0';
                                }
                            ?>
                            <input type="checkbox" name="toggle-<?php echo $key; ?>" id="toggle-<?php echo $key; ?>" class="toggle" data-toggle="<?php echo $seopress_get_toggle_option; ?>">
                            <label for="toggle-<?php echo $key; ?>"></label>
                            <?php
                                if($seopress_get_toggle_option =='1') { 
                                    echo '<span id="'.$key.'-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable','wp-seopress').'</span>';
                                    echo '<span id="'.$key.'-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable','wp-seopress').'</span>';
                                } else { 
                                    echo '<span id="'.$key.'-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable','wp-seopress').'</span>';
                                    echo '<span id="'.$key.'-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable','wp-seopress').'</span>';
                                }
                            }
                                $href = '';
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr' && isset($help['fr'])) {
                                        $href = ' href="'.$help['fr'].'"';
                                    } elseif (isset($help['en'])) {
                                        $href = ' href="'.$help['en'].'"';
                                    }
                                }

                                $target = '';
                                if (isset($help['external']) && $help['external'] === true) {
                                    $target = ' target="_blank"';
                                }

                                $aria = '';
                                if (isset($help['aria'])) {
                                    $aria = '<span class="screen-reader-text">'.$help['aria'].'</span>';
                                }

                                $help_title = '';
                                if (isset($help['title'])) {
                                    $help_title = 'title="'.$help['title'].'"';
                                }
                            if ($href !='') { ?>
                                <a <?php echo $href; ?> <?php echo $target; ?> class="seopress-doc" <?php echo $help_title; ?>>
                                    <span class="dashicons dashicons-editor-help"></span>
                                    <?php echo $aria; ?>
                                </a>
                            <?php } ?>
                        </span>
                    </div>
                <?php 
                }
            }
        }
    ?>
</div>