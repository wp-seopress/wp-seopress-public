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

/*
 * Remove WP default meta robots (added in WP 5.7)
 *
 * @since 4.4.0.7
 */
remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

/*
 * Remove WC default meta robots (added in WP 5.7)
 *
 * @since 4.6
 * @todo use wp_robots API
 * @updated 5.8
 */
function seopress_robots_wc_pages($robots) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if (is_plugin_active('woocommerce/woocommerce.php')) {
		if (function_exists('wc_get_page_id')) {
			if (is_page(wc_get_page_id('cart')) || is_page(wc_get_page_id('checkout')) || is_page(wc_get_page_id('myaccount'))) {
				if ('0' === get_option('blog_public')) {
					return $robots;
				} else {
					unset($robots);
					$robots = [];

					return $robots;
				}
			}
		}
	}
	//remove noindex on search archive pages
	if (is_search()) {
		if ('0' === get_option('blog_public')) {
			return $robots;
		} else {
			unset($robots);
			$robots = [];

			return $robots;
		}
	}

	return $robots;
}
add_filter('wp_robots', 'seopress_robots_wc_pages', 20);

/**
 * Remove default WC meta robots.
 *
 * @since 3.8.1
 */
function seopress_compatibility_woocommerce() {
	if (function_exists('is_plugin_active')) {
		if (is_plugin_active('woocommerce/woocommerce.php') && ! is_admin()) {
			remove_action('wp_head', 'wc_page_noindex');
		}
	}
}
add_action('wp_head', 'seopress_compatibility_woocommerce', 0);

/**
 * Remove Jetpack OpenGraph tags.
 *
 * @since 3.5.9
 */
function seopress_compatibility_jetpack() {
	if (function_exists('is_plugin_active')) {
		if (is_plugin_active('jetpack/jetpack.php') && ! is_admin()) {
			add_filter('jetpack_enable_open_graph', '__return_false');
			add_filter('jetpack_disable_seo_tools', '__return_true');
		}
	}
}
add_action('wp_head', 'seopress_compatibility_jetpack', 0);

/**
 * Remove Jetpack OpenGraph tags.
 *
 * @since 6.9
 */
function seopress_compatibility_hello_elementor() {
	remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
}
add_action( 'after_setup_theme', 'seopress_compatibility_hello_elementor' );

/**
 * Filter the xml sitemap URL used by SiteGround Optimizer for preheating.
 *
 * @since 6.6.0
 *
 * @param string $url URL to be preheated.
 */
if (function_exists('is_plugin_active')) {
    if (is_plugin_active('sg-cachepress/sg-cachepress.php')) {
        function sp_sg_file_caching_preheat_xml($url) {
            $url = get_home_url() . '/sitemaps.xml';

            return $url;
        }
        add_filter('sg_file_caching_preheat_xml', 'sp_sg_file_caching_preheat_xml');
    }
}

/**
 * Remove WPML home url filter.
 *
 * @since 3.8.6
 *
 * @param mixed $home_url
 * @param mixed $url
 * @param mixed $path
 * @param mixed $orig_scheme
 * @param mixed $blog_id
 */
function seopress_remove_wpml_home_url_filter($home_url, $url, $path, $orig_scheme, $blog_id) {
	return $url;
}

/*
 * Remove third-parties metaboxes on our CPT
 * @author Benjamin Denis
 * @since 4.2
 */
add_action('do_meta_boxes', 'seopress_remove_metaboxes', 10);
function seopress_remove_metaboxes() {
	//Oxygen Builder
	remove_meta_box('ct_views_cpt', 'seopress_404', 'normal');
	remove_meta_box('ct_views_cpt', 'seopress_schemas', 'normal');
	remove_meta_box('ct_views_cpt', 'seopress_bot', 'normal');
}

/**
 * @deprecated 6.5.0
 */
function seopress_titles_single_cpt_enable_option($cpt) {
    return seopress_get_service('TitleOption')->getSingleCptEnable($cpt);
}

/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_ps_col_option() {
    return seopress_get_service('AdvancedOption')->getAppearancePsCol();
}

/**
 * @deprecated 4.4.0
 */
function seopress_get_post_types() {
    return seopress_get_service('WordPressData')->getPostTypes();
}

/**
 * @deprecated 5.8.0
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
 * Check if a string is base64 encoded
 *
 * @author Benjamin Denis
 *
 * @return boolean
 **/
function seopress_is_base64_string($str) {
	$decoded = base64_decode($str, true);
	if ($decoded === false) {
        return false;
	}
	return base64_encode($decoded) === $str;
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
    if (!is_user_logged_in()) {
        return;
    }
    if (current_user_can('edit_posts')) {
        if (isset($_GET['no_admin_bar']) && '1' === $_GET['no_admin_bar']) {
            //Remove admin bar
            add_filter('show_admin_bar', '__return_false');

            //Disable Query Monitor
            add_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

            //Disable wptexturize
            add_filter('run_wptexturize', '__return_false');

            //Remove Edit nofollow links from TablePress
            add_filter( 'tablepress_edit_link_below_table', '__return_false');

            //Allow user to run custom action to clean content
            do_action('seopress_content_analysis_cleaning');
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
 * Returns the language code by supporting multilingual plugins
 *
 * @since 6.8
 *
 * @return string language code
 *
 * @author Benjamin
 */
function seopress_get_current_lang() {
    //Default
    $lang = get_locale();

    //Polylang
    if (function_exists('pll_current_language')) {
        $lang = pll_current_language('locale');
    }

    //WPML
    if (defined('ICL_SITEPRESS_VERSION')) {
        $lang = apply_filters( 'wpml_current_language', NULL );
    }

    return $lang;
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
        $templates   = seopress_get_service('WordPressData')->getTaxonomies();
        $notice_i18n = __('Custom Taxonomies', 'wp-seopress');
    }
    foreach ($templates as $key => $value) {
        $options            = get_option('seopress_titles_option_name');

        if (!empty($options)) {
            if ('cpt' === $type) {
                if (!empty($options['seopress_titles_single_titles'])) {
                    if (!array_key_exists($key, $options['seopress_titles_single_titles'])) {
                        $cpt_titles_empty[] = $key;
                    } else {
                        $data = isset($options['seopress_titles_single_titles'][$key][$metadata]) ? $options['seopress_titles_single_titles'][$key][$metadata] : '';
                    }
                }
            }
            if ('tax' === $type) {
                if (!empty($options['seopress_titles_tax_titles'])) {
                    if (!array_key_exists($key, $options['seopress_titles_tax_titles'])) {
                        $cpt_titles_empty[] = $key;
                    } else {
                        $data = isset($options['seopress_titles_tax_titles'][$key][$metadata]) ? $options['seopress_titles_tax_titles'][$key][$metadata] : '';
                    }
                }
            }
        }

        if (empty($data)) {
            if (seopress_get_service('TitleOption')->getSingleCptEnable($key) !== '1' && seopress_get_service('TitleOption')->getTaxEnable($key) !== '1') {
                $cpt_titles_empty[] = $key;
            }
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
            /* translators: %s: "Custom Post Types" or "Custom Taxonomies", %s: "title" or "description" */
            $html .= sprintf(__('Some <strong>%s</strong> have no <strong>meta %s</strong> set! We strongly encourage you to add one by filling in the fields below.', 'wp-seopress'), $notice_i18n, $metadata);
            $html .= '</p>';
            $html .= $list;
            $html .= '</div>';

            return $html;
        }
    }
}

/**
 * Generate Permalink notice to prevent users change the permastructure on a live site.
 *
 * @since 6.5
 *
 * @return string $message
 *
 * @author Benjamin
 */
function seopress_notice_permalinks() {
    global $pagenow;
    if (isset($pagenow) && 'options-permalink.php' !== $pagenow) {
        return;
    }

    $class   = 'notice notice-warning';
    $message = '<strong>' . __('WARNING', 'wp-seopress') . '</strong>';
    $message .= '<p>' . __('Do NOT change your permalink structure on a production site. Changing URLs can severely damage your SEO.', 'wp-seopress') . '</p>';

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
}
add_action('admin_notices', 'seopress_notice_permalinks');

/**
 * Generate a notice on permalink settings screen if URL rewriting is disabled.
 *
 * @since 6.5.0
 *
 * @return string $message
 *
 * @author Benjamin
 */
function seopress_notice_no_rewrite_url() {
    //Check we are on the Permalink settings page
    global $pagenow;
    if (isset($pagenow) && 'options-permalink.php' !== $pagenow) {
        return;
    }

    //Check permalink structure
    if ('' !== get_option('permalink_structure')) {
        return;
    }

    //Display the notice
    $class   = 'notice notice-warning';
    $message = '<strong>' . __('WARNING', 'wp-seopress') . '</strong>';
    $message .= '<p>' . __('URL rewriting is NOT enabled on your site. Select a permalink structure that is optimized for SEO (NOT Plain).', 'wp-seopress') . '</p>';

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
}
add_action('admin_notices', 'seopress_notice_no_rewrite_url');

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
 * @deprecated 6.7
 *
 * @return string HTML notification
 *
 * @author Benjamin
 */

 function seopress_notification($args) {
    remove_all_actions( 'seopress_notifications_center_item' );
    return;
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
        if (is_array($_REQUEST['post_type']) && !empty($_REQUEST['post_type'])) {
            return 0 === strpos($_REQUEST['post_type'][0], 'seopress');
        } else {
            return 0 === strpos($_REQUEST['post_type'], 'seopress');
        }
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
        remove_all_actions('network_admin_notices');
        remove_all_actions('admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('all_admin_notices');
        add_action('admin_notices', 'seopress_admin_notices');
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            if ( version_compare(SEOPRESS_PRO_VERSION, '6.4', '>=')) {
                add_action('admin_notices', 'seopress_pro_admin_notices');
            }
        }
        if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
            if ( version_compare(SEOPRESS_INSIGHTS_VERSION, '1.8.1', '>=')) {
                add_action('admin_notices', 'seopress_insights_notices');
            }
        }
    }
}
add_action('in_admin_header', 'seopress_remove_other_notices', 1000);//keep this value high to remove other notices

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
    } elseif (isset($pagenow) && ($pagenow === 'term.php' || $pagenow === 'post.php' || $pagenow === 'post-new.php') ) {
        $btn_classes_secondary = 'button button-secondary';
    } else {
        $btn_classes_secondary = 'btn btnSecondary';
    }

    return $btn_classes_secondary;
}

/**
 * Global check.
 *
 * @since 3.8
 *
 * @param string $feature
 *
 * @return string 1 if true
 *
 * @author Benjamin
 */
function seopress_get_toggle_option($feature) {
	$seopress_get_toggle_option = get_option('seopress_toggle');
	if ( ! empty($seopress_get_toggle_option)) {
		foreach ($seopress_get_toggle_option as $key => $seopress_get_toggle_value) {
			$options[$key] = $seopress_get_toggle_value;
			if (isset($seopress_get_toggle_option['toggle-' . $feature])) {
				return $seopress_get_toggle_option['toggle-' . $feature];
			}
		}
	}
}

/*
 * Global trailingslash option from SEO, Advanced, Advanced tab (useful for backwards compatibility with SEOPress < 5.9)
 * @since 5.9
 * @return string 1 if true
 * @author Benjamin
 */
if ( ! function_exists('seopress_advanced_advanced_trailingslash_option')) {
    function seopress_advanced_advanced_trailingslash_option()
    {
        $seopress_advanced_advanced_trailingslash_option = get_option('seopress_advanced_option_name');
        if (! empty($seopress_advanced_advanced_trailingslash_option)) {
            foreach ($seopress_advanced_advanced_trailingslash_option as $key => $seopress_advanced_advanced_trailingslash_value) {
                $options[$key] = $seopress_advanced_advanced_trailingslash_value;
            }
            if (isset($seopress_advanced_advanced_trailingslash_option['seopress_advanced_advanced_trailingslash'])) {
                return $seopress_advanced_advanced_trailingslash_option['seopress_advanced_advanced_trailingslash'];
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



/**
 * Helper function needed for PHP 8.1 compatibility with "current" function
 * Get mangled object vars
 * @since 6.2.0
 */
function seopress_maybe_mangled_object_vars($data){
    if(!function_exists('get_mangled_object_vars')){
        return $data;
    }

    if(!is_object($data)){
        return $data;
    }

    return get_mangled_object_vars($data);

}

/**
 * Automatically flush permalinks after saving XML sitemaps global settings
 * @since 6.0.0
 *
 * @param string $option
 * @param string $old_value
 * @param string $value
 *
 * @return void
 */
add_action('update_option', function( $option, $old_value, $value ) {
    if ($option ==='seopress_xml_sitemap_option_name') {
        set_transient('seopress_flush_rewrite_rules', 1);
    }
}, 10, 3);

add_action('admin_init', 'seopress_auto_flush_rewrite_rules');
function seopress_auto_flush_rewrite_rules() {
    if (get_transient('seopress_flush_rewrite_rules')) {
        flush_rewrite_rules(false);
        delete_transient('seopress_flush_rewrite_rules');
    }
}
