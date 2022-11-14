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
                            'tab_seopress_matomo'        => __('Matomo Analytics', 'wp-seopress'),
                            'tab_seopress_ps'       => __('PageSpeed', 'wp-seopress'),
                        ];

                        //GA
                        if (seopress_get_toggle_option('google-analytics') !=='1' || (function_exists('seopress_google_analytics_dashboard_widget_option') && seopress_google_analytics_dashboard_widget_option() === '1')) {
                            unset($dashboard_settings_tabs['tab_seopress_analytics']);
                        }

                        //Matomo
                        if (seopress_get_toggle_option('google-analytics') !=='1' || (function_exists('seopress_google_analytics_matomo_dashboard_widget_option') && '1' === seopress_google_analytics_matomo_dashboard_widget_option())) {
                            unset($dashboard_settings_tabs['tab_seopress_matomo']);
                        }

                        $matomoID = seopress_get_service('GoogleAnalyticsOption')->getMatomoId() ? seopress_get_service('GoogleAnalyticsOption')->getMatomoId() : null;
                        if (empty($matomoID)) {
                            unset($dashboard_settings_tabs['tab_seopress_matomo']);
                        }
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
                        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php') && seopress_get_toggle_option('google-analytics')) { ?>
                            <div id="tab_seopress_analytics" class="seopress-tab seopress-analytics <?php if ('tab_seopress_analytics' == $current_tab) { echo 'active'; } ?>">
                                <?php if (function_exists('seopress_google_analytics_dashboard_widget_option') && '1' !== seopress_google_analytics_dashboard_widget_option()) {
                                    if (function_exists('seopress_ga_dashboard_widget_display')) {
                                        seopress_ga_dashboard_widget_display();
                                    }
                                } ?>
                            </div>
                        <?php } ?>

                        <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php') && seopress_get_toggle_option('google-analytics')) { ?>
                            <div id="tab_seopress_matomo" class="seopress-tab seopress-analytics <?php if ('tab_seopress_matomo' == $current_tab) { echo 'active'; } ?>">
                                <?php if (function_exists('seopress_google_analytics_matomo_dashboard_widget_option') && '1' !== seopress_google_analytics_matomo_dashboard_widget_option() && !empty($matomoID)) {
                                    if (function_exists('seopress_matomo_dashboard_widget_display')) {
                                        seopress_matomo_dashboard_widget_display();
                                    }
                                } ?>
                            </div>
                        <?php } ?>

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
                                            <span class="green"><?php _e('Passed', 'wp-seopress'); ?></span>
                                            <?php } elseif ($core_web_vitals_score === null) { ?>
                                            <span class="red"><?php _e('No data found', 'wp-seopress'); ?></span>
                                            <?php } else { ?>
                                            <span class="red"><?php _e('Failed', 'wp-seopress'); ?></span>
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
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
