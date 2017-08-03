<?php 

namespace WPGodWpseopress\Services;

use WPGodWpseopress\Models\ServiceInterface;
use WPGodWpseopress\WPGod;

class GodApi implements ServiceInterface
{

    public static $NAME_TRANSIENT_OTHER         = "wpgod_580f97c174717_site";
    
    public static $NAME_TRANSIENT_ALREADY_CHECK = "wpgod_580f97c174754_already";
    
    public static $LAST_CHECK_TRACKING_GENERAL  = "wpgod_580f97c17478b_tracking_g";
        
    public function __construct($params){
        
        $this->basename        = (isset($params["basename"])) ? $params["basename"] : null;
        $this->pluginFile      = (isset($params["plugin_file"])) ? $params["plugin_file"] : null;
        $this->preventUser     = (isset($params["prevent_user"])) ? $params["prevent_user"] : null;
        $this->typeDevelopment = (isset($params["type_development"])) ? $params["type_development"] : null;
        $this->token           = (isset($params["token"])) ? $params["token"] : null;
        $this->nameTransient   = (isset($params["name_transient"])) ?  $params["name_transient"] : "_god_save_errors";
        $this->environment     = (isset($params["environment"])) ? $params["environment"] : null;
        $this->version         = (isset($params["version"])) ? $params["version"] : null;
    }

    public static function getLastCheckTrackingGeneral(){
        return get_option(self::$LAST_CHECK_TRACKING_GENERAL);
    }

    public static function initLastCheckTrackingGeneral(){
        update_option(self::$LAST_CHECK_TRACKING_GENERAL, time());
    }

    public static function getNameTransientOtherStatic(){
        return self::$NAME_TRANSIENT_OTHER;
    }

    public static function getNameTransientAlreadyCheck(){
        return self::$NAME_TRANSIENT_ALREADY_CHECK;
    }

    public function getPreventUser(){
        return $this->preventUser;
    }

    public function getPluginFile(){
        return $this->pluginFile;
    }

    public function getVersion(){
        return $this->version;
    }

    public function getEnvironment(){
        return $this->environment;
    }

    public function getBasename(){
        return $this->basename;
    }


    public function getClientSecret(){
        return $this->clientSecret;
    }

    public function getTypeDevelopment(){
        return $this->typeDevelopment;
    }

    public function getNameTransient(){
        return $this->nameTransient;
    }

    public function serializeError($params){
        return md5(vsprintf("%s-%s-%s-%s", array($params["file"], $params["line"], $params["code"], $params["message"])));
    }

    public function saveError($params, $nameTransient = null){

        $nameTransient = ($nameTransient == null) ? $this->getNameTransient() : $nameTransient;
        
        $serialize = $this->serializeError($params);

        if ( false === ( $transientError = get_transient( $nameTransient ) ) ) {
            $transientError = array();
            $transientError[$serialize] = $params;
            set_transient($nameTransient, $transientError);
        }
        else{

            if(!array_key_exists($serialize, $transientError)){
                $transientError[$serialize] = $params;
                set_transient($nameTransient, $transientError);
            }
        }
    
    }

    public function sendTrackingGeneral($params){
        $url         = sprintf("%s/trackings.json", WPGod::GOD_URL_API);
        $data        = json_encode($params, true);

        $this->callApi($url, $data);

    }

    public function sendError($params){
        $url         = sprintf("%s/errors.json", WPGod::GOD_URL_API);
        $data        = json_encode($params, true);

        $this->callApi($url, $data);

    }

    public function callApi($url, $data){

        $bearer      = sprintf("Authorization: Bearer %s", $this->token);
        $environment = sprintf("Environment: %s", $this->getEnvironment());

        try {
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, 
                array(
                    "Content-Type: application/json",
                    "Accept: application/json",
                    $bearer,
                    $environment
                )
            ); 
            $result = curl_exec($ch);
        } catch (Exception $e) {}
    }


}
