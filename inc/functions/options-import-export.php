<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Import / Exports settings page
///////////////////////////////////////////////////////////////////////////////////////////////////
//Export SEOPress Settings to JSON
function seopress_export_settings() {
    if (empty($_POST['seopress_action']) || 'export_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_nonce'], 'seopress_export_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    $settings['seopress_activated']                             = get_option('seopress_activated');
    $settings['seopress_titles_option_name']                    = get_option('seopress_titles_option_name');
    $settings['seopress_social_option_name']                    = get_option('seopress_social_option_name');
    $settings['seopress_google_analytics_option_name']          = get_option('seopress_google_analytics_option_name');
    $settings['seopress_advanced_option_name']                  = get_option('seopress_advanced_option_name');
    $settings['seopress_xml_sitemap_option_name']               = get_option('seopress_xml_sitemap_option_name');
    $settings['seopress_pro_option_name']                       = get_option('seopress_pro_option_name');
    $settings['seopress_pro_mu_option_name']                    = get_option('seopress_pro_mu_option_name');
    $settings['seopress_pro_license_key']                       = get_option('seopress_pro_license_key');
    $settings['seopress_pro_license_status']                    = get_option('seopress_pro_license_status');
    $settings['seopress_bot_option_name']                       = get_option('seopress_bot_option_name');
    $settings['seopress_toggle']                                = get_option('seopress_toggle');
    $settings['seopress_google_analytics_lock_option_name']     = get_option('seopress_google_analytics_lock_option_name');
    $settings['seopress_tools_option_name']                     = get_option('seopress_tools_option_name');
    $settings['seopress_dashboard_option_name']                 = get_option('seopress_dashboard_option_name');

    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-settings-export-' . date('m-d-Y') . '.json');
    header('Expires: 0');
    echo json_encode($settings);
    exit;
}
add_action('admin_init', 'seopress_export_settings');

//Import SEOPress Settings from JSON
function seopress_import_settings() {
    if (empty($_POST['seopress_action']) || 'import_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_nonce'], 'seopress_import_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(__('Please upload a valid .json file', 'wp-seopress'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];

    if (empty($import_file)) {
        wp_die(__('Please upload a file to import', 'wp-seopress'));
    }

    $settings = (array) json_decode(seopress_remove_utf8_bom(file_get_contents($import_file)), true);

    if (false !== $settings['seopress_activated']) {
        update_option('seopress_activated', $settings['seopress_activated'], false);
    }
    if (false !== $settings['seopress_titles_option_name']) {
        update_option('seopress_titles_option_name', $settings['seopress_titles_option_name'], false);
    }
    if (false !== $settings['seopress_social_option_name']) {
        update_option('seopress_social_option_name', $settings['seopress_social_option_name'], false);
    }
    if (false !== $settings['seopress_google_analytics_option_name']) {
        update_option('seopress_google_analytics_option_name', $settings['seopress_google_analytics_option_name'], false);
    }
    if (false !== $settings['seopress_advanced_option_name']) {
        update_option('seopress_advanced_option_name', $settings['seopress_advanced_option_name'], false);
    }
    if (false !== $settings['seopress_xml_sitemap_option_name']) {
        update_option('seopress_xml_sitemap_option_name', $settings['seopress_xml_sitemap_option_name'], false);
    }
    if (false !== $settings['seopress_pro_option_name']) {
        update_option('seopress_pro_option_name', $settings['seopress_pro_option_name'], false);
    }
    if (false !== $settings['seopress_pro_mu_option_name']) {
        update_option('seopress_pro_mu_option_name', $settings['seopress_pro_mu_option_name'], false);
    }
    if (false !== $settings['seopress_pro_license_key']) {
        update_option('seopress_pro_license_key', $settings['seopress_pro_license_key'], false);
    }
    if (false !== $settings['seopress_pro_license_status']) {
        update_option('seopress_pro_license_status', $settings['seopress_pro_license_status'], false);
    }
    if (false !== $settings['seopress_bot_option_name']) {
        update_option('seopress_bot_option_name', $settings['seopress_bot_option_name'], false);
    }
    if (false !== $settings['seopress_toggle']) {
        update_option('seopress_toggle', $settings['seopress_toggle'], false);
    }
    if (false !== $settings['seopress_google_analytics_lock_option_name']) {
        update_option('seopress_google_analytics_lock_option_name', $settings['seopress_google_analytics_lock_option_name'], false);
    }
    if (false !== $settings['seopress_tools_option_name']) {
        update_option('seopress_tools_option_name', $settings['seopress_tools_option_name'], false);
    }

    wp_safe_redirect(admin_url('admin.php?page=seopress-import-export&success=true'));
    exit;
}
add_action('admin_init', 'seopress_import_settings');

//Import Redirections from CSV
function seopress_import_redirections_settings() {
    if (empty($_POST['seopress_action']) || 'import_redirections_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_redirections_nonce'], 'seopress_import_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('csv' != $extension) {
        wp_die(__('Please upload a valid .csv file', 'wp-seopress'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import', 'wp-seopress'));
    }

    if ( ! $_POST['import_sep']) {
        wp_die(__('Please choose a separator', 'wp-seopress'));
    }

    $csv = array_map(function ($item) {
        if ('comma' == $_POST['import_sep']) {
            $sep = ',';
        } elseif ('semicolon' == $_POST['import_sep']) {
            $sep = ';';
        } else {
            wp_die(__('Invalid separator'));
        }

        return str_getcsv($item, $sep, '\"');
    }, file($import_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

    //Remove duplicates from CSV
    $csv = array_unique($csv, SORT_REGULAR);

    foreach ($csv as $key => $value) {
        $csv_line = $value;

        //Third column: redirections type
        if ('301' == $csv_line[2] || '302' == $csv_line[2] || '307' == $csv_line[2] || '410' == $csv_line[2] || '451' == $csv_line[2]) {
            $csv_type_redirects[2] = $csv_line[2];
        }

        //Fourth column: redirections enabled
        $csv_line[3] = strtolower($csv_line[3]);
        if ('yes' == $csv_line[3]) {
            $csv_type_redirects[3] = $csv_line[3];
        } else {
            $csv_type_redirects[3] = '';
        }

        //Fifth column: redirections query param
        if ( ! empty($csv_line[4])) {
            if ('exact_match' == $csv_line[4] || 'with_ignored_param' == $csv_line[4] || 'without_param' == $csv_line[4]) {
                $csv_type_redirects[4] = $csv_line[4];
            } else {
                $csv_type_redirects[4] = 'exact_match';
            }
        }

        //Seventh column: redirect categories
        if ( ! empty($csv_line[6])) {
            $cats = array_values(explode(',', $csv_line[6]));
            $cats = array_map('intval', $cats);
            $cats = array_unique($cats);
        }

        $regex_enable = '';
        //regex enabled
        $csv_line[7]= strtolower($csv_line[7]);
        if ('yes' === $csv_line[7]) {
            $regex_enable = 'yes';
        }


        //logged status
        $logged_status = 'both';
        $csv_line[8]= strtolower($csv_line[8]);
        if (!empty($csv_line[8])) {
            $logged_status = $csv_line[8];
        }


        if ( ! empty($csv_line[0])) {
            $count = null;
            if ( ! empty($csv_line[5])) {
                $count = $csv_line[5];
            }
            $id = wp_insert_post([
                    'post_title'  => rawurldecode($csv_line[0]),
                    'post_type'   => 'seopress_404',
                    'post_status' => 'publish',
                    'meta_input'  => [
                        '_seopress_redirections_value'      => rawurldecode($csv_line[1]),
                        '_seopress_redirections_type'       => $csv_type_redirects[2],
                        '_seopress_redirections_enabled'    => $csv_type_redirects[3],
                        '_seopress_redirections_enabled_regex'  => $regex_enable,
                        '_seopress_redirections_logged_status'  => $logged_status,
                        '_seopress_redirections_param'      => $csv_type_redirects[4],
                        'seopress_404_count'                => $count,
                    ],
                ]
            );

            //Assign terms
            if ( ! empty($csv_line[6])) {
                wp_set_object_terms($id, $cats, 'seopress_404_cat');
            }
        }
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_redirections_settings');

//Import Redirections from Yoast Premium (CSV)
function seopress_import_yoast_redirections() {
    if (empty($_POST['seopress_action']) || 'import_yoast_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_yoast_redirections_nonce'], 'seopress_import_yoast_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('csv' != $extension) {
        wp_die(__('Please upload a valid .csv file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $csv = array_map('str_getcsv', file($import_file));

    foreach (array_slice($csv, 1) as $_key => $_value) {
        $csv_line = $_value;

        //Third column: redirections type
        if ('301' == $csv_line[2] || '302' == $csv_line[2] || '307' == $csv_line[2] || '410' == $csv_line[2] || '451' == $csv_line[2]) {
            $csv_type_redirects[2] = $csv_line[2];
        }

        //Fourth column: redirections enabled
        $csv_type_redirects[3] = 'yes';

        //Fifth column: redirections query param
        $csv_type_redirects[4] = 'exact_match';


        if ( ! empty($csv_line[0])) {
            $csv_line[0] = substr($csv_line[0], 1);
            if ( ! empty($csv_line[1])) {
                if ('//' === $csv_line[1]) {
                    $csv_line[1] = '/';
                } else {
                    $csv_line[1] = home_url() . $csv_line[1];
                }
            }
            $id = wp_insert_post([
                'post_title'        => urldecode($csv_line[0]),
                'post_type'         => 'seopress_404',
                'post_status'       => 'publish',
                'meta_input'        => [
                    '_seopress_redirections_value'          => urldecode($csv_line[1]),
                    '_seopress_redirections_type'           => $csv_type_redirects[2],
                    '_seopress_redirections_enabled'        => $csv_type_redirects[3],
                    '_seopress_redirections_enabled_regex'  => '',
                    '_seopress_redirections_logged_status'  => 'both',
                    '_seopress_redirections_param'          => $csv_type_redirects[4],
                ],
            ]);
        }
    }
    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_yoast_redirections');

//Export Redirections to CSV file
function seopress_export_redirections_settings() {
    if (empty($_POST['seopress_action']) || 'export_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_redirections_nonce'], 'seopress_export_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    //Init
    $redirects_html = '';

    $args = [
        'post_type'      => 'seopress_404',
        'posts_per_page' => '-1',
        'meta_query'     => [
            [
                'key'     => '_seopress_redirections_type',
                'value'   => ['301', '302', '307', '410', '451'],
                'compare' => 'IN',
            ],
        ],
    ];

    $args = apply_filters('seopress_export_redirections_query', $args);

    $seopress_redirects_query = new WP_Query($args);

    if ($seopress_redirects_query->have_posts()) {
        while ($seopress_redirects_query->have_posts()) {
            $seopress_redirects_query->the_post();
            $redirect_categories = get_the_terms(get_the_ID(), 'seopress_404_cat');

            if(!empty($redirect_categories)){
                $redirect_categories = join(', ', wp_list_pluck($redirect_categories, 'term_id'));
            }
            else{
                $redirect_categories = "";
            }

            $redirects_html .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_the_title())))));
            $redirects_html .= ';';
            $redirects_html .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_post_meta(get_the_ID(), '_seopress_redirections_value', true))))));
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_type', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_enabled', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_param', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), 'seopress_404_count', true);
            $redirects_html .= ';';
            $redirects_html .= $redirect_categories;
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_enabled_regex', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_logged_status', true);
            $redirects_html .= "\n";
        }
        wp_reset_postdata();
    }

    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: application/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-redirections-export-' . date('m-d-Y') . '.csv');
    header('Expires: 0');
    echo $redirects_html;
    exit;
}
add_action('admin_init', 'seopress_export_redirections_settings');

//Export Redirections to txt file for .htaccess
function seopress_export_redirections_htaccess_settings() {
    if (empty($_POST['seopress_action']) || 'export_redirections_htaccess' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_redirections_htaccess_nonce'], 'seopress_export_redirections_htaccess_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    //Init
    $redirects_html = '';

    $args = [
        'post_type'      => 'seopress_404',
        'posts_per_page' => '-1',
        'meta_query'     => [
            [
                'key'     => '_seopress_redirections_type',
                'value'   => ['301', '302', '307', '410', '451'],
                'compare' => 'IN',
            ],
            [
                'key'     => '_seopress_redirections_enabled',
                'value'   => 'yes',
            ],
        ],
    ];
    $seopress_redirects_query = new WP_Query($args);

    if ($seopress_redirects_query->have_posts()) {
        while ($seopress_redirects_query->have_posts()) {
            $seopress_redirects_query->the_post();

            switch (get_post_meta(get_the_ID(), '_seopress_redirections_type', true)) {
                case '301':
                    $type = 'redirect 301 ';
                    break;
                case '302':
                    $type = 'redirect 302 ';
                    break;
                case '307':
                    $type = 'redirect 307 ';
                    break;
                case '410':
                    $type = 'redirect 410 ';
                    break;
                case '451':
                    $type = 'redirect 451 ';
                    break;
            }

            $redirects_html .= $type . ' /' . untrailingslashit(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_the_title()))))) . ' ';
            $redirects_html .= urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_post_meta(get_the_ID(), '_seopress_redirections_value', true)))));
            $redirects_html .= "\n";
        }
        wp_reset_postdata();
    }

    ignore_user_abort(true);
    echo $redirects_html;
    nocache_headers();
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-redirections-htaccess-export-' . date('m-d-Y') . '.txt');
    header('Expires: 0');
    exit;
}
add_action('admin_init', 'seopress_export_redirections_htaccess_settings');

//Import Redirections from Redirections plugin JSON file
function seopress_import_redirections_plugin_settings() {
    if (empty($_POST['seopress_action']) || 'import_redirections_plugin_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_redirections_plugin_nonce'], 'seopress_import_redirections_plugin_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(__('Please upload a valid .json file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $settings = (array) json_decode(file_get_contents($import_file), true);

    foreach ($settings['redirects'] as $redirect_key => $redirect_value) {
        $type = '';
        if ( ! empty($redirect_value['action_code'])) {
            $type = $redirect_value['action_code'];
        } else {
            $type = '301';
        }

        $param = '';
        if ( ! empty($redirect_value['match_data']['source']['flag_query'])) {
            $flag_query = $redirect_value['match_data']['source']['flag_query'];
            if ('pass' == $flag_query) {
                $param = 'with_ignored_param';
            } elseif ('ignore' == $flag_query) {
                $param = 'without_param';
            } else {
                $param = 'exact_match';
            }
        }

        $enabled ='';
        if ( ! empty(true == $redirect_value['enabled'])) {
            $enabled ='yes';
        }
        $regex_enable ='';
        if ( ! empty($redirect_value['regex'])) {
            $regex_enable ='yes';
        }

        wp_insert_post([
            'post_title'  => ltrim(urldecode($redirect_value['url']), '/'),
            'post_type'   => 'seopress_404',
            'post_status' => 'publish',
            'meta_input'  => [
                '_seopress_redirections_value'   => urldecode($redirect_value['action_data']['url']),
                '_seopress_redirections_type'    => $type,
                '_seopress_redirections_enabled' => $enabled,
                '_seopress_redirections_enabled_regex' => $regex_enable,
                '_seopress_redirections_logged_status'  => 'both',
                '_seopress_redirections_param'   => $param,
            ],
        ]);
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_redirections_plugin_settings');

//Import Redirections from Rank Math plugin TXT file
//@since 3.8.2
//@author Benjamin Denis
function seopress_import_rk_redirections() {
    if (empty($_POST['seopress_action']) || 'import_rk_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_rk_redirections_nonce'], 'seopress_import_rk_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('txt' != $extension) {
        wp_die(__('Please upload a valid .txt file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $settings = (array) json_decode(file_get_contents($import_file), true);

    foreach ($settings['redirections'] as $redirect_key => $redirect_value) {
        $type = '';
        if ( ! empty($redirect_value['header_code'])) {
            $type = $redirect_value['header_code'];
        }

        $source = '';
        if ( ! empty($redirect_value['sources'])) {
            $source = maybe_unserialize($redirect_value['sources']);
            $source = ltrim(urldecode($source[0]['pattern']), '/');
        }

        $param = 'exact_match';

        $enabled ='';
        if ( ! empty('active' == $redirect_value['status'])) {
            $enabled ='yes';
        }

        $redirect ='';
        if ( ! empty($redirect_value['url_to'])) {
            $redirect = urldecode($redirect_value['url_to']);
        }

        $count ='';
        if ( ! empty($redirect_value['hits'])) {
            $count = $redirect_value['hits'];
        }

        wp_insert_post(
            [
                'post_title'  => $source,
                'post_type'   => 'seopress_404',
                'post_status' => 'publish',
                'meta_input'  => [
                    '_seopress_redirections_value'   => $redirect,
                    '_seopress_redirections_type'    => $type,
                    '_seopress_redirections_enabled' => $enabled,
                    '_seopress_redirections_enabled_regex' => '',
                    '_seopress_redirections_logged_status'  => 'both',
                    'seopress_404_count'             => $count,
                    '_seopress_redirections_param'   => $param,
                ],
            ]
        );
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_rk_redirections');

//Clean all 404
function seopress_clean_404_query_hook($args) {
    unset($args['date_query']);

    return $args;
}

function seopress_clean_404() {
    if (empty($_POST['seopress_action']) || 'clean_404' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_clean_404_nonce'], 'seopress_clean_404_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', '404'))) {
        return;
    }

    add_filter('seopress_404_cleaning_query', 'seopress_clean_404_query_hook');
    do_action('seopress_404_cron_cleaning', true);
    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_clean_404');

//Reset Count column
function seopress_clean_counters() {
    if (empty($_POST['seopress_action']) || 'clean_counters' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_clean_counters_nonce'], 'seopress_clean_counters_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', '404'))) {
        return;
    }

    global $wpdb;

    //SQL query
    $sql = 'DELETE  FROM `' . $wpdb->prefix . 'postmeta` WHERE `meta_key` = \'seopress_404_count\'';

    $sql = $wpdb->prepare($sql);

    $wpdb->query($sql);

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_clean_counters');

//Clean all (redirects / 404 errors)
function seopress_clean_all() {
    if (empty($_POST['seopress_action']) || 'clean_all' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_clean_all_nonce'], 'seopress_clean_all_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', '404'))) {
        return;
    }

    global $wpdb;

    //SQL query
    $sql = 'DELETE `posts`, `pm`
		FROM `' . $wpdb->prefix . 'posts` AS `posts`
		LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
		WHERE `posts`.`post_type` = \'seopress_404\'';

    $sql = $wpdb->prepare($sql);

    $wpdb->query($sql);

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_clean_all');

//Reset SEOPress Notices Settings
function seopress_reset_notices_settings() {
    if (empty($_POST['seopress_action']) || 'reset_notices_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_reset_notices_nonce'], 'seopress_reset_notices_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'reset_settings'))) {
        return;
    }

    global $wpdb;

    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_notices' ");

    wp_safe_redirect(admin_url('admin.php?page=seopress-import-export'));
    exit;
}
add_action('admin_init', 'seopress_reset_notices_settings');

//Reset SEOPress Settings
function seopress_reset_settings() {
    if (empty($_POST['seopress_action']) || 'reset_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_reset_nonce'], 'seopress_reset_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'reset_settings'))) {
        return;
    }

    global $wpdb;

    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_%' ");

    wp_safe_redirect(admin_url('admin.php?page=seopress-import-export'));
    exit;
}
add_action('admin_init', 'seopress_reset_settings');

//Export SEOPress BOT Links to CSV
function seopress_bot_links_export_settings() {
    if (empty($_POST['seopress_action']) || 'export_csv_links_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_csv_links_nonce'], 'seopress_export_csv_links_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }
    $args = [
        'post_type'      => 'seopress_bot',
        'posts_per_page' => 1000,
        'post_status'    => 'publish',
        'order'          => 'DESC',
        'orderby'        => 'date',
    ];
    $the_query = new WP_Query($args);

    $settings['URL']        = [];
    $settings['Source']     = [];
    $settings['Source_Url'] = [];
    $settings['Status']     = [];
    $settings['Type']       = [];

    $csv_fields   = [];
    $csv_fields[] = 'URL';
    $csv_fields[] = 'Source';
    $csv_fields[] = 'Source URL';
    $csv_fields[] = 'Status';
    $csv_fields[] = 'Type';

    $output_handle = @fopen('php://output', 'w');

    //Header
    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-links-export-' . date('m-d-Y') . '.csv');
    header('Expires: 0');
    header('Pragma: public');

    //Insert header row
    fputcsv($output_handle, $csv_fields);

    // The Loop
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();

            array_push($settings['URL'], get_the_title());

            array_push($settings['Source'], get_post_meta(get_the_ID(), 'seopress_bot_source_title', true));

            array_push($settings['Source_Url'], get_post_meta(get_the_ID(), 'seopress_bot_source_url', true));

            array_push($settings['Status'], get_post_meta(get_the_ID(), 'seopress_bot_status', true));

            array_push($settings['Type'], get_post_meta(get_the_ID(), 'seopress_bot_type', true));

            fputcsv($output_handle, array_merge($settings['URL'], $settings['Source'], $settings['Source_Url'], $settings['Status'], $settings['Type']));

            //Clean arrays
            $settings['URL']        = [];
            $settings['Source']     = [];
            $settings['Source_Url'] = [];
            $settings['Status']     = [];
            $settings['Type']       = [];
        }
        wp_reset_postdata();
    }

    // Close output file stream
    fclose($output_handle);

    exit;
}
add_action('admin_init', 'seopress_bot_links_export_settings');

//Export metadata
function seopress_download_batch_export() {
    if (empty($_GET['seopress_action']) || 'seopress_download_batch_export' != $_GET['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_GET['nonce'], 'seopress_csv_batch_export_nonce')) {
        return;
    }
    if (current_user_can(seopress_capability('manage_options', 'export_settings')) && is_admin()) {
        if ('' != get_option('seopress_metadata_csv')) {
            $csv = get_option('seopress_metadata_csv');

            $csv_fields   = [];
            $csv_fields[] = 'id';
            $csv_fields[] = 'post_title';
            $csv_fields[] = 'url';
            $csv_fields[] = 'meta_title';
            $csv_fields[] = 'meta_desc';
            $csv_fields[] = 'fb_title';
            $csv_fields[] = 'fb_desc';
            $csv_fields[] = 'fb_img';
            $csv_fields[] = 'tw_title';
            $csv_fields[] = 'tw_desc';
            $csv_fields[] = 'tw_img';
            $csv_fields[] = 'noindex';
            $csv_fields[] = 'nofollow';
            $csv_fields[] = 'noodp';
            $csv_fields[] = 'noimageindex';
            $csv_fields[] = 'noarchive';
            $csv_fields[] = 'nosnippet';
            $csv_fields[] = 'canonical_url';
            $csv_fields[] = 'primary_cat';
            $csv_fields[] = 'redirect_active';
            $csv_fields[] = 'redirect_type';
            $csv_fields[] = 'redirect_url';
            $csv_fields[] = 'target_kw';
            ob_start();
            $output_handle = @fopen('php://output', 'w');

            //Insert header row
            fputcsv($output_handle, $csv_fields, ';');

            //Header
            ignore_user_abort(true);
            nocache_headers();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=seopress-metadata-export-' . date('m-d-Y') . '.csv');
            header('Expires: 0');
            header('Pragma: public');

            if ( ! empty($csv)) {
                foreach ($csv as $value) {
                    fputcsv($output_handle, $value, ';');
                }
            }

            // Close output file stream
            fclose($output_handle);

            //Clean database
            delete_option('seopress_metadata_csv');
            exit;
        }
    }
}
add_action('admin_init', 'seopress_download_batch_export');
