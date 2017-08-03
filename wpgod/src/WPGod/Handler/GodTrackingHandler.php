<?php

namespace WPGodWpseopress\Handler;

use WPGodWpseopress\Services\GodAbstractService;
use WPGodWpseopress\Helpers\GodTypeDevelopment;
use WPGodWpseopress\Models\HooksInterface;
use WPGodWpseopress\WPGod;

/**
 * 
 * GodTrackingHandler
 * 
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class GodTrackingHandler extends GodAbstractService implements HooksInterface {

    public function __construct($services = array(), $helpers = array(), $pause = false){

        parent::__construct($services, $helpers);

        $this->pause = $pause;
        
    }


    public function hooks(){
        $apiService    = $this->getService('GodApi');     

        if(!$this->pause):
            $lastDateCheck       = $apiService::getLastCheckTrackingGeneral();
            $intervalTracking    = apply_filters("god_interval_tracking", 60 * 60 * 24 * 5);
            $authorizeMonitoring = true;
            
            $preventUser         = $this->getService("GodApi")->getPreventUser();

            if($preventUser){
                
                $allowTracking = get_option('wpgod_seopress_allow_tracking');

                if(!$allowTracking){
                    $authorizeMonitoring = false;
                }
            }

            if((!$lastDateCheck || abs(time() - $lastDateCheck) > $intervalTracking) && $authorizeMonitoring){
                $apiService::initLastCheckTrackingGeneral();
                add_action('init', array($this, 'trackingGeneral'), 0);
                add_action( 'wp_ajax_tracking_general', array($this, 'prefix_ajax_tracking_general') );
                add_action( 'wp_ajax_nopriv_tracking_general', array($this, 'prefix_ajax_tracking_general') );
            }
        endif;
    }

    public function prefix_ajax_tracking_general(){
        $serverInfos = $this->getHelper("GodServerInfo")->getTrackingServerData();
        $wpInfos     = $this->getHelper("GodEnvironmentInfo")->getAllDataEnvironment();
        $pluginData  = $this->getHelper("GodEnvironmentInfo")->getPluginData($this->getService("GodApi")->getPluginFile());

        $this->getService("GodApi")->sendTrackingGeneral(array_merge($serverInfos, $wpInfos, $pluginData));
    }

    public function trackingGeneral(){
        $pluginData  = $this->getHelper("GodEnvironmentInfo")->getPluginData($this->getService("GodApi")->getPluginFile());
        if(is_admin()){
            add_action("admin_enqueue_scripts", array($this, "enqueue_script_god"));    
        }
        else{
            add_action("wp_enqueue_scripts", array($this, "enqueue_script_god"));
        }
    }

    public function enqueue_script_god(){
        $url               = admin_url("admin-ajax.php");
        $urlRegisterScript = sprintf("%s/%s.js", WPGod::$GOD_PATH_PUBLIC_JS, "send-tracking-general");
        $uniqId            = uniqid();
        wp_register_script( $uniqId . '-god-js', $urlRegisterScript, array( 'jquery' ) );

        $arr = array(
            "url"       => $url,
            "action"    => "tracking_general"
        );
        wp_localize_script( $uniqId . '-god-js', 'configGodTrackingGeneral', $arr );

        wp_enqueue_script( $uniqId . '-god-js' );
    }



}









