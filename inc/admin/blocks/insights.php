<?php
    // To prevent calling the plugin directly
    if (! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }
    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
            //do nothing
        } else {

            function seopress_google_analytics_dashboard_widget_option()
            {
                $seopress_google_analytics_dashboard_widget_option = get_option('seopress_google_analytics_option_name');
                if (! empty($seopress_google_analytics_dashboard_widget_option)) {
                    foreach ($seopress_google_analytics_dashboard_widget_option as $key => $seopress_google_analytics_dashboard_widget_value) {
                        $options[$key] = $seopress_google_analytics_dashboard_widget_value;
                    }
                    if (isset($seopress_google_analytics_dashboard_widget_option['seopress_google_analytics_dashboard_widget'])) {
                        return $seopress_google_analytics_dashboard_widget_option['seopress_google_analytics_dashboard_widget'];
                    }
                }
            }

            function seopress_get_hidden_notices_insights_option()
            {
                $seopress_get_hidden_notices_insights_option = get_option('seopress_notices');
                if (! empty($seopress_get_hidden_notices_insights_option)) {
                    foreach ($seopress_get_hidden_notices_insights_option as $key => $seopress_get_hidden_notices_insights_value) {
                        $options[$key] = $seopress_get_hidden_notices_insights_value;
                    }
                    if (isset($seopress_get_hidden_notices_insights_option['notice-insights'])) {
                        return $seopress_get_hidden_notices_insights_option['notice-insights'];
                    }
                }
            }

            function seopress_advanced_appearance_seo_tools_option()
            {
                $seopress_advanced_appearance_seo_tools_option = get_option('seopress_advanced_option_name');
                if (! empty($seopress_advanced_appearance_seo_tools_option)) {
                    foreach ($seopress_advanced_appearance_seo_tools_option as $key => $seopress_advanced_appearance_seo_tools_value) {
                        $options[$key] = $seopress_advanced_appearance_seo_tools_value;
                    }
                    if (isset($seopress_advanced_appearance_seo_tools_option['seopress_advanced_appearance_seo_tools'])) {
                        return $seopress_advanced_appearance_seo_tools_option['seopress_advanced_appearance_seo_tools'];
                    }
                }
            }

            $class = '1' != seopress_advanced_appearance_seo_tools_option() ? 'is-active' : '';
        ?>

        <div id="notice-insights-alert" class="seopress-page-list seopress-card <?php echo $class; ?>" style="display: none">
            <div class="seopress-card-title">
                <h2><?php _e('Site overview', 'wp-seopress'); ?>
                </h2>

                <div>
                    <span class="dashicons dashicons-sort"></span>
                </div>
            </div>
            <div class="seopress-card-content">
                <div id="seopress-admin-tabs" class="wrap">
                    <?php
                        $dashboard_settings_tabs = [
                            'tab_seopress_analytics'        => __('Google Analytics', 'wp-seopress'),
                            'tab_seopress_ps'       => __('PageSpeed', 'wp-seopress'),
                            'tab_seopress_seo_tools'        => __('SEO Tools', 'wp-seopress'),
                        ];
                    ?>

                    <div class="nav-tab-wrapper">
                        <?php foreach ($dashboard_settings_tabs as $tab_key => $tab_caption) { ?>
                        <a id="<?php echo $tab_key; ?>-tab" class="nav-tab"
                            href="?page=seopress-option#tab=<?php echo $tab_key; ?>"><?php echo $tab_caption; ?></a>
                        <?php } ?>
                    </div>

                    <?php
                        $seopress_page_speed_results     = [];
                        $seopress_page_speed_results     = json_decode(get_transient('seopress_results_page_speed'), true);
                        $seopress_page_speed_desktop_results     = [];
                        $seopress_page_speed_desktop_results     = json_decode(get_transient('seopress_results_page_speed_desktop'), true);
                        $cwv_svg = '<svg enable-background="new 0 0 24 24" focusable="false" height="15" viewBox="0 0 24 24" width="15" style="fill:#06f;vertical-align:middle"><g><g><path d="M0,0h24v24H0V0z" fill="none"></path></g></g><g><path d="M17,3H7C5.9,3,5,3.9,5,5v16l7-3l7,3V5C19,3.9,18.1,3,17,3z"></path></g></svg>';

                        $fetchTime = '';

                        $ps_score = '';
                        $core_web_vitals_score = '';
                        if (! empty($seopress_page_speed_results)) {
                            $ps_score = seopress_pro_get_ps_score($seopress_page_speed_results, true);
                            $ps_score_desktop = seopress_pro_get_ps_score($seopress_page_speed_desktop_results);
                            $core_web_vitals_score = seopress_pro_get_cwv_score($seopress_page_speed_results);
                        }
                    ?>

                    <div class="wrap-seopress-tab-content">
                        <div id="tab_seopress_ps" class="seopress-tab seopress-page-speed inside<?php if ('tab_seopress_ps' == $current_tab) {
                        echo 'active';
                    }?>">
                            <h3><?php _e('Google Page Speed Score','wp-seopress'); ?></h3>
                            <p><?php _e('Learn how your site has performed, based on data from your actual users around the world.','wp-seopress'); ?>
                            </p>
                            <?php if ($ps_score && $ps_score_desktop) { ?>
                                <div class="seopress-cwv seopress-summary-item-data">
                                    <?php echo $ps_score; ?>
                                    <?php echo $ps_score_desktop; ?>
                                    <p class="wrap-scale">
                                        <?php _e('<span><span class="score red"></span>0-49</span><span><span class="score yellow"></span>50-89</span><span><span class="score green"></span>90-100</span>','wp-seopress') ?>
                                    </p>
                                </div>
                                <div class="seopress-cwv">
                                    <?php if ($core_web_vitals_score === true) { ?>
                                    <img src="<?php echo SEOPRESS_PRO_ASSETS_DIR; ?>/img/cwv-pass.svg"
                                        alt='' width='96' height='96' />
                                    <?php } else { ?>
                                    <img src="<?php echo SEOPRESS_PRO_ASSETS_DIR; ?>/img/cwv-fail.svg"
                                        alt='' width='96' height='96' />
                                    <?php } ?>
                                    <div>
                                        <h3>
                                            <?php _e('Core Web Vitals Assessment: ', 'wp-seopress'); ?>

                                            <?php if ($core_web_vitals_score === true) { ?>
                                            <span class="green"><?php _e('Passed', 'wp-seopress-pro'); ?></span>
                                            <?php } elseif ($core_web_vitals_score === null) { ?>
                                            <span class="red"><?php _e('No data found', 'wp-seopress-pro'); ?></span>
                                            <?php } else { ?>
                                            <span class="red"><?php _e('Failed', 'wp-seopress-pro'); ?></span>
                                            <?php } ?>
                                        </h3>
                                        <p><?php printf(__('Computed from the %s Core Web Vitals metrics over the latest 28-day collection period.', 'wp-seopress'), $cwv_svg); ?></p>
                                    </div>
                                </div>
                            <?php } else {  ?>
                                <p><?php _e('No data available.','wp-seopress'); ?></p>
                            <?php } ?>
                            <p>
                                <a href="<?php echo admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed'); ?>"
                                    class="btn btnSecondary">
                                    <?php _e('See full report', 'wp-seopress'); ?>
                                </a>
                            </p>
                        </div>

                        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                        <div id="tab_seopress_analytics" class="seopress-tab seopress-analytics <?php if ('tab_seopress_analytics' == $current_tab) {
                        echo 'active';
                    } ?>">
                            <?php if ('1' == seopress_get_toggle_option('google-analytics') && '1' !== seopress_google_analytics_dashboard_widget_option()) {
                        $stats = get_transient('seopress_results_google_analytics');
                        $html  = [];
                        if (! empty($stats['sessions'])) {
                            $html[__('Sessions', 'wp-seopress')] = $stats['sessions'];
                        }
                        if (! empty($stats['users'])) {
                            $html[__('Users', 'wp-seopress')] = $stats['users'];
                        }
                        if (! empty($stats['pageviews'])) {
                            $html[__('Page Views', 'wp-seopress')] = $stats['pageviews'];
                        }
                        if (! empty($stats['pageviewsPerSession'])) {
                            $html[__('Page view / session', 'wp-seopress')] = $stats['pageviewsPerSession'];
                        }
                        if (! empty($stats['avgSessionDuration'])) {
                            $html[__('Average session duration', 'wp-seopress')] = $stats['avgSessionDuration'];
                        }
                        if (! empty($stats['bounceRate'])) {
                            $html[__('Bounce rate', 'wp-seopress')] = $stats['bounceRate'];
                        }
                        if (! empty($stats['percentNewSessions'])) {
                            $html[__('New sessions', 'wp-seopress')] = $stats['percentNewSessions'];
                        } ?>

                            <div class="seopress-summary-items">
                                <?php if (! empty($html)) { ?>
                                    <?php foreach ($html as $key => $value) { ?>
                                        <div class="seopress-summary-item">
                                    <div class="seopress-summary-item-label">
                                        <?php echo $key; ?>
                                    </div>
                                    <div class="seopress-summary-item-data">
                                        <?php echo $value; ?>
                                    </div>
                                    </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <p class="inside"><?php _e('No stats found', 'wp-seopress'); ?></p>
                                <?php } ?>
                            </div>
                            <?php
                    } else { ?>
                            <div class="seopress-summary-items">
                                <div class="seopress-summary-item">
                                    <p>
                                        <a class="btn btnSecondary" href="<?php echo admin_url( 'admin.php?page=seopress-google-analytics#tab=tab_seopress_google_analytics_dashboard' ); ?>">
                                            <?php _e('Connect Google Analytics','wp-seopress'); ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php') ) { ?>
                        <div id="tab_seopress_seo_tools" class="seopress-tab seopress-useful-tools inside <?php if ('tab_seopress_seo_tools' == $current_tab) {
                        echo 'active';
                    } ?>">

                            <!-- Reverse -->
                            <div class="widget widget-reverse">
                                <h3 class="widget-title"><?php _e('Check websites setup on your server', 'wp-seopress'); ?>
                                </h3>

                                <p>
                                    <?php
                                        if ('' != get_transient('seopress_results_reverse')) {
                                            $seopress_results_reverse = (array) json_decode(get_transient('seopress_results_reverse'));

                                            //Init
                                            $seopress_results_reverse_remote_ip_address = __('Not found', 'wp-seopress');
                                            if (isset($seopress_results_reverse['remoteIpAddress'])) {
                                                $seopress_results_reverse_remote_ip_address = $seopress_results_reverse['remoteIpAddress'];
                                            }

                                            $seopress_results_reverse_last_scrape = __('No scrape.', 'wp-seopress');
                                            if (isset($seopress_results_reverse['lastScrape'])) {
                                                $seopress_results_reverse_last_scrape = $seopress_results_reverse['lastScrape'];
                                            }

                                            $seopress_results_reverse_domain_count = __('No domain found.', 'wp-seopress');
                                            if (isset($seopress_results_reverse['domainCount'])) {
                                                $seopress_results_reverse_domain_count = $seopress_results_reverse['domainCount'];
                                            }

                                            $seopress_results_reverse_domain_array = '';
                                            if (isset($seopress_results_reverse['domainArray'])) {
                                                $seopress_results_reverse_domain_array = $seopress_results_reverse['domainArray'];
                                            } ?>
                                <p class="remote-ip">
                                    <strong>
                                        <?php _e('Server IP Address: ', 'wp-seopress'); ?>
                                    </strong>
                                    <?php echo $seopress_results_reverse_remote_ip_address; ?>
                                </p>

                                <p class="last-scrape">
                                    <strong>
                                        <?php _e('Last scrape: ', 'wp-seopress'); ?>
                                    </strong>
                                    <?php echo $seopress_results_reverse_last_scrape; ?>
                                </p>

                                <p class="domain-count">
                                    <strong>
                                        <?php _e('Number of websites on your server: ', 'wp-seopress'); ?>
                                    </strong>
                                    <?php echo $seopress_results_reverse_domain_count; ?>
                                </p>
                                <?php if ('' != $seopress_results_reverse_domain_array) { ?>
                                <ul>
                                    <?php foreach ($seopress_results_reverse_domain_array as $key => $value) { ?>
                                    <li>
                                        <span class="dashicons dashicons-minus"></span>
                                        <a href="//' . preg_replace('#^https?://#', '', $value[0]) . '" target="_blank">
                                            <?php echo $value[0]; ?>
                                        </a>
                                        <span class="dashicons dashicons-external"></span>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php }
                                        }
                                    ?>

                                <button id="seopress-reverse-submit" type="button" class="btn btnSecondary" name="submit">
                                    <?php _e('Get list', 'wp-seopress'); ?>
                                </button>

                                <span id="spinner-reverse" class="spinner"></span>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
