<?php

namespace WPGodWpseopress;

use WPGodWpseopress\Handler\GodErrorHandler;
use WPGodWpseopress\Handler\GodTrackingHandler;
use WPGodWpseopress\Handler\GodAdminNoticeHandler;
use WPGodWpseopress\Helpers\GodError;
use WPGodWpseopress\Helpers\GodServerInfo;
use WPGodWpseopress\Helpers\GodEnvironmentInfo;
use WPGodWpseopress\Helpers\GodTypeDevelopment;
use WPGodWpseopress\Services\GodApi;
use WPGodWpseopress\Services\GodFilesRules;
use WPGodWpseopress\Services\GodCheckErrors;
use WPGodWpseopress\Services\GodAuthorizeError;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * WPGod
 *
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class WPGod{
    
    public static $GOD_PATH_PUBLIC_JS;

    const PHP_VERSION_MINI = "5.4";
    const GOD_VERSION      = "2.2";
    const GOD_URL_API      = "https://api.wp-god.com/api/v1";

    protected $paramsObligatory = array(
        "basename",
        "type_development",
        "name_transient",
        "token",
        "environment"
    );

    protected $hooksServices   = array();

    public function __construct($params){
        self::$GOD_PATH_PUBLIC_JS = plugin_dir_url(__FILE__) . "../../public/js";
        
        $missingParameters = 0;

        foreach ($this->paramsObligatory as $key => $value) {
            if(!array_key_exists($value, $params)){
                $missingParameters++;
            }
        }

        if($missingParameters === 0){

            $godApi        = new GodApi($params);
            $godFilesRules = new GodFilesRules($params);
            $godAuthorizeError = new GodAuthorizeError(array(
                    $godApi,
                    $godFilesRules
                )
            );

            $pause                    = ($this->canBeTrigger()) ? false : true;
            $authorizeTracking        = get_option("wpgod_seopress_allow_tracking");
            $pauseTrackingEnvironment = (!($params["type_development"] == GodTypeDevelopment::PLUGIN) || !$authorizeTracking);

            $this->hooksServices = array(
                "god_handler"       => new GodErrorHandler(
                    array(
                        $godApi,
                        $godFilesRules,
                        $godAuthorizeError
                    ), 
                    array(
                        new GodError(),
                        new GodServerInfo(),
                        new GodEnvironmentInfo()
                    ),
                    $pause
                ),
                "god_tracking_handler" => new GodTrackingHandler(
                    array(
                        $godApi
                    ), 
                    array(
                        new GodEnvironmentInfo(),
                        new GodServerInfo()
                    ),
                    $pauseTrackingEnvironment
                ),
                "god_check_errors" => new GodCheckErrors(
                    array(
                        $godApi,
                        $godAuthorizeError
                    )
                )
            );
           
            if($params["type_development"] == GodTypeDevelopment::PLUGIN){
                $this->hooksServices["god_admin_notice_handler"] = new GodAdminNoticeHandler(
                    array(
                        $godApi
                    ), 
                    array(),
                    (isset($params["prevent_user"])) ? $params["prevent_user"] : false
                );
            }
        }
    }


    protected function canBeTrigger(){
        if(strpos(__DIR__, "_wpgod") !== false){
            return true;
        }

        if(file_exists(WPMU_PLUGIN_DIR . '/_wpgod')){
            return false;
        }

        return true;
    }


    protected function canLoadedWPGod(){

        if ( version_compare( PHP_VERSION, self::PHP_VERSION_MINI, '<' ) ) {
            return false;
        }

        if(!function_exists('curl_version')){
            return false;
        }

        return true;
    }

    public function execute(){

        if ($this->canLoadedWPGod()){
            add_action( 'plugins_loaded' , array($this,'hooks'), 0);
        }
    }

    public function hooks(){

        foreach ($this->hooksServices as $key => $hook) {
            if($hook instanceOf HooksInterface){
                $hook->hooks();
            }
            
            $interfaces = class_implements($hook);
            foreach ($interfaces as $key => $value) {
                $pos = strpos($key, "HooksInterface");
                if($pos !== false){
                    $hook->hooks();
                }
            }
        }
    }
}