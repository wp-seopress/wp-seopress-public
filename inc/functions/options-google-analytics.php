<?php
/**
 * Options google analytics
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Cookies user consent scripts
 *
 * @return void
 */
function seopress_cookies_user_consent_scripts() {
	if ( '1' !== seopress_get_service( 'GoogleAnalyticsOption' )->getEnableOption() ) {
		return;
	}

	if ( '' === seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() ) {
		return;
	}

	if ( isset( $_COOKIE['seopress-user-consent-accept'] ) ) {
		return;
	}

	$js = '
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }';

	// Default.
	$consent = "
    gtag('consent', 'default', {
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'ad_storage': 'denied',
        'analytics_storage': 'denied',
        'wait_for_update': 500,
      }); \n";

	if ( isset( $_COOKIE['seopress-user-consent-close'] ) && '1' === $_COOKIE['seopress-user-consent-close'] ) {
		$consent = "
        gtag('consent', 'default', {
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'ad_storage': 'denied',
            'analytics_storage': 'denied',
            'wait_for_update': 500,
          }); \n";
	}

	$consent = apply_filters( 'seopress_user_consent', $consent );

	$js .= $consent;

	$js .= "gtag('js', new Date()); \n";

	// Measurement ID.
	if ( '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() ) {
		$seopress_gtag_ga4 = "gtag('config', '" . seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() . "');";
		$seopress_gtag_ga4 = apply_filters( 'seopress_gtag_ga4', $seopress_gtag_ga4 );
		$js               .= $seopress_gtag_ga4;
		$js               .= "\n";
	}

	$js .=
	'</script>';

	echo $js;
}

/**
 * Cookies user consent html
 *
 * @return void
 */
function seopress_cookies_user_consent_html() {
	if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getOptOutMsg() ) ) {
		$msg = seopress_get_service( 'GoogleAnalyticsOption' )->getOptOutMsg();
	} elseif ( get_option( 'wp_page_for_privacy_policy' ) ) {
		$msg = __( 'This site uses cookies for analytics and to improve your experience. By clicking Accept, you consent to our use of cookies. Learn more in our <a href="[seopress_privacy_page]">privacy policy</a>.', 'wp-seopress' );
	} else {
		$msg = __( 'This site uses cookies for analytics and to improve your experience. By clicking Accept, you consent to our use of cookies.', 'wp-seopress' );
	}

	if ( get_option( 'wp_page_for_privacy_policy' ) && '' !== $msg ) {
		$seopress_privacy_page = esc_url( get_permalink( get_option( 'wp_page_for_privacy_policy' ) ) );
		$msg                   = str_replace( '[seopress_privacy_page]', $seopress_privacy_page, $msg );
	}

	$msg = apply_filters( 'seopress_rgpd_message', $msg );

	$consent_btn = seopress_get_service( 'GoogleAnalyticsOption' )->getOptOutMessageOk();
	if ( empty( $consent_btn ) || ! $consent_btn ) {
		$consent_btn = __( 'Accept', 'wp-seopress' );
	}

	$close_btn = seopress_get_service( 'GoogleAnalyticsOption' )->getOptOutMessageClose();
	if ( empty( $close_btn ) || ! $close_btn ) {
		$close_btn = __( 'Decline', 'wp-seopress' );
	}

	$user_msg = '<div data-nosnippet class="seopress-user-consent seopress-user-message seopress-user-consent-hide">
        <p>' . $msg . '</p>
        <p>
            <button id="seopress-user-consent-accept" type="button">' . $consent_btn . '</button>
            <button type="button" id="seopress-user-consent-close">' . $close_btn . '</button>
        </p>
    </div>';

	$backdrop = '<div class="seopress-user-consent-backdrop seopress-user-consent-hide"></div>';

	$user_msg = apply_filters( 'seopress_rgpd_full_message', $user_msg, $msg, $consent_btn, $close_btn, $backdrop );

	echo $user_msg . $backdrop;
}

/**
 * Cookies edit choice html
 *
 * @return void
 */
function seopress_cookies_edit_choice_html() {
	$opt_out_edit_choice = seopress_get_service( 'GoogleAnalyticsOption' )->getOptOutEditChoice();
	if ( '1' !== $opt_out_edit_choice ) {
		return;
	}

	$edit_cookie_btn = seopress_get_service( 'GoogleAnalyticsOption' )->getOptOutMessageEdit();
	if ( empty( $edit_cookie_btn ) || ! $edit_cookie_btn ) {
		$edit_cookie_btn = __( 'Cookie preferences', 'wp-seopress' );
	}

	$user_msg = '<div data-nosnippet class="seopress-user-consent seopress-edit-choice">
        <p>
            <button id="seopress-user-consent-edit" type="button">' . $edit_cookie_btn . '</button>
        </p>
    </div>';

	$user_msg = apply_filters( 'seopress_rgpd_edit_message', $user_msg, $edit_cookie_btn );

	echo $user_msg;
}

/**
 * Cookies user consent styles
 *
 * @return void
 */
function seopress_cookies_user_consent_styles() {
	// Width.
	$width = seopress_get_service( 'GoogleAnalyticsOption' )->getCbWidth();

	// Determine if full-width bar mode (100% width) or modal mode
	// Default to full-width if no width is set (more universal and less intrusive).
	$is_full_width = ( empty( $width ) || '100%' === $width || '100' === $width );

	$styles = '<style>.seopress-user-consent {position: fixed;z-index: 8000;display: inline-flex;flex-direction: column;justify-content: center;border: none;box-sizing: border-box;';

	// Alignment (only for modal mode).
	$alignment = seopress_get_service( 'GoogleAnalyticsOption' )->getCbAlign();
	if ( empty( $alignment ) ) {
		$alignment = 'center';
	}

	// Full-width bar mode vs Modal mode.
	if ( $is_full_width ) {
		// Full-width bar mode (improved traditional style).
		$styles .= 'left: 0;right: 0;width: 100%;padding: 18px 24px;';
	} else {
		// Modal mode (new modern style).
		$styles .= 'padding: 24px 28px;max-width:100%;';

		$needle = '%';

		if ( false !== strpos( $width, $needle ) ) {
			$unit = '';
		} else {
			$unit = 'px';
		}

		$styles .= 'width: ' . $width . $unit . ';';

		// Apply horizontal alignment.
		if ( 'left' === $alignment ) {
			$styles .= 'left: 20px;right: auto;';
		} elseif ( 'right' === $alignment ) {
			$styles .= 'right: 20px;left: auto;';
		} else {
			// Center (default).
			$styles .= 'left: 50%;';
		}
	}

	// Position.
	$position = seopress_get_service( 'GoogleAnalyticsOption' )->getCbPos();
	if ( $is_full_width ) {
		// Full-width bar positioning.
		if ( 'top' === $position ) {
			$styles .= 'top:0;';
		} elseif ( 'center' === $position ) {
			$styles .= 'top:50%;transform: translateY(-50%);';
		} else {
			$styles .= 'bottom:0;';
		}
	} else {
		// Modal positioning.
		if ( 'top' === $position ) {
			$styles .= 'top:20px;';
		} elseif ( 'center' === $position ) {
			$styles .= 'top:50%;';
		} else {
			$styles .= 'bottom:20px;';
		}

		// Apply transform based on alignment.
		if ( 'left' === $alignment || 'right' === $alignment ) {
			// No horizontal transform for left/right alignment.
			if ( 'center' === $position ) {
				$styles .= 'transform: translateY(-50%);';
			}
		} else {
			// Center alignment uses translateX.
			if ( 'top' === $position || 'bottom' === $position ) {
				$styles .= 'transform: translateX(-50%);';
			} else {
				$styles .= 'transform: translate(-50%, -50%);';
			}
		}
	}

	// Text alignment.
	$txt_align = seopress_get_service( 'GoogleAnalyticsOption' )->getCbTxtAlign();
	if ( 'left' === $txt_align ) {
		$styles .= 'text-align:left;';
	} elseif ( 'right' === $txt_align ) {
		$styles .= 'text-align:right;';
	} else {
		$styles .= 'text-align:center;';
	}

	// Background color.
	$bg_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBg();
	if ( ! empty( $bg_color ) ) {
		$styles .= 'background:' . $bg_color . ';';
	} else {
		// Modern white background for both modes.
		$styles .= 'background:#FFFFFF;';
	}

	// Modern enhancements - only apply in modal mode.
	if ( ! $is_full_width ) {
		$styles .= 'border-radius: 12px;';
		$styles .= 'box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);';
	} else {
		// Full-width bar gets subtle shadow instead of border.
		if ( 'bottom' === $position || empty( $position ) ) {
			$styles .= 'box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);';
		} else {
			$styles .= 'box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);';
		}
	}

	$styles .= '}';

	// Responsive improvements.
	if ( ! $is_full_width ) {
		// Modal mode responsive - always center horizontally on mobile.
		$styles .= '@media (max-width: 782px) {.seopress-user-consent {';
		$styles .= 'width: calc(100% - 32px) !important;';
		$styles .= 'max-width: calc(100% - 32px) !important;';
		$styles .= 'left: 50% !important;';
		$styles .= 'right: auto !important;';
		$styles .= 'transform: translateX(-50%) !important;';
		$styles .= 'bottom: 16px !important;';
		$styles .= 'top: auto !important;';
		$styles .= 'padding: 20px;';
		$styles .= '}}';
	} else {
		// Full-width mode responsive - stack buttons below text on mobile.
		$styles .= '@media (max-width: 782px) {';
		$styles .= '.seopress-user-consent {padding: 16px;}';
		$styles .= '.seopress-user-consent.seopress-user-message {flex-direction: column !important;align-items: stretch;gap: 16px;}';
		$styles .= '.seopress-user-consent.seopress-user-message p:first-child {margin: 0 !important;text-align: center;}';
		$styles .= '.seopress-user-consent.seopress-user-message p:last-child {width: 100%; flex-direction: column;justify-content: stretch;gap: 10px;}';
		$styles .= '.seopress-user-consent.seopress-user-message button {width: 100% !important;min-width: auto !important;}';
		$styles .= '}';
		$styles .= '}';
	}

	// Paragraph styling with better spacing.
	if ( ! $is_full_width ) {
		// Modal mode - vertical layout.
		$styles .= '.seopress-user-consent.seopress-user-message p:first-child {margin: 0 0 16px 0;line-height: 1.6;}';
	} else {
		// Full-width mode - text on left, buttons on right on desktop.
		$styles .= '.seopress-user-consent.seopress-user-message p:first-child {margin: 0;line-height: 1.6;flex: 1;}';
	}
	$styles .= '.seopress-user-consent p {margin: 0;font-size: 15px;line-height: 1.6;';

	// Text color.
	$txt_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbTxtCol();
	if ( ! empty( $txt_color ) ) {
		$styles .= 'color:' . $txt_color . ';';
	} else {
		$styles .= 'color:#2c3e50;';
	}

	$styles .= '}';

	// Link styling.
	$link_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbLkCol();
	if ( ! empty( $link_color ) ) {
		$styles .= '.seopress-user-consent a{color:' . $link_color . ';';
	} else {
		$styles .= '.seopress-user-consent a{color:#1a1a1a;';
	}
	$styles .= 'text-decoration: underline;font-weight: 500;}';
	$styles .= '.seopress-user-consent a:hover{text-decoration: none;opacity: 0.7;}';

	// Button container for better layout.
	if ( ! $is_full_width ) {
		// Modal mode - centered buttons below text.
		$styles .= '.seopress-user-consent.seopress-user-message p:last-child {display: flex;gap: 12px;justify-content: center;flex-wrap: wrap;margin: 0;}';
	} else {
		// Full-width mode - inline buttons to the right on desktop.
		$styles .= '.seopress-user-consent.seopress-user-message {flex-direction: row;align-items: center;gap: 24px;}';
		$styles .= '.seopress-user-consent.seopress-user-message p:last-child {display: flex;gap: 12px;justify-content: flex-end;flex-wrap: nowrap;margin: 0;flex-shrink: 0;}';
	}

	// Modern button styling.
	$styles .= '.seopress-user-consent button {';
	$styles .= 'padding: 12px 24px;';
	$styles .= 'border: none;';
	$styles .= 'border-radius: 6px;';
	$styles .= 'font-size: 15px;';
	$styles .= 'font-weight: 600;';
	$styles .= 'cursor: pointer;';
	$styles .= 'transition: all 0.2s ease;';
	$styles .= 'flex: 0 1 auto;';
	$styles .= 'min-width: 120px;';

	// Btn background color.
	$btn_bg_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnBg();
	if ( ! empty( $btn_bg_color ) ) {
		$styles .= 'background:' . $btn_bg_color . ';';
	} else {
		// Modern primary button color.
		$styles .= 'background:#1a1a1a;';
	}

	// Btn text color.
	$btn_txt_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnCol();
	if ( ! empty( $btn_txt_color ) ) {
		$styles .= 'color:' . $btn_txt_color . ';';
	} else {
		$styles .= 'color:#ffffff;';
	}

	$styles .= '}';

	// Button hover state.
	$styles .= '.seopress-user-consent button:hover{';
	$styles .= 'transform: translateY(-1px);';
	$styles .= 'box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);';

	// Background hover color.
	$bg_hover_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnBgHov();
	if ( ! empty( $bg_hover_color ) ) {
		$styles .= 'background:' . $bg_hover_color . ';';
	} else {
		$styles .= 'background:#000000;';
	}

	// Text hover color.
	$txt_hover_color = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnColHov();
	if ( ! empty( $txt_hover_color ) ) {
		$styles .= 'color:' . $txt_hover_color . ';';
	}

	$styles .= '}';

	// Secondary button (Decline).
	$styles .= '#seopress-user-consent-close{';
	$styles .= 'border: 2px solid #d1d5db !important;';

	// Background secondary button.
	$bg_secondary_btn = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnSecBg();
	if ( ! empty( $bg_secondary_btn ) ) {
		$styles .= 'background:' . $bg_secondary_btn . ';';
	} else {
		$styles .= 'background:#ffffff;';
	}

	// Color secondary button.
	$color_secondary_btn = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnSecCol();
	if ( ! empty( $color_secondary_btn ) ) {
		$styles .= 'color:' . $color_secondary_btn . ';';
	} else {
		$styles .= 'color:#374151;';
	}

	$styles .= '}';

	// Secondary button hover.
	$styles .= '#seopress-user-consent-close:hover{';

	// Background secondary button hover.
	$bg_secondary_btn_hover = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnSecBgHov();
	if ( ! empty( $bg_secondary_btn_hover ) ) {
		$styles .= 'background:' . $bg_secondary_btn_hover . ';';
	} else {
		$styles .= 'background:#f9fafb;';
		$styles .= 'border-color: #9ca3af !important;';
	}

	// Color secondary button hover.
	$color_secondary_btn_hover = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBtnSecColHov();
	if ( ! empty( $color_secondary_btn_hover ) ) {
		$styles .= 'color:' . $color_secondary_btn_hover . ';';
	} else {
		$styles .= 'color:#1f2937;';
	}

	$styles .= '}';

	// Mobile button adjustments.
	$styles .= '@media (max-width: 480px) {';
	$styles .= '.seopress-user-consent.seopress-user-message p:last-child {flex-direction: column;}';
	$styles .= '.seopress-user-consent button {width: 100%;min-width: auto;}';
	$styles .= '}';

	$cb_backdrop = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBackdrop();
	if ( ! empty( $cb_backdrop ) ) {
		$bg_backdrop = seopress_get_service( 'GoogleAnalyticsOption' )->getCbBackdropBg();
		if ( empty( $bg_backdrop ) || ! $bg_backdrop ) {
			$bg_backdrop = 'rgba(0,0,0,.5)';
		}

		$styles .= '.seopress-user-consent-backdrop{';
		$styles .= 'display: flex;';
		$styles .= '-webkit-box-align: center;';
		$styles .= '-webkit-align-items: center;';
		$styles .= '-ms-flex-align: center;';
		$styles .= 'align-items: center;';
		$styles .= 'background: ' . $bg_backdrop . ';';
		$styles .= 'backdrop-filter: blur(2px);';
		$styles .= 'bottom: 0;';
		$styles .= '-webkit-box-orient: vertical;';
		$styles .= '-webkit-box-direction: normal;';
		$styles .= '-webkit-flex-direction: column;';
		$styles .= '-ms-flex-direction: column;';
		$styles .= 'flex-direction: column;';
		$styles .= 'left: 0;';
		$styles .= '-webkit-overflow-scrolling: touch;';
		$styles .= 'overflow-y: auto;';
		$styles .= 'position: fixed;';
		$styles .= 'right: 0;';
		$styles .= '-webkit-tap-highlight-color: transparent;';
		$styles .= 'top: 0;';
		$styles .= 'z-index: 7998;';
		$styles .= '}';
	}

	$styles .= '.seopress-user-consent-hide{display:none !important;}';

	$styles .= '.seopress-edit-choice{
        background: none;
        justify-content: flex-start;
        align-items: flex-start;
        z-index: 7999;
        border: none;
        width: auto;
        transform: none !important;
        left: 20px !important;
        right: auto !important;
        bottom: 20px;
        top: auto;
        box-shadow: none;
        padding: 0;
    }';

	$styles .= '</style>';

	$styles = apply_filters( 'seopress_rgpd_full_message_styles', $styles );

	echo $styles;
}

/**
 * Cookies user consent render
 *
 * @return void
 */
function seopress_cookies_user_consent_render() {
	$hook = seopress_get_service( 'GoogleAnalyticsOption' )->getHook();
	if ( empty( $hook ) || ! $hook ) {
		$hook = 'wp_head';
	}

	add_action( $hook, 'seopress_cookies_user_consent_html' );
	add_action( $hook, 'seopress_cookies_edit_choice_html' );
	add_action( $hook, 'seopress_cookies_user_consent_styles' );
	add_action( 'wp_head', 'seopress_cookies_user_consent_scripts' );
}

if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getDisable() ) {
	if ( is_user_logged_in() ) {
		global $wp_roles;

		// Get current user role.
		if ( isset( wp_get_current_user()->roles[0] ) ) {
			$seopress_user_role = wp_get_current_user()->roles[0];
			// If current user role matchs values from SEOPress GA settings then apply.
			if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getRoles() ) ) {
				if ( array_key_exists( $seopress_user_role, seopress_get_service( 'GoogleAnalyticsOption' )->getRoles() ) ) {
					// Do nothing.
				} else {
					seopress_cookies_user_consent_render();
				}
			} else {
				seopress_cookies_user_consent_render();
			}
		} else {
			seopress_cookies_user_consent_render();
		}
	} else {
		seopress_cookies_user_consent_render();
	}
}

// Build Custom GA.
function seopress_google_analytics_js( $echo ) {
	if ( '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() && '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getEnableOption() ) {
		// Init.
		$tracking_id                      = seopress_get_service( 'GoogleAnalyticsOption' )->getGA4();
		$seopress_google_analytics_config = array();
		$seopress_google_analytics_event  = array();

		$seopress_google_analytics_html = "\n";

		if ( ! isset( $_COOKIE['seopress-user-consent-close'] ) ) {
			$seopress_google_analytics_html .=
			"<script async src='https://www.googletagmanager.com/gtag/js?id=" . $tracking_id . "'></script>";
		}

		$seopress_google_analytics_html .= '<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}';

		// Consent mode v2.
		$consent = '';

		$update = ( ! empty( $_POST['consent'] ) && $_POST['consent'] === 'update' ) ? true : false;

		if ( true === $update ) {
			if ( isset( $_COOKIE['seopress-user-consent-accept'] ) && '1' === $_COOKIE['seopress-user-consent-accept'] ) {
				$consent = "gtag('consent', 'update', {
                    'ad_storage': 'granted',
                    'ad_user_data': 'granted',
                    'ad_personalization': 'granted',
                    'analytics_storage': 'granted'
                });";
			}
			if ( isset( $_COOKIE['seopress-user-consent-close'] ) && '1' === $_COOKIE['seopress-user-consent-close'] ) {
				$consent = "gtag('consent', 'update', {
                    'ad_storage': 'denied',
                    'ad_user_data': 'denied',
                    'ad_personalization': 'denied',
                    'analytics_storage': 'denied'
                });";
			}
		} elseif ( isset( $_COOKIE['seopress-user-consent-accept'] ) && '1' === $_COOKIE['seopress-user-consent-accept'] ) {
			$consent = "
            gtag('consent', 'default', {
                'ad_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted',
                'analytics_storage': 'granted',
                'wait_for_update': 500,
            }); \n";
		}

		$consent = apply_filters( 'seopress_user_consent', $consent );

		$seopress_google_analytics_html .= $consent;

		$seopress_google_analytics_html .= "gtag('js', new Date());\n";

		if ( is_ssl() ) {
			// Set cookie domain to auto (prevent issues with subdomains).
			$seopress_google_analytics_html .= "gtag('set', 'cookie_domain', 'auto');\n";
			// Set cookie flags to SameSite=None;Secure (prevent issues with subdomains).
			$seopress_google_analytics_html .= "gtag('set', 'cookie_flags', 'SameSite=None;Secure');\n";
		}

		$features = '';

		if ( ! isset( $_COOKIE['seopress-user-consent-close'] ) ) {
			// Dimensions.
			$seopress_google_analytics_config['cd']['cd_hook'] = apply_filters( 'seopress_gtag_cd_hook_cf', isset( $seopress_google_analytics_config['cd']['cd_hook'] ) );
			if ( ! has_filter( 'seopress_gtag_cd_hook_cf' ) ) {
				unset( $seopress_google_analytics_config['cd']['cd_hook'] );
			}

			$seopress_google_analytics_event['cd_hook'] = apply_filters( 'seopress_gtag_cd_hook_ev', isset( $seopress_google_analytics_event['cd_hook'] ) );
			if ( ! has_filter( 'seopress_gtag_cd_hook_ev' ) ) {
				unset( $seopress_google_analytics_config['cd']['cd_hook'] );
			}

			$cd_author_option         = seopress_get_service( 'GoogleAnalyticsOption' )->getCdAuthor();
			$cd_category_option       = seopress_get_service( 'GoogleAnalyticsOption' )->getCdCategory();
			$cd_tag_option            = seopress_get_service( 'GoogleAnalyticsOption' )->getCdTag();
			$cd_post_type_option      = seopress_get_service( 'GoogleAnalyticsOption' )->getCdPostType();
			$cd_logged_in_user_option = seopress_get_service( 'GoogleAnalyticsOption' )->getCdLoggedInUser();
			if ( ( ! empty( $cd_author_option ) && 'none' !== $cd_author_option )
					|| ( ! empty( $cd_category_option ) && 'none' !== $cd_category_option )
					|| ( ! empty( $cd_tag_option ) && 'none' !== $cd_tag_option )
					|| ( ! empty( $cd_post_type_option ) && 'none' !== $cd_post_type_option )
					|| ( ! empty( $cd_logged_in_user_option ) && 'none' !== $cd_logged_in_user_option )
					|| ( '' !== isset( $seopress_google_analytics_config['cd']['cd_hook'] ) && '' !== isset( $seopress_google_analytics_event['cd_hook'] ) )
				) {
				$seopress_google_analytics_config['cd']['cd_start'] = '{';
			} else {
				unset( $seopress_google_analytics_config['cd'] );
			}

			if ( ! empty( $cd_author_option ) ) {
				if ( 'none' !== $cd_author_option ) {
					if ( is_singular() ) {
						$seopress_google_analytics_config['cd']['cd_author'] = "'" . $cd_author_option . "': 'cd_author',";

						$seopress_google_analytics_event['cd_author'] = "gtag('event', '" . __( 'Authors', 'wp-seopress' ) . "', {'cd_author': '" . get_the_author() . "', 'non_interaction': true});";

						$seopress_google_analytics_config['cd']['cd_author'] = apply_filters( 'seopress_gtag_cd_author_cf', $seopress_google_analytics_config['cd']['cd_author'] );

						$seopress_google_analytics_event['cd_author'] = apply_filters( 'seopress_gtag_cd_author_ev', $seopress_google_analytics_event['cd_author'] );
					}
				}
			}
			if ( ! empty( $cd_category_option ) ) {
				if ( 'none' !== $cd_category_option ) {
					if ( is_single() && has_category() ) {
						$categories = get_the_category();

						if ( ! empty( $categories ) ) {
							$get_first_category = esc_html( $categories[0]->name );
						}

						$seopress_google_analytics_config['cd']['cd_categories'] = "'" . $cd_category_option . "': 'cd_categories',";

						$seopress_google_analytics_event['cd_categories'] = "gtag('event', '" . __( 'Categories', 'wp-seopress' ) . "', {'cd_categories': '" . $get_first_category . "', 'non_interaction': true});";

						$seopress_google_analytics_config['cd']['cd_categories'] = apply_filters( 'seopress_gtag_cd_categories_cf', $seopress_google_analytics_config['cd']['cd_categories'] );

						$seopress_google_analytics_event['cd_categories'] = apply_filters( 'seopress_gtag_cd_categories_ev', $seopress_google_analytics_event['cd_categories'] );
					}
				}
			}

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

					$seopress_google_analytics_config['cd']['cd_tags'] = "'" . $cd_tag_option . "': 'cd_tags',";

					$seopress_google_analytics_event['cd_tags'] = "gtag('event', '" . __( 'Tags', 'wp-seopress' ) . "', {'cd_tags': '" . $get_tags . "', 'non_interaction': true});";

					$seopress_google_analytics_config['cd']['cd_tags'] = apply_filters( 'seopress_gtag_cd_tags_cf', $seopress_google_analytics_config['cd']['cd_tags'] );

					$seopress_google_analytics_event['cd_tags'] = apply_filters( 'seopress_gtag_cd_tags_ev', $seopress_google_analytics_event['cd_tags'] );
				}
			}

			if ( ! empty( $cd_post_type_option ) && 'none' !== $cd_post_type_option ) {
				if ( is_single() ) {
					$seopress_google_analytics_config['cd']['cd_cpt'] = "'" . $cd_post_type_option . "': 'cd_cpt',";

					$seopress_google_analytics_event['cd_cpt'] = "gtag('event', '" . __( 'Post types', 'wp-seopress' ) . "', {'cd_cpt': '" . get_post_type() . "', 'non_interaction': true});";

					$seopress_google_analytics_config['cd']['cd_cpt'] = apply_filters( 'seopress_gtag_cd_cpt_cf', $seopress_google_analytics_config['cd']['cd_cpt'] );

					$seopress_google_analytics_event['cd_cpt'] = apply_filters( 'seopress_gtag_cd_cpt_ev', $seopress_google_analytics_event['cd_cpt'] );
				}
			}

			if ( ! empty( $cd_logged_in_user_option ) && 'none' !== $cd_logged_in_user_option ) {
				if ( wp_get_current_user()->ID ) {
					$seopress_google_analytics_config['cd']['cd_logged_in'] = "'" . $cd_logged_in_user_option . "': 'cd_logged_in',";

					$seopress_google_analytics_event['cd_logged_in'] = "gtag('event', '" . __( 'Connected users', 'wp-seopress' ) . "', {'cd_logged_in': '" . wp_get_current_user()->ID . "', 'non_interaction': true});";

					$seopress_google_analytics_config['cd']['cd_logged_in'] = apply_filters( 'seopress_gtag_cd_logged_in_cf', $seopress_google_analytics_config['cd']['cd_logged_in'] );

					$seopress_google_analytics_event['cd_logged_in'] = apply_filters( 'seopress_gtag_cd_logged_in_ev', $seopress_google_analytics_event['cd_logged_in'] );
				}
			}

			if ( ! empty( $seopress_google_analytics_config['cd']['cd_logged_in'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_cpt'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_tags'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_categories'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_author'] ) ||
					( ! empty( $seopress_google_analytics_config['cd']['cd_hook'] ) && ! empty( $seopress_google_analytics_event['cd_hook'] ) ) ) {
				$seopress_google_analytics_config['cd']['cd_end'] = '}, ';
			} else {
				$seopress_google_analytics_config['cd']['cd_start'] = '';
			}

			// External links.
			if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getLinkTrackingEnable() ) ) {
				$seopress_google_analytics_click_event['link_tracking'] =
				"window.addEventListener('load', function () {
    var links = document.querySelectorAll('a');
    for (let i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function(e) {
            var n = this.href.includes('" . wp_parse_url( get_home_url(), PHP_URL_HOST ) . "');
            if (n == false) {
                gtag('event', 'click', {'event_category': 'external links','event_label' : this.href});
            }
        });
        }
    });
    ";
				$seopress_google_analytics_click_event['link_tracking'] = apply_filters( 'seopress_gtag_link_tracking_ev', $seopress_google_analytics_click_event['link_tracking'] );
				$seopress_google_analytics_html                        .= $seopress_google_analytics_click_event['link_tracking'];
			}

			// Downloads tracking.
			if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getDownloadTrackingEnable() ) ) {
				$download_tracking_option = seopress_get_service( 'GoogleAnalyticsOption' )->getDownloadTracking();
				if ( ! empty( $download_tracking_option ) ) {
					$seopress_google_analytics_click_event['download_tracking'] =
					"window.addEventListener('load', function () {
        var donwload_links = document.querySelectorAll('a');
        for (let j = 0; j < donwload_links.length; j++) {
            donwload_links[j].addEventListener('click', function(e) {
                var down = this.href.match(/.*\.(" . $download_tracking_option . ")(\?.*)?$/);
                if (down != null) {
                    gtag('event', 'click', {'event_category': 'downloads','event_label' : this.href});
                }
            });
            }
        });
    ";
					$seopress_google_analytics_click_event['download_tracking'] = apply_filters( 'seopress_gtag_download_tracking_ev', $seopress_google_analytics_click_event['download_tracking'] );
					$seopress_google_analytics_html                            .= $seopress_google_analytics_click_event['download_tracking'];
				}
			}

			// Affiliate tracking.
			if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getAffiliateTrackingEnable() ) ) {
				$affiliate_tracking_option = seopress_get_service( 'GoogleAnalyticsOption' )->getAffiliateTracking();
				if ( ! empty( $affiliate_tracking_option ) ) {
					$seopress_google_analytics_click_event['outbound_tracking'] =
					"window.addEventListener('load', function () {
        var outbound_links = document.querySelectorAll('a');
        for (let k = 0; k < outbound_links.length; k++) {
            outbound_links[k].addEventListener('click', function(e) {
                var out = this.href.match(/(?:\/" . $affiliate_tracking_option . "\/)/gi);
                if (out != null) {
                    gtag('event', 'click', {'event_category': 'outbound/affiliate','event_label' : this.href});
                }
            });
            }
        });";
					$seopress_google_analytics_click_event['outbound_tracking'] = apply_filters( 'seopress_gtag_outbound_tracking_ev', $seopress_google_analytics_click_event['outbound_tracking'] );
					$seopress_google_analytics_html                            .= $seopress_google_analytics_click_event['outbound_tracking'];
				}
			}

			// Phone tracking.
			if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getPhoneTracking() ) ) {
				$seopress_google_analytics_click_event['phone_tracking'] =
				"window.addEventListener('load', function () {
        var links = document.querySelectorAll('a');
        for (let i = 0; i < links.length; i++) {
            links[i].addEventListener('click', function(e) {
                var n = this.href.includes('tel:');
                if (n === true) {
                    gtag('event', 'click', {'event_category': 'phone','event_label' : this.href.slice(4)});
                }
            });
        }
    });";
				$seopress_google_analytics_click_event['phone_tracking'] = apply_filters( 'seopress_gtag_phone_tracking_ev', $seopress_google_analytics_click_event['phone_tracking'] );
				$seopress_google_analytics_html                         .= $seopress_google_analytics_click_event['phone_tracking'];
			}

			do_action( 'seopress_ga4_before_sending_data' );

			// Send data.
			$features = '';
			if ( ! empty( $seopress_google_analytics_config['cd']['cd_logged_in'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_cpt'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_tags'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_categories'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_author'] ) ||
					! empty( $seopress_google_analytics_config['cd']['cd_hook'] ) ) {
				$seopress_google_analytics_config['cd']['cd_start'] = "'custom_map': {";
			}
			if ( ! empty( $seopress_google_analytics_config ) ) {
				if ( ! empty( $seopress_google_analytics_config['cd']['cd_start'] ) ) {
					array_unshift( $seopress_google_analytics_config['cd'], $seopress_google_analytics_config['cd']['cd_start'] );
					unset( $seopress_google_analytics_config['cd']['cd_start'] );
				}
				$features = ', {';
				foreach ( $seopress_google_analytics_config as $key => $feature ) {
					if ( 'cd' === $key ) {
						foreach ( $feature as $_key => $cd ) {
							$features .= $cd;
						}
					} else {
						$features .= $feature;
					}
				}
				$features .= '}';
			}
		}

		// Measurement ID.
		if ( '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() ) {
			$seopress_gtag_ga4               = "\n gtag('config', '" . seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() . "' " . $features . ');';
			$seopress_gtag_ga4               = apply_filters( 'seopress_gtag_ga4', $seopress_gtag_ga4 );
			$seopress_google_analytics_html .= $seopress_gtag_ga4;
			$seopress_google_analytics_html .= "\n";
		}

		// Ads.
		if ( ! isset( $_COOKIE['seopress-user-consent-close'] ) ) {
			$ads_options = seopress_get_service( 'GoogleAnalyticsOption' )->getAds();
			if ( ! empty( $ads_options ) ) {
				$seopress_gtag_ads               = "\n gtag('config', '" . $ads_options . "');";
				$seopress_gtag_ads               = apply_filters( 'seopress_gtag_ads', $seopress_gtag_ads );
				$seopress_google_analytics_html .= $seopress_gtag_ads;
				$seopress_google_analytics_html .= "\n";
			}

			$events = '';
			if ( ! empty( $seopress_google_analytics_event ) ) {
				foreach ( $seopress_google_analytics_event as $event ) {
					$seopress_google_analytics_html .= $event;
					$seopress_google_analytics_html .= "\n";
				}
			}
		}

		$seopress_gtag_before_closing_script = '';
		$seopress_gtag_before_closing_script = apply_filters( 'seopress_gtag_before_closing_script', $seopress_gtag_before_closing_script );
		if ( ! empty( $seopress_gtag_before_closing_script ) ) {
			$seopress_google_analytics_html .= $seopress_gtag_before_closing_script;
		}

		$seopress_google_analytics_html .= '</script>';
		$seopress_google_analytics_html .= "\n";

		$seopress_google_analytics_html = apply_filters( 'seopress_gtag_html', $seopress_google_analytics_html );

		if ( true === $echo ) {
			echo $seopress_google_analytics_html;
		} else {
			return $seopress_google_analytics_html;
		}
	}
}
add_action( 'seopress_google_analytics_html', 'seopress_google_analytics_js', 10, 1 );

/**
 * Google analytics js arguments
 *
 * @return void
 */
function seopress_google_analytics_js_arguments() {
	$echo = true;
	do_action( 'seopress_google_analytics_html', $echo );
}

/**
 * Custom tracking hook
 *
 * @return void
 */
function seopress_custom_tracking_hook() {
	$data['custom'] = '';
	$data['custom'] = apply_filters( 'seopress_custom_tracking', $data['custom'] );
	echo $data['custom'];
}

/**
 * Build custom code after body tag opening
 *
 * @param bool $echo Echo.
 *
 * @return void
 */
function seopress_google_analytics_body_code( $echo ) {
	$seopress_html_body = seopress_get_service( 'GoogleAnalyticsOption' )->getOtherTrackingBody();
	if ( empty( $seopress_html_body ) || ! $seopress_html_body ) {
		return;
	}

	if ( 'none' === $seopress_html_body ) {
		return;
	}

	$seopress_html_body = apply_filters( 'seopress_custom_body_tracking', $seopress_html_body );
	if ( true === $echo ) {
		echo "\n" . $seopress_html_body;
	} else {
		return "\n" . $seopress_html_body;
	}
}
add_action( 'seopress_custom_body_tracking_html', 'seopress_google_analytics_body_code', 10, 1 );

/**
 * Custom tracking body hook
 *
 * @return void
 */
function seopress_custom_tracking_body_hook() {
	$echo = true;
	do_action( 'seopress_custom_body_tracking_html', $echo );
}

/**
 * Build custom code before body tag closing
 *
 * @param bool $echo Echo.
 *
 * @return void
 */
function seopress_google_analytics_footer_code( $echo ) {
	$seopress_html_footer = seopress_get_service( 'GoogleAnalyticsOption' )->getOtherTrackingFooter();
	if ( empty( $seopress_html_footer ) || ! $seopress_html_footer ) {
		return;
	}

	if ( 'none' === $seopress_html_footer ) {
		return;
	}

	$seopress_html_footer = apply_filters( 'seopress_custom_footer_tracking', $seopress_html_footer );
	if ( true === $echo ) {
		echo "\n" . $seopress_html_footer;
	} else {
		return "\n" . $seopress_html_footer;
	}
}
add_action( 'seopress_custom_footer_tracking_html', 'seopress_google_analytics_footer_code', 10, 1 );

/**
 * Custom tracking footer hook
 *
 * @return void
 */
function seopress_custom_tracking_footer_hook() {
	$echo = true;
	do_action( 'seopress_custom_footer_tracking_html', $echo );
}

/**
 * Build custom code in head
 *
 * @param bool $echo Echo.
 *
 * @return void
 */
function seopress_google_analytics_head_code( $echo ) {
	$seopress_html_head = seopress_get_service( 'GoogleAnalyticsOption' )->getOtherTracking();
	if ( empty( $seopress_html_head ) || ! $seopress_html_head ) {
		return;
	}

	if ( 'none' === $seopress_html_head ) {
		return;
	}

	$seopress_html_head = apply_filters( 'seopress_gtag_after_additional_tracking_html', $seopress_html_head );

	if ( true === $echo ) {
		echo "\n" . $seopress_html_head;
	} else {
		return "\n" . $seopress_html_head;
	}
}
add_action( 'seopress_custom_head_tracking_html', 'seopress_google_analytics_head_code', 10, 1 );

/**
 * Custom tracking head hook
 *
 * @return void
 */
function seopress_custom_tracking_head_hook() {
	$echo = true;
	do_action( 'seopress_custom_head_tracking_html', $echo );
}
