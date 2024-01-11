<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Google Analytics
//=================================================================================================

function seopress_cookies_user_consent_html() {
    if (!empty(seopress_get_service('GoogleAnalyticsOption')->getOptOutMsg())) {
        $msg = seopress_get_service('GoogleAnalyticsOption')->getOptOutMsg();
    } elseif (get_option('wp_page_for_privacy_policy')) {
        $msg = __('By visiting our site, you agree to our privacy policy regarding cookies, tracking statistics, etc.&nbsp;<a href="[seopress_privacy_page]">Read more</a>', 'wp-seopress');
    } else {
        $msg = __('By visiting our site, you agree to our privacy policy regarding cookies, tracking statistics, etc.', 'wp-seopress');
    }

    if (get_option('wp_page_for_privacy_policy') && '' != $msg) {
        $seopress_privacy_page = esc_url(get_permalink(get_option('wp_page_for_privacy_policy')));
        $msg                   = str_replace('[seopress_privacy_page]', $seopress_privacy_page, $msg);
    }

    $msg = apply_filters('seopress_rgpd_message', $msg);


    $consent_btn = seopress_get_service('GoogleAnalyticsOption')->getOptOutMessageOk();
    if (empty($consent_btn) || !$consent_btn) {
        $consent_btn = __('Accept', 'wp-seopress');
    }

    $close_btn = seopress_get_service('GoogleAnalyticsOption')->getOptOutMessageClose();
    if (empty($close_btn) || !$close_btn) {
        $close_btn = __('X', 'wp-seopress');
    }

    $user_msg = '<div data-nosnippet class="seopress-user-consent seopress-user-message seopress-user-consent-hide">
        <p>' . $msg . '</p>
        <p>
            <button id="seopress-user-consent-accept" type="button">' . $consent_btn . '</button>
            <button type="button" id="seopress-user-consent-close">' . $close_btn . '</button>
        </p>
    </div>';

    $backdrop = '<div class="seopress-user-consent-backdrop seopress-user-consent-hide"></div>';

    $user_msg = apply_filters('seopress_rgpd_full_message', $user_msg, $msg, $consent_btn, $close_btn, $backdrop);

    echo $user_msg . $backdrop;
}

function seopress_cookies_edit_choice_html() {
    $optOutEditChoice = seopress_get_service('GoogleAnalyticsOption')->getOptOutEditChoice();
    if ('1' !== $optOutEditChoice) {
        return;
    }

    $edit_cookie_btn = seopress_get_service('GoogleAnalyticsOption')->getOptOutMessageEdit();
    if (empty($edit_cookie_btn) || !$edit_cookie_btn) {
        $edit_cookie_btn = __('Manage cookies', 'wp-seopress');
    }

    $user_msg = '<div data-nosnippet class="seopress-user-consent seopress-edit-choice">
        <p>
            <button id="seopress-user-consent-edit" type="button">' . $edit_cookie_btn . '</button>
        </p>
    </div>';

    $user_msg = apply_filters('seopress_rgpd_full_message', $user_msg, $edit_cookie_btn);

    echo $user_msg;
}

function seopress_cookies_user_consent_styles() {
    $styles = '<style>.seopress-user-consent {left: 50%;position: fixed;z-index: 8000;padding: 20px;display: inline-flex;justify-content: center;border: 1px solid #CCC;max-width:100%;';

    //Width
    $width = seopress_get_service('GoogleAnalyticsOption')->getCbWidth();
    if (!empty($width)) {
        $needle = '%';

        if (false !== strpos($width, $needle)) {
            $unit = '';
        } else {
            $unit = 'px';
        }

        $styles .= 'width: ' . $width . $unit . ';';
    } else {
        $styles .= 'width:100%;';
    }

    //Position
    $position = seopress_get_service('GoogleAnalyticsOption')->getCbPos();
    if ('top' === $position) {
        $styles .= 'top:0;';
        $styles .= 'transform: translate(-50%, 0%);';
    } elseif ('center' === $position) {
        $styles .= 'top:45%;';
        $styles .= 'transform: translate(-50%, -50%);';
    } else {
        $styles .= 'bottom:0;';
        $styles .= 'transform: translate(-50%, 0);';
    }

    //Text alignment
    $txtAlign = seopress_get_service('GoogleAnalyticsOption')->getCbTxtAlign();
    if ('left' === $txtAlign) {
        $styles .= 'text-align:left;';
    } elseif ('right' === $position) {
        $styles .= 'text-align:right;';
    } else {
        $styles .= 'text-align:center;';
    }

    //Background color
    $bgColor = seopress_get_service('GoogleAnalyticsOption')->getCbBg();
    if (!empty($bgColor)) {
        $styles .= 'background:' . $bgColor . ';';
    } else {
        $styles .= 'background:#F1F1F1;';
    }

    $styles .= '}@media (max-width: 782px) {.seopress-user-consent {display: block;}}.seopress-user-consent.seopress-user-message p:first-child {margin-right:20px}.seopress-user-consent p {margin: 0;font-size: 0.8em;align-self: center;';

    //Text color
    $txtColor = seopress_get_service('GoogleAnalyticsOption')->getCbTxtCol();
    if (!empty($txtColor)) {
        $styles .= 'color:' . $txtColor . ';';
    }

    $styles .= '}.seopress-user-consent button {vertical-align: middle;margin: 0;font-size: 14px;';

    //Btn background color
    $btnBgColor = seopress_get_service('GoogleAnalyticsOption')->getCbBtnBg();
    if (!empty($btnBgColor)) {
        $styles .= 'background:' . $btnBgColor . ';';
    }

    //Btn text color
    $btnTxtColor = seopress_get_service('GoogleAnalyticsOption')->getCbBtnCol();
    if (!empty($btnTxtColor)) {
        $styles .= 'color:' . $btnTxtColor . ';';
    }

    $styles .= '}.seopress-user-consent button:hover{';

    //Background hover color
    $bgHovercolor = seopress_get_service('GoogleAnalyticsOption')->getCbBtnBgHov();
    if (!empty($bgHoverColor)) {
        $styles .= 'background:' . $bgHoverColor . ';';
    }

    //Text hover color
    $txtHovercolor = seopress_get_service('GoogleAnalyticsOption')->getCbBtnColHov();
    if (!empty($txtHoverColor)) {
        $styles .= 'color:' . $txtHoverColor . ';';
    }

    $styles .= '}#seopress-user-consent-close{margin: 0;position: relative;font-weight: bold;border: 1px solid #ccc;';

    //Background secondary button
    $bgSecondaryBtn = seopress_get_service('GoogleAnalyticsOption')->getCbBtnSecBg();
    if (!empty($bgSecondaryBtn)) {
        $styles .= 'background:' . $bgSecondaryBtn . ';';
    } else {
        $styles .= 'background:none;';
    }

    //Color secondary button
    $colorSecondaryBtn = seopress_get_service('GoogleAnalyticsOption')->getCbBtnSecCol();
    if (!empty($colorSecondaryBtn)) {
        $styles .= 'color:' . $colorSecondaryBtn . ';';
    } else {
        $styles .= 'color:inherit;';
    }

    $styles .= '}#seopress-user-consent-close:hover{cursor:pointer;';

    //Background secondary button hover
    $bgSecondaryBtnHover = seopress_get_service('GoogleAnalyticsOption')->getCbBtnSecBgHov();
    if (!empty($bgSecondaryBtnHover)) {
        $styles .= 'background:' . $bgSecondaryBtnHover . ';';
    } else {
        $styles .= 'background:#222;';
    }

    //Color secondary button hover
    $colorSecondaryBtnHover = seopress_get_service('GoogleAnalyticsOption')->getCbBtnSecColHov();
    if (!empty($colorSecondaryBtnHover)) {
        $styles .= 'color:' . $colorSecondaryBtnHover . ';';
    } else {
        $styles .= 'color:#fff;';
    }

    $styles .= '}';

    //Link color
    $linkColor = seopress_get_service('GoogleAnalyticsOption')->getCbLkCol();
    if (!empty($linkColor)) {
        $styles .= '.seopress-user-consent a{';
        $styles .= 'color:' . $linkColor;
        $styles .= '}';
    }

    $styles .= '.seopress-user-consent-hide{display:none;}';

    $cbBackdrop = seopress_get_service('GoogleAnalyticsOption')->getCbBackdrop();
    if (!empty($cbBackdrop)) {
        $bg_backdrop = seopress_get_service('GoogleAnalyticsOption')->getCbBackdropBg();
        if (empty($bg_backdrop) || !$bg_backdrop) {
            $bg_backdrop = 'rgba(0,0,0,.65)';
        }

        $styles .= '.seopress-user-consent-backdrop{-webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            background: ' . $bg_backdrop . ';
            bottom: 0;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -webkit-flex-direction: column;
            -ms-flex-direction: column;
            flex-direction: column;
            left: 0;
            -webkit-overflow-scrolling: touch;
            overflow-y: auto;
            position: fixed;
            right: 0;
            -webkit-tap-highlight-color: transparent;
            top: 0;
            z-index: 100;}';
    }

    $styles .= '.seopress-edit-choice{
        background: none;
        justify-content: start;
        z-index: 7500;
        border: none;
        width: inherit;
        transform: none;
        left: inherit;
        bottom: 0;
        top: inherit;
    }';

    $styles .= '</style>';

    $styles = apply_filters('seopress_rgpd_full_message_styles', $styles);

    echo $styles;
}

function seopress_cookies_user_consent_render() {
    $hook = seopress_get_service('GoogleAnalyticsOption')->getHook();
    if (empty($hook) || !$hook) {
        $hook = 'wp_head';
    }

    add_action($hook, 'seopress_cookies_user_consent_html');
    add_action($hook, 'seopress_cookies_edit_choice_html');
    add_action($hook, 'seopress_cookies_user_consent_styles');
}

if ('1' === seopress_get_service('GoogleAnalyticsOption')->getDisable()) {
    if (is_user_logged_in()) {
        global $wp_roles;

        //Get current user role
        if (isset(wp_get_current_user()->roles[0])) {
            $seopress_user_role = wp_get_current_user()->roles[0];
            //If current user role matchs values from SEOPress GA settings then apply
            if (!empty(seopress_get_service('GoogleAnalyticsOption')->getRoles())) {
                if (array_key_exists($seopress_user_role, seopress_get_service('GoogleAnalyticsOption')->getRoles())) {
                    //do nothing
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

//Build Custom GA
function seopress_google_analytics_js($echo) {
    if ('' !== seopress_get_service('GoogleAnalyticsOption')->getGA4() && '1' === seopress_get_service('GoogleAnalyticsOption')->getEnableOption()) {
        //Init
        $tracking_id = seopress_get_service('GoogleAnalyticsOption')->getGA4();
        $seopress_google_analytics_config = [];
        $seopress_google_analytics_event  = [];

        $seopress_google_analytics_html = "\n";
        $seopress_google_analytics_html .=
        "<script async src='https://www.googletagmanager.com/gtag/js?id=" . $tracking_id . "'></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}";
        $seopress_google_analytics_html .= "gtag('js', new Date());\n";

        //Dimensions
        $seopress_google_analytics_config['cd']['cd_hook'] = apply_filters('seopress_gtag_cd_hook_cf', isset($seopress_google_analytics_config['cd']['cd_hook']));
        if ( ! has_filter('seopress_gtag_cd_hook_cf')) {
            unset($seopress_google_analytics_config['cd']['cd_hook']);
        }

        $seopress_google_analytics_event['cd_hook'] = apply_filters('seopress_gtag_cd_hook_ev', isset($seopress_google_analytics_event['cd_hook']));
        if ( ! has_filter('seopress_gtag_cd_hook_ev')) {
            unset($seopress_google_analytics_config['cd']['cd_hook']);
        }

        $cdAuthorOption = seopress_get_service('GoogleAnalyticsOption')->getCdAuthor();
        $cdCategoryOption = seopress_get_service('GoogleAnalyticsOption')->getCdCategory();
        $cdTagOption = seopress_get_service('GoogleAnalyticsOption')->getCdTag();
        $cdPostTypeOption = seopress_get_service('GoogleAnalyticsOption')->getCdPostType();
        $cdLoggedInUserOption = seopress_get_service('GoogleAnalyticsOption')->getCdLoggedInUser();
        if ((!empty($cdAuthorOption) && 'none' != $cdAuthorOption)
                || (!empty($cdCategoryOption) && 'none' != $cdCategoryOption)
                || (!empty($cdTagOption) && 'none' != $cdTagOption)
                || (!empty($cdPostTypeOption) && 'none' != $cdPostTypeOption)
                || (!empty($cdLoggedInUserOption) && 'none' != $cdLoggedInUserOption)
                || ('' != isset($seopress_google_analytics_config['cd']['cd_hook']) && '' != isset($seopress_google_analytics_event['cd_hook']))
            ) {
            $seopress_google_analytics_config['cd']['cd_start'] = '{';
        } else {
            unset($seopress_google_analytics_config['cd']);
        }

        if (!empty($cdAuthorOption)) {
            if ('none' != $cdAuthorOption) {
                if (is_singular()) {
                    $seopress_google_analytics_config['cd']['cd_author'] = "'" . $cdAuthorOption . "': 'cd_author',";

                    $seopress_google_analytics_event['cd_author'] = "gtag('event', '" . __('Authors', 'wp-seopress') . "', {'cd_author': '" . get_the_author() . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_author'] = apply_filters('seopress_gtag_cd_author_cf', $seopress_google_analytics_config['cd']['cd_author']);

                    $seopress_google_analytics_event['cd_author'] = apply_filters('seopress_gtag_cd_author_ev', $seopress_google_analytics_event['cd_author']);
                }
            }
        }
        if (!empty($cdCategoryOption)) {
            if ('none' != $cdCategoryOption) {
                if (is_single() && has_category()) {
                    $categories = get_the_category();

                    if ( ! empty($categories)) {
                        $get_first_category = esc_html($categories[0]->name);
                    }

                    $seopress_google_analytics_config['cd']['cd_categories'] = "'" . $cdCategoryOption . "': 'cd_categories',";

                    $seopress_google_analytics_event['cd_categories'] = "gtag('event', '" . __('Categories', 'wp-seopress') . "', {'cd_categories': '" . $get_first_category . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_categories'] = apply_filters('seopress_gtag_cd_categories_cf', $seopress_google_analytics_config['cd']['cd_categories']);

                    $seopress_google_analytics_event['cd_categories'] = apply_filters('seopress_gtag_cd_categories_ev', $seopress_google_analytics_event['cd_categories']);
                }
            }
        }

        if (!empty($cdTagOption) && 'none' != $cdTagOption) {
            if (is_single() && has_tag()) {
                $tags = get_the_tags();
                if ( ! empty($tags)) {
                    $seopress_comma_count = count($tags);
                    $get_tags             = '';
                    foreach ($tags as $key => $value) {
                        $get_tags .= esc_html($value->name);
                        if ($key < $seopress_comma_count - 1) {
                            $get_tags .= ', ';
                        }
                    }
                }

                $seopress_google_analytics_config['cd']['cd_tags'] = "'" . $cdTagOption . "': 'cd_tags',";

                $seopress_google_analytics_event['cd_tags'] = "gtag('event', '" . __('Tags', 'wp-seopress') . "', {'cd_tags': '" . $get_tags . "', 'non_interaction': true});";

                $seopress_google_analytics_config['cd']['cd_tags'] = apply_filters('seopress_gtag_cd_tags_cf', $seopress_google_analytics_config['cd']['cd_tags']);

                $seopress_google_analytics_event['cd_tags'] = apply_filters('seopress_gtag_cd_tags_ev', $seopress_google_analytics_event['cd_tags']);
            }
        }

        if (!empty($cdPostTypeOption) && 'none' != $cdPostTypeOption) {
            if (is_single()) {
                $seopress_google_analytics_config['cd']['cd_cpt'] = "'" . $cdPostTypeOption . "': 'cd_cpt',";

                $seopress_google_analytics_event['cd_cpt'] = "gtag('event', '" . __('Post types', 'wp-seopress') . "', {'cd_cpt': '" . get_post_type() . "', 'non_interaction': true});";

                $seopress_google_analytics_config['cd']['cd_cpt'] = apply_filters('seopress_gtag_cd_cpt_cf', $seopress_google_analytics_config['cd']['cd_cpt']);

                $seopress_google_analytics_event['cd_cpt'] = apply_filters('seopress_gtag_cd_cpt_ev', $seopress_google_analytics_event['cd_cpt']);
            }
        }

        if (!empty($cdLoggedInUserOption) && 'none' != $cdLoggedInUserOption) {
            if (wp_get_current_user()->ID) {
                $seopress_google_analytics_config['cd']['cd_logged_in'] = "'" . $cdLoggedInUserOption . "': 'cd_logged_in',";

                $seopress_google_analytics_event['cd_logged_in'] = "gtag('event', '" . __('Connected users', 'wp-seopress') . "', {'cd_logged_in': '" . wp_get_current_user()->ID . "', 'non_interaction': true});";

                $seopress_google_analytics_config['cd']['cd_logged_in'] = apply_filters('seopress_gtag_cd_logged_in_cf', $seopress_google_analytics_config['cd']['cd_logged_in']);

                $seopress_google_analytics_event['cd_logged_in'] = apply_filters('seopress_gtag_cd_logged_in_ev', $seopress_google_analytics_event['cd_logged_in']);
            }
        }

        if ( ! empty($seopress_google_analytics_config['cd']['cd_logged_in']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_cpt']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_tags']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_categories']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_author']) ||
                ( ! empty($seopress_google_analytics_config['cd']['cd_hook']) && ! empty($seopress_google_analytics_event['cd_hook']))) {
            $seopress_google_analytics_config['cd']['cd_end'] = '}, ';
        } else {
            $seopress_google_analytics_config['cd']['cd_start'] = '';
        }

        //External links
        if (!empty(seopress_get_service('GoogleAnalyticsOption')->getLinkTrackingEnable())) {
            $seopress_google_analytics_click_event['link_tracking'] =
"window.addEventListener('load', function () {
var links = document.querySelectorAll('a');
for (let i = 0; i < links.length; i++) {
    links[i].addEventListener('click', function(e) {
        var n = this.href.includes('" . wp_parse_url(get_home_url(), PHP_URL_HOST) . "');
        if (n == false) {
            gtag('event', 'click', {'event_category': 'external links','event_label' : this.href});
        }
    });
    }
});
";
            $seopress_google_analytics_click_event['link_tracking'] = apply_filters('seopress_gtag_link_tracking_ev', $seopress_google_analytics_click_event['link_tracking']);
            $seopress_google_analytics_html .= $seopress_google_analytics_click_event['link_tracking'];
        }

        //Downloads tracking
        if (!empty(seopress_get_service('GoogleAnalyticsOption')->getDownloadTrackingEnable())) {
            $downloadTrackingOption = seopress_get_service('GoogleAnalyticsOption')->getDownloadTracking();
            if (!empty($downloadTrackingOption)) {
                $seopress_google_analytics_click_event['download_tracking'] =
"window.addEventListener('load', function () {
	var donwload_links = document.querySelectorAll('a');
	for (let j = 0; j < donwload_links.length; j++) {
		donwload_links[j].addEventListener('click', function(e) {
			var down = this.href.match(/.*\.(" . $downloadTrackingOption . ")(\?.*)?$/);
			if (down != null) {
				gtag('event', 'click', {'event_category': 'downloads','event_label' : this.href});
			}
		});
		}
	});
";
                $seopress_google_analytics_click_event['download_tracking'] = apply_filters('seopress_gtag_download_tracking_ev', $seopress_google_analytics_click_event['download_tracking']);
                $seopress_google_analytics_html .= $seopress_google_analytics_click_event['download_tracking'];
            }
        }

        //Affiliate tracking
        if (!empty(seopress_get_service('GoogleAnalyticsOption')->getAffiliateTrackingEnable())) {
            $affiliateTrackingOption = seopress_get_service('GoogleAnalyticsOption')->getAffiliateTracking();
            if (!empty($affiliateTrackingOption)) {
                $seopress_google_analytics_click_event['outbound_tracking'] =
"window.addEventListener('load', function () {
	var outbound_links = document.querySelectorAll('a');
	for (let k = 0; k < outbound_links.length; k++) {
		outbound_links[k].addEventListener('click', function(e) {
			var out = this.href.match(/(?:\/" . $affiliateTrackingOption . "\/)/gi);
			if (out != null) {
				gtag('event', 'click', {'event_category': 'outbound/affiliate','event_label' : this.href});
			}
		});
		}
	});";
                $seopress_google_analytics_click_event['outbound_tracking'] = apply_filters('seopress_gtag_outbound_tracking_ev', $seopress_google_analytics_click_event['outbound_tracking']);
                $seopress_google_analytics_html .= $seopress_google_analytics_click_event['outbound_tracking'];
            }
        }

        //Phone tracking
        if (!empty(seopress_get_service('GoogleAnalyticsOption')->getPhoneTracking())) {
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
            $seopress_google_analytics_click_event['phone_tracking'] = apply_filters('seopress_gtag_phone_tracking_ev', $seopress_google_analytics_click_event['phone_tracking']);
            $seopress_google_analytics_html .= $seopress_google_analytics_click_event['phone_tracking'];
        }

        // Google Enhanced Ecommerce
        require_once dirname(__FILE__) . '/options-google-ecommerce.php';

        //Send data
        $features = '';
        if ( ! empty($seopress_google_analytics_config['cd']['cd_logged_in']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_cpt']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_tags']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_categories']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_author']) ||
                ! empty($seopress_google_analytics_config['cd']['cd_hook'])) {
            $seopress_google_analytics_config['cd']['cd_start'] = "'custom_map': {";
        }
        if ( ! empty($seopress_google_analytics_config)) {
            if ( ! empty($seopress_google_analytics_config['cd']['cd_start'])) {
                array_unshift($seopress_google_analytics_config['cd'], $seopress_google_analytics_config['cd']['cd_start']);
                unset($seopress_google_analytics_config['cd']['cd_start']);
            }
            $features = ', {';
            foreach ($seopress_google_analytics_config as $key => $feature) {
                if ('cd' == $key) {
                    foreach ($feature as $_key => $cd) {
                        $features .= $cd;
                    }
                } else {
                    $features .= $feature;
                }
            }
            $features .= '}';
        }

        //Measurement ID
        if ('' !== seopress_get_service('GoogleAnalyticsOption')->getGA4()) {
            $seopress_gtag_ga4 = "gtag('config', '" . seopress_get_service('GoogleAnalyticsOption')->getGA4() . "' " . $features . ');';
            $seopress_gtag_ga4 = apply_filters('seopress_gtag_ga4', $seopress_gtag_ga4);
            $seopress_google_analytics_html .= $seopress_gtag_ga4;
            $seopress_google_analytics_html .= "\n";
        }

        //Ads
        $adsOptions = seopress_get_service('GoogleAnalyticsOption')->getAds();
        if (!empty($adsOptions)) {
            $seopress_gtag_ads = "gtag('config', '" . $adsOptions . "');";
            $seopress_gtag_ads = apply_filters('seopress_gtag_ads', $seopress_gtag_ads);
            $seopress_google_analytics_html .= $seopress_gtag_ads;
            $seopress_google_analytics_html .= "\n";
        }

        $events = '';
        if ( ! empty($seopress_google_analytics_event)) {
            foreach ($seopress_google_analytics_event as $event) {
                $seopress_google_analytics_html .= $event;
                $seopress_google_analytics_html .= "\n";
            }
        }

        // E-commerce
        if (isset($seopress_google_analytics_click_event['purchase_tracking'])) {
            $seopress_google_analytics_html .= $seopress_google_analytics_click_event['purchase_tracking'];
        }

        $seopress_google_analytics_html .= '</script>';
        $seopress_google_analytics_html .= "\n";

        $seopress_google_analytics_html = apply_filters('seopress_gtag_html', $seopress_google_analytics_html);

        if (true == $echo) {
            echo $seopress_google_analytics_html;
        } else {
            return $seopress_google_analytics_html;
        }
    }
}
add_action('seopress_google_analytics_html', 'seopress_google_analytics_js', 10, 1);

function seopress_google_analytics_js_arguments() {
    $echo = true;
    do_action('seopress_google_analytics_html', $echo);
}

function seopress_custom_tracking_hook() {
    $data['custom'] = '';
    $data['custom'] = apply_filters('seopress_custom_tracking', $data['custom']);
    echo $data['custom'];
}

//Build custom code after body tag opening
function seopress_google_analytics_body_code($echo) {
    $seopress_html_body = seopress_get_service('GoogleAnalyticsOption')->getOtherTrackingBody();
    if (empty($seopress_html_body) || !$seopress_html_body) {
        return;
    }

    $seopress_html_body = apply_filters('seopress_custom_body_tracking', $seopress_html_body);
    if (true == $echo) {
        echo "\n" . $seopress_html_body;
    } else {
        return "\n" . $seopress_html_body;
    }
}
add_action('seopress_custom_body_tracking_html', 'seopress_google_analytics_body_code', 10, 1);

function seopress_custom_tracking_body_hook() {
    $echo = true;
    do_action('seopress_custom_body_tracking_html', $echo);
}

//Build custom code before body tag closing
function seopress_google_analytics_footer_code($echo) {
    $seopress_html_footer = seopress_get_service('GoogleAnalyticsOption')->getOtherTrackingFooter();
    if(empty($seopress_html_footer) || !$seopress_html_footer) {
        return;
    }

    $seopress_html_footer = apply_filters('seopress_custom_footer_tracking', $seopress_html_footer);
    if (true == $echo) {
        echo "\n" . $seopress_html_footer;
    } else {
        return "\n" . $seopress_html_footer;
    }
}
add_action('seopress_custom_footer_tracking_html', 'seopress_google_analytics_footer_code', 10, 1);

function seopress_custom_tracking_footer_hook() {
    $echo = true;
    do_action('seopress_custom_footer_tracking_html', $echo);
}

//Build custom code in head
function seopress_google_analytics_head_code($echo) {
    $seopress_html_head = seopress_get_service('GoogleAnalyticsOption')->getOtherTracking();
    if (empty($seopress_html_head) || !$seopress_html_head) {
        return;
    }

    $seopress_html_head = apply_filters('seopress_gtag_after_additional_tracking_html', $seopress_html_head);

    if (true == $echo) {
        echo "\n" . $seopress_html_head;
    } else {
        return "\n" . $seopress_html_head;
    }
}
add_action('seopress_custom_head_tracking_html', 'seopress_google_analytics_head_code', 10, 1);

function seopress_custom_tracking_head_hook() {
    $echo = true;
    do_action('seopress_custom_head_tracking_html', $echo);
}


