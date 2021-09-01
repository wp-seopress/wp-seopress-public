<?php
    // To prevent calling the plugin directly
    if (! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }

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

    if ('1' != seopress_get_hidden_notices_insights_option()) {
?>

<div id="notice-insights-alert" class="seopress-page-list seopress-card">
    <div class="seopress-card-title">
        <h2><?php _e('Stats overview', 'wp-seopress'); ?>
        </h2>

        <span class="seopress-item-toggle-options"></span>
        <div class="seopress-card-popover">
            <?php
                $options = get_option('seopress_dashboard_option_name');
                $value   = isset($options['hide_insights']) ? esc_attr($options['hide_insights']) : 5;
            ?>

            <button id="notice-insights" name="notice-insights" data-notice="notice-insights" type="submit" class="btn btnSecondary">
                <?php _e('Hide this', 'wp-seopress'); ?>
            </button>
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

                $ps_score  = 0;
                $fetchTime = '';
                $class_score = '';
                $circumference = '';
                if (! empty($seopress_page_speed_results)) {
                    $ps_score  = ($seopress_page_speed_results['lighthouseResult']['categories']['performance']['score']) * 100;

                    if ($ps_score >= 0 && $ps_score < 49) {
                        $class_score = 'red';
                    } elseif ($ps_score >= 50 && $ps_score < 90) {
                        $class_score = 'yellow';
                    } elseif ($ps_score >= 90 && $ps_score <= 100) {
                        $class_score = 'green';
                    } else {
                        $class_score = 'grey';
                    }

                    //565.48
                    $circumference        = 90 * 2 * M_PI;
                    $circumference        = $circumference - $ps_score / 100 * $circumference;
                    $fetchTime            = $seopress_page_speed_results['lighthouseResult']['fetchTime'];
                }
            ?>

            <div class="wrap-seopress-tab-content"
                data-score="<?php echo $ps_score; ?>">
                <div id="tab_seopress_ps" class="seopress-tab seopress-page-speed inside<?php if ('tab_seopress_ps' == $current_tab) {
                echo 'active';
            }?>">
                    <p><?php _e('The speed score is based on the lab data analyzed by Lighthouse.', 'wp-seopress'); ?>
                    </p>
                    <div class="wrap-scale">
                        <div
                            class="ps-score <?php echo $class_score; ?>">
                            <svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%"
                                viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48"
                                    stroke-dashoffset="0"></circle>
                                <circle id="bar" r="90" cx="100" cy="100" fill="transparent"
                                    stroke-dasharray="<?php echo $circumference; ?>"
                                    stroke-dashoffset="0"></circle>
                            </svg>
                            <span><?php echo $ps_score . '%'; ?></span>
                        </div>

                        <a href="<?php echo admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_ps'); ?>"
                            class="btn btnSecondary">
                            <?php _e('See full report', 'wp-seopress'); ?>
                        </a>
                    </div>
                    <p class="wrap-scale">
                        <?php _e('<strong>Scale:</strong><span class="slow"></span>0-49 (slow) <span class="average"></span>50-89 (average) <span class="fast"></span>90-100 (fast)', 'wp-seopress'); ?>
                    </p>
                    <div class="last-date-analysis">
                        <p>
                            <strong><?php _e('Last analysis: ', 'wp-seopress'); ?></strong>
                            <?php echo date_i18n(get_option('date_format'), strtotime($fetchTime)); ?>
                            <?php _e(' at ', 'wp-seopress'); ?>
                            <?php echo date('H:i', strtotime($fetchTime)); ?>
                        </p>
                    </div>
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
                                <?php _e('No stats found', 'wp-seopress'); ?>
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

                <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
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

<?php }
