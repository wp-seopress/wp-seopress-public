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
class GodEnvironmentInfo implements HelperInterface {

    public function getThemeData(){
        if ( get_bloginfo( 'version' ) < '3.4' ) {
            $themeData = get_theme_data( get_stylesheet_directory() . '/style.css' );
            $theme     = $themeData['Name'] . ' ' . $themeData['Version'];
        } else {
            $themeData = wp_get_theme();
            $theme     = $themeData->Name . ' ' . $themeData->Version;
        }

        return array(
            "theme" => $theme,
            "theme_data" => $themeData
        );
    }

    public function getAllDataEnvironment(){

        $data = array();
        try {

            $themeData = $this->getThemeData();
            $data['admin_email']            = get_bloginfo( 'admin_email' );
            $data['locale']                 = get_locale();
            $data["multisite"]              = is_multisite();
            $data["site_url"]               = site_url();
            $data["home_url"]               = home_url();
            $data["wp_version"]             = get_bloginfo( 'version' );
            $data["permalink_structure"]    = get_option( 'permalink_structure' );
            $data["active_theme"]           = $themeData["theme"];
            
            $data["registered_post_stati"]  = implode( ', ', get_post_stati());
            $data["wp_debug"]               = defined( 'WP_DEBUG' )? WP_DEBUG : false;

           
            return $data;
            
        } catch (\Exception $e) {
            return $data;
        }

    }

    public function getPluginData($pluginFile){
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins        = get_plugins();
        $active_plugins = get_option( 'active_plugins', array() );

        $pluginData     = null;
        foreach ( $plugins as $plugin_path => $plugin ) {
            if ( ! in_array( $plugin_path, $active_plugins ) || $plugin_path != $pluginFile)
                continue;

            $pluginData = $plugin; 
            break;
        }

        return array(
            "plugin_version" => $pluginData['Version']
        );
    }

    public function getPluginDataMultisite($pluginFile){

        if ( !is_multisite() ){
            return false;
        }
        
        $plugins        = wp_get_active_network_plugins();
        $active_plugins = get_site_option( 'active_sitewide_plugins', array() );
        $pluginData     = null;

        foreach ( $plugins as $plugin_path ) {
            $plugin_base = plugin_basename( $plugin_path );

            if ( ! array_key_exists( $plugin_base, $active_plugins ) || $plugin_path != $pluginFile )
                continue;

            $pluginData = $plugin; 
            break;
        }

        return array(
            "plugin_version" => $pluginData['Version']
        );
    }

}


