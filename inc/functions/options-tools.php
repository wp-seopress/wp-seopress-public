<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Oxygen Builder
function seopress_setting_section_tools_compatibility_oxygen_option() {
    $seopress_setting_section_tools_compatibility_oxygen_option = get_option('seopress_tools_option_name');
    if ( ! empty($seopress_setting_section_tools_compatibility_oxygen_option)) {
        foreach ($seopress_setting_section_tools_compatibility_oxygen_option as $key => $seopress_setting_section_tools_compatibility_oxygen_value) {
            $options[$key] = $seopress_setting_section_tools_compatibility_oxygen_value;
        }
        if (isset($seopress_setting_section_tools_compatibility_oxygen_option['seopress_setting_section_tools_compatibility_oxygen'])) {
            return $seopress_setting_section_tools_compatibility_oxygen_option['seopress_setting_section_tools_compatibility_oxygen'];
        }
    }
}

if ('1' === seopress_setting_section_tools_compatibility_oxygen_option()) {
    //Oxygen Builder - generate automatic meta description
    function sp_oxygen_titles_template_variables_array($array) {
        $array[] = '%%oxygen%%';

        return $array;
    }
    add_filter('seopress_titles_template_variables_array', 'sp_oxygen_titles_template_variables_array');

    function sp_oxygen_titles_template_replace_array($array) {
        if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output') && ! isset($_GET['ct_builder'])) {
            add_filter('wp_doing_ajax', '__return_true');

            $content = do_shortcode(get_post_meta(get_the_id(), 'ct_builder_shortcodes', true));

            remove_filter('wp_doing_ajax', '__return_true');

            $array[] = preg_replace('/\r|\n/',  '',  substr(strip_tags(wp_filter_nohtml_kses($content)), 0, 160));
        }

        return $array;
    }
    add_filter('seopress_titles_template_replace_array', 'sp_oxygen_titles_template_replace_array');
}

//Divi Builder
function seopress_setting_section_tools_compatibility_divi_option() {
    $seopress_setting_section_tools_compatibility_divi_option = get_option('seopress_tools_option_name');
    if ( ! empty($seopress_setting_section_tools_compatibility_divi_option)) {
        foreach ($seopress_setting_section_tools_compatibility_divi_option as $key => $seopress_setting_section_tools_compatibility_divi_value) {
            $options[$key] = $seopress_setting_section_tools_compatibility_divi_value;
        }
        if (isset($seopress_setting_section_tools_compatibility_divi_option['seopress_setting_section_tools_compatibility_divi'])) {
            return $seopress_setting_section_tools_compatibility_divi_option['seopress_setting_section_tools_compatibility_divi'];
        }
    }
}

if ('1' === seopress_setting_section_tools_compatibility_divi_option()) {
    //Divi Builder - generate automatic meta description
    function sp_divi_titles_template_variables_array($array) {
        $array[] = '%%divi%%';

        return $array;
    }
    add_filter('seopress_titles_template_variables_array', 'sp_divi_titles_template_variables_array');

    function sp_divi_titles_template_replace_array($array) {
        $theme = wp_get_theme();
        if ('Divi' == $theme->template || 'Divi' == $theme->parent_theme) {
            $array[] = substr(normalize_whitespace(strip_tags(wp_filter_nohtml_kses(do_shortcode(get_post_field('post_content', get_the_ID()))))), 0, 160);
        }

        return $array;
    }
    add_filter('seopress_titles_template_replace_array', 'sp_divi_titles_template_replace_array');
}

//WP Bakery Builder
function seopress_setting_section_tools_compatibility_bakery_option() {
    $seopress_setting_section_tools_compatibility_bakery_option = get_option('seopress_tools_option_name');
    if ( ! empty($seopress_setting_section_tools_compatibility_bakery_option)) {
        foreach ($seopress_setting_section_tools_compatibility_bakery_option as $key => $seopress_setting_section_tools_compatibility_bakery_value) {
            $options[$key] = $seopress_setting_section_tools_compatibility_bakery_value;
        }
        if (isset($seopress_setting_section_tools_compatibility_bakery_option['seopress_setting_section_tools_compatibility_bakery'])) {
            return $seopress_setting_section_tools_compatibility_bakery_option['seopress_setting_section_tools_compatibility_bakery'];
        }
    }
}

if ('1' === seopress_setting_section_tools_compatibility_bakery_option()) {
    //WP Bakery Builder Generate automatic meta description
    function sp_bakery_titles_template_variables_array($array) {
        $array[] = '%%wpbakery%%';

        return $array;
    }
    add_filter('seopress_titles_template_variables_array', 'sp_bakery_titles_template_variables_array');

    function sp_bakery_titles_template_replace_array($array) {
        if (is_plugin_active('js_composer/js_composer.php')) {
            $array[] = preg_replace("/\r|\n/", '', substr(trim(strip_tags(wp_filter_nohtml_kses(do_shortcode(get_post_field('post_content', get_the_ID()))))), 0, 160));
        }

        return $array;
    }
    add_filter('seopress_titles_template_replace_array', 'sp_bakery_titles_template_replace_array');
}

//WP Avia Builder
function seopress_setting_section_tools_compatibility_avia_option() {
    $seopress_setting_section_tools_compatibility_avia_option = get_option('seopress_tools_option_name');
    if ( ! empty($seopress_setting_section_tools_compatibility_avia_option)) {
        foreach ($seopress_setting_section_tools_compatibility_avia_option as $key => $seopress_setting_section_tools_compatibility_avia_value) {
            $options[$key] = $seopress_setting_section_tools_compatibility_avia_value;
        }
        if (isset($seopress_setting_section_tools_compatibility_avia_option['seopress_setting_section_tools_compatibility_avia'])) {
            return $seopress_setting_section_tools_compatibility_avia_option['seopress_setting_section_tools_compatibility_avia'];
        }
    }
}

if ('1' === seopress_setting_section_tools_compatibility_avia_option()) {
    //WP Avia Layout Builder / Enfold theme - Generate automatic meta description
    add_filter('avf_preprocess_shortcode_in_header', 'sp_avia_avf_preprocess_shortcode_in_header', 10, 6);
    function sp_avia_avf_preprocess_shortcode_in_header($return, $class,  $atts, $content, $shortcodename, $fake) {
        return 'preprocess_shortcodes_in_header';
    }

    function sp_avia_titles_template_variables_array($array) {
        $array[] = '%%aviabuilder%%';

        return $array;
    }
    add_filter('seopress_titles_template_variables_array', 'sp_avia_titles_template_variables_array');

    function sp_avia_titles_template_replace_array($array) {
        $array[] = preg_replace("/\r|\n/", '', substr(trim(strip_tags(wp_filter_nohtml_kses(do_shortcode(get_post_meta(get_the_ID(), '_aviaLayoutBuilderCleanData', true))))), 0, 160));

        return $array;
    }
    add_filter('seopress_titles_template_replace_array', 'sp_avia_titles_template_replace_array');
}

//WP Fusion Builder
function seopress_setting_section_tools_compatibility_fusion_option() {
    $seopress_setting_section_tools_compatibility_fusion_option = get_option('seopress_tools_option_name');
    if ( ! empty($seopress_setting_section_tools_compatibility_fusion_option)) {
        foreach ($seopress_setting_section_tools_compatibility_fusion_option as $key => $seopress_setting_section_tools_compatibility_fusion_value) {
            $options[$key] = $seopress_setting_section_tools_compatibility_fusion_value;
        }
        if (isset($seopress_setting_section_tools_compatibility_fusion_option['seopress_setting_section_tools_compatibility_fusion'])) {
            return $seopress_setting_section_tools_compatibility_fusion_option['seopress_setting_section_tools_compatibility_fusion'];
        }
    }
}

if ('1' === seopress_setting_section_tools_compatibility_fusion_option()) {
    //Fusion Builder + Avadar Generate automatic meta description
    function sp_fusion_titles_template_variables_array($array) {
        $array[] = '%%fusionbuilder%%';

        return $array;
    }
    add_filter('seopress_titles_template_variables_array', 'sp_fusion_titles_template_variables_array');

    function sp_fusion_titles_template_replace_array($array) {
        if (is_plugin_active('fusion-builder/fusion-builder.php')) {
            $array[] = preg_replace("/\r|\n/", '', substr(trim(strip_tags(wp_filter_nohtml_kses(do_shortcode(get_post_field('post_content', get_the_ID()))))), 0, 160));
        }

        return $array;
    }
    add_filter('seopress_titles_template_replace_array', 'sp_fusion_titles_template_replace_array');
}
