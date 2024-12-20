<?php

namespace SEOPress\Actions\Front\Metas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class TitleMeta implements ExecuteHooksFrontend {

    protected $tagsToStringService;

    public function __construct() {
        $this->tagsToStringService = seopress_get_service('TagsToString');
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        add_action('wp_head', [$this, 'preLoad'], 0);
    }

    public function preLoad(){
        if ('1' !== seopress_get_toggle_option('titles')) {
            return;
        }

        if ((function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && \Ecwid_Store_Page::is_store_page())) {//disable on wpForo pages to avoid conflicts
            return;
        }

        $priority = apply_filters( 'seopress_titles_the_title_priority', 10 );
        add_filter('pre_get_document_title', [$this, 'render'], $priority);

        //Avoid TEC rewriting our title tag on Venue and Organizer pages
        if (is_plugin_active('the-events-calendar/the-events-calendar.php')) {
            if (
                function_exists('tribe_is_event') && tribe_is_event() ||
                function_exists('tribe_is_venue') && tribe_is_venue() ||
                function_exists('tribe_is_organizer') && tribe_is_organizer()
                // function_exists('tribe_is_month') && tribe_is_month() && is_tax() ||
                // function_exists('tribe_is_upcoming') && tribe_is_upcoming() && is_tax() ||
                // function_exists('tribe_is_past') && tribe_is_past() && is_tax() ||
                // function_exists('tribe_is_week') && tribe_is_week() && is_tax() ||
                // function_exists('tribe_is_day') && tribe_is_day() && is_tax() ||
                // function_exists('tribe_is_map') && tribe_is_map() && is_tax() ||
                // function_exists('tribe_is_photo') && tribe_is_photo() && is_tax()
            ) {
                add_filter('pre_get_document_title', 'seopress_titles_the_title', 20);
            }
        }

        //Avoid Surecart rewriting our title tag
        if (is_plugin_active('surecart/surecart.php')) {
            if (is_singular( 'sc_product' )) {
                add_filter('pre_get_document_title', 'seopress_titles_the_title', 214748364);
            }
        }
    }


    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function render() {
        $defaultHook = function_exists('seopress_get_service');

        if (apply_filters('seopress_old_pre_get_document_title', true)) {
            return;
        }

        $context = seopress_get_service('ContextPage')->getContext();

        $title = seopress_get_service('TitleMeta')->getValue($context);

        return $title;
    }
}
