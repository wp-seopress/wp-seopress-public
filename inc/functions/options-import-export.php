<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Import / Exports settings page
///////////////////////////////////////////////////////////////////////////////////////////////////

//Export WP Admin UI Settings in JSON
function seopress_export_settings() {
    if( empty( $_POST['seopress_action'] ) || 'export_settings' != $_POST['seopress_action'] )
        return;
    if( ! wp_verify_nonce( $_POST['seopress_export_nonce'], 'seopress_export_nonce' ) )
        return;
    if( ! current_user_can( 'manage_options' ) )
        return;
    
    $settings["seopress_activated"]                     = get_option( 'seopress_activated' );
    $settings["seopress_titles_option_name"]            = get_option( 'seopress_titles_option_name' );
    $settings["seopress_social_option_name"]            = get_option( 'seopress_social_option_name' );
    $settings["seopress_xml_sitemap_option_name"]       = get_option( 'seopress_xml_sitemap_option_name' );

    ignore_user_abort( true );
    nocache_headers();
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=seopress-settings-export-' . date( 'm-d-Y' ) . '.json' );
    header( "Expires: 0" );
    echo json_encode( $settings );
    exit;
}
add_action( 'admin_init', 'seopress_export_settings' );

//Import WP Admin UI Settings from JSON
function seopress_import_settings() {
    if( empty( $_POST['seopress_action'] ) || 'import_settings' != $_POST['seopress_action'] )
        return;
    if( ! wp_verify_nonce( $_POST['seopress_import_nonce'], 'seopress_import_nonce' ) )
        return;
    if( ! current_user_can( 'manage_options' ) )
        return;
    $extension = end( explode( '.', $_FILES['import_file']['name'] ) );
    if( $extension != 'json' ) {
        wp_die( __( 'Please upload a valid .json file' ) );
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if( empty( $import_file ) ) {
        wp_die( __( 'Please upload a file to import' ) );
    }

    $settings = (array) json_decode( file_get_contents( $import_file ), true );

    update_option( 'seopress_activated', $settings["seopress_activated"] ); 
    update_option( 'seopress_titles_option_name', $settings["seopress_titles_option_name"] ); 
    update_option( 'seopress_social_option_name', $settings["seopress_social_option_name"] ); 
    update_option( 'seopress_xml_sitemap_option_name', $settings["seopress_xml_sitemap_option_name"] ); 
     
    wp_safe_redirect( admin_url( 'admin.php?page=seopress-import-export' ) ); exit;
}
add_action( 'admin_init', 'seopress_import_settings' );

//Reset WP Admin UI Settings
function seopress_reset_settings() {
    if( empty( $_POST['seopress_action'] ) || 'reset_settings' != $_POST['seopress_action'] )
        return;
    if( ! wp_verify_nonce( $_POST['seopress_reset_nonce'], 'seopress_reset_nonce' ) )
        return;
    if( ! current_user_can( 'manage_options' ) )
        return;

    global $wpdb;
    
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_%' ");
     
    wp_safe_redirect( admin_url( 'admin.php?page=seopress-import-export' ) ); exit;
}
add_action( 'admin_init', 'seopress_reset_settings' );
