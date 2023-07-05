<?php

namespace SEOPress\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class ModuleMetabox implements ExecuteHooks
{
    /**
     * @since 5.0.0
     *
     * @return void
     */
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('init', [$this, 'enqueue']);

        if (current_user_can(seopress_capability('edit_posts'))) {
            add_action('wp_enqueue_scripts', [$this, 'enqueueFrontend']);
        }
    }

    /**
     * @since 5.0.0
     *
     * @return void
     *
     * @param mixed $argsLocalize
     */
    protected function enqueueModule($argsLocalize = [])
    {
        if (! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
            return;
        }

        //AMP compatibility
        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            return;
        }

        //Bricks builder compatibility
        if (function_exists('bricks_is_builder_call') && bricks_is_builder_call() === true) {
            return;
        }

        $isGutenberg = false;
        if(function_exists('get_current_screen')){
            $currentScreen = get_current_screen();
            if($currentScreen && method_exists($currentScreen,'is_block_editor')){
                $isGutenberg = true === get_current_screen()->is_block_editor();
            }
        }

        $dependencies = ['jquery-ui-datepicker'];
        if ($isGutenberg) {
            $dependencies = array_merge($dependencies, ['wp-components', 'wp-edit-post', 'wp-plugins']);
        }

        wp_enqueue_media();
        wp_enqueue_script('seopress-metabox', SEOPRESS_URL_PUBLIC . '/metaboxe.js', $dependencies, uniqid(), true);
        $value = wp_create_nonce('seopress_rest');

        $tags = seopress_get_service('TagsToString')->getTagsAvailable([
            'without_classes' => [
                '\SEOPress\Tags\PostThumbnailUrlHeight',
                '\SEOPress\Tags\PostThumbnailUrlWidth',

            ],
            'without_classes_pos' => ['\SEOPress\Tags\Schema', '\SEOPressPro\Tags\Schema']
        ]);


        $getLocale = get_locale();
        if (!empty($getLocale)) {
            $locale       = substr($getLocale, 0, 2);
            $country_code = substr($getLocale, -2);
        } else {
            $locale       = 'en';
            $country_code = 'US';
        }

        $settingsAdvanced = seopress_get_service('AdvancedOption');
        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;

        $postId = is_singular() ? get_the_ID() : null;
        $postType = null;
        if($postId){
            $postType = get_post_type($postId);
        }

        $args = array_merge([
            'SEOPRESS_URL_PUBLIC'       => SEOPRESS_URL_PUBLIC,
            'SEOPRESS_URL_ASSETS'     => SEOPRESS_URL_ASSETS,
            'SITENAME'                => get_bloginfo('name'),
            'SITEURL'                 => site_url(),
            'ADMIN_URL_TITLES'        => admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_single'),
            'TAGS'                    => array_values($tags),
            'REST_URL'                => rest_url(),
            'NONCE'                   => wp_create_nonce('wp_rest'),
            'POST_ID'                 => $postId,
            'POST_TYPE'               => $postType,
            'IS_GUTENBERG'            => apply_filters('seopress_module_metabox_is_gutenberg', $isGutenberg),
            'SELECTOR_GUTENBERG'      => apply_filters('seopress_module_metabox_selector_gutenberg', '.edit-post-header .edit-post-header-toolbar__left'),
            'TOGGLE_MOBILE_PREVIEW' => apply_filters('seopress_toggle_mobile_preview', 1),
            'GOOGLE_SUGGEST' => [
                'ACTIVE'        => apply_filters('seopress_ui_metabox_google_suggest', false),
                'LOCALE'       => $locale,
                'COUNTRY_CODE' => $country_code,
            ],
            'USER_ROLES' => array_values($roles),
            'ROLES_BLOCKED' => [
                'GLOBAL' => $settingsAdvanced->getSecurityMetaboxRole(),
                'CONTENT_ANALYSIS' => $settingsAdvanced->getSecurityMetaboxRoleContentAnalysis()
            ],
            'OPTIONS' => [
                "AI" => seopress_get_service('ToggleOption')->getToggleAi() === "1" ? true : false,
            ],
            'TABS' => [
                'SCHEMAS' => apply_filters('seopress_active_schemas_manual_universal_metabox', false)
            ],
            'SUB_TABS' => [
                'GOOGLE_NEWS' => apply_filters('seopress_active_google_news', false),
                'VIDEO_SITEMAP' => apply_filters('seopress_active_video_sitemap', false),
                'INSPECT_URL' => apply_filters('seopress_active_inspect_url', false),
                'INTERNAL_LINKING' => apply_filters('seopress_active_internal_linking', false),
                'SCHEMA_MANUAL' =>  apply_filters('seopress_active_schemas', false)
            ],
            'FAVICON' => get_site_icon_url(32),
            'BEACON_SVG' => apply_filters('seopress_beacon_svg', SEOPRESS_URL_ASSETS.'/img/beacon.svg'),
        ], $argsLocalize);

        wp_localize_script('seopress-metabox', 'SEOPRESS_DATA', $args);
        wp_localize_script('seopress-metabox', 'SEOPRESS_I18N', seopress_get_service('I18nUniversalMetabox')->getTranslations());
    }

    /**
     * @since 5.0.0
     *
     * @return void
     */
    public function enqueueFrontend()
    {
        $this->enqueueModule(['POST_ID' => get_the_ID()]);
    }

    /**
     * @since 5.0.0
     *
     * @param string $page
     *
     * @return void
     */
    public function enqueue($page)
    {
        if (! in_array($page, ['post.php'], true)) {
            return;
        }
        $this->enqueueModule();
    }

    /**
     * @since 5.0.0
     *
     * @return void
     */
    public function enqueueElementor()
    {
        $this->enqueueModule();
    }
}
