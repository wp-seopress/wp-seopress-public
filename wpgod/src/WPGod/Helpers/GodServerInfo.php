<?php

namespace WPGodWpseopress\Helpers;

use WPGodWpseopress\Models\HelperInterface;

/**
 * GodServerInfo
 *
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 2.0.0
 * @since 1.0.0
 */
class GodServerInfo implements HelperInterface {


    public function getInfosServerForSavePost(){
        $data["is_ajax"]                 = (defined('DOING_AJAX') && DOING_AJAX) ? true : false;
        $data['HTTP_HOST']               = esc_html( $_SERVER['HTTP_HOST'] );
        $data['SERVER_ADDR']             = $_SERVER['SERVER_ADDR'];
        $data['SERVER_PORT']             = $_SERVER['SERVER_PORT'];
        $data['REQUEST_URI']             = str_replace( '&amp;nbsp;', '&nbsp;', esc_html( $_SERVER['REQUEST_URI'] ) );
        $data["php_safe_mode"]           = ini_get( 'safe_mode' );
        $data["php_memory_limit"]        = ini_get( 'memory_limit' );
        $data["php_upload_max_size"]     = ini_get( 'upload_max_filesize' );
        $data["php_post_max_size"]       = ini_get( 'post_max_size' );
        $data["php_upload_max_filesize"] = ini_get( 'upload_max_filesize' );
        $data["php_time_limit"]          = ini_get( 'max_execution_time' );
        $data["php_max_input_vars"]      = ini_get( 'max_input_vars' );
        $data["php_arg_separator"]       = ini_get( 'arg_separator.output' );
        $data["php_allow_url_file_open"] = ini_get( 'allow_url_fopen' );
        $data["wp_memory_limit"]         = WP_MEMORY_LIMIT;
        $data["session"]                 = isset( $_SESSION );
        $data["cookies"]                 = ini_get( 'session.use_cookies' );
        $data["only_cookies"]            = ini_get( 'session.use_only_cookies' );
        $data["display_errors"]          = ini_get( 'display_errors' );
        $data["fsockopen"]               = function_exists( 'fsockopen' );
        $data["curl"]                    = function_exists( 'curl_init' );
        $data["soap"]                    = class_exists( 'SoapClient');
        $data["suhosin"]                 = extension_loaded( 'suhosin' );

        $data["active_plugins"]         = array();
        $data["network_active_plugins"] = array();
        
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins                        = get_plugins();
        $active_plugins                 = get_option( 'active_plugins', array() );

        $i = 0;
        foreach ( $plugins as $plugin_path => $plugin ) {
            if ( ! in_array( $plugin_path, $active_plugins ) )
                continue;
            $data["active_plugins"][$i]["name"]    =  $plugin['Name'];
            $data["active_plugins"][$i]["version"] =  $plugin['Version'];
            $i++;
        }

        if ( is_multisite() ){
            $i = 0;
            $plugins        = wp_get_active_network_plugins();
            $active_plugins = get_site_option( 'active_sitewide_plugins', array() );

            foreach ( $plugins as $plugin_path ) {
                $plugin_base = plugin_basename( $plugin_path );

                if ( ! array_key_exists( $plugin_base, $active_plugins ) )
                    continue;

                $plugin = get_plugin_data( $plugin_path );

                $data["network_active_plugins"][$i]["name"]    =  $plugin['Name'];
                $data["network_active_plugins"][$i]["version"] =  $plugin['Version'];
                $i++;
            }
        }



        return $data;
    }

    public function getTrackingServerData(){

        $data = array();
        if(function_exists("mysql_get_server_info")){
            $data["mysql_version"] = mysql_get_server_info();
        }
        
        $data["web_server"]  = $_SERVER['SERVER_SOFTWARE'];
        $data['server_addr'] = $_SERVER['SERVER_ADDR'];
        $data["php_version"] = PHP_VERSION;
        
        return $data;
    }


}


