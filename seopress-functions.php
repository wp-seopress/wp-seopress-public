<?php

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Kernel;

/**
 * Get a service.
 *
 * @since 4.3.0
 *
 * @param string $service
 *
 * @return object
 */
function seopress_get_service($service) {
    return Kernel::getContainer()->getServiceByName($service);
}

/*
 * Get first key of an array if PHP < 7.3
 * @since 4.2.1
 * @return string
 * @author Benjamin
 */
if ( ! function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach ($arr as $key => $unused) {
            return $key;
        }

        return null;
    }
}

/*
 * Get last key of an array if PHP < 7.3
 * @since 4.2.1
 * @return string
 * @author Benjamin
 */
if ( ! function_exists('array_key_last')) {
    function array_key_last(array $arr) {
        end($arr);
        $key = key($arr);

        return $key;
    }
}

/**
 * Get all registered post types.
 *
 * @author Benjamin Denis
 *
 * @deprecated 4.4.0
 *
 * @return (array) $wp_post_types
 */
function seopress_get_post_types() {
    return seopress_get_service('WordPressData')->getPostTypes();
}

/**
 * Get all registered custom taxonomies.
 *
 * @author Benjamin Denis
 *
 * @param bool $with_terms
 *
 * @return array $taxonomies
 **/
function seopress_get_taxonomies($with_terms = false) {
    $args = [
        'show_ui' => true,
        'public'  => true,
    ];
    $args = apply_filters('seopress_get_taxonomies_args', $args);

    $output     = 'objects'; // or objects
    $operator   = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies($args, $output, $operator);

    unset(
        $taxonomies['seopress_bl_competitors']
    );

    $taxonomies = apply_filters('seopress_get_taxonomies_list', $taxonomies);

    if ( ! $with_terms) {
        return $taxonomies;
    }

    foreach ($taxonomies as $_tax_slug => &$_tax) {
        $_tax->terms = get_terms(['taxonomy' => $_tax_slug]);
    }

    return $taxonomies;
}

/**
 * Get all custom fields (limit: 250).
 *
 * @author Benjamin Denis
 *
 * @return array custom field keys
 */
function seopress_get_custom_fields() {
    $cf_keys = wp_cache_get('seopress_get_custom_fields');

    if (false === $cf_keys) {
        global $wpdb;

        $limit   = (int) apply_filters('postmeta_form_limit', 250);
        $cf_keys = $wpdb->get_col($wpdb->prepare("
			SELECT DISTINCT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			HAVING meta_key NOT LIKE '\_%%'
			ORDER BY meta_key
			LIMIT %d", $limit));

        if (is_plugin_active('types/wpcf.php')) {
            $wpcf_fields = get_option('wpcf-fields');

            if ( ! empty($wpcf_fields)) {
                foreach ($wpcf_fields as $key => $value) {
                    $cf_keys[] = $value['meta_key'];
                }
            }
        }

        $cf_keys = apply_filters('seopress_get_custom_fields', $cf_keys);

        if ($cf_keys) {
            natcasesort($cf_keys);
        }
        wp_cache_set('seopress_get_custom_fields', $cf_keys);
    }

    return $cf_keys;
}

/**
 * Check SSL for schema.org.
 *
 * @author Benjamin Denis
 *
 * @return string correct protocol
 */
function seopress_check_ssl() {
    if (is_ssl()) {
        return 'https://';
    } else {
        return 'http://';
    }
}

/**
 * Get IP address.
 *
 * @author Benjamin Denis
 *
 * @return (string) $ip
 **/
function seopress_get_ip_address() {
    foreach (['HTTP_CLIENT_IP', 'HTTP_CF_CONNECTING_IP', 'HTTP_VIA', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
        if (true === array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip); // just to be safe

                return apply_filters('seopress_404_ip', $ip ? $ip : '');
            }
        }
    }
}

/**
 * Disable Query Monitor for CA.
 *
 * @return array
 *
 * @author Benjamin
 *
 * @param mixed $url
 * @param mixed $allcaps
 * @param mixed $caps
 * @param mixed $args
 */
function seopress_disable_qm($allcaps, $caps, $args) {
    $allcaps['view_query_monitor'] = false;

    return $allcaps;
}
/**
 * Clear content for CA.
 *
 * @author Benjamin
 */
function seopress_clean_content_analysis() {
    if (current_user_can('edit_posts')) {
        if (isset($_GET['no_admin_bar']) && '1' === $_GET['no_admin_bar']) {
            //Remove admin bar
            add_filter('show_admin_bar', '__return_false');

            //Disable Query Monitor
            add_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

            //Disable wptexturize
            add_filter('run_wptexturize', '__return_false');

            //Oxygen compatibility
            if (function_exists('ct_template_output')) { //disable for Oxygen
                add_action('template_redirect', 'seopress_get_oxygen_content');
            }
        }
    }
}
add_action('plugins_loaded', 'seopress_clean_content_analysis');

/**
 * Test if a URL is in absolute.
 *
 * @return bool true if absolute
 *
 * @author Benjamin
 *
 * @param mixed $url
 */
function seopress_is_absolute($url) {
    $pattern = "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu";

    return (bool) preg_match($pattern, $url);
}

/**
 * Manage localized links.
 *
 * @return string locale for documentation links
 *
 * @author Benjamin
 */
function seopress_get_locale() {
    switch (get_user_locale(get_current_user_id())) {
        case 'fr_FR':
        case 'fr_BE':
        case 'fr_CA':
        case 'fr_LU':
        case 'fr_MC':
        case 'fr_CH':
            $locale_link = 'fr';
        break;
        default:
            $locale_link = '';
        break;
    }

    return $locale_link;
}

/**
 * Check empty global title template.
 *
 * @since 5.0
 *
 * @param string $type
 * @param string $metadata
 * @param bool   $notice
 *
 * @return string notice with list of empty cpt titles
 *
 * @author Benjamin
 */
function seopress_get_empty_templates($type, $metadata, $notice = true) {
    $cpt_titles_empty = [];
    $templates        = '';
    $data             = '';
    $html             = '';
    $list             = '';

    if ('cpt' === $type) {
        $templates   = $postTypes = seopress_get_service('WordPressData')->getPostTypes();
        $notice_i18n = __('Custom Post Types', 'wp-seopress');
    }
    if ('tax' === $type) {
        $templates   = seopress_get_taxonomies();
        $notice_i18n = __('Custom Taxonomies', 'wp-seopress');
    }
    foreach ($templates as $key => $value) {
        $options            = get_option('seopress_titles_option_name');

        if (!empty($options)) {
            if ('cpt' === $type) {
                if (!array_key_exists($key, $options['seopress_titles_single_titles'])) {
                    $cpt_titles_empty[] = $key;
                } else {
                    $data = $options['seopress_titles_single_titles'][$key][$metadata];
                }
            }
            if ('tax' === $type) {
                if (!array_key_exists($key, $options['seopress_titles_tax_titles'])) {
                    $cpt_titles_empty[] = $key;
                } else {
                    $data = $options['seopress_titles_tax_titles'][$key][$metadata];
                }
            }
        }

        if (empty($data)) {
            $cpt_titles_empty[] = $key;
        }
    }

    if ( ! empty($cpt_titles_empty)) {
        $list .= '<ul>';
        foreach ($cpt_titles_empty as $cpt) {
            $list .= '<li>' . $cpt . '</li>';
        }
        $list .= '</ul>';

        if (false === $notice) {
            return $list;
        } else {
            $html .= '<div class="seopress-notice is-warning">
    <p>';
            /* translators: %s: "Custom Post Types" or "Custom Taxonomies" %s: "title" or "description" */
            $html .= sprintf(__('Some <strong>%s</strong> have no <strong>meta %s</strong> set! We strongly encourage you to add one by filling in the fields below.', 'wp-seopress'), $notice_i18n, $metadata);
            $html .= '</p>';
            $html .= $list;
            $html .= '</div>';

            return $html;
        }
    }
}

/**
 * Generate Tooltip.
 *
 * @since 3.8.2
 *
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_desc
 * @param mixed  $tooltip_code
 *
 * @return string tooltip title, tooltip description, tooltip url
 *
 * @author Benjamin
 */
function seopress_tooltip($tooltip_title, $tooltip_desc, $tooltip_code) {
    $html =
    '<button type="button" class="sp-tooltip"><span class="dashicons dashicons-editor-help"></span>
	<span class="sp-tooltiptext" role="tooltip" tabindex="0">
		<span class="sp-tooltip-headings">' . $tooltip_title . '</span>
		<span class="sp-tooltip-desc">' . $tooltip_desc . '</span>
		<span class="sp-tooltip-code">' . $tooltip_code . '</span>
	</span></button>';

    return $html;
}

/**
 * Generate Tooltip (alternative version).
 *
 * @since 3.8.6
 *
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_anchor
 * @param mixed  $tooltip_desc
 *
 * @return string tooltip title, tooltip description, tooltip url
 *
 * @author Benjamin
 */
function seopress_tooltip_alt($tooltip_anchor, $tooltip_desc) {
    $html =
    '<button type="button" class="sp-tooltip alt">' . $tooltip_anchor . '
	<span class="sp-tooltiptext" role="tooltip" tabindex="0">
		<span class="sp-tooltip-desc">' . $tooltip_desc . '</span>
	</span>
	</button>';

    return $html;
}

/**
 * Generate Tooltip link.
 *
 * @since 5.0
 *
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_anchor
 * @param mixed  $tooltip_desc
 *
 * @return string tooltip title, tooltip description, tooltip url
 *
 * @author Benjamin
 */
function seopress_tooltip_link($tooltip_anchor, $tooltip_desc) {
    $html = '<a href="' . $tooltip_anchor . '"
    target="_blank" class="seopress-doc">
    <span class="dashicons dashicons-editor-help"></span>
    <span class="screen-reader-text">
        ' . $tooltip_desc . '
    </span>
</a>';

    return $html;
}

/**
 * Remove BOM.
 *
 * @since 3.8.2
 *
 * @param mixed $text
 *
 * @return mixed $text
 *
 * @author Benjamin
 */
function seopress_remove_utf8_bom($text) {
    $bom  = pack('H*', 'EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);

    return $text;
}

/**
 * Generate notification (Notifications Center).
 *
 * @since 3.8.2
 *
 * @param array $args
 *
 * @return string HTML notification
 *
 * @author Benjamin
 */
function seopress_notification($args) {
    if ( ! empty($args)) {
        $id             = isset($args['id']) ? $args['id'] : null;
        $title          = isset($args['title']) ? $args['title'] : null;
        $desc           = isset($args['desc']) ? $args['desc'] : null;
        $impact         = isset($args['impact']) ? $args['impact'] : [];
        $link           = isset($args['link']) ? $args['link'] : null;
        $deleteable     = isset($args['deleteable']) ? $args['deleteable'] : null;
        $icon           = isset($args['icon']) ? $args['icon'] : null;
        $wrap           = isset($args['wrap']) ? $args['wrap'] : null;

        $class = '';
        if ( ! empty($impact)) {
            $class .= ' impact';
            $class .= ' ' . key($impact);
        }

        if (true === $deleteable) {
            $class .= ' deleteable';
        }

        $html = '<div id="' . $id . '-alert" class="seopress-alert seopress-card">';

        if ( ! empty($impact)) {
            $html .= '<span class="screen-reader-text">' . reset($impact) . '</span>';
        }

        if ( ! empty($icon)) {
            $html .= '<span class="dashicons ' . $icon . '"></span>';
        } else {
            $html .= '<span class="dashicons dashicons-info"></span>';
        }

        $html .= '<h3>' . $title . '</h3>';

        if (false === $wrap) {
            $html .= $desc;
        } else {
            $html .= '<p>' . $desc . '</p>';
        }

        $href = '';
        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale() && isset($link['fr'])) {
            $href = ' href="' . $link['fr'] . '"';
        } elseif (isset($link['en'])) {
            $href = ' href="' . $link['en'] . '"';
        }

        $target = '';
        if (isset($link['external']) && true === $link['external']) {
            $target = ' target="_blank"';
        }

        if ( ! empty($link) || true === $deleteable) {
            $html .= '<p class="seopress-card-actions">';

            if ( ! empty($link)) {
                $html .= '<a class="btn btnSecondary"' . $href . $target . '>' . $link['title'] . '</a>';
            }
            if (true === $deleteable) {
                $html .= '<button id="' . $id . '" name="notice-title-tag" type="button" class="btn btnTertiary" data-notice="' . $id . '">' . __('Dismiss', 'wp-seopress') . '</button>';
            }

            $html .= '</p>';
        }
        $html .= '</div>';
        echo $html;
    }
}
/**
 * Filter the capability to allow other roles to use the plugin.
 *
 * @since 3.8.2
 *
 * @author Julio Potier
 *
 * @return string
 *
 * @param mixed $cap
 * @param mixed $context
 */
function seopress_capability($cap, $context = '') {
    $newcap = apply_filters('seopress_capability', $cap, $context);
    if ( ! current_user_can($newcap)) {
        return $cap;
    }

    return $newcap;
}

/**
 * Check if the page is one of ours.
 *
 * @since 3.8.2
 *
 * @author Julio Potier
 *
 * @return bool
 */
function is_seopress_page() {
    if ( ! is_admin() && ( ! isset($_REQUEST['page']) || ! isset($_REQUEST['post_type']))) {
        return false;
    }

    if (isset($_REQUEST['page'])) {
        return 0 === strpos($_REQUEST['page'], 'seopress');
    } elseif (isset($_REQUEST['post_type'])) {
        return 0 === strpos($_REQUEST['post_type'], 'seopress');
    }
}

/**
 * Only add our notices on our pages.
 *
 * @since 3.8.2
 *
 * @author Julio Potier
 *
 * @return bool
 */
function seopress_remove_other_notices() {
    if (is_seopress_page()) {
        add_filter('admin_footer_text', '__return_false');
        remove_all_actions('network_admin_notices');
        remove_all_actions('admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('all_admin_notices');
        add_action('admin_notices', 'seopress_admin_notices');
        if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
            add_action('admin_notices', 'seopress_insights_notice');
        }
    }
}
add_action('in_admin_header', 'seopress_remove_other_notices');

/**
 * We replace the WP action by ours.
 *
 * @since 3.8.2
 *
 * @author Julio Potier
 *
 * @return bool
 */
function seopress_admin_notices() {
    do_action('seopress_admin_notices');
}

/**
 * Return the 7 days in correct order.
 *
 * @since 3.8.2
 *
 * @author Julio Potier
 *
 * @return bool
 */
function seopress_get_days() {
    $start_of_week = (int) get_option('start_of_week');

    return array_map(
        function () use ($start_of_week) {
            static $start_of_week;

            return ucfirst(date_i18n('l', strtotime($start_of_week++ - date('w', 0) . ' day', 0)));
        },
        array_combine(
            array_merge(
                array_slice(range(0, 6), $start_of_week, 7),
                array_slice(range(0, 6), 0, $start_of_week)
            ),
            range(0, 6)
        )
    );
}

/**
 * Check if a key exists in a multidimensional array.
 *
 * @since 3.8.2
 *
 * @author Benjamin Denis
 *
 * @return bool
 *
 * @param mixed $key
 */
function seopress_if_key_exists(array $arr, $key) {
    // is in base array?
    if (array_key_exists($key, $arr)) {
        return true;
    }

    // check arrays contained in this array
    foreach ($arr as $element) {
        if (is_array($element)) {
            if (seopress_if_key_exists($element, $key)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Get Oxygen Content.
 *
 * @since 3.8.5
 *
 * @author Benjamin Denis
 *
 * @return null
 */
function seopress_get_oxygen_content() {
    if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output')) {
        $seopress_get_the_content = ct_template_output();

        if ( ! $seopress_get_the_content) {
            //Get post content
            $seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));
        }

        $seopress_get_the_content = normalize_whitespace(wp_strip_all_tags($seopress_get_the_content));

        if ($seopress_get_the_content) {
            //Get Target Keywords
            if (get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true)) {
                $seopress_analysis_target_kw = array_filter(explode(',', strtolower(esc_attr(get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true)))));

                $seopress_analysis_target_kw = apply_filters( 'seopress_content_analysis_target_keywords', $seopress_analysis_target_kw, get_the_ID() );

                //Keywords density
                foreach ($seopress_analysis_target_kw as $kw) {
                    if (preg_match_all('#\b(' . $kw . ')\b#iu', $seopress_get_the_content, $m)) {
                        $data['kws_density']['matches'][$kw][] = $m[0];
                    }
                }
            }

            //Words Counter
            $data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", $seopress_get_the_content, $matches);

            if ( ! empty($matches[0])) {
                $words_counter_unique = count(array_unique($matches[0]));
            } else {
                $words_counter_unique = '0';
            }
            $data['words_counter_unique'] = $words_counter_unique;

            //Update analysis
            update_post_meta(get_the_ID(), '_seopress_analysis_data_oxygen', $data);
        }
    }
}

/**
 * Output submit button.
 *
 * @since 5.0
 *
 * @author Benjamin Denis
 *
 * @param mixed $value
 * @param mixed $classes
 * @param mixed $type
 */
function sp_submit_button($value ='', $classes = 'btn btnPrimary', $type = 'submit') {
    if ('' === $value) {
        $value = __('Save changes', 'wp-seopress');
    }

    $html = '<p class="submit"><input id="submit" name="submit" type="' . $type . '" class="' . $classes . '" value="' . $value . '"/></p>';

    echo $html;
}

/**
 * Generate HTML buttons classes
 *
 * @since 5.0
 *
 * @author Benjamin Denis
 * @return
 */
function seopress_btn_secondary_classes() {
    //Classic Editor compatibility
    global $pagenow;
    if (function_exists('get_current_screen') && method_exists(get_current_screen(), 'is_block_editor') && true === get_current_screen()->is_block_editor()) {
        $btn_classes_secondary = 'components-button is-secondary';
    } elseif (isset($pagenow) && ($pagenow === 'term.php' || $pagenow === 'post.php') ) {
        $btn_classes_secondary = 'button button-secondary';
    } else {
        $btn_classes_secondary = 'btn btnSecondary';
    }

    return $btn_classes_secondary;
}

/*
 * Global noindex from SEO, Titles settings
 * @since 4.0
 * @param string $feature
 * @return string 1 if true
 * @author Benjamin
 */
if ( ! function_exists('seopress_global_noindex_option')) {
    function seopress_global_noindex_option() {
        $seopress_titles_noindex_option = get_option('seopress_titles_option_name');
        if ( ! empty($seopress_titles_noindex_option)) {
            foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value) {
                $options[$key] = $seopress_titles_noindex_value;
            }
            if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) {
                return $seopress_titles_noindex_option['seopress_titles_noindex'];
            }
        }
    }
}

/*
 * Disable Add to cart GA tracking code on archive page / related products for Elementor PRO to avoid a JS conflict
 * @since 5.3
 * @return empty string
 * @author Benjamin
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (is_plugin_active('elementor-pro/elementor-pro.php')) {
    add_filter('seopress_gtag_ec_add_to_cart_archive_ev', 'sp_elementor_gtag_ec_add_to_cart_archive_ev');
    function sp_elementor_gtag_ec_add_to_cart_archive_ev($js) {
        return '';
    }
}
