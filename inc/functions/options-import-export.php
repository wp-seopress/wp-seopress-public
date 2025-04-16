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
    if (! isset($_POST['seopress_export_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seopress_export_nonce'])), 'seopress_export_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    $settings = seopress_get_service('ExportSettings')->handle();

    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-settings-export-' . gmdate('m-d-Y') . '.json');
    header('Expires: 0');
    echo wp_json_encode($settings);
    exit;
}
add_action('admin_init', 'seopress_export_settings');

//Import SEOPress Settings from JSON
function seopress_import_settings() {
    if (empty($_POST['seopress_action']) || 'import_settings' != $_POST['seopress_action']) {
        return;
    }
    if (! isset($_POST['seopress_import_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seopress_import_nonce'])), 'seopress_import_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(esc_html__('Please upload a valid .json file', 'wp-seopress'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];

    if (empty($import_file)) {
        wp_die(esc_html__('Please upload a file to import', 'wp-seopress'));
    }

    $settings = (array) json_decode(seopress_remove_utf8_bom(file_get_contents($import_file)), true);

    seopress_get_service('ImportSettings')->handle($settings);

    wp_safe_redirect(admin_url('admin.php?page=seopress-import-export&success=true'));
    exit;
}
add_action('admin_init', 'seopress_import_settings');

// Delete all content scans
function seopress_clean_content_scans() {
    if (empty($_POST['seopress_action']) || 'clean_content_scans' != $_POST['seopress_action']) {
        return;
    }
    if (!isset($_POST['seopress_clean_content_scans_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seopress_clean_content_scans_nonce'])), 'seopress_clean_content_scans_nonce')) {
        return;
    }
    if (!current_user_can(seopress_capability('manage_options', 'cleaning'))) {
        return;
    }

    // Delete cache option
    delete_option('seopress_content_analysis_api_in_progress');

    global $wpdb;

    // Clean our post metas
    $sql = 'DELETE FROM `' . $wpdb->prefix . 'postmeta` WHERE `meta_key` IN ( \'_seopress_analysis_data\', \'_seopress_content_analysis_api\', \'_seopress_analysis_data_oxygen\', \'_seopress_content_analysis_api_in_progress\')';
    $sql = $wpdb->prepare($sql);
    $wpdb->query($sql);

    // Clean custom table if it exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}seopress_content_analysis'") === $wpdb->prefix . 'seopress_content_analysis') {
        $sql = 'DELETE FROM `' . $wpdb->prefix . 'seopress_content_analysis`';
        $sql = $wpdb->prepare($sql);
        $wpdb->query($sql);
    }

    wp_safe_redirect(admin_url('admin.php?page=seopress-import-export'));
    exit;
}
add_action('admin_init', 'seopress_clean_content_scans');


//Reset SEOPress Notices Settings
function seopress_reset_notices_settings() {
    if (empty($_POST['seopress_action']) || 'reset_notices_settings' != $_POST['seopress_action']) {
        return;
    }
    if (!isset($_POST['seopress_reset_notices_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seopress_reset_notices_nonce'])), 'seopress_reset_notices_nonce')) {
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
    if (!isset($_POST['seopress_reset_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seopress_reset_nonce'])), 'seopress_reset_nonce')) {
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
