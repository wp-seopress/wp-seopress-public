<?php

namespace SEOPress\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Helpers\PagesAdmin;

class CustomCapabilities implements ExecuteHooksBackend
{
    /**
     * @since 4.6.0
     *
     * @return void
     */
    public function hooks()
    {
        if ('1' == seopress_get_toggle_option('advanced')) {
            add_filter('seopress_capability', [$this, 'custom'], 9999, 2);
            add_filter('option_page_capability_seopress_titles_option_group', [$this, 'capabilitySaveTitlesMetas']);
            add_filter('option_page_capability_seopress_xml_sitemap_option_group', [$this, 'capabilitySaveXmlSitemap']);
            add_filter('option_page_capability_seopress_social_option_group', [$this, 'capabilitySaveSocial']);
            add_filter('option_page_capability_seopress_google_analytics_option_group', [$this, 'capabilitySaveAnalytics']);
            add_filter('option_page_capability_seopress_advanced_option_group', [$this, 'capabilitySaveAdvanced']);
            add_filter('option_page_capability_seopress_tools_option_group', [$this, 'capabilitySaveTools']);
            add_filter('option_page_capability_seopress_import_export_option_group', [$this, 'capabilitySaveImportExport']);

            add_filter('option_page_capability_seopress_pro_mu_option_group', [$this, 'capabilitySavePro']);
            add_filter('option_page_capability_seopress_pro_option_group', [$this, 'capabilitySavePro']);
            add_filter('option_page_capability_seopress_bot_option_group', [$this, 'capabilitySaveBot']);

            add_action('init', [$this, 'addCapabilities']);
        }
    }

    /**
     * @since 4.6.0
     *
     * @return void
     */
    public function addCapabilities()
    {
        $roles = wp_roles();
        $pages = PagesAdmin::getPages();

        if (isset($roles->role_objects['administrator'])) {
            $role  = $roles->role_objects['administrator'];
            foreach ($pages as $key => $value) {
                $role->add_cap(\sprintf('seopress_manage_%s', $value), true);
            }
        }

        $options = seopress_get_service('AdvancedOption')->getOption();
        if (! $options) {
            return;
        }
        $needle  = 'seopress_advanced_security_metaboxe';

        foreach ($pages as $key => $pageValue) {
            $pageForCapability = PagesAdmin::getPageByCapability($pageValue);
            $capability        = PagesAdmin::getCapabilityByPage($pageForCapability);

            $optionKey=  sprintf('%s_%s', $needle, $pageForCapability);
            if (! \array_key_exists($optionKey, $options)) {
                // Remove all cap for a specific role if option not set
                foreach ($roles->role_objects as $keyRole => $role) {
                    if ('administrator' === $keyRole) {
                        continue;
                    }

                    if($capability === null){
                        continue;
                    }

                    $role->remove_cap(\sprintf('seopress_manage_%s', $capability));
                }
            } else {
                foreach ($roles->role_objects as $keyRole => $role) {
                    if (! \array_key_exists($role->name, $options[$optionKey]) && 'administrator' !== $keyRole) {
                        $role->remove_cap(\sprintf('seopress_manage_%s', $capability));
                    } else {
                        $role->add_cap(\sprintf('seopress_manage_%s', $capability), true);
                    }
                }
            }
        }
    }

    /**
     * @since 4.6.0
     *
     * @param string $cap
     * @param string $context
     *
     * @return string
     */
    public function custom($cap, $context)
    {
        switch ($context) {
            case 'xml_html_sitemap':
            case 'social_networks':
            case 'analytics':
            case 'tools':
            case 'instant_indexing':
            case 'titles_metas':
            case 'advanced':
            case 'pro':
            case 'bot':
                return PagesAdmin::getCustomCapability($context);
            case 'dashboard':
                $capabilities = [
                    'xml_html_sitemap',
                    'social_networks',
                    'analytics',
                    'tools',
                    'instant_indexing',
                    'titles_metas',
                    'advanced',
                    'pro',
                    'bot',
                ];
                foreach ($capabilities as $key => $value) {
                    if (current_user_can(PagesAdmin::getCustomCapability($value))) {
                        return PagesAdmin::getCustomCapability($value);
                    }
                }

                return $cap;
            default:
                return $cap;
        }
    }

    /**
     * @since 4.6.0
     *
     * @param string $cap
     *
     * @return string
     */
    public function capabilitySaveTitlesMetas($cap)
    {
        return PagesAdmin::getCustomCapability('titles_metas');
    }

    public function capabilitySaveXmlSitemap($cap)
    {
        return PagesAdmin::getCustomCapability('xml_html_sitemap');
    }

    public function capabilitySaveSocial($cap)
    {
        return PagesAdmin::getCustomCapability('social_networks');
    }

    public function capabilitySaveAnalytics($cap)
    {
        return PagesAdmin::getCustomCapability('analytics');
    }

    public function capabilitySaveAdvanced($cap)
    {
        return PagesAdmin::getCustomCapability('advanced');
    }

    public function capabilitySaveTools($cap)
    {
        return PagesAdmin::getCustomCapability('tools');
    }

    public function capabilitySaveInstantIndexing($cap)
    {
        return PagesAdmin::getCustomCapability('instant_indexing');
    }

    public function capabilitySaveImportExport($cap)
    {
        return PagesAdmin::getCustomCapability('tools');
    }

    public function capabilitySavePro($cap)
    {
        return PagesAdmin::getCustomCapability('pro');
    }

    public function capabilitySaveBot($cap)
    {
        return PagesAdmin::getCustomCapability('bot');
    }
}
