<?php
/**
 * Options Matomo
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Preload Matomo resources
 *
 * @param array  $urls The URLs to preload.
 * @param string $relation_type The relation type.
 * @return array The URLs to preload.
 */
function seopress_resource_hints( $urls, $relation_type ) {
	if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoEnable() ) {
		if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() ) && ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() ) ) {
			if ( 'preconnect' === $relation_type ) {
				$seopress_matomo_src = 'cdn.matomo.cloud/' . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId();

				if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSelfHosted() === '1' ) {
					$seopress_matomo_src = seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId();
				}

				$urls[] = array(
					'href' => 'https://' . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() . '/',
					'crossorigin',
				);
			}
		}
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'seopress_resource_hints', 10, 2 );

/**
 * Build Custom Matomo
 *
 * @param bool $echo Whether to echo the output.
 * @return string The Matomo JS code.
 */
function seopress_matomo_js( $echo ) {
	if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoEnable() && ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() ) && ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() ) ) {
		// Init.
		$seopress_matomo_config = array();
		$seopress_matomo_event  = array();

		$seopress_matomo_html  = "\n";
		$seopress_matomo_html .= "<script async>
var _paq = window._paq || [];\n";

		// Subdomains.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSubdomains() === '1' ) {
			$parse_url = wp_parse_url( get_home_url() );
			if ( ! empty( $parse_url['host'] ) ) {
				$seopress_matomo_config['subdomains'] = "_paq.push(['setCookieDomain', '*." . $parse_url['host'] . "']);\n";
				$seopress_matomo_config['subdomains'] = apply_filters( 'seopress_matomo_cookie_domain', $seopress_matomo_config['subdomains'] );
			}
		}

		// Site domain.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteDomain() === '1' ) {
			$seopress_matomo_config['site_domain'] = "_paq.push(['setDocumentTitle', document.domain + '/' + document.title]);\n";
			$seopress_matomo_config['site_domain'] = apply_filters( 'seopress_matomo_site_domain', $seopress_matomo_config['site_domain'] );
		}

		// DNT.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoDnt() === '1' ) {
			$seopress_matomo_config['dnt'] = "_paq.push(['setDoNotTrack', true]);\n";
			$seopress_matomo_config['dnt'] = apply_filters( 'seopress_matomo_dnt', $seopress_matomo_config['dnt'] );
		}

		// Disable cookies.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoNoCookies() === '1' ) {
			$seopress_matomo_config['no_cookies'] = "_paq.push(['disableCookies']);\n";
			$seopress_matomo_config['no_cookies'] = apply_filters( 'seopress_matomo_disable_cookies', $seopress_matomo_config['no_cookies'] );
		}

		// Cross domains.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoCrossDomain() === '1' && seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoCrossDomainSites() !== '' ) {
			$domains = array_map( 'trim', array_filter( explode( ',', seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoCrossDomainSites() ) ) );

			if ( ! empty( $domains ) ) {
				$domains_count = count( $domains );

				$link_domains = '';

				foreach ( $domains as $key => $domain ) {
					$link_domains .= "'" . $domain . "'";
					if ( $key < $domains_count - 1 ) {
						$link_domains .= ',';
					}
				}
				$seopress_matomo_config['set_domains'] = "_paq.push(['setDomains', [" . $link_domains . "]]);\n_paq.push(['enableCrossDomainLinking']);\n";
				$seopress_matomo_config['set_domains'] = apply_filters( 'seopress_matomo_linker', $seopress_matomo_config['set_domains'] );
			}
		}

		// Link tracking.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoLinkTracking() === '1' ) {
			$seopress_matomo_config['link_tracking'] = "_paq.push(['enableLinkTracking']);\n";
			$seopress_matomo_config['link_tracking'] = apply_filters( 'seopress_matomo_link_tracking', $seopress_matomo_config['link_tracking'] );
		}

		// No heatmaps.
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoNoHeatmaps() === '1' ) {
			$seopress_matomo_config['no_heatmaps'] = "_paq.push(['HeatmapSessionRecording::disable']);\n";
			$seopress_matomo_config['no_heatmaps'] = apply_filters( 'seopress_matomo_no_heatmaps', $seopress_matomo_config['no_heatmaps'] );
		}

		// Dimensions.
		$cd_author_option = seopress_get_service( 'GoogleAnalyticsOption' )->getCdAuthor();
		if ( ! empty( $cd_author_option ) && 'none' !== $cd_author_option ) {
			if ( is_singular() ) {
				$seopress_matomo_event['cd_author'] = "_paq.push(['setCustomVariable', '" . substr( $cd_author_option, -1 ) . "', '" . __( 'Authors', 'wp-seopress' ) . "', '" . get_the_author() . "', 'visit']);\n";
				$seopress_matomo_event['cd_author'] = apply_filters( 'seopress_matomo_cd_author_ev', $seopress_matomo_event['cd_author'] );
			}
		}

		$cd_category_option = seopress_get_service( 'GoogleAnalyticsOption' )->getCdCategory();
		if ( ! empty( $cd_category_option ) && 'none' !== $cd_category_option ) {
			if ( is_single() && has_category() ) {
				$categories = get_the_category();

				if ( ! empty( $categories ) ) {
					$get_first_category = esc_html( $categories[0]->name );
				}
				$seopress_matomo_event['cd_categories'] = "_paq.push(['setCustomVariable', '" . substr( $cd_category_option, -1 ) . "', '" . __( 'Categories', 'wp-seopress' ) . "', '" . $get_first_category . "', 'visit']);\n";
				$seopress_matomo_event['cd_categories'] = apply_filters( 'seopress_matomo_cd_categories_ev', $seopress_matomo_event['cd_categories'] );
			}
		}

		$cd_tag_option = seopress_get_service( 'GoogleAnalyticsOption' )->getCdTag();
		if ( ! empty( $cd_tag_option ) && 'none' !== $cd_tag_option ) {
			if ( is_single() && has_tag() ) {
				$tags = get_the_tags();
				if ( ! empty( $tags ) ) {
					$seopress_comma_count = count( $tags );
					$get_tags             = '';
					foreach ( $tags as $key => $value ) {
						$get_tags .= esc_html( $value->name );
						if ( $key < $seopress_comma_count - 1 ) {
							$get_tags .= ', ';
						}
					}
				}
				$seopress_matomo_event['cd_tags'] = "_paq.push(['setCustomVariable', '" . substr( $cd_tag_option, -1 ) . "', '" . __( 'Tags', 'wp-seopress' ) . "', '" . $get_tags . "', 'visit']);\n";
				$seopress_matomo_event['cd_tags'] = apply_filters( 'seopress_matomo_cd_tags_ev', $seopress_matomo_event['cd_tags'] );
			}
		}

		$cd_post_type_option = seopress_get_service( 'GoogleAnalyticsOption' )->getCdPostType();
		if ( ! empty( $cd_post_type_option ) && 'none' !== $cd_post_type_option ) {
			if ( is_single() ) {
				$seopress_matomo_event['cd_cpt'] = "_paq.push(['setCustomVariable', '" . substr( $cd_post_type_option, -1 ) . "', '" . __( 'Post types', 'wp-seopress' ) . "', '" . get_post_type() . "', 'visit']);\n";
				$seopress_matomo_event['cd_cpt'] = apply_filters( 'seopress_matomo_cd_cpt_ev', $seopress_matomo_event['cd_cpt'] );
			}
		}

		$cd_logged_in_user_option = seopress_get_service( 'GoogleAnalyticsOption' )->getCdLoggedInUser();
		if ( ! empty( $cd_logged_in_user_option ) && 'none' !== $cd_logged_in_user_option ) {
			if ( wp_get_current_user()->ID ) {
				$seopress_matomo_event['cd_logged_in'] = "_paq.push(['setCustomVariable', '" . substr( $cd_logged_in_user_option, -1 ) . "', '" . __( 'Connected users', 'wp-seopress' ) . "', '" . wp_get_current_user()->ID . "', 'visit']);\n";
				$seopress_matomo_event['cd_logged_in'] = apply_filters( 'seopress_matomo_cd_logged_in_ev', $seopress_matomo_event['cd_logged_in'] );
			}
		}

		// Send data config.
		if ( ! empty( $seopress_matomo_config ) ) {
			foreach ( $seopress_matomo_config as $key => $value ) {
				$seopress_matomo_html .= $value;
			}
		}

		// Send data dimensions.
		if ( ! empty( $seopress_matomo_event ) ) {
			foreach ( $seopress_matomo_event as $key => $value ) {
				$seopress_matomo_html .= $value;
			}
		}

		$seopress_matomo_src = 'cdn.matomo.cloud/' . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId();

		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSelfHosted() === '1' ) {
			$seopress_matomo_src = seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId();
		}

		$seopress_matomo_html .= "_paq.push(['trackPageView']);
(function() {
	var u='https://" . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() . "/';
	_paq.push(['setTrackerUrl', u+'matomo.php']);
	_paq.push(['setSiteId', '" . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() . "']);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.type='text/javascript'; g.async=true; g.defer=true; g.src='https://" . untrailingslashit( $seopress_matomo_src ) . "/matomo.js'; s.parentNode.insertBefore(g,s);
	})();\n";

		$seopress_matomo_html .= '</script>';

		$seopress_matomo_html = apply_filters( 'seopress_matomo_tracking_html', $seopress_matomo_html );

		if ( true === $echo ) {
			echo $seopress_matomo_html;
		} else {
			return $seopress_matomo_html;
		}
	}
}
add_action( 'seopress_matomo_html', 'seopress_matomo_js', 10, 1 );

/**
 * Matomo JS Arguments
 *
 * @return void
 */
function seopress_matomo_js_arguments() {
	$echo = true;
	do_action( 'seopress_matomo_html', $echo );
}

/**
 * Matomo No JS
 *
 * @return void
 */
function seopress_matomo_nojs() {
	$echo = true;
	do_action( 'seopress_matomo_body_html', $echo );
}

add_action( 'seopress_matomo_body_html', 'seopress_matomo_body_js', 10, 1 );

/**
 * Matomo Body JS
 *
 * @param bool $echo Whether to echo the output.
 * @return string The Matomo JS code.
 */
function seopress_matomo_body_js( $echo ) {
	if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoEnable() && ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() ) && ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() ) ) {
		// Init.
		$html = '';

		// No JS.
		$no_js = null;
		if ( seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoNoJS() === '1' ) {
			$no_js = '<noscript><p><img src="https://' . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() . '/matomo.php?idsite=' . seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() . '&amp;rec=1" style="border:0;" alt="" /></p></noscript>';
			$no_js = apply_filters( 'seopress_matomo_no_js', $no_js );
		}

		if ( $no_js ) {
			$html .= $no_js;
		}

		$html = apply_filters( 'seopress_matomo_tracking_body_html', $html );

		if ( true === $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}
}
