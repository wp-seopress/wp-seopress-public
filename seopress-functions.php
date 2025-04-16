<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use SEOPress\Core\Kernel;

/**
 * Get a service.
 *
 * @param string $service
 *
 * @return object
 */
function seopress_get_service($service) {
	return Kernel::getContainer()->getServiceByName($service);
}

/**
 * Get last key of an array if PHP < 7.3
 * @return string
 */
if ( ! function_exists('array_key_last')) {
	function array_key_last(array $arr) {
		end($arr);
		$key = key($arr);

		return $key;
	}
}

/**
 * Get first key of an array if PHP < 7.3
 * @return string
 */
if ( ! function_exists('array_key_first')) {
	function array_key_first(array $arr) {
		foreach ($arr as $key => $unused) {
			return $key;
		}

		return null;
	}
}

/**
 * Remove default WordPress Canonical
 */
remove_action('wp_head', 'rel_canonical');

/**
 * Remove WP default meta robots (added in WP 5.7)
 */
remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

/**
 * Remove WC default meta robots (added in WP 5.7)
 * @todo use wp_robots API
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
 * Remove default WC meta robots (useful for WooCommerce < 5.7).
 */
function seopress_compatibility_woocommerce() {
	if (! is_admin() && function_exists('is_plugin_active') && is_plugin_active('woocommerce/woocommerce.php')) {
		remove_action('wp_head', 'wc_page_noindex');
	}
}
add_action('wp_head', 'seopress_compatibility_woocommerce', 0);

/**
 * Remove Elementor description meta tag.
 */
function seopress_compatibility_hello_elementor() {
	remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
}
add_action( 'after_setup_theme', 'seopress_compatibility_hello_elementor' );

/**
 * Filter the xml sitemap URL used by SiteGround Optimizer for preheating.
 *
 * @param string $url URL to be preheated.
 */
if (function_exists('is_plugin_active') && is_plugin_active('sg-cachepress/sg-cachepress.php')) {
	function sp_sg_file_caching_preheat_xml($url) {
		$url = get_home_url() . '/sitemaps.xml';

		return $url;
	}
	add_filter('sg_file_caching_preheat_xml', 'sp_sg_file_caching_preheat_xml');
}

/**
 * Remove WPML home url filter.
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

/**
 * Remove third-parties metaboxes on our CPT
 */
add_action('do_meta_boxes', 'seopress_remove_metaboxes', 10);
function seopress_remove_metaboxes() {
	//Oxygen Builder
	remove_meta_box('ct_views_cpt', 'seopress_404', 'normal');
	remove_meta_box('ct_views_cpt', 'seopress_schemas', 'normal');
	remove_meta_box('ct_views_cpt', 'seopress_bot', 'normal');
}

/**
 * Get all custom fields (limit: 250).
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
 * @param string $str The string to check
 * @return bool Returns true if the string is base64 encoded, false otherwise
 */
function seopress_is_base64_string($str) {
	// Check if the string is empty or not a string
	if (empty($str) || !is_string($str)) {
		return false;
	}

	// Decode the string and check if it decodes properly
	$decoded = base64_decode($str, true);
	if ($decoded === false) {
		return false;
	}

	// Encode the decoded string and compare with the original string
	return base64_encode($decoded) === $str;
}

/**
 * Disable Query Monitor for CA.
 *
 * @return array
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
 */
function seopress_clean_content_analysis() {
    // Check if 'no_admin_bar' is set and equals '1'; sanitize input
    if (!isset($_GET['no_admin_bar']) || '1' !== sanitize_text_field(wp_unslash($_GET['no_admin_bar']))) {
        return;
    }

    // Check if the user is logged in and has the necessary capability
    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        return;
    }

    // Remove admin bar
    add_filter('show_admin_bar', '__return_false');

    // Disable Query Monitor
    add_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

    // Disable wptexturize
    add_filter('run_wptexturize', '__return_false');

    // Remove Edit nofollow links from TablePress
    add_filter('tablepress_edit_link_below_table', '__return_false');

    // Allow user to run custom action to clean content
    do_action('seopress_content_analysis_cleaning');
}
add_action('plugins_loaded', 'seopress_clean_content_analysis');

/**
 * Test if a URL is in absolute.
 *
 * @return bool true if absolute
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
 * Extract correct locale in ISO format from get_locale().
 *
 * @return string locale
 */
function seopress_normalized_locale($current_locale) {
	if (!function_exists('locale_get_primary_language')) {
		return $current_locale;
	}

	// Extract primary language and region
	$primary_language = locale_get_primary_language($current_locale);
	$region = locale_get_region($current_locale);

	// Check if region is available, if not, return only the primary language
	$normalized_locale = $primary_language . ($region ? '_' . $region : '');

	return $normalized_locale;
}

/**
 * Returns the language code by supporting multilingual plugins
 *
 * @return string language code
 */
function seopress_get_current_lang() {
	//Default
	$lang = seopress_normalized_locale(get_locale());

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
 * @param string $type
 * @param string $metadata
 * @param bool   $notice
 *
 * @return string notice with list of empty cpt titles
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
			/* translators: %1$s: "Custom Post Types" or "Custom Taxonomies", %2$s: "title" or "description" */
			$html .= wp_kses_post(sprintf(__('Some <strong>%1$s</strong> have no <strong>meta %2$s</strong> set! We strongly encourage you to add one by filling in the fields below.', 'wp-seopress'), esc_attr($notice_i18n), esc_attr($metadata)));
			$html .= '</p>';
			$html .= $list;
			$html .= '</div>';

			return $html;
		}
	}
}

/**
 * Generate Permalink notice to prevent users from changing the permastructure on a live site.
 *
 * @return void
 */
function seopress_notice_permalinks() {
	$pagenow = isset($GLOBALS['pagenow']) ? $GLOBALS['pagenow'] : '';

	if ('options-permalink.php' !== $pagenow) {
		return;
	}

	$class   = 'notice notice-warning';
	$message = sprintf(
		'<p><strong>%s</strong></p><p>%s</p>',
		__('WARNING', 'wp-seopress'),
		__('Do NOT change your permalink structure on a production site. Changing URLs can severely damage your SEO.', 'wp-seopress')
	);

	printf('<div class="%1$s">%2$s</div>', esc_attr($class), wp_kses_post($message));
}
add_action('admin_notices', 'seopress_notice_permalinks');

/**
 * Generate a notice on permalink settings screen if URL rewriting is disabled.
 *
 * @return void
 */
function seopress_notice_no_rewrite_url() {
	$pagenow = isset($GLOBALS['pagenow']) ? $GLOBALS['pagenow'] : '';

	// Check we are on the Permalink settings page
	if ('options-permalink.php' !== $pagenow) {
		return;
	}

	// Check permalink structure
	if ('' !== get_option('permalink_structure')) {
		return;
	}

	// Display the notice
	$class   = 'notice notice-warning';
	$message = sprintf(
		'<p><strong>%s</strong></p><p>%s</p>',
		__('WARNING', 'wp-seopress'),
		__('URL rewriting is NOT enabled on your site. Select a permalink structure that is optimized for SEO (NOT Plain).', 'wp-seopress')
	);

	printf('<div class="%1$s">%2$s</div>', esc_attr($class), wp_kses_post($message));
}
add_action('admin_notices', 'seopress_notice_no_rewrite_url');

/**
 * Generate Tooltip.
 *
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_desc
 * @param mixed  $tooltip_code
 *
 * @return string tooltip title, tooltip description, tooltip url
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
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_anchor
 * @param mixed  $tooltip_desc
 *
 * @return string tooltip title, tooltip description, tooltip url
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
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_anchor
 * @param mixed  $tooltip_desc
 *
 * @return string tooltip title, tooltip description, tooltip url
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
 * @param mixed $text
 *
 * @return mixed $text
 */
function seopress_remove_utf8_bom($text) {
	$bom  = pack('H*', 'EFBBBF');
	$text = preg_replace("/^$bom/", '', $text);

	return $text;
}

/**
 * Filter the capability to allow other roles to use the plugin.
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
 * @return bool
 */
function is_seopress_page() {
	if (!is_admin()) {
		return false;
	}

	$page = isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : null;
	$post_type = isset($_REQUEST['post_type']) ? sanitize_text_field(wp_unslash($_REQUEST['post_type'])) : null;

	if ($page) {
		return strpos($page, 'seopress') === 0;
	}

	if ($post_type) {
		if (is_array($post_type) && !empty($post_type)) {
			return strpos($post_type[0], 'seopress') === 0;
		}
		return strpos($post_type, 'seopress') === 0;
	}

	return false;
}

/**
 * Only add our notices on our pages.
 *
 * @since 3.8.2
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
add_action('in_admin_header', 'seopress_remove_other_notices', 1000); //keep this value high to remove other notices

/**
 * Only add our notices on our pages.
 *
 * @since 8.2.0
 *
 * @return bool
 */
function seopress_remove_other_plugin_notices() {
	if (is_seopress_page()) {
		//SEOKEY plugin doesn't hook properly, we have to make a specific case
		remove_all_filters('seokey_filter_admin_notices_launch', 10);
	}
}
add_action('admin_init', 'seopress_remove_other_plugin_notices');

/**
 * We replace the WP action by ours.
 *
 * @since 3.8.2
 *
 * @return bool
 */
function seopress_admin_notices() {
	do_action('seopress_admin_notices');
}

/**
 * Check if a key exists in a multidimensional array.
 *
 * @since 3.8.2
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
 * @param mixed $value
 * @param mixed $classes
 * @param mixed $type
 */
function sp_submit_button($value = '', $classes = 'btn btnPrimary', $type = 'submit') {
	if ('' === $value) {
		$value = __('Save changes', 'wp-seopress');
	}

	// Use esc_attr_e to escape attributes in the output
	$html = '<p class="submit"><input id="submit" name="submit" type="' . esc_attr($type) . '" class="' . esc_attr($classes) . '" value="' . esc_attr($value) . '"/></p>';

	echo $html;
}


/**
 * Generate HTML buttons classes
 *
 * @since 5.0
 *
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

/**
 * Disable Add to cart GA tracking code on archive page / related products for Elementor PRO to avoid a JS conflict
 * @since 5.3
 * @return empty string
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
 * Generate dynamically the Instant Indexing API key
 *
 * @since 8.6.0
 *
 * @param bool $init
 *
 * @return void
 */
function seopress_instant_indexing_generate_api_key_fn($init = false) {
    $options            = get_option('seopress_instant_indexing_option_name') ? get_option('seopress_instant_indexing_option_name') : [];

    $api_key = wp_generate_uuid4();
    $api_key = preg_replace('[-]', '', $api_key);
    $options['seopress_instant_indexing_bing_api_key'] = base64_encode($api_key);

    if ($init === true) {
        $options['seopress_instant_indexing_automate_submission'] = '1';
    }

    update_option('seopress_instant_indexing_option_name', $options);

    if ($init === false) {
        wp_send_json_success();
    }
}