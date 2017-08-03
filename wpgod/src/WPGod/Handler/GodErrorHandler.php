<?php

namespace WPGodWpseopress\Handler;

use WPGodWpseopress\Models\GodHandlerInterface;
use WPGodWpseopress\Services\GodAbstractService;
use WPGodWpseopress\Helpers\GodTypeDevelopment;

/**
 * 
 * GodErrorHandler
 * 
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class GodErrorHandler extends GodAbstractService implements GodHandlerInterface{

    public static $functionExclude = array(
        "getBackTrace",
        "register_error",
        "godErrorHandler",
        "godErrorShutdownHandler",
        "verifyExistErrors"
    );

    public function __construct($services = array(), $helpers = array(), $pause = false){

        parent::__construct($services, $helpers);

        if(!$pause):
            set_error_handler(array($this, 'godErrorHandler'));
            register_shutdown_function(array($this, 'godErrorShutdownHandler'));

        endif;
    }


    public function checkLibraryPlugin($file){

        $pluginDir     = dirname(str_replace(WP_PLUGIN_DIR, "", $file));
        $namespaceName = "\WPGod" . ucfirst(str_replace("/", "", $pluginDir));
        $class         = $namespaceName . "\Services\GodApi";
        if(file_exists(WP_PLUGIN_DIR . $pluginDir . '/wpgod/src/WPGod/Services/GodApi.php') && 
            method_exists($class, "getNameTransientOtherStatic") && 
            method_exists($class, "getNameTransientAlreadyCheck")){
                return array(
                    "already_check" => $class::getNameTransientAlreadyCheck(),
                    "other"         => $class::getNameTransientOtherStatic()
                );
        }

        return false;

    }

    public function godErrorHandler($code, $message, $file, $line, $ctx = array()) {
        $serializeParams = array(
            "file" => $file,
            "code" => $code,
            "line" => $line,
            "message" => $message
        );
        if($this->getService("GodAuthorizeError")->authorizeHandler($file, $code) && 
            !$this->getService("GodAuthorizeError")->checkAlreadyTrack($serializeParams) ) {

            $this->code    = $code;
            $this->message = $message;
            $this->file    = $file;
            $this->line    = $line;
            $this->ctx     = $ctx;

            $this->registerError();

            $transients = $this->checkLibraryPlugin($file);

            if($transients && !$this->getService("GodAuthorizeError")->checkAlreadyTrack($serializeParams, $transients["already_check"])){
                $this->registerError($transients["other"]);
            }

        }
    }

    protected function registerError($nameTransient = null){
        
        $this->file = addslashes($this->file);

        global $wp_version;
        $locale    = get_locale();
        $locale    = str_replace('_', '-', $locale);


        $pluginData = $this->getHelper("GodEnvironmentInfo")->getPluginData($this->getService("GodApi")->getPluginFile());
        
        $version    = $this->getService("GodApi")->getVersion();
        if($pluginData){
            $version = $pluginData["plugin_version"];
        }

        $params = array(
            "file"     => $this->file,
            "message"  => $this->message,
            "line"     => $this->line,
            "code"     => $this->code,
            "wp_infos" => array(
                "wp_version"       => $wp_version,
                "blog_id"          => get_current_blog_id(),
                "wp_local_package" => $locale
            ),
            "server_infos" => $this->getHelper("GodServerInfo")->getInfosServerForSavePost(),
            "version"      => $version,
            "home_url"     => get_home_url(),
            "php_version"  => PHP_VERSION
        ); 

        $this->getService("GodApi")->saveError($params, $nameTransient);
    }

    public function godErrorShutdownHandler(){

        $last_error = error_get_last();
        if ($last_error !== null) {
            $this->godErrorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }

}









