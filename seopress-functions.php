<?php

/**
 * Functions file
 *
 * @package SEOPress
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

use SEOPress\Core\Kernel;

/**
 * Get a service.
 *
 * @param string $service
 *
 * @return object
 */
function seopress_get_service( $service ) {
	return Kernel::getContainer()->getServiceByName( $service );
}

if ( ! function_exists( 'array_key_last' ) ) {
	/**
	 * Get last key of an array if PHP < 7.3
	 *
	 * @param array $arr
	 *
	 * @return string
	 */
	function array_key_last( array $arr ) {
		end( $arr );
		$key = key( $arr );

		return $key;
	}
}

if ( ! function_exists( 'array_key_first' ) ) {
	/**
	 * Get first key of an array if PHP < 7.3
	 *
	 * @param array $arr
	 *
	 * @return string
	 */
	function array_key_first( array $arr ) {
		foreach ( $arr as $key => $unused ) {
			return $key;
		}

		return null;
	}
}

/**
 * Remove Pinterest for WooCommerce OpenGraph tags.
 */
add_filter( 'pinterest_for_woocommerce_opengraph_tags', '__return_empty_array', 10, 2 );

/**
 * Remove default WordPress Canonical
 */
remove_action( 'wp_head', 'rel_canonical' );

/**
 * Remove WP default meta robots (added in WP 5.7)
 */
remove_filter( 'wp_robots', 'wp_robots_max_image_preview_large' );

/**
 * Remove WC default meta robots (added in WP 5.7)
 *
 * @param array $robots
 *
 * @todo use wp_robots API
 */
function seopress_robots_wc_pages( $robots ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		if ( function_exists( 'wc_get_page_id' ) ) {
			if ( is_page( wc_get_page_id( 'cart' ) ) || is_page( wc_get_page_id( 'checkout' ) ) || is_page( wc_get_page_id( 'myaccount' ) ) ) {
				if ( '0' === get_option( 'blog_public' ) ) {
					return $robots;
				} else {
					unset( $robots );
					$robots = array();

					return $robots;
				}
			}
		}
	}
	// Remove noindex on search archive pages.
	if ( is_search() ) {
		if ( '0' === get_option( 'blog_public' ) ) {
			return $robots;
		} else {
			unset( $robots );
			$robots = array();

			return $robots;
		}
	}

	return $robots;
}
add_filter( 'wp_robots', 'seopress_robots_wc_pages', 20 );

/**
 * Remove default WC meta robots (useful for WooCommerce < 5.7).
 */
function seopress_compatibility_woocommerce() {
	if ( ! is_admin() && function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		remove_action( 'wp_head', 'wc_page_noindex' );
	}
}
add_action( 'wp_head', 'seopress_compatibility_woocommerce', 0 );

/**
 * Remove Elementor description meta tag.
 */
function seopress_compatibility_hello_elementor() {
	remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
}
add_action( 'after_setup_theme', 'seopress_compatibility_hello_elementor' );

if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'sg-cachepress/sg-cachepress.php' ) ) {
	/**
	 * Filter the xml sitemap URL used by SiteGround Optimizer for preheating.
	 *
	 * @param string $url URL to be preheated.
	 */
	function sp_sg_file_caching_preheat_xml( $url ) {
		$url = get_home_url() . '/sitemaps.xml';

		return $url;
	}
	add_filter( 'sg_file_caching_preheat_xml', 'sp_sg_file_caching_preheat_xml' );
}

/**
 * Remove WPML home url filter.
 *
 * @param string  $home_url Home URL.
 * @param string  $url URL.
 * @param string  $path Path.
 * @param string  $orig_scheme Original scheme.
 * @param integer $blog_id Blog ID.
 */
function seopress_remove_wpml_home_url_filter( $home_url, $url, $path, $orig_scheme, $blog_id ) {
	return $url;
}

/**
 * Switch WPML to a post's own language while a sitemap is being generated.
 *
 * Hooked on `the_post`, so it runs once per entry. Two things matter here, and
 * both used to be wrong.
 *
 * The element type has to be the post's actual type. WPML prefixes it (`page`
 * becomes `post_page`) and looks the row up in `icl_translations` under that
 * exact type, so asking for a post or a custom post type as if it were a page
 * matches nothing and returns null. `wpml_switch_language` then falls back to
 * the default language, and a sitemap mixing post types produces an
 * alternating default, real, default, real sequence.
 *
 * And the switch has to be skipped when the language is already the current
 * one. WPML writes its language cookie on every switch that differs from the
 * previous value, which the alternation above defeats entirely: a 1000 URL
 * sitemap emitted 651 Set-Cookie headers, over 70 KB of response headers, and
 * any reverse proxy capping header size at 32 KB answers 502. Only the last of
 * those cookies is ever observable, so the rest are pure overhead. Comparing
 * against WPML's live current language rather than a remembered value keeps
 * this correct even when something else switches in between.
 *
 * Named rather than a closure so it can be detached with remove_action(), and
 * filterable so it can be turned off without touching the hook at all.
 *
 * @since 10.2.0
 *
 * @param WP_Post $post The post being rendered.
 *
 * @return void
 */
function seopress_sitemap_switch_wpml_language( $post ) {
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	/**
	 * Filter whether SEOPress switches the WPML language for a sitemap entry.
	 *
	 * @since 10.2.0
	 *
	 * @param bool    $switch Whether to switch. Default true.
	 * @param WP_Post $post   The post being rendered.
	 */
	if ( ! apply_filters( 'seopress_sitemap_wpml_switch_language', true, $post ) ) {
		return;
	}

	$language = apply_filters(
		'wpml_element_language_code',
		null,
		array(
			'element_id'   => $post->ID,
			'element_type' => $post->post_type,
		)
	);

	// Nothing resolved: leave WPML where it is rather than sending it back to
	// the default language.
	if ( empty( $language ) ) {
		return;
	}

	if ( $language === apply_filters( 'wpml_current_language', null ) ) {
		return;
	}

	do_action( 'wpml_switch_language', $language );
}

/**
 * Remove third-parties metaboxes on our CPT
 */
function seopress_remove_metaboxes() {
	// Oxygen Builder.
	remove_meta_box( 'ct_views_cpt', 'seopress_404', 'normal' );
	remove_meta_box( 'ct_views_cpt', 'seopress_schemas', 'normal' );
	remove_meta_box( 'ct_views_cpt', 'seopress_bot', 'normal' );

	// Avada Builder.
	remove_meta_box( 'seopress_cpt', 'fusion_element', 'normal' );
	remove_meta_box( 'seopress_content_analysis', 'fusion_element', 'normal' );
	remove_meta_box( 'seopress_pro_cpt', 'fusion_element', 'normal' );
}
add_action( 'do_meta_boxes', 'seopress_remove_metaboxes', 10 );

/**
 * Get all custom fields (limit: 250).
 *
 * @return array custom field keys
 */
function seopress_get_custom_fields() {
	$cf_keys = wp_cache_get( 'seopress_get_custom_fields' );

	if ( false === $cf_keys ) {
		global $wpdb;

		$limit   = (int) apply_filters( 'postmeta_form_limit', 250 );
		$cf_keys = $wpdb->get_col(
			$wpdb->prepare(
				"
			SELECT DISTINCT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			HAVING meta_key NOT LIKE '\_%%'
			ORDER BY meta_key
			LIMIT %d",
				$limit
			)
		);

		if ( is_plugin_active( 'types/wpcf.php' ) ) {
			$wpcf_fields = get_option( 'wpcf-fields' );

			if ( ! empty( $wpcf_fields ) ) {
				foreach ( $wpcf_fields as $key => $value ) {
					$cf_keys[] = $value['meta_key'];
				}
			}
		}

		$cf_keys = apply_filters( 'seopress_get_custom_fields', $cf_keys );

		if ( $cf_keys ) {
			natcasesort( $cf_keys );
		}
		wp_cache_set( 'seopress_get_custom_fields', $cf_keys );
	}

	return $cf_keys;
}

/**
 * Check SSL for schema.org.
 *
 * @return string correct protocol
 */
function seopress_check_ssl() {
	if ( is_ssl() ) {
		return 'https://';
	} else {
		return 'http://';
	}
}

/**
 * Recursively decode HTML entities in a schema value.
 *
 * WordPress hands most of its content back entity-encoded: `get_the_title()`
 * returns `&#038;` for an ampersand, `wp_kses()` turns a bare `&` into `&amp;`,
 * and `esc_html()` adds `&lt;` and `&quot;`. None of that belongs in JSON-LD,
 * which carries plain strings.
 *
 * @since 10.2.0
 *
 * @param mixed $value The schema value (array or scalar).
 *
 * @return mixed
 */
function seopress_json_ld_decode_entities( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'seopress_json_ld_decode_entities', $value );
	}

	if ( is_string( $value ) ) {
		return html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

	return $value;
}

/**
 * Encode schema data for a `<script type="application/ld+json">` element.
 *
 * Google's structured data parser applies a single pass of HTML unescaping to
 * the body of the script element, so any entity left in a value is read as the
 * character it stands for rather than as text. That makes an entity a poor way
 * to carry anything: `&quot;` inside a string turns into the quote that ends
 * it, and a title mentioning `&amp;` loses the mention. Since March 2026 the
 * pass is applied once and only once, and Google asks for standard JSON escapes
 * or Unicode hexadecimal escapes instead.
 *
 * So values are decoded once, then handed to JSON's own escaping: `&`, `<`,
 * `>`, `'` and `"` leave as `\u0026`, `\u003C`, `\u003E`, `\u0027` and
 * `\u0022`, which no unescaping pass can act on. Only those five need it —
 * an accent or an emoji is never mistaken for markup, so they stay readable
 * rather than becoming a run of `\uXXXX`.
 *
 * `JSON_HEX_TAG` has a second effect worth naming: no value can close the
 * script element it sits in, whatever a filter put there. That is what makes
 * `JSON_UNESCAPED_SLASHES` safe to add, which keeps URLs readable.
 *
 * @since 10.2.0
 *
 * @param mixed $data The schema data.
 *
 * @return string|false The JSON-LD, or false if the data cannot be encoded.
 */
function seopress_json_ld_encode( $data ) {
	return wp_json_encode(
		seopress_json_ld_decode_entities( $data ),
		JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);
}

/**
 * Check if a string is base64 encoded
 *
 * @param string $str The string to check
 * @return bool Returns true if the string is base64 encoded, false otherwise
 */
function seopress_is_base64_string( $str ) {
	// Check if the string is empty or not a string.
	if ( empty( $str ) || ! is_string( $str ) ) {
		return false;
	}

	// Decode the string and check if it decodes properly.
	$decoded = base64_decode( $str, true );
	if ( $decoded === false ) {
		return false;
	}

	// Encode the decoded string and compare with the original string.
	return base64_encode( $decoded ) === $str;
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
function seopress_disable_qm( $allcaps, $caps, $args ) {
	$allcaps['view_query_monitor'] = false;

	return $allcaps;
}
/**
 * Clear content for CA.
 */
function seopress_clean_content_analysis() {
	// Check if 'no_admin_bar' is set and equals '1'; sanitize input.
	if ( ! isset( $_GET['no_admin_bar'] ) || '1' !== sanitize_text_field( wp_unslash( $_GET['no_admin_bar'] ) ) ) {
		return;
	}

	// Check if the user is logged in and has the necessary capability.
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	// Remove admin bar.
	add_filter( 'show_admin_bar', '__return_false' );

	// Disable Query Monitor.
	add_filter( 'user_has_cap', 'seopress_disable_qm', 10, 3 );

	// Disable wptexturize.
	add_filter( 'run_wptexturize', '__return_false' );

	// Remove Edit nofollow links from TablePress.
	add_filter( 'tablepress_edit_link_below_table', '__return_false' );

	// Allow user to run custom action to clean content.
	do_action( 'seopress_content_analysis_cleaning' );
}
add_action( 'plugins_loaded', 'seopress_clean_content_analysis' );

/**
 * Test if a URL is in absolute.
 *
 * @return bool true if absolute
 *
 * @param mixed $url
 */
function seopress_is_absolute( $url ) {
	$pattern = "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu";

	return (bool) preg_match( $pattern, $url );
}

/**
 * Resolve an image `src` read from post content into an absolute URL, or
 * reject it.
 *
 * The XML sitemap collects images from the stored post content, so a `src`
 * arrives in whatever form the editor or the page builder left it in. Only a
 * value with one unambiguous absolute form is resolved; anything else is
 * rejected rather than guessed at.
 *
 * Prefixing the site URL onto whatever was found is what used to publish
 * `https://example.com/{{featured_image key:url}}` for a page builder's
 * unresolved dynamic tag, `https://example.com//wp-content/…` for a
 * root-relative path, and `https://example.com///cdn.example.com/…` for a
 * protocol-relative one, that last case announcing a CDN image on the site's
 * own domain where nothing exists.
 *
 * @since 10.2.0
 *
 * @param string $url      Raw `src` attribute value.
 * @param string $home_url Site home URL.
 *
 * @return string An absolute URL, or an empty string when the value cannot be one.
 */
function seopress_sitemap_resolve_image_url( $url, $home_url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	// Already absolute: nothing to resolve.
	if ( true === seopress_is_absolute( $url ) ) {
		return $url;
	}

	// Braces mean a dynamic tag the builder resolves at render time and that
	// is simply absent here ({{featured_image key:url}}, {post_title}); `../`
	// is a path relative to the document rather than to the site root; a line
	// break is a value that was never a URL. None of these has a single
	// correct absolute form, so none is published.
	if ( preg_match( '/[{}\r\n\t]/', $url ) || false !== strpos( $url, '../' ) ) {
		return '';
	}

	$home_url = trailingslashit( $home_url );

	// Protocol-relative (//host/path): only the scheme is missing, and it is
	// the site's own.
	if ( 0 === strpos( $url, '//' ) ) {
		$scheme = wp_parse_url( $home_url, PHP_URL_SCHEME );

		return ( ! empty( $scheme ) ? $scheme : 'https' ) . ':' . $url;
	}

	// Rooted at the site (/path) or relative to it (wp-content/…): one correct
	// form either way. Trimming the leading slash keeps the join from doubling
	// it.
	return $home_url . ltrim( $url, '/' );
}

/**
 * Get the page standing in for a post type archive, when there is one.
 *
 * Two archives are served by a real page: `post` when a static Posts page is
 * set in Settings > Reading, and `product` when WooCommerce maps the catalog to
 * its shop page. Both pages carry their own SEOPress metas, which the post type
 * settings know nothing about.
 *
 * The Posts page only backs the archive when the front page is static, since
 * WordPress falls back to the home URL otherwise while still keeping the stored
 * `page_for_posts` value around.
 *
 * @since 10.2.0
 *
 * @param string $path Post type name.
 *
 * @return int Page ID, or 0 when the archive is not backed by a page.
 */
function seopress_sitemap_get_archive_page_id( $path ) {
	$page_id = 0;

	if ( 'post' === $path ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_id = (int) get_option( 'page_for_posts' );
		}
	} elseif ( 'product' === $path && function_exists( 'wc_get_page_id' ) ) {
		// wc_get_page_id() returns -1 when the page is not configured.
		$page_id = (int) wc_get_page_id( 'shop' );
	}

	return $page_id > 0 ? $page_id : 0;
}

/**
 * Whether a page is excluded from the sitemaps by its own noindex setting.
 *
 * @since 10.2.0
 *
 * @param int $page_id Page ID.
 *
 * @return bool
 */
function seopress_sitemap_is_page_noindex( $page_id ) {
	$page_id = (int) $page_id;

	if ( $page_id <= 0 ) {
		return false;
	}

	return 'yes' === get_post_meta( $page_id, '_seopress_robots_index', true );
}

/**
 * Manage localized links.
 *
 * @return string locale for documentation links
 */
function seopress_get_locale() {
	switch ( get_user_locale( get_current_user_id() ) ) {
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
function seopress_normalized_locale( $current_locale ) {
	if ( ! function_exists( 'locale_get_primary_language' ) ) {
		return $current_locale;
	}

	// Extract primary language and region.
	$primary_language = locale_get_primary_language( $current_locale );
	$region           = locale_get_region( $current_locale );

	// Check if region is available, if not, return only the primary language.
	$normalized_locale = $primary_language . ( $region ? '_' . $region : '' );

	return $normalized_locale;
}

/**
 * Returns the language code by supporting multilingual plugins
 *
 * @return string language code
 */
function seopress_get_current_lang() {
	// Default.
	$lang = seopress_normalized_locale( get_locale() );

	// Polylang.
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'locale' );
	}

	// WPML.
	if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
		$lang = apply_filters( 'wpml_current_language', null );
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
function seopress_get_empty_templates( $type, $metadata, $notice = true ) {
	$cpt_titles_empty = array();
	$templates        = '';
	$data             = '';
	$html             = '';
	$list             = '';

	if ( 'cpt' === $type ) {
		$templates   = $postTypes = seopress_get_service( 'WordPressData' )->getPostTypes();
		$notice_i18n = __( 'Custom Post Types', 'wp-seopress' );
	}
	if ( 'tax' === $type ) {
		$templates   = seopress_get_service( 'WordPressData' )->getTaxonomies();
		$notice_i18n = __( 'Custom Taxonomies', 'wp-seopress' );
	}
	if ( ! is_array( $templates ) && ! is_object( $templates ) ) {
		$templates = array();
	}
	foreach ( $templates as $key => $value ) {
		$data    = '';
		$options = get_option( 'seopress_titles_option_name' );

		if ( ! empty( $options ) ) {
			if ( 'cpt' === $type ) {
				if ( ! empty( $options['seopress_titles_single_titles'] ) ) {
					if ( ! array_key_exists( $key, $options['seopress_titles_single_titles'] ) ) {
						$cpt_titles_empty[] = $key;
					} else {
						$data = isset( $options['seopress_titles_single_titles'][ $key ][ $metadata ] ) ? $options['seopress_titles_single_titles'][ $key ][ $metadata ] : '';
					}
				}
			}
			if ( 'tax' === $type ) {
				if ( ! empty( $options['seopress_titles_tax_titles'] ) ) {
					if ( ! array_key_exists( $key, $options['seopress_titles_tax_titles'] ) ) {
						$cpt_titles_empty[] = $key;
					} else {
						$data = isset( $options['seopress_titles_tax_titles'][ $key ][ $metadata ] ) ? $options['seopress_titles_tax_titles'][ $key ][ $metadata ] : '';
					}
				}
			}
		}

		if ( empty( $data ) ) {
			if ( seopress_get_service( 'TitleOption' )->getSingleCptEnable( $key ) !== '1' && seopress_get_service( 'TitleOption' )->getTaxEnable( $key ) !== '1' ) {
				$cpt_titles_empty[] = $key;
			}
		}
	}

	if ( ! empty( $cpt_titles_empty ) ) {
		$list .= '<ul>';
		foreach ( $cpt_titles_empty as $cpt ) {
			$list .= '<li>' . $cpt . '</li>';
		}
		$list .= '</ul>';

		if ( false === $notice ) {
			return $list;
		} else {
			$html .= '<div class="seopress-notice is-warning">
	<p>';
			/* translators: %1$s: "Custom Post Types" or "Custom Taxonomies", %2$s: "title" or "description" */
			$html .= wp_kses_post( sprintf( __( 'Some <strong>%1$s</strong> have no <strong>meta %2$s</strong> set! We strongly encourage you to add one by filling in the fields below.', 'wp-seopress' ), esc_attr( $notice_i18n ), esc_attr( $metadata ) ) );
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
	$pagenow = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '';

	if ( 'options-permalink.php' !== $pagenow ) {
		return;
	}

	$class   = 'notice notice-warning';
	$message = sprintf(
		'<p><strong>%s</strong></p><p>%s</p>',
		__( 'WARNING', 'wp-seopress' ),
		__( 'Do NOT change your permalink structure on a production site. Changing URLs can severely damage your SEO.', 'wp-seopress' )
	);

	printf( '<div class="%1$s">%2$s</div>', esc_attr( $class ), wp_kses_post( $message ) );
}
add_action( 'admin_notices', 'seopress_notice_permalinks' );

/**
 * Generate a notice on permalink settings screen if URL rewriting is disabled.
 *
 * @return void
 */
function seopress_notice_no_rewrite_url() {
	$pagenow = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '';

	// Check we are on the Permalink settings page.
	if ( 'options-permalink.php' !== $pagenow ) {
		return;
	}

	// Check permalink structure.
	if ( '' !== get_option( 'permalink_structure' ) ) {
		return;
	}

	// Display the notice.
	$class   = 'notice notice-warning';
	$message = sprintf(
		'<p><strong>%s</strong></p><p>%s</p>',
		__( 'WARNING', 'wp-seopress' ),
		__( 'URL rewriting is NOT enabled on your site. Select a permalink structure that is optimized for SEO (NOT Plain).', 'wp-seopress' )
	);

	printf( '<div class="%1$s">%2$s</div>', esc_attr( $class ), wp_kses_post( $message ) );
}
add_action( 'admin_notices', 'seopress_notice_no_rewrite_url' );

/**
 * Generate Tooltip.
 *
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @param mixed  $tooltip_desc
 * @param mixed  $tooltip_code
 *
 * @return string tooltip title, tooltip description, tooltip url
 */
function seopress_tooltip( $tooltip_title, $tooltip_desc, $tooltip_code ) {
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
function seopress_tooltip_alt( $tooltip_anchor, $tooltip_desc ) {
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
function seopress_tooltip_link( $tooltip_anchor, $tooltip_desc ) {
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
function seopress_remove_utf8_bom( $text ) {
	$bom  = pack( 'H*', 'EFBBBF' );
	$text = preg_replace( "/^$bom/", '', $text );

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
function seopress_capability( $cap, $context = '' ) {
	$newcap = apply_filters( 'seopress_capability', $cap, $context );

	if ( ! current_user_can( $newcap ) ) {
		return $cap;
	}

	return $newcap;
}

/**
 * Whether the current user is blocked from editing a metabox area by the
 * Advanced > Security role restrictions.
 *
 * Server-side mirror of the React `isTabBlockedForUser()` helper
 * (app/react/constants/tabs.js): a user is blocked for an area when any of
 * their roles is flagged ("1") for that area in the matching option. The tab
 * bar and content panel already hide blocked areas client-side; this lets the
 * REST endpoints, the Gutenberg meta auth callback and the Classic Editor save
 * fallback enforce the same restriction server-side, so a crafted request
 * cannot persist fields the UI hides.
 *
 * Super admins are never blocked: the settings screen excludes the
 * administrator role, so they can never be a legitimate target.
 *
 * @since 10.1.1
 *
 * @param string $type Area to check: 'CONTENT_ANALYSIS' or 'GLOBAL'
 *                     (the SEO metabox — the default for any other value).
 *
 * @return bool True when the current user is blocked from editing that area.
 */
function seopress_metabox_role_is_blocked( $type ) {
	if ( is_super_admin() ) {
		return false;
	}

	$advanced = seopress_get_service( 'AdvancedOption' );

	if ( 'CONTENT_ANALYSIS' === $type ) {
		$blocked_roles = $advanced->getSecurityMetaboxRoleContentAnalysis();
	} else {
		$blocked_roles = $advanced->getSecurityMetaboxRole();
	}

	if ( empty( $blocked_roles ) || ! is_array( $blocked_roles ) ) {
		return false;
	}

	$user = wp_get_current_user();
	if ( ! $user || empty( $user->roles ) ) {
		return false;
	}

	foreach ( (array) $user->roles as $role ) {
		if ( isset( $blocked_roles[ $role ] ) && '1' === (string) $blocked_roles[ $role ] ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether the WordPress Abilities API is available on this site.
 *
 * The server-side Abilities API ships with WordPress 6.9. We feature-detect it
 * so SEOPress keeps working untouched on older WordPress versions.
 *
 * @since 9.9.0
 *
 * @return bool
 */
function seopress_abilities_api_available() {
	return function_exists( 'wp_register_ability' ) && class_exists( 'WP_Ability' );
}

/**
 * Whether the admin opted in to expose SEOPress abilities to external clients.
 *
 * Disabled by default. When off, abilities are still callable in PHP/JS locally
 * but are not exposed on the /wp-abilities/v1/ REST endpoint, nor to MCP
 * servers built on top of the Abilities API.
 *
 * @since 9.9.0
 *
 * @return bool
 */
function seopress_abilities_api_rest_enabled() {
	$options = get_option( 'seopress_advanced_option_name' );

	$enabled = is_array( $options ) && ! empty( $options['seopress_advanced_abilities_api_rest'] );

	/**
	 * Filter whether SEOPress abilities are exposed to external clients.
	 *
	 * Since 10.2.0 this also controls MCP exposure, not only the REST API.
	 *
	 * @since 9.9.0
	 *
	 * @param bool $enabled Whether external exposure is enabled.
	 */
	return (bool) apply_filters( 'seopress_abilities_api_rest_enabled', $enabled );
}

/**
 * Build the "meta" array shared by every SEOPress ability.
 *
 * The Abilities API is secure by default: an ability is only reachable by
 * external clients when it opts in explicitly.
 *
 * - meta.mcp.public   is what the WordPress MCP Adapter actually reads to decide
 *                     whether an ability is exposed to MCP clients. It has no
 *                     fallback on meta.public, so this key is the one that makes
 *                     the abilities show up as MCP tools.
 * - meta.public       is the high-level flag of the Abilities API. WordPress core
 *                     starts honouring it for the REST API in 7.1, and other
 *                     clients read it today, so we keep it in sync.
 * - meta.show_in_rest is still required for REST access on WordPress 6.9/7.0.
 * - meta.mcp.type     tells MCP servers to surface the ability as a tool.
 *
 * @since 10.2.0
 *
 * @param array $annotations Optional MCP annotations (readonly, destructive, idempotent).
 *
 * @return array
 */
function seopress_abilities_api_meta( $annotations = array() ) {
	$exposed = seopress_abilities_api_rest_enabled();

	return array(
		'public'       => $exposed,
		'show_in_rest' => $exposed,
		'mcp'          => array(
			'public' => $exposed,
			'type'   => 'tool',
		),
		'annotations'  => $annotations,
	);
}

/**
 * Check if the page is one of ours.
 *
 * @return bool
 */
function is_seopress_page() {
	if ( ! is_admin() ) {
		return false;
	}

	$page      = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : null;
	$post_type = isset( $_REQUEST['post_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ) : null;

	if ( $page ) {
		return strpos( $page, 'seopress' ) === 0;
	}

	if ( $post_type ) {
		if ( is_array( $post_type ) && ! empty( $post_type ) ) {
			return strpos( $post_type[0], 'seopress' ) === 0;
		}
		return strpos( $post_type, 'seopress' ) === 0;
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
	if ( is_seopress_page() ) {
		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		if ( function_exists( 'seopress_admin_notices' ) ) {
			add_action( 'admin_notices', 'seopress_admin_notices' );
		}
		// Guard on the constant, not only is_plugin_active(): a plugin can be
		// listed as active while its main file did not finish loading (it
		// crashed on init, or the active_plugins state is stale within the
		// request), in which case its version constant is undefined and
		// version_compare() would fatal on PHP 8+.
		//
		// Guard on function_exists() too: these callbacks live in the other
		// plugins, which are free to drop them (the Insights license reminder
		// moved to React in 3.1). A version_compare() on its own keeps passing
		// once the function is gone, and admin_notices then fatals on an
		// invalid callback.
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) && defined( 'SEOPRESS_PRO_VERSION' ) && function_exists( 'seopress_pro_admin_notices' ) ) {
			if ( version_compare( SEOPRESS_PRO_VERSION, '6.4', '>=' ) ) {
				add_action( 'admin_notices', 'seopress_pro_admin_notices' );
			}
		}
		if ( is_plugin_active( 'wp-seopress-insights/seopress-insights.php' ) && defined( 'SEOPRESS_INSIGHTS_VERSION' ) && function_exists( 'seopress_insights_notices' ) ) {
			if ( version_compare( SEOPRESS_INSIGHTS_VERSION, '1.8.1', '>=' ) ) {
				add_action( 'admin_notices', 'seopress_insights_notices' );
			}
		}
	}
}
add_action( 'in_admin_header', 'seopress_remove_other_notices', 1000 ); // Keep this value high to remove other notices.

/**
 * Only add our notices on our pages.
 *
 * @since 8.2.0
 *
 * @return bool
 */
function seopress_remove_other_plugin_notices() {
	if ( is_seopress_page() ) {
		// SEOKEY plugin doesn't hook properly, we have to make a specific case.
		remove_all_filters( 'seokey_filter_admin_notices_launch', 10 );
	}
}
add_action( 'admin_init', 'seopress_remove_other_plugin_notices' );

/**
 * We replace the WP action by ours.
 *
 * @since 3.8.2
 *
 * @return bool
 */
function seopress_admin_notices() {
	do_action( 'seopress_admin_notices' );
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
function seopress_if_key_exists( array $arr, $key ) {
	// is in base array?
	if ( array_key_exists( $key, $arr ) ) {
		return true;
	}

	// Check arrays contained in this array.
	foreach ( $arr as $element ) {
		if ( is_array( $element ) ) {
			if ( seopress_if_key_exists( $element, $key ) ) {
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
function sp_submit_button( $value = '', $classes = 'btn btnPrimary', $type = 'submit' ) {
	if ( '' === $value ) {
		$value = __( 'Save changes', 'wp-seopress' );
	}

	// Use esc_attr_e to escape attributes in the output.
	$html = '<p class="submit"><input id="submit" name="submit" type="' . esc_attr( $type ) . '" class="' . esc_attr( $classes ) . '" value="' . esc_attr( $value ) . '"/></p>';

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
	// Classic Editor compatibility.
	global $pagenow;
	if ( function_exists( 'get_current_screen' ) && method_exists( get_current_screen(), 'is_block_editor' ) && true === get_current_screen()->is_block_editor() ) {
		$btn_classes_secondary = 'components-button is-secondary';
	} elseif ( isset( $pagenow ) && $pagenow === 'term.php' ) {
		// The term edit screen loads wp-components, so use the native secondary
		// button styling there (matching the block editor) instead of the classic
		// button, which renders these tag buttons too tall and inconsistent.
		$btn_classes_secondary = 'components-button is-secondary';
	} elseif ( isset( $pagenow ) && ( $pagenow === 'post.php' || $pagenow === 'post-new.php' ) ) {
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
 * @param string $feature Feature name to check.
 *
 * @return string|null Toggle option value if exists, null otherwise.
 */
function seopress_get_toggle_option( $feature ) {
	$seopress_get_toggle_option = get_option( 'seopress_toggle' );
	if ( ! empty( $seopress_get_toggle_option ) ) {
		foreach ( $seopress_get_toggle_option as $key => $seopress_get_toggle_value ) {
			$options[ $key ] = $seopress_get_toggle_value;
			if ( isset( $seopress_get_toggle_option[ 'toggle-' . $feature ] ) ) {
				return $seopress_get_toggle_option[ 'toggle-' . $feature ];
			}
		}
	}
}

/**
 * Disable Add to cart GA tracking code on archive page / related products for Elementor PRO to avoid a JS conflict.
 *
 * @since 5.3
 * @return empty string
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
	add_filter( 'seopress_gtag_ec_add_to_cart_archive_ev', 'sp_elementor_gtag_ec_add_to_cart_archive_ev' );
	function sp_elementor_gtag_ec_add_to_cart_archive_ev( $js ) {
		return '';
	}
}

/**
 * Helper function needed for PHP 8.1 compatibility with "current" function
 * Get mangled object vars.
 *
 * @since 6.2.0
 */
function seopress_maybe_mangled_object_vars( $data ) {
	if ( ! function_exists( 'get_mangled_object_vars' ) ) {
		return $data;
	}

	if ( ! is_object( $data ) ) {
		return $data;
	}

	return get_mangled_object_vars( $data );
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
function seopress_instant_indexing_generate_api_key_fn( $init = false ) {
	$options = get_option( 'seopress_instant_indexing_option_name' ) ? get_option( 'seopress_instant_indexing_option_name' ) : array();

	// Generate a 32-char hexadecimal key, which is a valid IndexNow key
	// (8-128 chars from [A-Za-z0-9-]). It is stored as-is so the value shown in
	// the settings matches the public verification .txt file. Legacy keys that
	// were stored base64-encoded are still handled on read by
	// seopress_instant_indexing_get_api_key().
	$api_key = wp_generate_uuid4();
	$api_key = preg_replace( '[-]', '', $api_key );
	$options['seopress_instant_indexing_bing_api_key'] = $api_key;

	if ( true === $init ) {
		$options['seopress_instant_indexing_automate_submission'] = '1';
	}

	update_option( 'seopress_instant_indexing_option_name', $options );

	if ( false === $init ) {
		wp_send_json_success();
	}
}

/**
 * Regenerate SEO issues after content analysis is saved.
 *
 * This ensures the Site Audit admin page shows up-to-date issues
 * when content analysis is refreshed from the frontend.
 *
 * @since 9.4.1
 *
 * @param int   $post_id  The post ID.
 * @param array $items    The analysis data.
 * @param array $keywords The keywords.
 * @param array $data     The raw content analysis data.
 */
function seopress_regenerate_seo_issues_after_content_analysis( $post_id, $items, $keywords, $data ) {
	// Only run if Pro version is active.
	if ( ! function_exists( 'seopress_pro_get_service' ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	// Create a fresh instance to ensure Pro services are available in constructor.
	// The singleton from the service container might have been instantiated before
	// the Pro plugin loaded, causing the SEO issues repository/database to be null.
	$get_content = new \SEOPress\Services\ContentAnalysis\GetContent();

	// getAnalyzes() will:
	// 1. Read fresh data from ContentAnalysisDatabase (which was just saved with fresh keywords)
	// 2. Run all analyze* methods
	// 3. Each method deletes old issues of that type and saves new ones.
	$get_content->getAnalyzes( $post );
}
add_action( 'seopress_content_analysis_saved', 'seopress_regenerate_seo_issues_after_content_analysis', 10, 4 );
