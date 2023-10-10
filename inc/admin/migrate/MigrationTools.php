<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_migration_tool($plugin, $name) {
    $seo_title = 'SEOPress';
    if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
        $seo_title = method_exists(seopress_pro_get_service('OptionPro'), 'getWhiteLabelListTitle') && seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() ? seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() : 'SEOPress';
    }

    $html = '<div id="' . $plugin . '-migration-tool" class="postbox section-tool">
        <div class="inside">
                <h3>' . /* translators: %s SEO plugin name */ sprintf(__('Import posts and terms (if available) metadata from %s', 'wp-seopress'), $name) . '</h3>

                <p>' . __('By clicking Migrate, we\'ll import:', 'wp-seopress') . '</p>

                <ul>
                    <li>' . __('Title tags', 'wp-seopress') . '</li>
                    <li>' . __('Meta description', 'wp-seopress') . '</li>
                    <li>' . __('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress') . '</li>';
    if ('premium-seo-pack' != $plugin) {
        $html .= '<li>' . __('Twitter tags (title, description and image thumbnail)', 'wp-seopress') . '</li>';
    }
    if ('wp-meta-seo' != $plugin && 'seo-ultimate' != $plugin) {
        $html .= '<li>' . __('Meta Robots (noindex, nofollow...)', 'wp-seopress') . '</li>';
    }
    if ('wp-meta-seo' != $plugin && 'seo-ultimate' != $plugin && 'slim-seo' != $plugin) {
        $html .= '<li>' . __('Canonical URL', 'wp-seopress') . '</li>';
    }
    if ('wp-meta-seo' != $plugin && 'seo-ultimate' != $plugin && 'squirrly' != $plugin && 'slim-seo' != $plugin) {
        $html .= '<li>' . __('Focus / target keywords', 'wp-seopress') . '</li>';
    }
    if ('wp-meta-seo' != $plugin && 'premium-seo-pack' != $plugin && 'seo-ultimate' != $plugin && 'squirrly' != $plugin && 'aio' != $plugin && 'slim-seo' != $plugin) {
        $html .= '<li>' . __('Primary category', 'wp-seopress') . '</li>';
    }
    if ('wpseo' == $plugin || 'platinum-seo' == $plugin || 'smart-crawl' == $plugin || 'seopressor' == $plugin || 'rk' == $plugin || 'seo-framework' == $plugin || 'aio' == $plugin) {
        $html .= '<li>' . __('Redirect URL', 'wp-seopress') . '</li>';
    }
    $html .= '</ul>

                <div class="seopress-notice is-warning">
                    <p>
                        ' . /* translators: %1$s defaut: SEOPress, %2$s competitor SEO plugin name */ sprintf(__('<strong>WARNING:</strong> Migration will delete / update all <strong>%1$s posts and terms metadata</strong>. Some dynamic variables will not be interpreted. We do <strong>NOT delete any %2$s data</strong>.', 'wp-seopress'), $seo_title, $name) . '
                    </p>
                </div>

                <button id="seopress-' . $plugin . '-migrate" type="button" class="btn btnSecondary">
                    ' . __('Migrate now', 'wp-seopress') . '
                </button>

                <span class="spinner"></span>

                <div class="log"></div>
            </div>
        </div>';

    return $html;
}
