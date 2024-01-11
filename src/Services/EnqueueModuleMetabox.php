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

        global $pagenow;

        if ('widgets.php' == $pagenow) {
            $response = false;
        }

        if (isset($_GET['seopress_preview']) || isset($_GET['preview'])) {
            $response = false;
        }

        if (isset($_GET['oxygen_iframe'])) {
            $response = false;
        }

        if (isset($_GET['brickspreview'])) {
            $response = false;
        }

        if (isset($_GET['et_bfb'])) {
            $response = false;
        }

        if(!is_admin() && !is_singular()){
            $response = false;
        }

        if(get_the_ID() === (int) get_option('page_on_front')){
            $response = true;
        }

        if(get_the_ID() ===  (int) get_option('page_for_posts')){
            $response = true;
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

        if(!current_user_can('edit_posts')){
            $response = false;
        }

        // Compatibility with WooCommerce beta product page
        if(isset($_GET['page']) && $_GET['page'] === 'wc-admin'){
            $response = false;
        }

        if(isset($_GET['path']) && strpos($_GET['path'], 'product') !== false){
            $response = true;
        }

        $settingsAdvanced = seopress_get_service('AdvancedOption');
        $rolesTabs = [
            "GLOBAL" => $settingsAdvanced->getSecurityMetaboxRole(),
            "CONTENT_ANALYSIS" => $settingsAdvanced->getSecurityMetaboxRoleContentAnalysis(),
        ];


        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;
        $counterCanEdit = 0;

        foreach ($rolesTabs as $key => $roleTab) {
            if($roleTab === null){
                continue;
            }

            $diff = array_diff($roles, array_keys($roleTab));
            if(count($diff) !== count($roles)){
                $counterCanEdit++;
            }
        }

        if($counterCanEdit >= 2){
            $response = false;
        }

        if(isset($_POST['can_enqueue_seopress_metabox']) && $_POST['can_enqueue_seopress_metabox'] !== '1'){
            $response = false;
        }
        if(isset($_POST['can_enqueue_seopress_metabox']) && $_POST['can_enqueue_seopress_metabox'] === '1'){
            $response = true;
        }

        return apply_filters('seopress_can_enqueue_universal_metabox', $response);
    }
}
