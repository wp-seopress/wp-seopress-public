<?php
    // To prevent calling the plugin directly
    if (! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else { ?>
    <?php $class = '1' !== seopress_get_service('AdvancedOption')->getAppearanceNotification() ? 'is-active' : ''; ?>

    <div id="seopress-notifications-center"
        class="seopress-page-list <?php echo $class; ?>"
        style="display: none">
        <?php
            do_action('seopress_notifications_center_item');

            if ('1' !== seopress_get_service('NoticeOption')->getNoticeReview()) {
                $args = [
                    'id'     => 'notice-review',
                    'title'  => __('You like SEOPress? Please help us by rating us 5 stars!', 'wp-seopress'),
                    'desc'   => __('Support the development and improvement of the plugin by taking 15 seconds of your time to leave us a user review on the official WordPress plugins repository. Thank you!', 'wp-seopress'),
                    'impact' => [
                        'info' => __('Information', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => 'https://wordpress.org/support/view/plugin-reviews/wp-seopress?rate=5#postform',
                        'title'    => __('Rate us!', 'wp-seopress'),
                        'external' => true,
                    ],
                    'icon'       => 'dashicons-thumbs-up',
                    'deleteable' => true,
                ];
                seopress_notification($args);
            }

            if ('1' !== seopress_get_service('NoticeOption')->getNoticeUSM() && '1' !== seopress_get_service('AdvancedOption')->getAccessUniversalMetaboxGutenberg() ) {
                $args = [
                    'id'     => 'notice-usm',
                    'title'  => __('Enable our universal SEO metabox for the Block Editor', 'wp-seopress'),
                    'desc'   => __('By default, our new SEO metabox is disabled for Gutenberg. Test it without further delay!', 'wp-seopress'),
                    'impact' => [
                        'info' => __('Wizard', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-advanced#tab=tab_seopress_advanced_appearance'),
                        'title'    => __('Activate it', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-admin-tools',
                    'deleteable' => true,
                ];
                seopress_notification($args);
            }

            if ('1' != seopress_get_service('NoticeOption')->getNoticeWizard()) {
                $args = [
                    'id'     => 'notice-wizard',
                    'title'  => __('Configure SEOPress in a few minutes with our installation wizard', 'wp-seopress'),
                    'desc'   => __('The best way to quickly setup SEOPress on your site.', 'wp-seopress'),
                    'impact' => [
                        'info' => __('Wizard', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-setup'),
                        'title'    => __('Start the wizard', 'wp-seopress'),
                        'external' => true,
                    ],
                    'icon'       => 'dashicons-admin-tools',
                    'deleteable' => true,
                ];
                seopress_notification($args);
            }

            if ('1' !== seopress_get_service('NoticeOption')->getNoticeSEOConsultant()) {
                $args = [
                    'id'     => 'notice-seo-consultant',
                    'title'  => __('Talk to a SEO consultant', 'wp-seopress'),
                    'desc'   => __('Your site is growing and you want support for your SEO strategy, increase your sales and conversions? We are there for that. Contact us!', 'wp-seopress'),
                    'impact' => [
                        'info' => __('Wizard', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => 'https://www.seopress.org/wordpress-seo-plugins/seo-audit-config/',
                        'fr'       => 'https://www.seopress.org/fr/extensions-seo-wordpress/audit-seo-et-configuration-de-seopress/',
                        'title'    => __('Yes, please', 'wp-seopress'),
                        'external' => true,
                    ],
                    'icon'       => 'dashicons-sos',
                    'deleteable' => true,
                ];
                seopress_notification($args);
            }

            //AMP
            if (is_plugin_active('amp/amp.php')) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeAMPAnalytics()) {
                    $args = [
                        'id'     => 'notice-amp-analytics',
                        'title'  => __('Use Google Analytics with AMP plugin', 'wp-seopress'),
                        'desc'   => __('Your site is using the AMP official plugin. To track users with Google Analytics on AMP pages, please go to this settings page.', 'wp-seopress'),
                        'impact' => [
                            'info' => __('Medium impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('admin.php?page=amp-options#analytics-options'),
                            'title'    => __('Fix this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-chart-area',
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }

            //DIVI SEO options conflict
            $theme = wp_get_theme();
            if ('Divi' == $theme->template || 'Divi' == $theme->parent_theme) {
                $divi_options = get_option('et_divi');
                if (! empty($divi_options)) {
                    if (
                        (array_key_exists('divi_seo_home_title', $divi_options) && 'on' == $divi_options['divi_seo_home_title']) ||
                        (array_key_exists('divi_seo_home_description', $divi_options) && 'on' == $divi_options['divi_seo_home_description']) ||
                        (array_key_exists('divi_seo_home_keywords', $divi_options) && 'on' == $divi_options['divi_seo_home_keywords']) ||
                        (array_key_exists('divi_seo_home_canonical', $divi_options) && 'on' == $divi_options['divi_seo_home_canonical']) ||
                        (array_key_exists('divi_seo_single_title', $divi_options) && 'on' == $divi_options['divi_seo_single_title']) ||
                        (array_key_exists('divi_seo_single_description', $divi_options) && 'on' == $divi_options['divi_seo_single_description']) ||
                        (array_key_exists('divi_seo_single_keywords', $divi_options) && 'on' == $divi_options['divi_seo_single_keywords']) ||
                        (array_key_exists('divi_seo_single_canonical', $divi_options) && 'on' == $divi_options['divi_seo_single_canonical']) ||
                        (array_key_exists('divi_seo_index_canonical', $divi_options) && 'on' == $divi_options['divi_seo_index_canonical']) ||
                        (array_key_exists('divi_seo_index_description', $divi_options) && 'on' == $divi_options['divi_seo_index_description'])
                    ) {
                        $args = [
                            'id'     => 'notice-divi-seo',
                            'title'  => __('We noticed that some SEO DIVI options are enabled!', 'wp-seopress'),
                            'desc'   => __('To avoid any SEO conflicts, please disable every SEO option from <strong>DIVI theme options page, SEO tab</strong>.', 'wp-seopress'),
                            'impact' => [
                                'high' => __('High impact', 'wp-seopress'),
                            ],
                            'link' => [
                                'en'       => admin_url('admin.php?page=et_divi_options#seo-1'),
                                'title'    => __('Fix this!', 'wp-seopress'),
                                'external' => false,
                            ],
                            'icon'       => 'dashicons-admin-plugins',
                            'deleteable' => false,
                        ];
                        seopress_notification($args);
                    }
                }
            }
            if ('1' != get_theme_support('title-tag') && true !== wp_is_block_theme()) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeTitleTag()) {
                    $args = [
                        'id'     => 'notice-title-tag',
                        'title'  => __('Your theme doesn\'t use <strong>add_theme_support(\'title-tag\');</strong>', 'wp-seopress'),
                        'desc'   => __('This error indicates that your theme uses a deprecated function to generate the title tag of your pages. SEOPress will not be able to generate your custom title tags if this error is not fixed.', 'wp-seopress'),
                        'impact' => [
                            'high' => __('High impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'fr'       => 'https://www.seopress.org/fr/support/guides/resoudre-add_theme_support-manquant-dans-votre-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                            'en'       => 'https://www.seopress.org/support/guides/fixing-missing-add_theme_support-in-your-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
                            'title'    => __('Learn more', 'wp-seopress'),
                            'external' => true,
                        ],
                        'icon'       => 'dashicons-admin-customizer',
                        'deleteable' => false,
                    ];
                    seopress_notification($args);
                }
            }
            if (is_plugin_active('swift-performance-lite/performance.php')) {
                if (seopress_get_service('NoticeOption')->getNoticeCacheSitemap() === "1") {
                    if ('1' !== seopress_get_service('NoticeOption')->getNoticeSwift()) {
                        $args = [
                                'id'     => 'notice-swift',
                                'title'  => __('Your XML sitemap is cached!', 'wp-seopress'),
                                'desc'   => __('Swift Performance is caching your XML sitemap. You must disable this option to prevent any compatibility issue (Swift Performance > Settings > Caching, General tab).', 'wp-seopress'),
                                'impact' => [
                                    'high' => __('High impact', 'wp-seopress'),
                                ],
                                'link' => [
                                    'en'       => admin_url('tools.php?page=swift-performance'),
                                    'title'    => __('Fix this!', 'wp-seopress'),
                                    'external' => false,
                                ],
                                'icon'       => 'dashicons-media-spreadsheet',
                                'deleteable' => false,
                            ];
                        seopress_notification($args);
                    }
                }
            }
            $seo_plugins = [
                'wordpress-seo/wp-seo.php'                       => 'Yoast SEO',
                'wordpress-seo-premium/wp-seo-premium.php'       => 'Yoast SEO Premium',
                'all-in-one-seo-pack/all_in_one_seo_pack.php'    => 'All In One SEO',
                'autodescription/autodescription.php'            => 'The SEO Framework',
                'squirrly-seo/squirrly.php'                      => 'Squirrly SEO',
                'seo-by-rank-math/rank-math.php'                 => 'Rank Math',
                'seo-ultimate/seo-ultimate.php'                  => 'SEO Ultimate',
                'wp-meta-seo/wp-meta-seo.php'                    => 'WP Meta SEO',
                'premium-seo-pack/plugin.php'                    => 'Premium SEO Pack',
                'wpseo/wpseo.php'                                => 'wpSEO',
                'platinum-seo-pack/platinum-seo-pack.php'        => 'Platinum SEO Pack',
                'smartcrawl-seo/wpmu-dev-seo.php'                => 'SmartCrawl',
                'seo-pressor/seo-pressor.php'                    => 'SEOPressor',
                'slim-seo/slim-seo.php'                          => 'Slim SEO'
            ];

            foreach ($seo_plugins as $key => $value) {
                if (is_plugin_active($key)) {
                    $args = [
                        'id' => 'notice-seo-plugins',
                        /* translators: %s name of a SEO plugin (eg: Yoast SEO) */
                        'title'  => sprintf(__('We noticed that you use <strong>%s</strong> plugin.', 'wp-seopress'), $value),
                        'desc'   => __('Do you want to migrate all your metadata to SEOPress? Do not use multiple SEO plugins at once to avoid conflicts!', 'wp-seopress'),
                        'impact' => [
                            'high' => __('High impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('admin.php?page=seopress-import-export'),
                            'title'    => __('Migrate!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-admin-plugins',
                        'deleteable' => false,
                    ];
                    seopress_notification($args);
                }
            }
            $indexing_plugins = [
                'indexnow/indexnow-url-submission.php'                       => 'IndexNow',
                'bing-webmaster-tools/bing-url-submission.php'               => 'Bing Webmaster Url Submission',
                'fast-indexing-api/instant-indexing.php'                     => 'Instant Indexing',
            ];

            foreach ($indexing_plugins as $key => $value) {
                if (is_plugin_active($key)) {
                    $args = [
                        'id' => 'notice-indexing-plugins',
                        /* translators: %s name of a WP plugin (eg: IndexNow) */
                        'title'  => sprintf(__('We noticed that you use <strong>%s</strong> plugin.', 'wp-seopress'), $value),
                        'desc'   => __('To prevent any conflicts with our Indexing feature, please disable it.', 'wp-seopress'),
                        'impact' => [
                            'high' => __('High impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('plugins.php'),
                            'title'    => __('Fix this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-admin-plugins',
                        'deleteable' => false,
                    ];
                    seopress_notification($args);
                }
            }
            //Enfold theme
            $avia_options_enfold       = get_option('avia_options_enfold');
            $avia_options_enfold_child = get_option('avia_options_enfold_child');
            $theme                     = wp_get_theme();
            if ('enfold' == $theme->template || 'enfold' == $theme->parent_theme) {
                if ('plugin' != $avia_options_enfold['avia']['seo_robots'] || 'plugin' != $avia_options_enfold_child['avia']['seo_robots']) {
                    if ('1' !== seopress_get_service('NoticeOption')->getNoticeEnfold()) {
                        $args = [
                            'id'     => 'notice-enfold',
                            'title'  => __('Enfold theme is not correctly setup for SEO!', 'wp-seopress'),
                            'desc'   => __('You must disable "Meta tag robots" option from Enfold settings (SEO Support tab) to avoid any SEO issues.', 'wp-seopress'),
                            'impact' => [
                                'low' => __('High impact', 'wp-seopress'),
                            ],
                            'link' => [
                                'en'       => admin_url('admin.php?avia_welcome=true&page=avia'),
                                'title'    => __('Fix this!', 'wp-seopress'),
                                'external' => true,
                            ],
                            'icon'       => 'dashicons-admin-tools',
                            'deleteable' => true,
                        ];
                        seopress_notification($args);
                    }
                }
            }
            if (seopress_get_empty_templates('cpt', 'title')) {
                $args = [
                    'id'     => 'notice-cpt-empty-title',
                    'title'  => __('Global meta title missing for several custom post types!', 'wp-seopress'),
                    'desc'   => seopress_get_empty_templates('cpt', 'title', false),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_single'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-editor-table',
                    'deleteable' => false,
                    'wrap'       => false,
                ];
                seopress_notification($args);
            }
            if (seopress_get_empty_templates('cpt', 'description')) {
                $args = [
                    'id'     => 'notice-cpt-empty-desc',
                    'title'  => __('Global meta description missing for several custom post types!', 'wp-seopress'),
                    'desc'   => seopress_get_empty_templates('cpt', 'description', false),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_single'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-editor-table',
                    'deleteable' => false,
                    'wrap'       => false,
                ];
                seopress_notification($args);
            }
            if (seopress_get_empty_templates('tax', 'title')) {
                $args = [
                    'id'     => 'notice-tax-empty-title',
                    'title'  => __('Global meta title missing for several taxonomies!', 'wp-seopress'),
                    'desc'   => seopress_get_empty_templates('tax', 'title', false),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_tax'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-editor-table',
                    'deleteable' => false,
                    'wrap'       => false,
                ];
                seopress_notification($args);
            }
            if (seopress_get_empty_templates('tax', 'description')) {
                $args = [
                    'id'     => 'notice-tax-empty-templates',
                    'title'  => __('Global meta description missing for several taxonomies!', 'wp-seopress'),
                    'desc'   => seopress_get_empty_templates('tax', 'description', false),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_tax'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-editor-table',
                    'deleteable' => false,
                    'wrap'       => false,
                ];
                seopress_notification($args);
            }

            if (! is_ssl()) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeSSL()) {
                    $args = [
                        'id'     => 'notice-ssl',
                        'title'  => __('Your site doesn\'t use an SSL certificate!', 'wp-seopress'),
                        'desc'   => __('Https is considered by Google as a positive signal for the ranking of your site. It also reassures your visitors for data security, and improves trust.', 'wp-seopress') . '</a>',
                        'impact' => [
                            'low' => __('Low impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => 'https://webmasters.googleblog.com/2014/08/https-as-ranking-signal.html',
                            'title'    => __('Learn more', 'wp-seopress'),
                            'external' => true,
                        ],
                        'icon'       => 'dashicons-unlock',
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }
            if (function_exists('extension_loaded') && ! extension_loaded('dom')) {
                $args = [
                    'id'     => 'notice-dom',
                    'title'  => __('PHP module "DOM" is missing on your server.', 'wp-seopress'),
                    'desc'   => __('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.', 'wp-seopress'),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'fr'       => 'https://www.seopress.org/fr/support/guides/debutez-seopress/',
                        'en'       => 'https://www.seopress.org/support/guides/get-started-seopress/',
                        'title'    => __('Learn more', 'wp-seopress'),
                        'external' => true,
                    ],
                    'deleteable' => false,
                ];
                seopress_notification($args);
            }
            if (function_exists('extension_loaded') && ! extension_loaded('mbstring')) {
                $args = [
                    'id'     => 'notice-mbstring',
                    'title'  => __('PHP module "mbstring" is missing on your server.', 'wp-seopress'),
                    'desc'   => __('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.', 'wp-seopress'),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'fr'       => 'https://www.seopress.org/fr/support/guides/debutez-seopress/',
                        'en'       => 'https://www.seopress.org/support/guides/get-started-seopress/',
                        'title'    => __('Learn more', 'wp-seopress'),
                        'external' => true,
                    ],
                    'deleteable' => false,
                ];
                seopress_notification($args);
            }
            if ('1' !== seopress_get_service('NoticeOption')->getNoticeNoIndex()) {
                if ('1' === seopress_get_service('TitleOption')->getTitleNoIndex() || '1' != get_option('blog_public')) {
                    $args = [
                        'id'     => 'notice-noindex',
                        'title'  => __('Your site is not visible to Search Engines!', 'wp-seopress'),
                        'desc'   => __('You have activated the blocking of the indexing of your site. If your site is under development, this is probably normal. Otherwise, check your settings. Delete this notification using the cross on the right if you are not concerned.', 'wp-seopress'),
                        'impact' => [
                            'high' => __('High impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('options-reading.php'),
                            'title'    => __('Fix this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-warning',
                        'deleteable' => true,
                    ];
                    if (seopress_get_service('TitleOption')->getTitleNoIndex() ==='1') {
                        $args['link']['en'] = admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_advanced');
                    }
                    seopress_notification($args);
                }
            }
            if ('' == get_option('blogname')) {
                $args = [
                    'id'     => 'notice-title-empty',
                    'title'  => __('Your site title is empty!', 'wp-seopress'),
                    'desc'   => __('Your Site Title is used by WordPress, your theme and your plugins including SEOPress. It is an essential component in the generation of title tags, but not only. Enter one!', 'wp-seopress'),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('options-general.php'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'deleteable' => false,
                ];
                seopress_notification($args);
            }
            if ('' == get_option('permalink_structure')) {
                $args = [
                    'id'     => 'notice-permalinks',
                    'title'  => __('Your permalinks are not SEO Friendly! Enable pretty permalinks to fix this.', 'wp-seopress'),
                    'desc'   => __('Why is this important? Showing only the summary of each article significantly reduces the theft of your content by third party sites. Not to mention, the increase in your traffic, your advertising revenue, conversions...', 'wp-seopress'),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('options-permalink.php'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-admin-links',
                    'deleteable' => false,
                ];
                seopress_notification($args);
            }
            if ('0' == get_option('rss_use_excerpt')) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeRSSUseExcerpt()) {
                    $args = [
                        'id'     => 'notice-rss-use-excerpt',
                        'title'  => __('Your RSS feed shows full text!', 'wp-seopress'),
                        'desc'   => __('Why is this important? Showing only the summary of each article significantly reduces the theft of your content by third party sites. Not to mention, the increase in your traffic, your advertising revenue, conversions...', 'wp-seopress'),
                        'impact' => [
                            'medium' => __('Medium impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('options-reading.php'),
                            'title'    => __('Fix this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-rss',
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }

            if ('' === seopress_get_service('GoogleAnalyticsOption')->getUA() && '' === seopress_get_service('GoogleAnalyticsOption')->getGA4() && '1' === seopress_get_service('GoogleAnalyticsOption')->getEnableOption()) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeGAIds()) {
                    $args = [
                        'id'     => 'notice-ga-ids',
                        'title'  => __('You have activated Google Analytics tracking without adding identifiers!', 'wp-seopress'),
                        'desc'   => __('Google Analytics will not track your visitors until you finish the configuration.', 'wp-seopress'),
                        'impact' => [
                            'medium' => __('Medium impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('admin.php?page=seopress-google-analytics'),
                            'title'    => __('Fix this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-chart-area',
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }
            if ('1' == get_option('page_comments')) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeDivideComments()) {
                    $args = [
                        'id'     => 'notice-divide-comments',
                        'title'  => __('Break comments into pages is ON!', 'wp-seopress'),
                        'desc'   => __('Enabling this option will create duplicate content for each article beyond x comments. This can have a disastrous effect by creating a large number of poor quality pages, and slowing the Google bot unnecessarily, so your ranking in search results.', 'wp-seopress'),
                        'impact' => [
                            'high' => __('High impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('options-discussion.php'),
                            'title'    => __('Disable this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'icon'       => 'dashicons-admin-comments',
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }
            if (get_option('posts_per_page') < '16') {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticePostsNumber()) {
                    $args = [
                        'id'     => 'notice-posts-number',
                        'title'  => __('Display more posts per page on homepage and archives', 'wp-seopress'),
                        'desc'   => __('To reduce the number pages search engines have to crawl to find all your articles, it is recommended displaying more posts per page. This should not be a problem for your users. Using mobile, we prefer to scroll down rather than clicking on next page links.', 'wp-seopress'),
                        'impact' => [
                            'medium' => __('Medium impact', 'wp-seopress'),
                        ],
                        'link' => [
                            'en'       => admin_url('options-reading.php'),
                            'title'    => __('Fix this!', 'wp-seopress'),
                            'external' => false,
                        ],
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }
            if ('1' !== seopress_get_service('SitemapOption')->isEnabled()) {
                $args = [
                    'id'     => 'notice-xml-sitemaps',
                    'title'  => __('You don\'t have an XML Sitemap!', 'wp-seopress'),
                    'desc'   => __('XML Sitemaps are useful to facilitate the crawling of your content by search engine robots. Indirectly, this can benefit your ranking by reducing the crawl bugdet.', 'wp-seopress'),
                    'impact' => [
                        'medium' => __('Medium impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => admin_url('admin.php?page=seopress-xml-sitemap'),
                        'title'    => __('Fix this!', 'wp-seopress'),
                        'external' => false,
                    ],
                    'icon'       => 'dashicons-warning',
                    'deleteable' => false,
                ];
                seopress_notification($args);
            }
            if ('1' !== seopress_get_service('NoticeOption')->getNoticeGoogleBusiness()) {
                $args = [
                    'id'     => 'notice-google-business',
                    'title'  => __('Do you have a Google My Business page? It\'s free!', 'wp-seopress'),
                    'desc'   => __('Local Business websites should have a My Business page to improve visibility in search results. Click on the cross on the right to delete this notification if you are not concerned.', 'wp-seopress'),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => 'https://www.google.com/business/go/',
                        'title'    => __('Create your page now!', 'wp-seopress'),
                        'external' => true,
                    ],
                    'deleteable' => true,
                ];
                seopress_notification($args);
            }
            if ('1' !== seopress_get_service('NoticeOption')->getNoticeSearchConsole() && '' === seopress_get_service('AdvancedOption')->getAdvancedGoogleVerification()) {
                $args = [
                    'id'     => 'notice-search-console',
                    'title'  => __('Add your site to Google. It\'s free!', 'wp-seopress'),
                    'desc'   => __('Is your brand new site online? So reference it as quickly as possible on Google to get your first visitors via Google Search Console. Already the case? Click on the cross on the right to remove this alert.', 'wp-seopress'),
                    'impact' => [
                        'high' => __('High impact', 'wp-seopress'),
                    ],
                    'link' => [
                        'en'       => 'https://www.google.com/webmasters/tools/home',
                        'title'    => __('Add your site to Search Console!', 'wp-seopress'),
                        'external' => true,
                    ],
                    'deleteable' => true,
                ];
                seopress_notification($args);
            }
            if (! is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeGoPro() && '' === seopress_get_service('NoticeOption')->getNoticeGoPro()) {
                    $args = [
                        'id'     => 'notice-go-pro',
                        'title'  => __('Take your SEO to the next level with SEOPress PRO!', 'wp-seopress'),
                        'desc'   => __('The PRO version of SEOPress allows you to easily manage your structured data (schemas), add a breadcrumb optimized for SEO and accessibility, improve SEO for WooCommerce, gain productivity with our import / export tool from a CSV of your metadata and so much more.', 'wp-seopress'),
                        'impact' => [
                            'info' => __('PRO', 'wp-seopress'),
                        ],
                        'link' => [
                            'fr'       => 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-pro/?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard',
                            'en'       => 'https://www.seopress.org/wordpress-seo-plugins/pro/?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard',
                            'title'    => __('Upgrade now!', 'wp-seopress'),
                            'external' => true,
                        ],
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }
            if (! is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                if ('1' !== seopress_get_service('NoticeOption')->getNoticeGoInsights() && '' === seopress_get_service('NoticeOption')->getNoticeGoInsights()) {
                    $args = [
                        'id'     => 'notice-go-insights',
                        'title'  => __('Discover SEOPress Insights, off-site SEO plugin for WordPress!', 'wp-seopress'),
                        'desc'   => __('Rank & Backlink Tracking directly in your WordPress admin with competition, Google Trends, email and Slack notifications.', 'wp-seopress'),
                        'impact' => [
                            'info' => __('Insights', 'wp-seopress'),
                        ],
                        'link' => [
                            'fr'       => 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-insights/?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard',
                            'en'       => 'https://www.seopress.org/wordpress-seo-plugins/insights/?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard',
                            'title'    => __('Upgrade now!', 'wp-seopress'),
                            'external' => true,
                        ],
                        'deleteable' => true,
                    ];
                    seopress_notification($args);
                }
            }
        ?>
    </div>
    <!--#seopress-notifications-center-->
<?php
}
