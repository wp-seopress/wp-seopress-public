<?php

namespace SEOPress\Services;

if (! defined('ABSPATH')) {
    exit;
}

class EnqueueModuleMetabox
{
    public function canEnqueue()
    {
        $response = true;

        if (isset($_GET['seopress_preview']) || isset($_GET['preview'])) {
            $response = false;
        }

        if (isset($_GET['oxygen_iframe'])) {
            $response = false;
        }

        if (isset($_GET['brickspreview'])) {
            $response = false;
        }

        if (isset($_GET['vcv-editable'])) {
            $response = false;
        }

        if(!is_admin() && (!is_singular() || is_home() || is_front_page())){
            $response = false;
        }

        if (function_exists('get_current_screen')) {
            $currentScreen = \get_current_screen();

            if($currentScreen && method_exists($currentScreen, 'is_block_editor') &&  $currentScreen->is_block_editor() === false){
                $response = false;
            }

            if($currentScreen && !seopress_get_service('AdvancedOption')->getAccessUniversalMetaboxGutenberg() && method_exists($currentScreen, 'is_block_editor') &&  $currentScreen->is_block_editor() !== false){
                $response = false;
            }
        }

        if(seopress_get_service('AdvancedOption')->getDisableUniversalMetaboxGutenberg()){
            $response = false;
        }

        return apply_filters('seopress_can_enqueue_universal_metabox', $response);
    }
}
