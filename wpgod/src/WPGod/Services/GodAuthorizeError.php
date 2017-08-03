<?php 

namespace WPGodWpseopress\Services;

use WPGodWpseopress\Models\GodHandlerInterface;
use WPGodWpseopress\Services\GodAbstractService;
use WPGodWpseopress\Helpers\GodTypeDevelopment;
use WPGodWpseopress\Models\ServiceInterface;


class GodAuthorizeError extends GodAbstractService implements ServiceInterface
{    

    public function authorizeHandler($file, $code){
        $basename        = $this->getService("GodApi")->getBasename();
        $typeDevelopment = $this->getService("GodApi")->getTypeDevelopment();

        if (in_array($typeDevelopment, array(GodTypeDevelopment::PLUGIN, GodTypeDevelopment::MU_PLUGIN, GodTypeDevelopment::THEME))) {
            switch ($typeDevelopment) {
                case 'plugin':
                    $verify = sprintf("%s/%s", PLUGINDIR, $basename);
                    break;
                case 'mu-plugin':
                    $verify = sprintf("%s/%s", WPMU_PLUGIN_DIR, $basename);
                    break;
                case 'theme':
                    $verify = sprintf("themes/%s", $basename);
                    break;
            }

            if(strpos(str_replace("\\", "/", $file), $verify) === false){
                return false;
            }

        }

        if($this->getService("GodFilesRules")->isInformationIgnore("file_dir", $file) ||
            $this->getService("GodFilesRules")->isInformationIgnore("code_error", $code)){
            return false;
        }

        return true;

    }

    public function checkAlreadyTrack($params, $nameTransient = null){
        $apiServices   = $this->getService("GodApi");
        $nameTransient = ($nameTransient == null) ? $apiServices::getNameTransientAlreadyCheck() : $nameTransient;
        
        $serialize = $apiServices->serializeError($params);
        if ( false === ( $transientAlreadyCheckError = get_transient( $nameTransient ) ) ) {
            $transientAlreadyCheckError = array();
            $transientAlreadyCheckError[$serialize] = 1;
            set_transient($nameTransient, $transientAlreadyCheckError);
            return false;
        }
        else{

            if(!array_key_exists($serialize, $transientAlreadyCheckError)){
                $transientAlreadyCheckError[$serialize] = 1;
                set_transient($nameTransient, $transientAlreadyCheckError);
                return false;
            }

            return true;
        }
    }

}
