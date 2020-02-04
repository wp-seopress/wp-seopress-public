<?php
    //Notifications Center
    function seopress_advanced_appearance_notifications_option() {
        $seopress_advanced_appearance_notifications_option = get_option("seopress_advanced_option_name");
        if ( ! empty ( $seopress_advanced_appearance_notifications_option ) ) {
            foreach ($seopress_advanced_appearance_notifications_option as $key => $seopress_advanced_appearance_notifications_value)
                $options[$key] = $seopress_advanced_appearance_notifications_value;
                if (isset($seopress_advanced_appearance_notifications_option['seopress_advanced_appearance_notifications'])) { 
                return $seopress_advanced_appearance_notifications_option['seopress_advanced_appearance_notifications'];
                }
        }
    }
    //SEO Tools
    function seopress_advanced_appearance_seo_tools_option() {
        $seopress_advanced_appearance_seo_tools_option = get_option("seopress_advanced_option_name");
        if ( ! empty ( $seopress_advanced_appearance_seo_tools_option ) ) {
            foreach ($seopress_advanced_appearance_seo_tools_option as $key => $seopress_advanced_appearance_seo_tools_value)
                $options[$key] = $seopress_advanced_appearance_seo_tools_value;
                if (isset($seopress_advanced_appearance_seo_tools_option['seopress_advanced_appearance_seo_tools'])) { 
                return $seopress_advanced_appearance_seo_tools_option['seopress_advanced_appearance_seo_tools'];
                }
        }
    }
    //Useful links
    function seopress_advanced_appearance_useful_links_option() {
        $seopress_advanced_appearance_useful_links_option = get_option("seopress_advanced_option_name");
        if ( ! empty ( $seopress_advanced_appearance_useful_links_option ) ) {
            foreach ($seopress_advanced_appearance_useful_links_option as $key => $seopress_advanced_appearance_useful_links_value)
                $options[$key] = $seopress_advanced_appearance_useful_links_value;
                if (isset($seopress_advanced_appearance_useful_links_option['seopress_advanced_appearance_useful_links'])) { 
                return $seopress_advanced_appearance_useful_links_option['seopress_advanced_appearance_useful_links'];
                }
        }
    }
?>     

<?php if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
    //do nothing
} else { ?>
    <div id="seopress-admin-tabs" class="wrap">
        <?php 
            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                $dashboard_settings_tabs = array(
                    'tab_seopress_notifications' => __( "Notifications Center", "wp-seopress" ),
                    'tab_seopress_seo_tools' => __( "SEO Tools", "wp-seopress" ),
                    'tab_seopress_links' => __( "Useful links", "wp-seopress" )
                );
            } else {
                $dashboard_settings_tabs = array(
                    'tab_seopress_notifications' => __( "Notifications Center", "wp-seopress" ),
                    'tab_seopress_links' => __( "Useful links", "wp-seopress" )
                );
            }

            if (seopress_advanced_appearance_notifications_option() !='') {
                unset($dashboard_settings_tabs['tab_seopress_notifications']);
            }
            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                if (seopress_advanced_appearance_seo_tools_option() !='') {
                    unset($dashboard_settings_tabs['tab_seopress_seo_tools']);
                }
            }
            if (seopress_advanced_appearance_useful_links_option() !='') {
                unset($dashboard_settings_tabs['tab_seopress_links']);
            }
            
            echo '<div class="nav-tab-wrapper">';
            foreach ( $dashboard_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-option#tab=' . $tab_key . '">' . $tab_caption . '</a>';
            }
            echo '</div>';
        ?>

        <div class="wrap-seopress-tab-content">
            <?php if(seopress_advanced_appearance_notifications_option() !='1') { ?>
                <div id="tab_seopress_notifications" class="seopress-tab <?php if ($current_tab == 'tab_seopress_notifications') { echo 'active'; } ?>">
                    <div id="seopress-notifications-center">
                        <?php 
                            function seopress_get_hidden_notices_wizard_option() {
                                $seopress_get_hidden_notices_wizard_option = get_option("seopress_notices");
                                if ( ! empty ( $seopress_get_hidden_notices_wizard_option ) ) {
                                    foreach ($seopress_get_hidden_notices_wizard_option as $key => $seopress_get_hidden_notices_wizard_value)
                                        $options[$key] = $seopress_get_hidden_notices_wizard_value;
                                        if (isset($seopress_get_hidden_notices_wizard_option['notice-wizard'])) {
                                            return $seopress_get_hidden_notices_wizard_option['notice-wizard'];
                                        }
                                }
                            }
                            if(seopress_get_hidden_notices_wizard_option() =='1') {
                                //do nothing
                            } else { ?>
                                <div id="notice-wizard-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('Configure SEOPress in a few minutes with our installation wizard','wp-seopress'); ?>
                                        </p>
                                        <p>
                                            <?php _e('The best way to quickly setup SEOPress on your site.','wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="notice-right">
                                        <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-setup' ); ?>"><?php _e('Start the wizard','wp-seopress'); ?></a>
                                        <span name="notice-wizard" id="notice-wizard" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-wizard"></span>
                                    </div>
                                </div>
                            <?php }
                        ?>
                        <?php if (get_theme_support('title-tag') !='1') {
                            function seopress_get_hidden_notices_title_tag_option() {
                                $seopress_get_hidden_notices_title_tag_option = get_option("seopress_notices");
                                if ( !empty ( $seopress_get_hidden_notices_title_tag_option ) ) {
                                    foreach ($seopress_get_hidden_notices_title_tag_option as $key => $seopress_get_hidden_notices_title_tag_value)
                                        $options[$key] = $seopress_get_hidden_notices_title_tag_value;
                                        if (isset($seopress_get_hidden_notices_title_tag_option['notice-title-tag'])) { 
                                        return $seopress_get_hidden_notices_title_tag_option['notice-title-tag'];
                                        }
                                }
                            }
                            if(seopress_get_hidden_notices_title_tag_option() =='1') { 
                                //do nothing
                            } else { ?>
                                <div id="notice-title-tag-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('Your theme doesn\'t use <strong>add_theme_support(\'title-tag\');</strong>','wp-seopress'); ?>
                                            <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                        </p>
                                        <p>
                                            <?php _e('This error indicates that your theme uses a deprecated function to generate the title tag of your pages. SEOPress will not be able to generate your custom title tags if this error is not fixed.','wp-seopress'); ?>
                                        </p>
                                    </div>

                                    <?php
                                    if (function_exists('seopress_get_locale')) {
                                        if (seopress_get_locale() =='fr') {
                                            $seopress_docs_link['support']['title-tag'] = 'https://www.seopress.org/fr/support/guides/resoudre-add_theme_support-manquant-dans-votre-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        } else {
                                            $seopress_docs_link['support']['title-tag'] = 'https://www.seopress.org/support/guides/fixing-missing-add_theme_support-in-your-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                        }
                                    } ?>
                                    <div class="notice-right">
                                        <?php echo '<a class="button-primary" href="'.$seopress_docs_link['support']['title-tag'].'" target="_blank">'.__('Learn more','wp-seopress').'</a>'; ?>
                                        <span name="notice-title-tag" id="notice-title-tag" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-title-tag"></span>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                        <?php 
                            $seo_plugins = array(
                                'wordpress-seo/wp-seo.php' => 'Yoast SEO',
                                'wordpress-seo-premium/wp-seo-premium.php' => 'Yoast SEO Premium',
                                'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All In One SEO',
                                'autodescription/autodescription.php' => 'The SEO Framework',
                                'squirrly-seo/squirrly.php' => 'Squirrly SEO',
                                'seo-by-rank-math/rank-math.php' => 'Rank Math',
                            );

                            foreach($seo_plugins as $key => $value) {
                                if (is_plugin_active($key)) { ?>
                                    <div class="seopress-alert">
                                        <span class="dashicons dashicons-info"></span>
                                        <div class="notice-left">
                                            <p>
                                                <?php echo sprintf(__('We noticed that you use <strong>%s</strong> plugin.','wp-seopress'), $value); ?>
                                                <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                            </p>
                                            <p>
                                                <?php _e('Do you want to migrate all your metadata to SEOPress? Do not use multiple SEO plugins at once to avoid conflicts!','wp-seopress'); ?>
                                            </p>
                                        </div>
                                        <div class="notice-right">
                                            <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-import-export' ); ?>"><?php _e('Migrate!','wp-seopress'); ?></a>
                                        </div>
                                    </div>
                                <?php }
                            }
                        ?>
                        <?php if (is_plugin_active('seo-ultimate/seo-ultimate.php') || is_plugin_active('premium-seo-pack/index.php') || is_plugin_active('wp-meta-seo/wp-meta-seo.php')) { ?>
                            <div class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('We noticed that you use another SEO plugin.','wp-seopress'); ?>
                                        <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('Do not use multiple SEO plugins at once to avoid conflicts!','wp-seopress'); ?>
                                    </p>
                                </div>
                                <div class="notice-right">
                                    <a class="button-primary" href="<?php echo admin_url( 'plugins.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                            if (seopress_404_cleaning_option() == 1 && !wp_next_scheduled('seopress_404_cron_cleaning')) { ?>
                                <div class="seopress-alert">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('You have enabled 404 cleaning BUT the scheduled task is not running.','wp-seopress'); ?>
                                        </p>
                                        <p>
                                            <?php _e('To solve this, please disable and re-enable SEOPress PRO. No data will be lost.','wp-seopress'); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php } 
                        } ?>
                        <?php if (!is_ssl()) { ?>
                            <?php
                            function seopress_get_hidden_notices_ssl_option() {
                                $seopress_get_hidden_notices_ssl_option = get_option("seopress_notices");
                                if ( ! empty ( $seopress_get_hidden_notices_ssl_option ) ) {
                                    foreach ($seopress_get_hidden_notices_ssl_option as $key => $seopress_get_hidden_notices_ssl_value)
                                        $options[$key] = $seopress_get_hidden_notices_ssl_value;
                                        if (isset($seopress_get_hidden_notices_ssl_option['notice-ssl'])) { 
                                        return $seopress_get_hidden_notices_ssl_option['notice-ssl'];
                                        }
                                }
                            }
                            if(seopress_get_hidden_notices_ssl_option() =='1') { 
                                //do nothing
                            } else { ?>
                                <div id="notice-ssl-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('Your site doesn\'t use an SSL certificate!','wp-seopress'); ?> 
                                            <span class="impact low"><?php _e('Low impact','wp-seopress'); ?></span>
                                        </p>
                                        <p>
                                            <?php _e('Https is considered by Google as a positive signal for the ranking of your site. It also reassures your visitors for data security, and improves trust.','wp-seopress'); ?>
                                            <a href="https://webmasters.googleblog.com/2014/08/https-as-ranking-signal.html" target="_blank"><?php _e('Learn more','wp-seopress'); ?></a>
                                        </p>
                                    </div>
                                    <div class="notice-right">
                                        <a class="button-primary" href="https://www.namecheap.com/?aff=105841" target="_blank"><?php _e('Buy an SSL!','wp-seopress'); ?></a>
                                        <span name="notice-ssl" id="notice-ssl" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-ssl"></span>
                                    </div>
                                </div>
                            <?php }
                            ?>
                        <?php } ?>
                        <?php if (function_exists('extension_loaded') && !extension_loaded('dom')) { ?>
                            <div id="notice-ssl-alert" class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('PHP module "DOM" is missing on your server.','wp-seopress'); ?> 
                                        <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.','wp-seopress'); ?>
                                    </p>
                                </div>
                                <?php
                                if (function_exists('seopress_get_locale')) {
                                    if (seopress_get_locale() =='fr') {
                                        $seopress_docs_link['support']['dom'] = 'https://www.seopress.org/fr/support/guides/debutez-seopress/';
                                    } else {
                                        $seopress_docs_link['support']['dom'] = 'https://www.seopress.org/support/guides/get-started-seopress/';
                                    }
                                } ?>
                                <div class="notice-right">
                                    <?php echo '<a class="button-primary" href="'.$seopress_docs_link['support']['dom'].'" target="_blank">'.__('Learn more','wp-seopress').'</a>'; ?>
                                </div>
                            </div>
                        <?php }
                        if (!function_exists('seopress_titles_noindex_option')) {
                            function seopress_titles_noindex_option() {
                                $seopress_titles_noindex_option = get_option("seopress_titles_option_name");
                                if ( ! empty ( $seopress_titles_noindex_option ) ) {
                                    foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value)
                                        $options[$key] = $seopress_titles_noindex_value;
                                    if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) { 
                                        return $seopress_titles_noindex_option['seopress_titles_noindex'];
                                    }
                                }
                            }
                        }
                        if (seopress_titles_noindex_option()=='1' || get_option('blog_public') !='1') { ?>
                            <div class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('Your site is not visible to Search Engines!','wp-seopress'); ?>
                                        <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('You have activated the blocking of the indexing of your site. If your site is under development, this is probably normal. Otherwise, check your settings. Delete this notification using the cross on the right if you are not concerned.','wp-seopress'); ?>
                                    </p>
                                </div>
                                <div class="notice-right">
                                    <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (get_option('blogname') =='') { ?>
                            <div class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('Your site title is empty!','wp-seopress'); ?>
                                        <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('Your Site Title is used by WordPress, your theme and your plugins including SEOPress. It is an essential component in the generation of title tags, but not only. Enter one!','wp-seopress'); ?>
                                    </p>
                                </div>
                                <div class="notice-right">
                                    <a class="button-primary" href="<?php echo admin_url( 'options-general.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (get_option('permalink_structure') =='') { ?>
                            <div class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('Your permalinks are not SEO Friendly! Enable pretty permalinks to fix this.','wp-seopress'); ?>
                                        <span class="impact high"><?php _e('High impact','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('Why is this important? Showing only the summary of each article significantly reduces the theft of your content by third party sites. Not to mention, the increase in your traffic, your advertising revenue, conversions...','wp-seopress'); ?>
                                    </p>
                                </div>
                                <div class="notice-right">
                                    <a class="button-primary" href="<?php echo admin_url( 'options-permalink.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php 
                            if(get_option('rss_use_excerpt') =='0') {
                                function seopress_get_hidden_notices_rss_use_excerpt_option() {
                                    $seopress_get_hidden_notices_rss_use_excerpt_option = get_option("seopress_notices");
                                    if ( ! empty ( $seopress_get_hidden_notices_rss_use_excerpt_option ) ) {
                                        foreach ($seopress_get_hidden_notices_rss_use_excerpt_option as $key => $seopress_get_hidden_notices_rss_use_excerpt_value)
                                            $options[$key] = $seopress_get_hidden_notices_rss_use_excerpt_value;
                                            if (isset($seopress_get_hidden_notices_rss_use_excerpt_option['notice-rss-use-excerpt'])) {
                                                return $seopress_get_hidden_notices_rss_use_excerpt_option['notice-rss-use-excerpt'];
                                            }
                                    }
                                }
                                if(seopress_get_hidden_notices_rss_use_excerpt_option() =='1') {
                                    //do nothing
                                } else { ?>
                                    <div id="notice-rss-use-excerpt-alert" class="seopress-alert deleteable">
                                        <span class="dashicons dashicons-info"></span>
                                        <div class="notice-left">
                                            <p>
                                                <?php _e('Your RSS feed shows full text!','wp-seopress'); ?>
                                                <span class="impact medium"><?php _e('Medium impact','wp-seopress'); ?></span>
                                            </p>
                                            <p>
                                                <?php _e('Why is this important? Showing only the summary of each article significantly reduces the theft of your content by third party sites. Not to mention, the increase in your traffic, your advertising revenue, conversions...','wp-seopress'); ?>
                                            </p>
                                        </div>
                                        <div class="notice-right">
                                            <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                            <span name="notice-rss-use-excerpt" id="notice-rss-use-excerpt" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-rss-use-excerpt"></span>
                                        </div>
                                    </div>
                                <?php }
                            }
                        ?>
                        <?php 
                            function seopress_get_hidden_notices_review_option() {
                                $seopress_get_hidden_notices_review_option = get_option("seopress_notices");
                                if ( ! empty ( $seopress_get_hidden_notices_review_option ) ) {
                                    foreach ($seopress_get_hidden_notices_review_option as $key => $seopress_get_hidden_notices_review_value)
                                        $options[$key] = $seopress_get_hidden_notices_review_value;
                                        if (isset($seopress_get_hidden_notices_review_option['notice-review'])) {
                                            return $seopress_get_hidden_notices_review_option['notice-review'];
                                        }
                                }
                            }
                            if(seopress_get_hidden_notices_review_option() =='1') {
                                //do nothing
                            } else { ?>
                                <div id="notice-review-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('You like SEOPress? Please help us by rating us 5 stars!','wp-seopress'); ?>
                                        </p>
                                        <p>
                                            <?php _e('Support the development and improvement of the plugin by taking 15 seconds of your time to leave us a user review on the official WordPress plugins repository. Thank you!','wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="notice-right">
                                        <a class="button-primary" href="https://wordpress.org/support/view/plugin-reviews/wp-seopress?rate=5#postform" target="_blank"><?php _e('Rate us!','wp-seopress'); ?></a>
                                        <span name="notice-review" id="notice-review" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-review"></span>
                                    </div>
                                </div>
                            <?php }
                            
                        ?>
                        <?php 
                            if(get_option('page_comments') =='1') {
                                function seopress_get_hidden_notices_divide_comments_option() {
                                    $seopress_get_hidden_notices_divide_comments_option = get_option("seopress_notices");
                                    if ( ! empty ( $seopress_get_hidden_notices_divide_comments_option ) ) {
                                        foreach ($seopress_get_hidden_notices_divide_comments_option as $key => $seopress_get_hidden_notices_divide_comments_value)
                                            $options[$key] = $seopress_get_hidden_notices_divide_comments_value;
                                            if (isset($seopress_get_hidden_notices_divide_comments_option['notice-divide-comments'])) {
                                                return $seopress_get_hidden_notices_divide_comments_option['notice-divide-comments'];
                                            }
                                    }
                                }
                                if(seopress_get_hidden_notices_divide_comments_option() =='1') {
                                    //do nothing
                                } else { ?>
                                    <div id="notice-divide-comments-alert" class="seopress-alert deleteable">
                                        <span class="dashicons dashicons-info"></span>
                                        <div class="notice-left">
                                            <p>
                                                <?php _e('Break comments into pages is ON!','wp-seopress'); ?>
                                                <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                            </p>
                                            <p>
                                                <?php _e('Enabling this option will create duplicate content for each article beyond x comments. This can have a disastrous effect by creating a large number of poor quality pages, and slowing the Google bot unnecessarily, so your ranking in search results.','wp-seopress'); ?>
                                            </p>
                                        </div>
                                        <div class="notice-right">
                                            <a class="button-primary" href="<?php echo admin_url( 'options-discussion.php' ); ?>"><?php _e('Disable this!','wp-seopress'); ?></a>
                                            <span name="notice-divide-comments" id="notice-divide-comments" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-divide-comments"></span>
                                        </div>
                                    </div>
                                <?php }
                            }
                        ?>
                        <?php 
                            if(get_option('posts_per_page') < '16') {
                                function seopress_get_hidden_notices_posts_number_option() {
                                    $seopress_get_hidden_notices_posts_number_option = get_option("seopress_notices");
                                    if ( ! empty ( $seopress_get_hidden_notices_posts_number_option ) ) {
                                        foreach ($seopress_get_hidden_notices_posts_number_option as $key => $seopress_get_hidden_notices_posts_number_value)
                                            $options[$key] = $seopress_get_hidden_notices_posts_number_value;
                                            if (isset($seopress_get_hidden_notices_posts_number_option['notice-posts-number'])) {
                                                return $seopress_get_hidden_notices_posts_number_option['notice-posts-number'];
                                            }
                                    }
                                }
                                if(seopress_get_hidden_notices_posts_number_option() =='1') {
                                    //do nothing
                                } else { ?>
                                    <div id="notice-posts-number-alert" class="seopress-alert deleteable">
                                        <span class="dashicons dashicons-info"></span>
                                        <div class="notice-left">
                                            <p>
                                                <?php _e('Display more posts per page on homepage and archives','wp-seopress'); ?>
                                                <span class="impact medium"><?php _e('Medium impact','wp-seopress'); ?></span>
                                            </p>
                                            <p>
                                                <?php _e('To reduce the number pages search engines have to crawl to find all your articles, it is recommended displaying more posts per page. This should not be a problem for your users. Using mobile, we prefer to scroll down rather than clicking on next page links. If you can do it, try adding an infinite scroll to your post listings.','wp-seopress'); ?>
                                            </p>
                                        </div>
                                        <div class="notice-right">
                                            <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                            <span name="notice-posts-number" id="notice-posts-number" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-posts-number"></span>
                                        </div>
                                    </div>
                                <?php }
                            }
                        ?>
                        <?php if (seopress_xml_sitemap_general_enable_option() !='1') { ?>
                            <div class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('You don\'t have an XML Sitemap!','wp-seopress'); ?>
                                        <span class="impact medium"><?php _e('Medium impact','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('XML Sitemaps are useful to facilitate the crawling of your content by search engine robots. Indirectly, this can benefit your ranking by reducing the crawl bugdet.','wp-seopress'); ?>
                                    </p>
                                </div>
                                <div class="notice-right">
                                    <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                            function seopress_get_hidden_notices_google_business_option() {
                                $seopress_get_hidden_notices_google_business_option = get_option("seopress_notices");
                                if ( ! empty ( $seopress_get_hidden_notices_google_business_option ) ) {
                                    foreach ($seopress_get_hidden_notices_google_business_option as $key => $seopress_get_hidden_notices_google_business_value)
                                        $options[$key] = $seopress_get_hidden_notices_google_business_value;
                                        if (isset($seopress_get_hidden_notices_google_business_option['notice-google-business'])) { 
                                        return $seopress_get_hidden_notices_google_business_option['notice-google-business'];
                                        }
                                }
                            }
                            if(seopress_get_hidden_notices_google_business_option() =='1') { 
                                //do nothing
                            } else { ?>
                                <div id="notice-google-business-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('Do you have a Google My Business page? It\'s free!','wp-seopress'); ?>
                                            <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                        </p>
                                        <p>
                                            <?php _e('Local Business websites should have a My Business page to improve visibility in search results. Click on the cross on the right to delete this notification if you are not concerned.','wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="notice-right">
                                        <a class="button-primary" href="https://www.google.com/business/go/" target="_blank"><?php _e('Create your page now!','wp-seopress'); ?></a>
                                        <span name="notice-google-business" id="notice-google-business" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-google-business"></span>
                                    </div>
                                </div>
                            <?php }
                        ?>

                        <?php
                            function seopress_get_hidden_notices_search_console_option() {
                                $seopress_get_hidden_notices_search_console_option = get_option("seopress_notices");
                                if ( ! empty ( $seopress_get_hidden_notices_search_console_option ) ) {
                                    foreach ($seopress_get_hidden_notices_search_console_option as $key => $seopress_get_hidden_notices_search_console_value)
                                        $options[$key] = $seopress_get_hidden_notices_search_console_value;
                                        if (isset($seopress_get_hidden_notices_search_console_option['notice-search-console'])) { 
                                        return $seopress_get_hidden_notices_search_console_option['notice-search-console'];
                                        }
                                }
                            }
                            function seopress_get_google_site_verification_option() {
                                $seopress_get_google_site_verification_option = get_option("seopress_advanced_option_name");
                                if ( ! empty ( $seopress_get_google_site_verification_option ) ) {
                                    foreach ($seopress_get_google_site_verification_option as $key => $seopress_get_google_site_verification_value)
                                        $options[$key] = $seopress_get_google_site_verification_value;
                                        if (isset($seopress_get_google_site_verification_option['seopress_advanced_advanced_google'])) { 
                                        return $seopress_get_google_site_verification_option['seopress_advanced_advanced_google'];
                                        }
                                }
                            }
                            if(seopress_get_hidden_notices_search_console_option() =='1') { 
                                //do nothing
                            } elseif(seopress_get_google_site_verification_option() =='') { ?>
                                <div id="notice-search-console-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <?php _e('Add your site to Google. It\'s free!','wp-seopress'); ?>
                                            <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                        </p>
                                        <p>
                                            <?php _e('Is your brand new site online? So reference it as quickly as possible on Google to get your first visitors via Google Search Console. Already the case? Click on the cross on the right to remove this alert.','wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="notice-right">
                                        <a class="button-primary" href="https://www.google.com/webmasters/tools/home" target="_blank"><?php _e('Add your site to Search Console!','wp-seopress'); ?></a>
                                        <span name="notice-search-console" id="notice-search-console" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-search-console"></span>
                                    </div>
                                </div>
                            <?php }
                        ?>
                        
                        <?php 
                            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                                if(function_exists('seopress_rich_snippets_enable_option') && seopress_rich_snippets_enable_option() !="1") {
                                    ?>
                                    <div id="notice-schemas-metabox-alert" class="seopress-alert">
                                        <span class="dashicons dashicons-info"></span>
                                        <div class="notice-left">
                                            <p>
                                                <strong><?php _e('Structured data types is not correctly enabled','wp-seopress'); ?></strong>
                                                <span class="impact high"><?php _e('Huge impact','wp-seopress'); ?></span>
                                            </p>
                                            <p>
                                                <?php _e('Please enable <strong>Structured Data Types metabox for your posts, pages and custom post types</strong> option in order to use automatic and manual schemas. (SEO > PRO > Structured Data Types (schema.org)','wp-seopress'); ?>
                                            </p>
                                        </div>
                                        <div class="notice-right">
                                            <a class="button-primary" href="<?php echo esc_url( admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets')); ?>" target="_blank"><?php _e('Fix this!','wp-seopress'); ?></a>
                                        </div>
                                    </div>
                                <?php
                                }
                            } 
                        ?>

                        <?php if (get_option( 'seopress_pro_license_status' ) !='valid' && is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                            <div class="seopress-alert">
                                <span class="dashicons dashicons-info"></span>
                                <div class="notice-left">
                                    <p>
                                        <?php _e('You have to enter your licence key to get updates and support','wp-seopress'); ?>
                                        <span class="impact high info"><?php _e('License','wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <?php _e('Please activate the SEOPress PRO license key to automatically receive updates to guarantee you the best user experience possible.','wp-seopress'); ?>
                                    </p>
                                </div>
                                <div class="notice-right">
                                    <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-license' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                                </div>
                            </div>
                        <?php } ?>

                        <?php 
                            if (!is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                                function seopress_get_hidden_notices_go_pro_option() {
                                    $seopress_get_hidden_notices_go_pro_option = get_option("seopress_notices");
                                    if ( ! empty ( $seopress_get_hidden_notices_go_pro_option ) ) {
                                        foreach ($seopress_get_hidden_notices_go_pro_option as $key => $seopress_get_hidden_notices_go_pro_value)
                                            $options[$key] = $seopress_get_hidden_notices_go_pro_value;
                                        if (isset($seopress_get_hidden_notices_go_pro_option['notice-go-pro'])) { 
                                            return $seopress_get_hidden_notices_go_pro_option['notice-go-pro'];
                                        }
                                    }
                                }
                                if(seopress_get_hidden_notices_go_pro_option() =='1') { 
                                    //do nothing
                                } elseif(seopress_get_hidden_notices_go_pro_option() =='') {
                                ?>
                                <div id="notice-go-pro-alert" class="seopress-alert deleteable">
                                    <span class="dashicons dashicons-info"></span>
                                    <div class="notice-left">
                                        <p>
                                            <strong><?php _e('Take your SEO to the next level with SEOPress PRO!','wp-seopress'); ?></strong>
                                            <span class="impact high info"><?php _e('PRO','wp-seopress'); ?></span>
                                        </p>
                                        <p>
                                            <?php _e('The PRO version of SEOPress allows you to easily manage your structured data (schemas), add a breadcrumb optimized for SEO and accessibility, improve SEO for WooCommerce, gain productivity with our import / export tool from a CSV of your metadata and so much more.','wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="notice-right">
                                        <a class="button-primary" href="https://www.seopress.org/?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard" target="_blank"><?php _e('Upgrade now!','wp-seopress'); ?></a>
                                        <span name="notice-go-pro" id="notice-go-pro" class="dashicons dashicons-no-alt remove-notice" data-notice="notice-go-pro"></span>
                                    </div>
                                </div>
                            <?php } 
                        } ?>
                    </div><!--#seopress-notifications-center-->
                        
                </div>
            <?php } ?>

            <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                <div id="tab_seopress_seo_tools" class="seopress-tab seopress-useful-tools <?php if ($current_tab == 'tab_seopress_seo_tools') { echo 'active'; } ?>">
                    
                    <!-- Reverse -->
                    <div class="widget widget-reverse">
                        <h3 class="widget-title"><span class="dashicons dashicons-welcome-view-site"></span><?php _e('Check websites setup on your server','wp-seopress'); ?></h3>

                        <p>
                        <?php
                            if ( get_transient( 'seopress_results_reverse' ) !='' ) { 

                                $seopress_results_reverse = (array)json_decode(get_transient( 'seopress_results_reverse' ));

                                //Init
                                $seopress_results_reverse_remote_ip_address = __('Not found','wp-seopress');
                                if(isset($seopress_results_reverse['remoteIpAddress'])) { 
                                    $seopress_results_reverse_remote_ip_address = $seopress_results_reverse['remoteIpAddress'];
                                }

                                $seopress_results_reverse_last_scrape = __('No scrape.','wp-seopress');
                                if(isset($seopress_results_reverse['lastScrape'])) { 
                                    $seopress_results_reverse_last_scrape = $seopress_results_reverse['lastScrape'];
                                }

                                $seopress_results_reverse_domain_count = __('No domain found.','wp-seopress');
                                if(isset($seopress_results_reverse['domainCount'])) { 
                                    $seopress_results_reverse_domain_count = $seopress_results_reverse['domainCount'];
                                }

                                $seopress_results_reverse_domain_array = '';
                                if(isset($seopress_results_reverse['domainArray'])) { 
                                    $seopress_results_reverse_domain_array = $seopress_results_reverse['domainArray'];
                                }
                                    
                                echo '<p class="remote-ip"><strong>'.__('Server IP Address: ','wp-seopress').'</strong>'.$seopress_results_reverse_remote_ip_address.'</p>';
                                

                                echo '<p class="last-scrape"><strong>'.__('Last scrape: ','wp-seopress').'</strong>'.$seopress_results_reverse_last_scrape.'</p>';
                                echo '<p class="domain-count"><strong>'.__('Number of websites on your server: ','wp-seopress').'</strong>'.$seopress_results_reverse_domain_count.'</p>';
                                
                                if ($seopress_results_reverse_domain_array !='') {
                                    echo '<ul>';
                                        foreach ($seopress_results_reverse_domain_array as $key => $value) {
                                            echo '<li><span class="dashicons dashicons-minus"></span><a href="//'.preg_replace('#^https?://#', '', $value[0]).'" target="_blank">'.$value[0].'</a><span class="dashicons dashicons-external"></span></li>';
                                        }
                                    echo '</ul>';
                                }
                            }
                        ?>
                        <br>
                        <button id="seopress-reverse-submit" class="button button-primary" name="submit">
                            <?php _e('Get list','wp-seopress'); ?>
                        </button>

                        <span id="spinner-reverse" class="spinner"></span>
                    </div>
                </div>
            <?php } ?>
            <div id="tab_seopress_links" class="seopress-tab seopress-useful-tools <?php if ($current_tab == 'tab_seopress_links') { echo 'active'; } ?>">
                <ul>
                    <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.seopress.org/blog/" target="_blank"><?php _e('Our blog: SEO news, how-to, tips and tricks...','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                    <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.google.com/webmasters/tools/disavow-links-main" target="_blank"><?php _e('Upload a list of links to disavow to Google','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                    <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://trends.google.com/trends/" target="_blank"><?php _e('Google Trends','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                    <?php if ( !is_plugin_active( 'imageseo/imageseo.php' )) {
                        echo '<li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://imageseo.io?_from=seopress" target="_blank">'.__('Image SEO plugin to optimize your image ALT texts and names for Search Engines.','wp-seopress-pro').'</a><span class="dashicons dashicons-external"></span></li>';
                    } ?>
                    <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.dareboost.com/en/home" target="_blank"><?php _e('Dareboost: Test, analyze and optimize your website','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                    <li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://ga-dev-tools.appspot.com/campaign-url-builder/" target="_blank"><?php _e('Google Campaign URL Builder tool','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>