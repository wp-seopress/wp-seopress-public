<?php

namespace SEOPress\Actions\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ActivationHook;
use SEOPress\Helpers\TagCompose;
use SEOPress\Tags\PostTitle;
use SEOPress\Tags\SiteTagline;
use SEOPress\Tags\SiteTitle;
use SEOPress\Constants\MetasDefaultValues;

class Init implements ActivationHook
{
    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function activate() {
        //Enable features==========================================================================
        $this->setToggleOptions();

        //Titles & metas===========================================================================
        $this->setTitleOptions();

        //XML Sitemap==============================================================================
        $this->setSitemapOptions();

        //Social===================================================================================
        $this->setSocialOptions();

        //Advanced=================================================================================
        $this->setAdvancedOptions();

        //Instant Indexing=================================================================================
        $this->setInstantIndexingOptions();
    }

    /**
     * @since 8.6.0
     *
     * @return void
     */
    protected function setInstantIndexingOptions() {
        $instantIndexingOptions = get_option('seopress_instant_indexing_option_name');

        //Init if option doesn't exist
        if (false === $instantIndexingOptions) {
            $instantIndexingOptions = [];

            if ('1' === seopress_get_toggle_option('instant-indexing')) {
				seopress_instant_indexing_generate_api_key_fn(true);
			}
        }

        $instantIndexingOptions = [
            'seopress_instant_indexing_automate_submission' => '1',
        ];

		// Check if the value is an array (important!)
		if (is_array($instantIndexingOptions)) {
			add_option('seopress_instant_indexing_option_name', $instantIndexingOptions);
		}
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    protected function setAdvancedOptions() {
        $advancedOptions = get_option('seopress_advanced_option_name');

        //Init if option doesn't exist
        if (false === $advancedOptions) {
            $advancedOptions = [];
        }

        $advancedOptions = [
            'seopress_advanced_advanced_attachments'       => '1',
            'seopress_advanced_advanced_tax_desc_editor'   => '1',
            'seopress_advanced_appearance_title_col'       => '1',
            'seopress_advanced_appearance_meta_desc_col'   => '1',
            'seopress_advanced_appearance_score_col'       => '1',
            'seopress_advanced_appearance_noindex_col'     => '1',
            'seopress_advanced_appearance_nofollow_col'    => '1',
            'seopress_advanced_appearance_universal_metabox_disable'    => '1',
            'seopress_advanced_advanced_image_auto_alt_txt'    => '1',
        ];

        //Check if the value is an array (important!)
        if (is_array($advancedOptions)) {
            add_option('seopress_advanced_option_name', $advancedOptions);
        }
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    protected function setSocialOptions() {
        $socialOptions = get_option('seopress_social_option_name');

        //Init if option doesn't exist
        if (false === $socialOptions) {
            $socialOptions = [];
        }

        $socialOptions = [
            'seopress_social_facebook_og'  => '1',
            'seopress_social_twitter_card' => '1',
        ];

        //Check if the value is an array (important!)
        if (is_array($socialOptions)) {
            add_option('seopress_social_option_name', $socialOptions);
        }
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    protected function setSitemapOptions() {
        $sitemapOptions = get_option('seopress_xml_sitemap_option_name');

        //Init if option doesn't exist
        if (false === $sitemapOptions) {
            $sitemapOptions = [];
        }

        $sitemapOptions = [
            'seopress_xml_sitemap_general_enable' => '1',
            'seopress_xml_sitemap_img_enable'     => '1',
        ];

        global $wp_post_types;

        $args = [
            'show_ui' => true,
        ];

        $post_types = get_post_types($args, 'objects', 'and');

        foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) {
            if ('post' === $seopress_cpt_key || 'page' === $seopress_cpt_key || 'product' === $seopress_cpt_key) {
                $sitemapOptions['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'] = '1';
            }
        }

        $args = [
            'show_ui' => true,
            'public'  => true,
        ];

        $taxonomies = get_taxonomies($args, 'objects', 'and');

        foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
            if ('category' === $seopress_tax_key) {
                $sitemapOptions['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'] = '1';
            }
        }

        //Check if the value is an array (important!)
        if (is_array($sitemapOptions)) {
            add_option('seopress_xml_sitemap_option_name', $sitemapOptions);
        }
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    protected function setToggleOptions() {
        $toggleOptions = get_option('seopress_toggle');

        //Init if option doesn't exist
        if (false === $toggleOptions) {
            $toggleOptions = [];
        }

        $toggleOptions = [
            'toggle-titles'           => '1',
            'toggle-xml-sitemap'      => '1',
            'toggle-social'           => '1',
            'toggle-google-analytics' => '1',
            'toggle-instant-indexing' => '1',
            'toggle-advanced'         => '1',
            'toggle-dublin-core'      => '1',
            'toggle-local-business'   => '1',
            'toggle-rich-snippets'    => '1',
            'toggle-breadcrumbs'      => '1',
            'toggle-robots'           => '1',
            'toggle-404'              => '1',
            'toggle-bot'              => '1',
            'toggle-inspect-url'      => '1',
            'toggle-ai'               => '1',
        ];

        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $toggleOptions['toggle-woocommerce'] = '1';
        }

        //Check if the value is an array (important!)
        if (is_array($toggleOptions)) {
            add_option('seopress_toggle', $toggleOptions);
        }
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    protected function setTitleOptions() {
        $titleOptions = get_option('seopress_titles_option_name');

        //Init if option doesn't exist
        if (false === $titleOptions) {
            $titleOptions = [];
        }

        //Site Title
        $titleOptions = [
            'seopress_titles_home_site_title' => TagCompose::getValueWithTag(SiteTitle::NAME),
            'seopress_titles_home_site_desc'  => TagCompose::getValueWithTag(SiteTagline::NAME),
            'seopress_titles_sep'             => '-',
        ];

        //Post Types
        $postTypes = seopress_get_service('WordPressData')->getPostTypes();
        if ( ! empty($postTypes)) {
            foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
                $titleOptions['seopress_titles_single_titles'][$seopress_cpt_key] = [
                    'title' => MetasDefaultValues::getPostTypeTitleValue(),
                    'description' => MetasDefaultValues::getPostTypeDescriptionValue(),
                ];
            }
        }

        //Taxonomies
        $taxonomies = seopress_get_service('WordPressData')->getTaxonomies();
        if (! empty($taxonomies)) {
            foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
                //Title
                if ('category' === $seopress_tax_key) {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = MetasDefaultValues::getTaxonomyCategoryValue();
                } elseif ('post_tag' === $seopress_tax_key) {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = MetasDefaultValues::getTagTitleValue();
                } else {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = MetasDefaultValues::getTermTitleValue();
                }

                //Desc
                if ('category' === $seopress_tax_key) {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = MetasDefaultValues::getTaxonomyCategoryDescriptionValue();
                } elseif ('post_tag' === $seopress_tax_key) {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = MetasDefaultValues::getTagDescriptionValue();
                } else {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = MetasDefaultValues::getTermDescriptionValue();
                }

                //Noindex
                if ('post_tag' === $seopress_tax_key) {
                    $titleOptions['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'] = '1';
                }
            }
        }

        //Archives
        $postTypes = seopress_get_service('WordPressData')->getPostTypes();
        if (! empty($postTypes)) {
            foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
                $titleOptions['seopress_titles_archive_titles'][$seopress_cpt_key]['title'] = MetasDefaultValues::getArchiveTitlePostType();
            }
        }

        //Author
        $titleOptions['seopress_titles_archives_author_title']   =  MetasDefaultValues::getAuthorTitleValue();
        $titleOptions['seopress_titles_archives_author_noindex'] = '1';

        //Date
        $titleOptions['seopress_titles_archives_date_title']   = MetasDefaultValues::getArchiveDateTitleValue();
        $titleOptions['seopress_titles_archives_date_noindex'] = '1';

        //BuddyPress Groups
        if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
            $titleOptions['seopress_titles_bp_groups_title'] = MetasDefaultValues::getPostTypeTitleValue();
        }

        //Search
        $titleOptions['seopress_titles_archives_search_title'] = '%%search_keywords%% %%sep%% %%sitetitle%%';
        $titleOptions['seopress_titles_archives_search_title_noindex'] = '1';

        //404
        $titleOptions['seopress_titles_archives_404_title'] = __('404 - Page not found', 'wp-seopress') . ' %%sep%% %%sitetitle%%';

        //Link rel prev/next
        $titleOptions['seopress_titles_paged_rel'] = '1';

        //Noindex on attachments
        $titleOptions['seopress_titles_attachments_noindex'] = '1';

        //Check if the value is an array (important!)
        if (is_array($titleOptions)) {
            add_option('seopress_titles_option_name', $titleOptions);
        }
    }
}
