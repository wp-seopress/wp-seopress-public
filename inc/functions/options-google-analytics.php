<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Google Analytics
//=================================================================================================
function seopress_google_analytics_hook_option() {
    $seopress_google_analytics_hook_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_hook_option)) {
        foreach ($seopress_google_analytics_hook_option as $key => $seopress_google_analytics_hook_value) {
            $options[$key] = $seopress_google_analytics_hook_value;
        }
        if (isset($seopress_google_analytics_hook_option['seopress_google_analytics_hook'])) {
            return $seopress_google_analytics_hook_option['seopress_google_analytics_hook'];
        }
    }
}
function seopress_google_analytics_opt_out_msg_ok_option() {
    $seopress_google_analytics_opt_out_msg_ok_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_opt_out_msg_ok_option)) {
        foreach ($seopress_google_analytics_opt_out_msg_ok_option as $key => $seopress_google_analytics_opt_out_msg_ok_value) {
            $options[$key] = $seopress_google_analytics_opt_out_msg_ok_value;
        }
        if (isset($seopress_google_analytics_opt_out_msg_ok_option['seopress_google_analytics_opt_out_msg_ok'])) {
            return $seopress_google_analytics_opt_out_msg_ok_option['seopress_google_analytics_opt_out_msg_ok'];
        }
    }
}

function seopress_google_analytics_opt_out_msg_close_option() {
    $seopress_google_analytics_opt_out_msg_close_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_opt_out_msg_close_option)) {
        foreach ($seopress_google_analytics_opt_out_msg_close_option as $key => $seopress_google_analytics_opt_out_msg_close_value) {
            $options[$key] = $seopress_google_analytics_opt_out_msg_close_value;
        }
        if (isset($seopress_google_analytics_opt_out_msg_close_option['seopress_google_analytics_opt_out_msg_close'])) {
            return $seopress_google_analytics_opt_out_msg_close_option['seopress_google_analytics_opt_out_msg_close'];
        }
    }
}

function seopress_google_analytics_cb_bg_option() {
    $seopress_google_analytics_cb_bg_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_bg_option)) {
        foreach ($seopress_google_analytics_cb_bg_option as $key => $seopress_google_analytics_cb_bg_value) {
            $options[$key] = $seopress_google_analytics_cb_bg_value;
        }
        if (isset($seopress_google_analytics_cb_bg_option['seopress_google_analytics_cb_bg'])) {
            return $seopress_google_analytics_cb_bg_option['seopress_google_analytics_cb_bg'];
        }
    }
}

function seopress_google_analytics_cb_txt_col_option() {
    $seopress_google_analytics_cb_txt_col_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_txt_col_option)) {
        foreach ($seopress_google_analytics_cb_txt_col_option as $key => $seopress_google_analytics_cb_txt_col_value) {
            $options[$key] = $seopress_google_analytics_cb_txt_col_value;
        }
        if (isset($seopress_google_analytics_cb_txt_col_option['seopress_google_analytics_cb_txt_col'])) {
            return $seopress_google_analytics_cb_txt_col_option['seopress_google_analytics_cb_txt_col'];
        }
    }
}

function seopress_google_analytics_cb_lk_col_option() {
    $seopress_google_analytics_cb_lk_col_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_lk_col_option)) {
        foreach ($seopress_google_analytics_cb_lk_col_option as $key => $seopress_google_analytics_cb_lk_col_value) {
            $options[$key] = $seopress_google_analytics_cb_lk_col_value;
        }
        if (isset($seopress_google_analytics_cb_lk_col_option['seopress_google_analytics_cb_lk_col'])) {
            return $seopress_google_analytics_cb_lk_col_option['seopress_google_analytics_cb_lk_col'];
        }
    }
}

function seopress_google_analytics_cb_btn_bg_option() {
    $seopress_google_analytics_cb_btn_bg_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_bg_option)) {
        foreach ($seopress_google_analytics_cb_btn_bg_option as $key => $seopress_google_analytics_cb_btn_bg_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_bg_value;
        }
        if (isset($seopress_google_analytics_cb_btn_bg_option['seopress_google_analytics_cb_btn_bg'])) {
            return $seopress_google_analytics_cb_btn_bg_option['seopress_google_analytics_cb_btn_bg'];
        }
    }
}

function seopress_google_analytics_cb_btn_bg_hov_option() {
    $seopress_google_analytics_cb_btn_bg_hov_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_bg_hov_option)) {
        foreach ($seopress_google_analytics_cb_btn_bg_hov_option as $key => $seopress_google_analytics_cb_btn_bg_hov_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_bg_hov_value;
        }
        if (isset($seopress_google_analytics_cb_btn_bg_hov_option['seopress_google_analytics_cb_btn_bg_hov'])) {
            return $seopress_google_analytics_cb_btn_bg_hov_option['seopress_google_analytics_cb_btn_bg_hov'];
        }
    }
}

function seopress_google_analytics_cb_btn_col_option() {
    $seopress_google_analytics_cb_btn_col_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_col_option)) {
        foreach ($seopress_google_analytics_cb_btn_col_option as $key => $seopress_google_analytics_cb_btn_col_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_col_value;
        }
        if (isset($seopress_google_analytics_cb_btn_col_option['seopress_google_analytics_cb_btn_col'])) {
            return $seopress_google_analytics_cb_btn_col_option['seopress_google_analytics_cb_btn_col'];
        }
    }
}

function seopress_google_analytics_cb_btn_col_hov_option() {
    $seopress_google_analytics_cb_btn_col_hov_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_col_hov_option)) {
        foreach ($seopress_google_analytics_cb_btn_col_hov_option as $key => $seopress_google_analytics_cb_btn_col_hov_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_col_hov_value;
        }
        if (isset($seopress_google_analytics_cb_btn_col_hov_option['seopress_google_analytics_cb_btn_col_hov'])) {
            return $seopress_google_analytics_cb_btn_col_hov_option['seopress_google_analytics_cb_btn_col_hov'];
        }
    }
}

function seopress_google_analytics_cb_btn_sec_bg_option() {
    $seopress_google_analytics_cb_btn_sec_bg_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_sec_bg_option)) {
        foreach ($seopress_google_analytics_cb_btn_sec_bg_option as $key => $seopress_google_analytics_cb_btn_sec_bg_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_sec_bg_value;
        }
        if (isset($seopress_google_analytics_cb_btn_sec_bg_option['seopress_google_analytics_cb_btn_sec_bg'])) {
            return $seopress_google_analytics_cb_btn_sec_bg_option['seopress_google_analytics_cb_btn_sec_bg'];
        }
    }
}

function seopress_google_analytics_cb_btn_sec_col_option() {
    $seopress_google_analytics_cb_btn_sec_col_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_sec_col_option)) {
        foreach ($seopress_google_analytics_cb_btn_sec_col_option as $key => $seopress_google_analytics_cb_btn_sec_col_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_sec_col_value;
        }
        if (isset($seopress_google_analytics_cb_btn_sec_col_option['seopress_google_analytics_cb_btn_sec_col'])) {
            return $seopress_google_analytics_cb_btn_sec_col_option['seopress_google_analytics_cb_btn_sec_col'];
        }
    }
}

function seopress_google_analytics_cb_btn_sec_bg_hov_option() {
    $seopress_google_analytics_cb_btn_sec_bg_hov_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_sec_bg_hov_option)) {
        foreach ($seopress_google_analytics_cb_btn_sec_bg_hov_option as $key => $seopress_google_analytics_cb_btn_sec_bg_hov_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_sec_bg_hov_value;
        }
        if (isset($seopress_google_analytics_cb_btn_sec_bg_hov_option['seopress_google_analytics_cb_btn_sec_bg_hov'])) {
            return $seopress_google_analytics_cb_btn_sec_bg_hov_option['seopress_google_analytics_cb_btn_sec_bg_hov'];
        }
    }
}

function seopress_google_analytics_cb_btn_sec_col_hov_option() {
    $seopress_google_analytics_cb_btn_sec_col_hov_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_btn_sec_col_hov_option)) {
        foreach ($seopress_google_analytics_cb_btn_sec_col_hov_option as $key => $seopress_google_analytics_cb_btn_sec_col_hov_value) {
            $options[$key] = $seopress_google_analytics_cb_btn_sec_col_hov_value;
        }
        if (isset($seopress_google_analytics_cb_btn_sec_col_hov_option['seopress_google_analytics_cb_btn_sec_col_hov'])) {
            return $seopress_google_analytics_cb_btn_sec_col_hov_option['seopress_google_analytics_cb_btn_sec_col_hov'];
        }
    }
}

function seopress_google_analytics_cb_pos_option() {
    $seopress_google_analytics_cb_pos_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_pos_option)) {
        foreach ($seopress_google_analytics_cb_pos_option as $key => $seopress_google_analytics_cb_pos_value) {
            $options[$key] = $seopress_google_analytics_cb_pos_value;
        }
        if (isset($seopress_google_analytics_cb_pos_option['seopress_google_analytics_cb_pos'])) {
            return $seopress_google_analytics_cb_pos_option['seopress_google_analytics_cb_pos'];
        }
    }
}

function seopress_google_analytics_cb_width_option() {
    $seopress_google_analytics_cb_width_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_width_option)) {
        foreach ($seopress_google_analytics_cb_width_option as $key => $seopress_google_analytics_cb_width_value) {
            $options[$key] = $seopress_google_analytics_cb_width_value;
        }
        if (isset($seopress_google_analytics_cb_width_option['seopress_google_analytics_cb_width'])) {
            return $seopress_google_analytics_cb_width_option['seopress_google_analytics_cb_width'];
        }
    }
}

function seopress_google_analytics_cb_backdrop_option() {
    $seopress_google_analytics_cb_backdrop_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_backdrop_option)) {
        foreach ($seopress_google_analytics_cb_backdrop_option as $key => $seopress_google_analytics_cb_backdrop_value) {
            $options[$key] = $seopress_google_analytics_cb_backdrop_value;
        }
        if (isset($seopress_google_analytics_cb_backdrop_option['seopress_google_analytics_cb_backdrop'])) {
            return $seopress_google_analytics_cb_backdrop_option['seopress_google_analytics_cb_backdrop'];
        }
    }
}

function seopress_google_analytics_cb_backdrop_bg_option() {
    $seopress_google_analytics_cb_backdrop_bg_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_backdrop_bg_option)) {
        foreach ($seopress_google_analytics_cb_backdrop_bg_option as $key => $seopress_google_analytics_cb_backdrop_bg_value) {
            $options[$key] = $seopress_google_analytics_cb_backdrop_bg_value;
        }
        if (isset($seopress_google_analytics_cb_backdrop_bg_option['seopress_google_analytics_cb_backdrop_bg'])) {
            return $seopress_google_analytics_cb_backdrop_bg_option['seopress_google_analytics_cb_backdrop_bg'];
        }
    }
}

function seopress_google_analytics_cb_txt_align_option() {
    $seopress_google_analytics_cb_txt_align_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cb_txt_align_option)) {
        foreach ($seopress_google_analytics_cb_txt_align_option as $key => $seopress_google_analytics_cb_txt_align_value) {
            $options[$key] = $seopress_google_analytics_cb_txt_align_value;
        }
        if (isset($seopress_google_analytics_cb_txt_align_option['seopress_google_analytics_cb_txt_align'])) {
            return $seopress_google_analytics_cb_txt_align_option['seopress_google_analytics_cb_txt_align'];
        }
    }
}

function seopress_google_analytics_opt_out_edit_choice_option() {
    $seopress_google_analytics_opt_out_edit_choice_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_opt_out_edit_choice_option)) {
        foreach ($seopress_google_analytics_opt_out_edit_choice_option as $key => $seopress_google_analytics_opt_out_edit_choice_value) {
            $options[$key] = $seopress_google_analytics_opt_out_edit_choice_value;
        }
        if (isset($seopress_google_analytics_opt_out_edit_choice_option['seopress_google_analytics_opt_out_edit_choice'])) {
            return $seopress_google_analytics_opt_out_edit_choice_option['seopress_google_analytics_opt_out_edit_choice'];
        }
    }
}

function seopress_google_analytics_opt_out_msg_edit_option() {
    $seopress_google_analytics_opt_out_msg_edit_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_opt_out_msg_edit_option)) {
        foreach ($seopress_google_analytics_opt_out_msg_edit_option as $key => $seopress_google_analytics_opt_out_msg_edit_value) {
            $options[$key] = $seopress_google_analytics_opt_out_msg_edit_value;
        }
        if (isset($seopress_google_analytics_opt_out_msg_edit_option['seopress_google_analytics_opt_out_msg_edit'])) {
            return $seopress_google_analytics_opt_out_msg_edit_option['seopress_google_analytics_opt_out_msg_edit'];
        }
    }
}

function seopress_cookies_user_consent_html() {
    if ('' != seopress_google_analytics_opt_out_msg_option()) {
        $msg = seopress_google_analytics_opt_out_msg_option();
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

    if ('' != seopress_google_analytics_opt_out_msg_ok_option()) {
        $consent_btn = seopress_google_analytics_opt_out_msg_ok_option();
    } else {
        $consent_btn = __('Accept', 'wp-seopress');
    }

    if ('' != seopress_google_analytics_opt_out_msg_close_option()) {
        $close_btn = seopress_google_analytics_opt_out_msg_close_option();
    } else {
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
    if ('1' === seopress_google_analytics_opt_out_edit_choice_option()) {
        if ('' != seopress_google_analytics_opt_out_msg_edit_option()) {
            $edit_cookie_btn = seopress_google_analytics_opt_out_msg_edit_option();
        } else {
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
}

function seopress_cookies_user_consent_styles() {
    $styles = '<style>.seopress-user-consent {left: 50%;position: fixed;z-index: 8000;padding: 20px;display: inline-flex;justify-content: center;border: 1px solid #CCC;max-width:100%;';

    //Width
    if ('' != seopress_google_analytics_cb_width_option()) {
        $width  = seopress_google_analytics_cb_width_option();
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
    if ('top' === seopress_google_analytics_cb_pos_option()) {
        $styles .= 'top:0;';
        $styles .= 'transform: translate(-50%, 0%);';
    } elseif ('center' === seopress_google_analytics_cb_pos_option()) {
        $styles .= 'top:45%;';
        $styles .= 'transform: translate(-50%, -50%);';
    } else {
        $styles .= 'bottom:0;';
        $styles .= 'transform: translate(-50%, 0);';
    }

    //Text alignment
    if ('left' === seopress_google_analytics_cb_txt_align_option()) {
        $styles .= 'text-align:left;';
    } elseif ('right' === seopress_google_analytics_cb_pos_option()) {
        $styles .= 'text-align:right;';
    } else {
        $styles .= 'text-align:center;';
    }

    //Background color
    if ('' != seopress_google_analytics_cb_bg_option()) {
        $styles .= 'background:' . seopress_google_analytics_cb_bg_option() . ';';
    } else {
        $styles .= 'background:#F1F1F1;';
    }

    $styles .= '}@media (max-width: 782px) {.seopress-user-consent {display: block;}}.seopress-user-consent.seopress-user-message p:first-child {margin-right:20px}.seopress-user-consent p {margin: 0;font-size: 0.8em;align-self: center;';

    //Text color
    if ('' != seopress_google_analytics_cb_txt_col_option()) {
        $styles .= 'color:' . seopress_google_analytics_cb_txt_col_option() . ';';
    }

    $styles .= '}.seopress-user-consent button {vertical-align: middle;margin: 0;font-size: 14px;';

    //Btn background color
    if ('' != seopress_google_analytics_cb_btn_bg_option()) {
        $styles .= 'background:' . seopress_google_analytics_cb_btn_bg_option() . ';';
    }

    //Btn text color
    if ('' != seopress_google_analytics_cb_btn_col_option()) {
        $styles .= 'color:' . seopress_google_analytics_cb_btn_col_option() . ';';
    }

    $styles .= '}.seopress-user-consent button:hover{';

    //Background hover color
    if ('' != seopress_google_analytics_cb_btn_bg_hov_option()) {
        $styles .= 'background:' . seopress_google_analytics_cb_btn_bg_hov_option() . ';';
    }

    //Text hover color
    if ('' != seopress_google_analytics_cb_btn_col_hov_option()) {
        $styles .= 'color:' . seopress_google_analytics_cb_btn_col_hov_option() . ';';
    }

    $styles .= '}#seopress-user-consent-close{margin: 0;position: relative;font-weight: bold;border: 1px solid #ccc;';

    //Background secondary button
    if ('' != seopress_google_analytics_cb_btn_sec_bg_option()) {
        $styles .= 'background:' . seopress_google_analytics_cb_btn_sec_bg_option() . ';';
    } else {
        $styles .= 'background:none;';
    }

    //Color secondary button
    if ('' != seopress_google_analytics_cb_btn_sec_col_option()) {
        $styles .= 'color:' . seopress_google_analytics_cb_btn_sec_col_option() . ';';
    } else {
        $styles .= 'color:inherit;';
    }

    $styles .= '}#seopress-user-consent-close:hover{cursor:pointer;';

    //Background secondary button hover
    if ('' != seopress_google_analytics_cb_btn_sec_bg_hov_option()) {
        $styles .= 'background:' . seopress_google_analytics_cb_btn_sec_bg_hov_option() . ';';
    } else {
        $styles .= 'background:#222;';
    }

    //Color secondary button hover
    if ('' != seopress_google_analytics_cb_btn_sec_col_hov_option()) {
        $styles .= 'color:' . seopress_google_analytics_cb_btn_sec_col_hov_option() . ';';
    } else {
        $styles .= 'color:#fff;';
    }

    $styles .= '}';

    //Link color
    if ('' != seopress_google_analytics_cb_lk_col_option()) {
        $styles .= '.seopress-user-consent a{';
        $styles .= 'color:' . seopress_google_analytics_cb_lk_col_option();
        $styles .= '}';
    }

    $styles .= '.seopress-user-consent-hide{display:none;}';

    if ('' != seopress_google_analytics_cb_backdrop_option()) {
        $bg_backdrop = 'rgba(0,0,0,.65)';
        if ('' != seopress_google_analytics_cb_backdrop_bg_option()) {
            $bg_backdrop = seopress_google_analytics_cb_backdrop_bg_option();
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
    $hook = 'wp_head';
    if (seopress_google_analytics_hook_option() !='') {
        $hook = seopress_google_analytics_hook_option();
    }

    add_action($hook, 'seopress_cookies_user_consent_html');
    add_action($hook, 'seopress_cookies_edit_choice_html');
    add_action($hook, 'seopress_cookies_user_consent_styles');
}

if ('1' == seopress_google_analytics_disable_option()) {
    if (is_user_logged_in()) {
        global $wp_roles;

        //Get current user role
        if (isset(wp_get_current_user()->roles[0])) {
            $seopress_user_role = wp_get_current_user()->roles[0];
            //If current user role matchs values from SEOPress GA settings then apply
            if (function_exists('seopress_google_analytics_roles_option') && '' != seopress_google_analytics_roles_option()) {
                if (array_key_exists($seopress_user_role, seopress_google_analytics_roles_option())) {
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

//Optimize
function seopress_google_analytics_optimize_option() {
    $seopress_google_analytics_optimize_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_optimize_option)) {
        foreach ($seopress_google_analytics_optimize_option as $key => $seopress_google_analytics_optimize_value) {
            $options[$key] = $seopress_google_analytics_optimize_value;
        }
        if (isset($seopress_google_analytics_optimize_option['seopress_google_analytics_optimize'])) {
            return $seopress_google_analytics_optimize_option['seopress_google_analytics_optimize'];
        }
    }
}

//Ads
function seopress_google_analytics_ads_option() {
    $seopress_google_analytics_ads_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_ads_option)) {
        foreach ($seopress_google_analytics_ads_option as $key => $seopress_google_analytics_ads_value) {
            $options[$key] = $seopress_google_analytics_ads_value;
        }
        if (isset($seopress_google_analytics_ads_option['seopress_google_analytics_ads'])) {
            return $seopress_google_analytics_ads_option['seopress_google_analytics_ads'];
        }
    }
}

//Additional tracking code - head
function seopress_google_analytics_other_tracking_option() {
    $seopress_google_analytics_other_tracking_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_other_tracking_option)) {
        foreach ($seopress_google_analytics_other_tracking_option as $key => $seopress_google_analytics_other_tracking_value) {
            $options[$key] = $seopress_google_analytics_other_tracking_value;
        }
        if (isset($seopress_google_analytics_other_tracking_option['seopress_google_analytics_other_tracking'])) {
            return $seopress_google_analytics_other_tracking_option['seopress_google_analytics_other_tracking'];
        }
    }
}

//Additional tracking code - body
function seopress_google_analytics_other_tracking_body_option() {
    $seopress_google_analytics_other_tracking_body_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_other_tracking_body_option)) {
        foreach ($seopress_google_analytics_other_tracking_body_option as $key => $seopress_google_analytics_other_tracking_body_value) {
            $options[$key] = $seopress_google_analytics_other_tracking_body_value;
        }
        if (isset($seopress_google_analytics_other_tracking_body_option['seopress_google_analytics_other_tracking_body'])) {
            return $seopress_google_analytics_other_tracking_body_option['seopress_google_analytics_other_tracking_body'];
        }
    }
}

//Additional tracking code - footer
function seopress_google_analytics_other_tracking_footer_option() {
    $seopress_google_analytics_other_tracking_footer_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_other_tracking_footer_option)) {
        foreach ($seopress_google_analytics_other_tracking_footer_option as $key => $seopress_google_analytics_other_tracking_footer_value) {
            $options[$key] = $seopress_google_analytics_other_tracking_footer_value;
        }
        if (isset($seopress_google_analytics_other_tracking_footer_option['seopress_google_analytics_other_tracking_footer'])) {
            return $seopress_google_analytics_other_tracking_footer_option['seopress_google_analytics_other_tracking_footer'];
        }
    }
}

//Remarketing
function seopress_google_analytics_remarketing_option() {
    $seopress_google_analytics_remarketing_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_remarketing_option)) {
        foreach ($seopress_google_analytics_remarketing_option as $key => $seopress_google_analytics_remarketing_value) {
            $options[$key] = $seopress_google_analytics_remarketing_value;
        }
        if (isset($seopress_google_analytics_remarketing_option['seopress_google_analytics_remarketing'])) {
            return $seopress_google_analytics_remarketing_option['seopress_google_analytics_remarketing'];
        }
    }
}

//IP Anonymization
function seopress_google_analytics_ip_anonymization_option() {
    $seopress_google_analytics_ip_anonymization_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_ip_anonymization_option)) {
        foreach ($seopress_google_analytics_ip_anonymization_option as $key => $seopress_google_analytics_ip_anonymization_value) {
            $options[$key] = $seopress_google_analytics_ip_anonymization_value;
        }
        if (isset($seopress_google_analytics_ip_anonymization_option['seopress_google_analytics_ip_anonymization'])) {
            return $seopress_google_analytics_ip_anonymization_option['seopress_google_analytics_ip_anonymization'];
        }
    }
}

//Link attribution
function seopress_google_analytics_link_attribution_option() {
    $seopress_google_analytics_link_attribution_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_link_attribution_option)) {
        foreach ($seopress_google_analytics_link_attribution_option as $key => $seopress_google_analytics_link_attribution_value) {
            $options[$key] = $seopress_google_analytics_link_attribution_value;
        }
        if (isset($seopress_google_analytics_link_attribution_option['seopress_google_analytics_link_attribution'])) {
            return $seopress_google_analytics_link_attribution_option['seopress_google_analytics_link_attribution'];
        }
    }
}

//Cross Domain Enable
function seopress_google_analytics_cross_enable_option() {
    $seopress_google_analytics_cross_enable_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cross_enable_option)) {
        foreach ($seopress_google_analytics_cross_enable_option as $key => $seopress_google_analytics_cross_enable_value) {
            $options[$key] = $seopress_google_analytics_cross_enable_value;
        }
        if (isset($seopress_google_analytics_cross_enable_option['seopress_google_analytics_cross_enable'])) {
            return $seopress_google_analytics_cross_enable_option['seopress_google_analytics_cross_enable'];
        }
    }
}

//Cross Domain
function seopress_google_analytics_cross_domain_option() {
    $seopress_google_analytics_cross_domain_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cross_domain_option)) {
        foreach ($seopress_google_analytics_cross_domain_option as $key => $seopress_google_analytics_cross_domain_value) {
            $options[$key] = $seopress_google_analytics_cross_domain_value;
        }
        if (isset($seopress_google_analytics_cross_domain_option['seopress_google_analytics_cross_domain'])) {
            return $seopress_google_analytics_cross_domain_option['seopress_google_analytics_cross_domain'];
        }
    }
}

//Events external links tracking Enable
function seopress_google_analytics_link_tracking_enable_option() {
    $seopress_google_analytics_link_tracking_enable_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_link_tracking_enable_option)) {
        foreach ($seopress_google_analytics_link_tracking_enable_option as $key => $seopress_google_analytics_link_tracking_enable_value) {
            $options[$key] = $seopress_google_analytics_link_tracking_enable_value;
        }
        if (isset($seopress_google_analytics_link_tracking_enable_option['seopress_google_analytics_link_tracking_enable'])) {
            return $seopress_google_analytics_link_tracking_enable_option['seopress_google_analytics_link_tracking_enable'];
        }
    }
}

//Events downloads tracking Enable
function seopress_google_analytics_download_tracking_enable_option() {
    $seopress_google_analytics_download_tracking_enable_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_download_tracking_enable_option)) {
        foreach ($seopress_google_analytics_download_tracking_enable_option as $key => $seopress_google_analytics_download_tracking_enable_value) {
            $options[$key] = $seopress_google_analytics_download_tracking_enable_value;
        }
        if (isset($seopress_google_analytics_download_tracking_enable_option['seopress_google_analytics_download_tracking_enable'])) {
            return $seopress_google_analytics_download_tracking_enable_option['seopress_google_analytics_download_tracking_enable'];
        }
    }
}

//Events tracking file types
function seopress_google_analytics_download_tracking_option() {
    $seopress_google_analytics_download_tracking_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_download_tracking_option)) {
        foreach ($seopress_google_analytics_download_tracking_option as $key => $seopress_google_analytics_download_tracking_value) {
            $options[$key] = $seopress_google_analytics_download_tracking_value;
        }
        if (isset($seopress_google_analytics_download_tracking_option['seopress_google_analytics_download_tracking'])) {
            return $seopress_google_analytics_download_tracking_option['seopress_google_analytics_download_tracking'];
        }
    }
}

//Events affiliate links tracking Enable
function seopress_google_analytics_affiliate_tracking_enable_option() {
    $seopress_google_analytics_affiliate_tracking_enable_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_affiliate_tracking_enable_option)) {
        foreach ($seopress_google_analytics_affiliate_tracking_enable_option as $key => $seopress_google_analytics_affiliate_tracking_enable_value) {
            $options[$key] = $seopress_google_analytics_affiliate_tracking_enable_value;
        }
        if (isset($seopress_google_analytics_affiliate_tracking_enable_option['seopress_google_analytics_affiliate_tracking_enable'])) {
            return $seopress_google_analytics_affiliate_tracking_enable_option['seopress_google_analytics_affiliate_tracking_enable'];
        }
    }
}

//Events tracking affiliate match
function seopress_google_analytics_affiliate_tracking_option() {
    $seopress_google_analytics_affiliate_tracking_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_affiliate_tracking_option)) {
        foreach ($seopress_google_analytics_affiliate_tracking_option as $key => $seopress_google_analytics_affiliate_tracking_value) {
            $options[$key] = $seopress_google_analytics_affiliate_tracking_value;
        }
        if (isset($seopress_google_analytics_affiliate_tracking_option['seopress_google_analytics_affiliate_tracking'])) {
            return $seopress_google_analytics_affiliate_tracking_option['seopress_google_analytics_affiliate_tracking'];
        }
    }
}

//Custom Dimension Author
function seopress_google_analytics_cd_author_option() {
    $seopress_google_analytics_cd_author_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cd_author_option)) {
        foreach ($seopress_google_analytics_cd_author_option as $key => $seopress_google_analytics_cd_author_value) {
            $options[$key] = $seopress_google_analytics_cd_author_value;
        }
        if (isset($seopress_google_analytics_cd_author_option['seopress_google_analytics_cd_author'])) {
            return $seopress_google_analytics_cd_author_option['seopress_google_analytics_cd_author'];
        }
    }
}

//Custom Dimension Category
function seopress_google_analytics_cd_category_option() {
    $seopress_google_analytics_cd_category_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cd_category_option)) {
        foreach ($seopress_google_analytics_cd_category_option as $key => $seopress_google_analytics_cd_category_value) {
            $options[$key] = $seopress_google_analytics_cd_category_value;
        }
        if (isset($seopress_google_analytics_cd_category_option['seopress_google_analytics_cd_category'])) {
            return $seopress_google_analytics_cd_category_option['seopress_google_analytics_cd_category'];
        }
    }
}

//Custom Dimension Tag
function seopress_google_analytics_cd_tag_option() {
    $seopress_google_analytics_cd_tag_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cd_tag_option)) {
        foreach ($seopress_google_analytics_cd_tag_option as $key => $seopress_google_analytics_cd_tag_value) {
            $options[$key] = $seopress_google_analytics_cd_tag_value;
        }
        if (isset($seopress_google_analytics_cd_tag_option['seopress_google_analytics_cd_tag'])) {
            return $seopress_google_analytics_cd_tag_option['seopress_google_analytics_cd_tag'];
        }
    }
}

//Custom Dimension Post Type
function seopress_google_analytics_cd_post_type_option() {
    $seopress_google_analytics_cd_post_type_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cd_post_type_option)) {
        foreach ($seopress_google_analytics_cd_post_type_option as $key => $seopress_google_analytics_cd_post_type_value) {
            $options[$key] = $seopress_google_analytics_cd_post_type_value;
        }
        if (isset($seopress_google_analytics_cd_post_type_option['seopress_google_analytics_cd_post_type'])) {
            return $seopress_google_analytics_cd_post_type_option['seopress_google_analytics_cd_post_type'];
        }
    }
}

//Custom Dimension Logged In
function seopress_google_analytics_cd_logged_in_user_option() {
    $seopress_google_analytics_cd_logged_in_user_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_cd_logged_in_user_option)) {
        foreach ($seopress_google_analytics_cd_logged_in_user_option as $key => $seopress_google_analytics_cd_logged_in_user_value) {
            $options[$key] = $seopress_google_analytics_cd_logged_in_user_value;
        }
        if (isset($seopress_google_analytics_cd_logged_in_user_option['seopress_google_analytics_cd_logged_in_user'])) {
            return $seopress_google_analytics_cd_logged_in_user_option['seopress_google_analytics_cd_logged_in_user'];
        }
    }
}

// Get option for "Measure purchases"
function seopress_google_analytics_purchases_option() {
    $seopress_google_analytics_add_to_cart_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_add_to_cart_option)) {
        foreach ($seopress_google_analytics_add_to_cart_option as $key => $seopress_google_analytics_add_to_cart_value) {
            $options[$key] = $seopress_google_analytics_add_to_cart_value;
        }
        if (isset($seopress_google_analytics_add_to_cart_option['seopress_google_analytics_purchases'])) {
            return $seopress_google_analytics_add_to_cart_option['seopress_google_analytics_purchases'];
        }
    }
}
// Get option for "Add to cart event"
function seopress_google_analytics_add_to_cart_option() {
    $seopress_google_analytics_add_to_cart_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_add_to_cart_option)) {
        foreach ($seopress_google_analytics_add_to_cart_option as $key => $seopress_google_analytics_add_to_cart_value) {
            $options[$key] = $seopress_google_analytics_add_to_cart_value;
        }
        if (isset($seopress_google_analytics_add_to_cart_option['seopress_google_analytics_add_to_cart'])) {
            return $seopress_google_analytics_add_to_cart_option['seopress_google_analytics_add_to_cart'];
        }
    }
}
// Get option for "Remove from cart event"
function seopress_google_analytics_remove_from_cart_option() {
    $seopress_google_analytics_remove_from_option = get_option('seopress_google_analytics_option_name');
    if ( ! empty($seopress_google_analytics_remove_from_option)) {
        foreach ($seopress_google_analytics_remove_from_option as $key => $seopress_google_analytics_remove_from_value) {
            $options[$key] = $seopress_google_analytics_remove_from_value;
        }
        if (isset($seopress_google_analytics_remove_from_option['seopress_google_analytics_remove_from_cart'])) {
            return $seopress_google_analytics_remove_from_option['seopress_google_analytics_remove_from_cart'];
        }
    }
}

//Build Custom GA
function seopress_google_analytics_js($echo) {
    if (('' != seopress_google_analytics_ua_option() || '' != seopress_google_analytics_ga4_option()) && '1' == seopress_google_analytics_enable_option()) {
        //Init
        $seopress_google_analytics_config = [];
        $seopress_google_analytics_event  = [];

        $seopress_google_analytics_html = "\n";
        $seopress_google_analytics_html .=
        "<script async src='https://www.googletagmanager.com/gtag/js?id=" . seopress_google_analytics_ua_option() . "'></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}";
        $seopress_google_analytics_html .= "gtag('js', new Date());\n";

        //Cross domains
        if ('1' == seopress_google_analytics_cross_enable_option() && seopress_google_analytics_cross_domain_option()) {
            $domains = array_map('trim', array_filter(explode(',', seopress_google_analytics_cross_domain_option())));

            if ( ! empty($domains)) {
                $domains_count = count($domains);

                $link_domains = '';

                foreach ($domains as $key => $domain) {
                    $link_domains .= "'" . $domain . "'";
                    if ($key < $domains_count - 1) {
                        $link_domains .= ',';
                    }
                }
                $seopress_google_analytics_config['linker'] = "'linker': {'domains': [" . $link_domains . ']},';
                $seopress_google_analytics_config['linker'] = apply_filters('seopress_gtag_linker', $seopress_google_analytics_config['linker']);
            }
        }

        //Optimize
        if ('' != seopress_google_analytics_optimize_option()) {
            $seopress_google_analytics_config['optimize'] = "'optimize_id': '" . seopress_google_analytics_optimize_option() . "',";
            $seopress_google_analytics_config['optimize'] = apply_filters('seopress_gtag_optimize_id', $seopress_google_analytics_config['optimize']);
        }

        //Remarketing
        if ('1' != seopress_google_analytics_remarketing_option()) {
            $seopress_google_analytics_config['allow_display_features'] = "'allow_display_features': false,";
            $seopress_google_analytics_config['allow_display_features'] = apply_filters('seopress_gtag_allow_display_features', $seopress_google_analytics_config['allow_display_features']);
        }

        //Link attribution
        if ('1' == seopress_google_analytics_link_attribution_option()) {
            $seopress_google_analytics_config['link_attribution'] = "'link_attribution': true,";
            $seopress_google_analytics_config['link_attribution'] = apply_filters('seopress_gtag_link_attribution', $seopress_google_analytics_config['link_attribution']);
        }

        //Dimensions
        $seopress_google_analytics_config['cd']['cd_hook'] = apply_filters('seopress_gtag_cd_hook_cf', isset($seopress_google_analytics_config['cd']['cd_hook']));
        if ( ! has_filter('seopress_gtag_cd_hook_cf')) {
            unset($seopress_google_analytics_config['cd']['cd_hook']);
        }

        $seopress_google_analytics_event['cd_hook'] = apply_filters('seopress_gtag_cd_hook_ev', isset($seopress_google_analytics_event['cd_hook']));
        if ( ! has_filter('seopress_gtag_cd_hook_ev')) {
            unset($seopress_google_analytics_config['cd']['cd_hook']);
        }

        if (('' != seopress_google_analytics_cd_author_option() && 'none' != seopress_google_analytics_cd_author_option())
                || ('' != seopress_google_analytics_cd_category_option() && 'none' != seopress_google_analytics_cd_category_option())
                || ('' != seopress_google_analytics_cd_tag_option() && 'none' != seopress_google_analytics_cd_tag_option())
                || ('' != seopress_google_analytics_cd_post_type_option() && 'none' != seopress_google_analytics_cd_post_type_option())
                || ('' != seopress_google_analytics_cd_logged_in_user_option() && 'none' != seopress_google_analytics_cd_logged_in_user_option())
                || ('' != isset($seopress_google_analytics_config['cd']['cd_hook']) && '' != isset($seopress_google_analytics_event['cd_hook']))
            ) {
            $seopress_google_analytics_config['cd']['cd_start'] = '{';
        } else {
            unset($seopress_google_analytics_config['cd']);
        }

        if ('' != seopress_google_analytics_cd_author_option()) {
            if ('none' != seopress_google_analytics_cd_author_option()) {
                if (is_singular()) {
                    $seopress_google_analytics_config['cd']['cd_author'] = "'" . seopress_google_analytics_cd_author_option() . "': 'cd_author',";

                    $seopress_google_analytics_event['cd_author'] = "gtag('event', '" . __('Authors', 'wp-seopress') . "', {'cd_author': '" . get_the_author() . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_author'] = apply_filters('seopress_gtag_cd_author_cf', $seopress_google_analytics_config['cd']['cd_author']);

                    $seopress_google_analytics_event['cd_author'] = apply_filters('seopress_gtag_cd_author_ev', $seopress_google_analytics_event['cd_author']);
                }
            }
        }
        if ('' != seopress_google_analytics_cd_category_option()) {
            if ('none' != seopress_google_analytics_cd_category_option()) {
                if (is_single() && has_category()) {
                    $categories = get_the_category();

                    if ( ! empty($categories)) {
                        $get_first_category = esc_html($categories[0]->name);
                    }

                    $seopress_google_analytics_config['cd']['cd_categories'] = "'" . seopress_google_analytics_cd_category_option() . "': 'cd_categories',";

                    $seopress_google_analytics_event['cd_categories'] = "gtag('event', '" . __('Categories', 'wp-seopress') . "', {'cd_categories': '" . $get_first_category . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_categories'] = apply_filters('seopress_gtag_cd_categories_cf', $seopress_google_analytics_config['cd']['cd_categories']);

                    $seopress_google_analytics_event['cd_categories'] = apply_filters('seopress_gtag_cd_categories_ev', $seopress_google_analytics_event['cd_categories']);
                }
            }
        }

        if ('' != seopress_google_analytics_cd_tag_option()) {
            if ('none' != seopress_google_analytics_cd_tag_option()) {
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

                    $seopress_google_analytics_config['cd']['cd_tags'] = "'" . seopress_google_analytics_cd_tag_option() . "': 'cd_tags',";

                    $seopress_google_analytics_event['cd_tags'] = "gtag('event', '" . __('Tags', 'wp-seopress') . "', {'cd_tags': '" . $get_tags . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_tags'] = apply_filters('seopress_gtag_cd_tags_cf', $seopress_google_analytics_config['cd']['cd_tags']);

                    $seopress_google_analytics_event['cd_tags'] = apply_filters('seopress_gtag_cd_tags_ev', $seopress_google_analytics_event['cd_tags']);
                }
            }
        }

        if ('' != seopress_google_analytics_cd_post_type_option()) {
            if ('none' != seopress_google_analytics_cd_post_type_option()) {
                if (is_single()) {
                    $seopress_google_analytics_config['cd']['cd_cpt'] = "'" . seopress_google_analytics_cd_post_type_option() . "': 'cd_cpt',";

                    $seopress_google_analytics_event['cd_cpt'] = "gtag('event', '" . __('Post types', 'wp-seopress') . "', {'cd_cpt': '" . get_post_type() . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_cpt'] = apply_filters('seopress_gtag_cd_cpt_cf', $seopress_google_analytics_config['cd']['cd_cpt']);

                    $seopress_google_analytics_event['cd_cpt'] = apply_filters('seopress_gtag_cd_cpt_ev', $seopress_google_analytics_event['cd_cpt']);
                }
            }
        }

        if ('' != seopress_google_analytics_cd_logged_in_user_option()) {
            if ('none' != seopress_google_analytics_cd_logged_in_user_option()) {
                if (wp_get_current_user()->ID) {
                    $seopress_google_analytics_config['cd']['cd_logged_in'] = "'" . seopress_google_analytics_cd_logged_in_user_option() . "': 'cd_logged_in',";

                    $seopress_google_analytics_event['cd_logged_in'] = "gtag('event', '" . __('Connected users', 'wp-seopress') . "', {'cd_logged_in': '" . wp_get_current_user()->ID . "', 'non_interaction': true});";

                    $seopress_google_analytics_config['cd']['cd_logged_in'] = apply_filters('seopress_gtag_cd_logged_in_cf', $seopress_google_analytics_config['cd']['cd_logged_in']);

                    $seopress_google_analytics_event['cd_logged_in'] = apply_filters('seopress_gtag_cd_logged_in_ev', $seopress_google_analytics_event['cd_logged_in']);
                }
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
        if ('' != seopress_google_analytics_link_tracking_enable_option()) {
            if ('' != seopress_google_analytics_link_tracking_enable_option()) {
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
        }

        //Downloads tracking
        if ('' != seopress_google_analytics_download_tracking_enable_option()) {
            if ('' != seopress_google_analytics_download_tracking_option()) {
                $seopress_google_analytics_click_event['download_tracking'] =
"window.addEventListener('load', function () {
	var donwload_links = document.querySelectorAll('a');
	for (let j = 0; j < donwload_links.length; j++) {
		donwload_links[j].addEventListener('click', function(e) {
			var down = this.href.match(/.*\.(" . seopress_google_analytics_download_tracking_option() . ")(\?.*)?$/);
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
        if ('' != seopress_google_analytics_affiliate_tracking_enable_option()) {
            if ('' != seopress_google_analytics_affiliate_tracking_option()) {
                $seopress_google_analytics_click_event['outbound_tracking'] =
"window.addEventListener('load', function () {
	var outbound_links = document.querySelectorAll('a');
	for (let k = 0; k < outbound_links.length; k++) {
		outbound_links[k].addEventListener('click', function(e) {
			var out = this.href.match(/(?:\/" . seopress_google_analytics_affiliate_tracking_option() . "\/)/gi);
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

        // Google Enhanced Ecommerce
        require_once dirname(__FILE__) . '/options-google-ecommerce.php';

        //Anonymize IP
        if ('1' == seopress_google_analytics_ip_anonymization_option()) {
            $seopress_google_analytics_config['anonymize_ip'] = "'anonymize_ip': true,";
            $seopress_google_analytics_config['anonymize_ip'] = apply_filters('seopress_gtag_anonymize_ip', $seopress_google_analytics_config['anonymize_ip']);
        }

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

        //UA
        if ('' != seopress_google_analytics_ua_option()) {
            $seopress_gtag_ua = "gtag('config', '" . seopress_google_analytics_ua_option() . "' " . $features . ');';
            $seopress_gtag_ua = apply_filters('seopress_gtag_ua', $seopress_gtag_ua);
            $seopress_google_analytics_html .= $seopress_gtag_ua;
            $seopress_google_analytics_html .= "\n";
        }

        //Measurement ID
        if ('' != seopress_google_analytics_ga4_option()) {
            $seopress_gtag_ga4 = "gtag('config', '" . seopress_google_analytics_ga4_option() . "');";
            $seopress_gtag_ga4 = apply_filters('seopress_gtag_ga4', $seopress_gtag_ga4);
            $seopress_google_analytics_html .= $seopress_gtag_ga4;
            $seopress_google_analytics_html .= "\n";
        }

        //Ads
        if ('' != seopress_google_analytics_ads_option()) {
            $seopress_gtag_ads = "gtag('config', '" . seopress_google_analytics_ads_option() . "');";
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
    if ('' != seopress_google_analytics_other_tracking_body_option()) {
        $seopress_html_body = seopress_google_analytics_other_tracking_body_option();
        $seopress_html_body = apply_filters('seopress_custom_body_tracking', $seopress_html_body);
        if (true == $echo) {
            echo "\n" . $seopress_html_body;
        } else {
            return "\n" . $seopress_html_body;
        }
    }
}
add_action('seopress_custom_body_tracking_html', 'seopress_google_analytics_body_code', 10, 1);

function seopress_custom_tracking_body_hook() {
    $echo = true;
    do_action('seopress_custom_body_tracking_html', $echo);
}

//Build custom code before body tag closing
function seopress_google_analytics_footer_code($echo) {
    if ('' != seopress_google_analytics_other_tracking_footer_option()) {
        $seopress_html_footer = seopress_google_analytics_other_tracking_footer_option();
        $seopress_html_footer = apply_filters('seopress_custom_footer_tracking', $seopress_html_footer);
        if (true == $echo) {
            echo "\n" . $seopress_html_footer;
        } else {
            return "\n" . $seopress_html_footer;
        }
    }
}
add_action('seopress_custom_footer_tracking_html', 'seopress_google_analytics_footer_code', 10, 1);

function seopress_custom_tracking_footer_hook() {
    $echo = true;
    do_action('seopress_custom_footer_tracking_html', $echo);
}

//Build custom code in head
function seopress_google_analytics_head_code($echo) {
    if ('' != seopress_google_analytics_other_tracking_option()) {
        $seopress_html_head = seopress_google_analytics_other_tracking_option();
        $seopress_html_head = apply_filters('seopress_gtag_after_additional_tracking_html', $seopress_html_head);

        if (true == $echo) {
            echo "\n" . $seopress_html_head;
        } else {
            return "\n" . $seopress_html_head;
        }
    }
}
add_action('seopress_custom_head_tracking_html', 'seopress_google_analytics_head_code', 10, 1);

function seopress_custom_tracking_head_hook() {
    $echo = true;
    do_action('seopress_custom_head_tracking_html', $echo);
}

//MATOMO
require_once dirname(__FILE__) . '/options-matomo.php';

if ('1' == seopress_google_analytics_half_disable_option() || (((isset($_COOKIE['seopress-user-consent-accept']) && '1' == $_COOKIE['seopress-user-consent-accept']) && '1' == seopress_google_analytics_disable_option()) || ('1' != seopress_google_analytics_disable_option()))) { //User consent cookie OK
    if (is_user_logged_in()) {
        global $wp_roles;

        //Get current user role
        if (isset(wp_get_current_user()->roles[0])) {
            $seopress_user_role = wp_get_current_user()->roles[0];
            //If current user role matchs values from SEOPress GA settings then apply
            if (function_exists('seopress_google_analytics_roles_option') && '' != seopress_google_analytics_roles_option()) {
                if (array_key_exists($seopress_user_role, seopress_google_analytics_roles_option())) {
                    //do nothing
                } else {
                    if ('1' == seopress_google_analytics_enable_option() && ('' != seopress_google_analytics_ua_option() || '' != seopress_google_analytics_ga4_option())) {
                        add_action('wp_head', 'seopress_google_analytics_js_arguments', 999, 1);
                        add_action('wp_head', 'seopress_custom_tracking_hook', 1000, 1);
                    }
                    if ('1' == seopress_google_analytics_matomo_enable_option() && '' != seopress_google_analytics_matomo_id_option() && '' != seopress_google_analytics_matomo_site_id_option()) {
                        add_action('wp_head', 'seopress_matomo_js_arguments', 990, 1);
                    }
                    add_action('wp_head', 'seopress_custom_tracking_head_hook', 1010, 1);
                    add_action('wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1);
                    add_action('wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1);

                    //ecommerce
                    if ('1' == seopress_google_analytics_purchases_option() || '1' == seopress_google_analytics_add_to_cart_option() || '1' == seopress_google_analytics_remove_from_cart_option()) {
                        add_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);
                    }

                    //Oxygen Builder
                    add_action('ct_before_builder', 'seopress_custom_tracking_body_hook', 1020, 1);
                }
            } else {
                if ('1' == seopress_google_analytics_enable_option() && ('' != seopress_google_analytics_ua_option() || '' != seopress_google_analytics_ga4_option())) {
                    add_action('wp_head', 'seopress_google_analytics_js_arguments', 999, 1);
                    add_action('wp_head', 'seopress_custom_tracking_hook', 1000, 1);
                }
                if ('1' == seopress_google_analytics_matomo_enable_option() && '' != seopress_google_analytics_matomo_id_option() && '' != seopress_google_analytics_matomo_site_id_option()) {
                    add_action('wp_head', 'seopress_matomo_js_arguments', 990, 1);
                }
                add_action('wp_head', 'seopress_custom_tracking_head_hook', 1010, 1);
                add_action('wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1);
                add_action('wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1);

                //ecommerce
                if ('1' == seopress_google_analytics_purchases_option() || '1' == seopress_google_analytics_add_to_cart_option() || '1' == seopress_google_analytics_remove_from_cart_option()) {
                    add_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);
                }

                //Oxygen Builder
                add_action('ct_before_builder', 'seopress_custom_tracking_body_hook', 1020, 1);
            }
        }
    } else {
        if ('1' == seopress_google_analytics_enable_option() && ('' != seopress_google_analytics_ua_option() || '' != seopress_google_analytics_ga4_option())) {
            add_action('wp_head', 'seopress_google_analytics_js_arguments', 999, 1);
            add_action('wp_head', 'seopress_custom_tracking_hook', 1000, 1);
        }
        if ('1' == seopress_google_analytics_matomo_enable_option() && '' != seopress_google_analytics_matomo_id_option() && '' != seopress_google_analytics_matomo_site_id_option()) {
            add_action('wp_head', 'seopress_matomo_js_arguments', 990, 1);
        }
        add_action('wp_head', 'seopress_custom_tracking_head_hook', 1010, 1);
        add_action('wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1);
        add_action('wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1);

        //ecommerce
        if ('1' == seopress_google_analytics_purchases_option() || '1' == seopress_google_analytics_add_to_cart_option() || '1' == seopress_google_analytics_remove_from_cart_option()) {
            add_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);
        }

        add_action('ct_before_builder', 'seopress_custom_tracking_body_hook', 1020, 1);
    }
}
