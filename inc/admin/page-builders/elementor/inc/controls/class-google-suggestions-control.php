<?php

namespace WPSeoPressElementorAddon\Controls;

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

class Google_Suggestions_Control extends \Elementor\Base_Control {
    public function get_type() {
        return 'seopress-google-suggestions';
    }

    public function enqueue() {
        wp_enqueue_style(
            'sp-el-google-suggestions-style',
            SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/css/google-suggestions.css',
            [],
            SEOPRESS_VERSION
        );

        wp_enqueue_script(
            'sp-el-google-suggestions-script',
            SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/js/google-suggestions.js',
            ['jquery'],
            SEOPRESS_VERSION,
            true
        );

        if ('' != get_locale()) {
            $locale = function_exists('locale_get_primary_language') ? locale_get_primary_language(get_locale()) : get_locale();
            $country_code = function_exists('locale_get_region') ? locale_get_region(get_locale()) : get_locale();
        } else {
            $locale       = 'en';
            $country_code = 'US';
        }

        wp_localize_script(
            'sp-el-google-suggestions-script',
            'googleSuggestions',
            [
                'locale'      => $locale,
                'countryCode' => $country_code,
            ]
        );
    }

    protected function get_default_settings() {
        global $post;

        return [
            'label'       => esc_html__('Google suggestions', 'wp-seopress'),
            'placeholder' => esc_html__('Get suggestions from Google', 'wp-seopress'),
            'buttonLabel' => esc_html__('Get suggestions!', 'wp-seopress'),
        ];
    }

    public function content_template() {
        ?>
<div class="elementor-control-field seopress-google-suggestions">
    <label for="seopress_google_suggest_kw_meta">
        <div>{{{ data.label }}}</div>
        <p class="elementor-control-field-description">{{{data.description}}}</p>
        <input id="seopress_google_suggest_kw_meta" type="text" placeholder="{{ data.placeholder }}"
            aria-label="Google suggestions">
    </label>
    <button id="seopress_get_suggestions" type="button"
        class="btn btnSecondary elementor-button elementor-button-default">{{{ data.buttonLabel }}}</button>
    <ul id='seopress_suggestions'></ul>
</div>
<?php
    }
}
