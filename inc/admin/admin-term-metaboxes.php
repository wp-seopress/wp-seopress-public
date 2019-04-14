<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Restrict SEO metaboxes to user roles
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_advanced_security_metaboxe_role_hook_option() {
    $seopress_advanced_security_metaboxe_role_hook_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_security_metaboxe_role_hook_option ) ) {
        foreach ($seopress_advanced_security_metaboxe_role_hook_option as $key => $seopress_advanced_security_metaboxe_role_hook_value)
            $options[$key] = $seopress_advanced_security_metaboxe_role_hook_value;
         if (isset($seopress_advanced_security_metaboxe_role_hook_option['seopress_advanced_security_metaboxe_role'])) { 
            return $seopress_advanced_security_metaboxe_role_hook_option['seopress_advanced_security_metaboxe_role'];
         }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Check global settings
///////////////////////////////////////////////////////////////////////////////////////////////////
if (!function_exists('seopress_titles_single_term_noindex_option')) {
    function seopress_titles_single_term_noindex_option() {
        global $tax;
        $seopress_get_current_tax = $tax->name;
        
        $seopress_titles_single_term_noindex_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_single_term_noindex_option ) ) {
            foreach ($seopress_titles_single_term_noindex_option as $key => $seopress_titles_single_term_noindex_value)
                $options[$key] = $seopress_titles_single_term_noindex_value;
             if (isset($seopress_titles_single_term_noindex_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['noindex'])) {
                return $seopress_titles_single_term_noindex_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['noindex'];
             }
        }
    }
}

if (!function_exists('seopress_titles_noindex_option')) {
    function seopress_titles_noindex_option() {
        $seopress_titles_noindex_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_noindex_option ) ) {
            foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value)
                $options[$key] = $seopress_titles_noindex_value;
             if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) { 
                return $seopress_titles_noindex_option['seopress_titles_noindex'];
             }
        }
    }
}

if (!function_exists('seopress_titles_single_term_nofollow_option')) {
    function seopress_titles_single_term_nofollow_option() {
        global $tax;
        $seopress_get_current_tax = $tax->name;

        $seopress_titles_single_term_nofollow_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_single_term_nofollow_option ) ) {
            foreach ($seopress_titles_single_term_nofollow_option as $key => $seopress_titles_single_term_nofollow_value)
                $options[$key] = $seopress_titles_single_term_nofollow_value;
             if (isset($seopress_titles_single_term_nofollow_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['nofollow'])) { 
                return $seopress_titles_single_term_nofollow_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['nofollow'];
             }
        }
    }
}

if (!function_exists('seopress_titles_nofollow_option')) {
    function seopress_titles_nofollow_option() {
        $seopress_titles_nofollow_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_nofollow_option ) ) {
            foreach ($seopress_titles_nofollow_option as $key => $seopress_titles_nofollow_value)
                $options[$key] = $seopress_titles_nofollow_value;
             if (isset($seopress_titles_nofollow_option['seopress_titles_nofollow'])) { 
                return $seopress_titles_nofollow_option['seopress_titles_nofollow'];
             }
        }
    }
}

if (!function_exists('seopress_titles_noodp_option')) {
    function seopress_titles_noodp_option() {
        $seopress_titles_noodp_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_noodp_option ) ) {
            foreach ($seopress_titles_noodp_option as $key => $seopress_titles_noodp_value)
                $options[$key] = $seopress_titles_noodp_value;
             if (isset($seopress_titles_noodp_option['seopress_titles_noodp'])) { 
                return $seopress_titles_noodp_option['seopress_titles_noodp'];
             }
        }
    }
}

if (!function_exists('seopress_titles_noarchive_option')) {
    function seopress_titles_noarchive_option() {
        $seopress_titles_noarchive_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_noarchive_option ) ) {
            foreach ($seopress_titles_noarchive_option as $key => $seopress_titles_noarchive_value)
                $options[$key] = $seopress_titles_noarchive_value;
             if (isset($seopress_titles_noarchive_option['seopress_titles_noarchive'])) { 
                return $seopress_titles_noarchive_option['seopress_titles_noarchive'];
             }
        }
    }
}

if (!function_exists('seopress_titles_nosnippet_option')) {
    function seopress_titles_nosnippet_option() {
        $seopress_titles_nosnippet_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_nosnippet_option ) ) {
            foreach ($seopress_titles_nosnippet_option as $key => $seopress_titles_nosnippet_value)
                $options[$key] = $seopress_titles_nosnippet_value;
             if (isset($seopress_titles_nosnippet_option['seopress_titles_nosnippet'])) { 
                return $seopress_titles_nosnippet_option['seopress_titles_nosnippet'];
             }
        }
    }
}

if (!function_exists('seopress_titles_noimageindex_option')) {
    function seopress_titles_noimageindex_option() {
        $seopress_titles_noimageindex_option = get_option("seopress_titles_option_name");
        if ( ! empty ( $seopress_titles_noimageindex_option ) ) {
            foreach ($seopress_titles_noimageindex_option as $key => $seopress_titles_noimageindex_value)
                $options[$key] = $seopress_titles_noimageindex_value;
             if (isset($seopress_titles_noimageindex_option['seopress_titles_noimageindex'])) { 
                return $seopress_titles_noimageindex_option['seopress_titles_noimageindex'];
             }
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox in Custom Taxonomy
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_display_seo_term_metaboxe() {
    
    add_action('init','seopress_init_term_metabox');

    function seopress_init_term_metabox() {
        if (function_exists('seopress_get_taxonomies')) {
            
            $seopress_get_taxonomies = seopress_get_taxonomies();
            $seopress_get_taxonomies = apply_filters('seopress_metaboxe_term_seo', $seopress_get_taxonomies);
            
            if (!empty($seopress_get_taxonomies)) {
                foreach ($seopress_get_taxonomies as $key => $value) {
                    add_action( $key.'_edit_form_fields', 'seopress_tax', 10, 2 ); //Edit term page
                    add_action( 'edit_'.$key,   'seopress_tax_save_term', 10, 2 ); //Edit save term
                }
            }
        }
    }

    function seopress_tax($term) {
        global $typenow;
        
        //init 
        $disabled = array();

        wp_enqueue_script( 'seopress-cpt-tabs-js', plugins_url( 'assets/js/seopress-tabs2.js', dirname(dirname(__FILE__ ))), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION);

        if ("seopress_404" != $typenow) {
            //Register Google Snippet Preview / Content Analysis JS
            wp_enqueue_script( 'seopress-cpt-counters-js', plugins_url( 'assets/js/seopress-counters.js', dirname(dirname( __FILE__ ))), array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-accordion' ), SEOPRESS_VERSION );

            $seopress_real_preview = array(
                'seopress_nonce' => wp_create_nonce('seopress_real_preview_nonce'),
                'seopress_real_preview' => admin_url('admin-ajax.php'),
            );
            wp_localize_script( 'seopress-cpt-counters-js', 'seopressAjaxRealPreview', $seopress_real_preview );

            wp_enqueue_script( 'seopress-media-uploader-js', plugins_url('assets/js/seopress-media-uploader.js', dirname(dirname( __FILE__ ))), array('jquery'), SEOPRESS_VERSION, false );
            wp_enqueue_media();
        }

        $seopress_titles_title             = get_term_meta($term->term_id,'_seopress_titles_title', true);
        $seopress_titles_desc              = get_term_meta($term->term_id,'_seopress_titles_desc', true);

        $disabled['robots_index'] ='';
        if (seopress_titles_single_term_noindex_option() || seopress_titles_noindex_option()) {
            $seopress_robots_index              = 'yes';
            $disabled['robots_index']           = 'disabled';
        } else {
            $seopress_robots_index              = get_term_meta($term->term_id,'_seopress_robots_index',true);

        }

        $disabled['robots_follow'] ='';
        if (seopress_titles_single_term_nofollow_option() || seopress_titles_nofollow_option()) {
            $seopress_robots_follow             = 'yes';
            $disabled['robots_follow']          = 'disabled';
        } else {
            $seopress_robots_follow             = get_term_meta($term->term_id,'_seopress_robots_follow',true);
        }
        
        $disabled['robots_odp'] ='';
        if (seopress_titles_noodp_option()) {
            $seopress_robots_odp                = 'yes';
            $disabled['robots_odp']             = 'disabled';
        } else {
            $seopress_robots_odp                = get_term_meta($term->term_id,'_seopress_robots_odp',true);

        }        

        $disabled['archive'] ='';
        if (seopress_titles_noarchive_option()) {
            $seopress_robots_archive            = 'yes';
            $disabled['archive']                = 'disabled';
        } else {
            $seopress_robots_archive            = get_term_meta($term->term_id,'_seopress_robots_archive',true);
        }        

        $disabled['snippet'] ='';
        if (seopress_titles_nosnippet_option()) {
            $seopress_robots_snippet            = 'yes';
            $disabled['snippet']                = 'disabled';
        } else {
            $seopress_robots_snippet            = get_term_meta($term->term_id,'_seopress_robots_snippet',true);
        }

        $disabled['imageindex'] ='';
        if (seopress_titles_noimageindex_option()) {
            $seopress_robots_imageindex         = 'yes';
            $disabled['imageindex']             = 'disabled';
        } else {
            $seopress_robots_imageindex         = get_term_meta($term->term_id,'_seopress_robots_imageindex',true);
        }

        $seopress_robots_canonical         = get_term_meta($term->term_id,'_seopress_robots_canonical',true);
        $seopress_social_fb_title          = get_term_meta($term->term_id,'_seopress_social_fb_title',true);
        $seopress_social_fb_desc           = get_term_meta($term->term_id,'_seopress_social_fb_desc',true);
        $seopress_social_fb_img            = get_term_meta($term->term_id,'_seopress_social_fb_img',true);    
        $seopress_social_twitter_title     = get_term_meta($term->term_id,'_seopress_social_twitter_title',true);
        $seopress_social_twitter_desc      = get_term_meta($term->term_id,'_seopress_social_twitter_desc',true);
        $seopress_social_twitter_img       = get_term_meta($term->term_id,'_seopress_social_twitter_img',true);
        $seopress_redirections_enabled     = get_term_meta($term->term_id,'_seopress_redirections_enabled',true);
        $seopress_redirections_type        = get_term_meta($term->term_id,'_seopress_redirections_type',true);
        $seopress_redirections_value       = get_term_meta($term->term_id,'_seopress_redirections_value',true);

        require_once ( dirname( __FILE__ ) . '/admin-metaboxes-form.php'); //Metaboxe HTML
    }

    function seopress_tax_save_term( $term_id ) {
        if(isset($_POST['seopress_titles_title'])){
            update_term_meta($term_id, '_seopress_titles_title', esc_html($_POST['seopress_titles_title']));
        }
        if(isset($_POST['seopress_titles_desc'])){
            update_term_meta($term_id, '_seopress_titles_desc', esc_html($_POST['seopress_titles_desc']));
        }
        if( isset( $_POST[ 'seopress_robots_index' ] ) ) {
            update_term_meta( $term_id, '_seopress_robots_index', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_robots_index', '' );
        }
        if( isset( $_POST[ 'seopress_robots_follow' ] ) ) {
            update_term_meta( $term_id, '_seopress_robots_follow', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_robots_follow', '' );
        }
        if( isset( $_POST[ 'seopress_robots_odp' ] ) ) {
            update_term_meta( $term_id, '_seopress_robots_odp', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_robots_odp', '' );
        }
        if( isset( $_POST[ 'seopress_robots_imageindex' ] ) ) {
            update_term_meta( $term_id, '_seopress_robots_imageindex', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_robots_imageindex', '' );
        }
        if( isset( $_POST[ 'seopress_robots_archive' ] ) ) {
            update_term_meta( $term_id, '_seopress_robots_archive', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_robots_archive', '' );
        }
        if( isset( $_POST[ 'seopress_robots_snippet' ] ) ) {
            update_term_meta( $term_id, '_seopress_robots_snippet', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_robots_snippet', '' );
        }
        if(isset($_POST['seopress_robots_canonical'])){
            update_term_meta($term_id, '_seopress_robots_canonical', esc_html($_POST['seopress_robots_canonical']));
        }
        if(isset($_POST['seopress_social_fb_title'])){
            update_term_meta($term_id, '_seopress_social_fb_title', esc_html($_POST['seopress_social_fb_title']));
        }
        if(isset($_POST['seopress_social_fb_desc'])){
            update_term_meta($term_id, '_seopress_social_fb_desc', esc_html($_POST['seopress_social_fb_desc']));
        }
        if(isset($_POST['seopress_social_fb_img'])){
            update_term_meta($term_id, '_seopress_social_fb_img', esc_html($_POST['seopress_social_fb_img']));
        }
        if(isset($_POST['seopress_social_twitter_title'])){
            update_term_meta($term_id, '_seopress_social_twitter_title', esc_html($_POST['seopress_social_twitter_title']));
        }
        if(isset($_POST['seopress_social_twitter_desc'])){
            update_term_meta($term_id, '_seopress_social_twitter_desc', esc_html($_POST['seopress_social_twitter_desc']));
        }
        if(isset($_POST['seopress_social_twitter_img'])){
            update_term_meta($term_id, '_seopress_social_twitter_img', esc_html($_POST['seopress_social_twitter_img']));
        }         
        if(isset($_POST['seopress_redirections_type'])){
            update_term_meta($term_id, '_seopress_redirections_type', $_POST['seopress_redirections_type']);
        }     
        if(isset($_POST['seopress_redirections_value'])){
            update_term_meta($term_id, '_seopress_redirections_value', esc_html($_POST['seopress_redirections_value']));
        }
        if( isset( $_POST[ 'seopress_redirections_enabled' ] ) ) {
            update_term_meta( $term_id, '_seopress_redirections_enabled', 'yes' );
        } else {
            delete_term_meta( $term_id, '_seopress_redirections_enabled', '' );
        }
    }
}

if (is_user_logged_in()) {
    if(is_super_admin()) {
        echo seopress_display_seo_term_metaboxe();
    } else {
        global $wp_roles;
            
        //Get current user role
        if(isset(wp_get_current_user()->roles[0])) {
            $seopress_user_role = wp_get_current_user()->roles[0];

            //If current user role matchs values from Security settings then apply
            if (function_exists('seopress_advanced_security_metaboxe_role_hook_option') && seopress_advanced_security_metaboxe_role_hook_option() !='') {
                if( array_key_exists( $seopress_user_role, seopress_advanced_security_metaboxe_role_hook_option())) {
                    //do nothing
                } else {
                    echo seopress_display_seo_term_metaboxe();
                }
            } else {
                echo seopress_display_seo_term_metaboxe();
            }
        }
    }
}
