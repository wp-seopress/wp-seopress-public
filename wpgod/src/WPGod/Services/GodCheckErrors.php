<?php 

namespace WPGodWpseopress\Services;

use WPGodWpseopress\Models\ServiceInterface;
use WPGodWpseopress\Models\HooksInterface;
use WPGodWpseopress\WPGod;

class GodCheckErrors extends GodAbstractService implements HooksInterface{

    
    public function hooks(){
        add_action('init', array($this, 'checkOtherTransient'), 0);
        add_action('init', array($this, 'searchErrors'), 1);
        add_action( 'wp_ajax_send_error', array($this, 'prefix_ajax_send_error') );
        add_action( 'wp_ajax_nopriv_send_error', array($this, 'prefix_ajax_send_error') );
    }

    public function prefix_ajax_send_error(){
       $this->sendErrors();
    }

    public function searchErrors(){
        $transient      = get_transient( $this->getService('GodApi')->getNameTransient());

        if(!$transient || empty($transient)){
            return;
        }

        if(is_admin()){
            add_action("admin_enqueue_scripts", array($this, "enqueue_script_god"));    
        }
        else{
            add_action("wp_enqueue_scripts", array($this, "enqueue_script_god"));
        }
    }

    public function enqueue_script_god(){
        $url = admin_url("admin-ajax.php");
        $urlRegisterScript = sprintf("%s/%s.js", WPGod::$GOD_PATH_PUBLIC_JS, "send-error");
        $uniqId = uniqid();
        wp_register_script( $uniqId . '-god-js', $urlRegisterScript, array( 'jquery' ) );

        $arr = array(
            "url"       => $url,
            "action"    => "send_error"
        );
        wp_localize_script( $uniqId . '-god-js', 'configGodError', $arr );

        wp_enqueue_script( $uniqId . '-god-js' );
    }

    public function checkOtherTransient(){
        $godApi    = $this->getService('GodApi');
        $name      = $godApi::getNameTransientOtherStatic();
        $transient = get_transient($name);

        if(!$transient || empty($transient)){
            return;
        }

        foreach ($transient as $key => $value) {
            if($this->getService('GodAuthorizeError')->authorizeHandler($value["file"], $value["code"])){
                $this->getService("GodApi")->sendError($value);
            }
        }

        delete_transient($name);

    }

    public function sendErrors(){
        $name      = $this->getService("GodApi")->getNameTransient();
        $transient = get_transient($name);

        if(!$transient || empty($transient)){
            return;
        }

        foreach ($transient as $key => $value) {
            $this->getService("GodApi")->sendError($value);
        }

        delete_transient($name);
    }
}
