<?php

namespace WPSeoPressElementorAddon\Admin;

if ( ! defined('ABSPATH')) {
    exit();
}

class Document_Settings_Section {
    use \WPSeoPressElementorAddon\Singleton;

    /**
     * Initialize class.
     *
     * @return void
     */
    private function _initialize() {
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'check_security']);
        add_action('elementor/documents/register_controls', [$this, 'add_wp_seopress_section_to_document_settings'], 20);
        add_action('elementor/document/after_save', [$this, 'on_save'], 99, 2);
        add_action('seopress/page-builders/elementor/save_meta', [$this, 'on_seopress_meta_save'], 99);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'register_elements_assets'], 9999);
    }

    /**
     * Is the current user allowed to view metaboxes?
     *
     * @return boolean
     */
    public function check_security($metabox) {
        if (is_bool($metabox)) {
            return true;
        }

        if (is_super_admin()) {
            return true;
        }

        global $wp_roles;

        //Get current user role
        if (isset(wp_get_current_user()->roles[0])) {
            $seopress_user_role = wp_get_current_user()->roles[0];
            //If current user role matchs values from Security settings then apply
            if (empty($metabox)) {
                return true;
            }
            if (!array_key_exists($seopress_user_role, $metabox)) {
                return true;
            }

            return false;
        }
    }

    public function register_elements_assets() {
        wp_register_script(
            'seopress-elementor-base-script',
            SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/js/base.js',
            ['jquery'],
            SEOPRESS_VERSION,
            true
        );

        if (get_current_user_id()) {
            if (get_user_meta(get_current_user_id(), 'elementor_preferences', true)) {
                $settings = get_user_meta(get_current_user_id(), 'elementor_preferences', true);

                if ( ! empty($settings) && isset($settings['ui_theme']) && 'dark' == $settings['ui_theme']) {
                    wp_enqueue_style(
                        'sp-el-dark-mode-style',
                        SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/css/dark-mode.css'
                    );
                }
            }
        }

        global $post;

        $term      = '';
        $origin    = '';
        $post_type = '';
        $post_id   = '';
        $keywords  = '';

        if (is_archive()) {
            $origin = 'term';
        }

        if (is_singular()) {
            $post_id   = $post->ID;
            $post_type = $post->post_type;
            $origin    = 'post';
            $keywords  = get_post_meta($post_id, '_seopress_analysis_target_kw', true);
            if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $is_elementor = true;
            }
        }

        $seopress_real_preview = [
            'seopress_nonce'        => wp_create_nonce('seopress_real_preview_nonce'),
            'seopress_real_preview' => admin_url('admin-ajax.php'),
            'post_id'               => $post_id,
            'i18n'                  => ['progress' => __('Analysis in progress...', 'wp-seopress')],
            'post_type'             => $post_type,
            'post_tax'              => $term,
            'origin'                => $origin,
            'keywords'              => $keywords,
            'is_elementor'          => $is_elementor,
        ];

        wp_localize_script('seopress-elementor-base-script', 'seopressElementorBase', $seopress_real_preview);
    }

    /**
     * Add WP SEOPress section under document settings.
     *
     * @return void
     */
    public function add_wp_seopress_section_to_document_settings(\Elementor\Core\Base\Document $document) {
        $post_id = $document->get_main_id();

        $seo_metabox = seopress_get_service('AdvancedOption')->getSecurityMetaboxRole() ? seopress_get_service('AdvancedOption')->getSecurityMetaboxRole() : true;
        $ca_metabox = seopress_get_service('AdvancedOption')->getSecurityMetaboxRoleContentAnalysis() ? seopress_get_service('AdvancedOption')->getSecurityMetaboxRoleContentAnalysis() : true;

        if ($this->check_security($seo_metabox) === true) {
            $this->_add_title_section($document, $post_id);
            $this->_add_advanced_section($document, $post_id);
            $this->_add_social_section($document, $post_id);
            $this->_add_redirection_section($document, $post_id);
        }

        if ($this->check_security($ca_metabox) === true) {
            $this->_add_content_analysis_section($document, $post_id);
        }
    }

    /**
     * Add title section.
     *
     * @param \Elementor\Core\Base\Document $document
     * @param int                           $post_id
     *
     * @return void
     */
    private function _add_title_section($document, $post_id) {
        $document->start_controls_section(
            'seopress_title_settings',
            [
                'label' => __('SEO Title / Description', 'wp-seopress'),
                'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $s_title = get_post_meta($post_id, '_seopress_titles_title', true);
        $s_desc  = get_post_meta($post_id, '_seopress_titles_desc', true);

        $original_desc = substr(strip_tags(get_the_content(null, true, $post_id)), 0, 140);

        $desc  = $s_desc ? $s_desc : $original_desc;
        $title = ! empty($s_title) ? $s_title : get_the_title($post_id);

        $document->add_control(
            '_seopress_titles_title',
            [
                'label'       => __('Title', 'wp-seopress'),
                'type'        => 'seopresstextlettercounter',
                'field_type'  => 'text',
                'label_block' => true,
                'separator'   => 'none',
                'default'	    => $s_title ? $s_title : '',
            ]
        );

        $document->add_control(
            '_seopress_titles_desc',
            [
                'label'       => __('Meta Description', 'wp-seopress'),
                'type'        => 'seopresstextlettercounter',
                'field_type'  => 'textarea',
                'label_block' => true,
                'separator'   => 'none',
                'default'	    => $s_desc ? $s_desc : '',
            ]
        );

        $document->add_control(
            'social_preview_google',
            [
                'label'       => __('Google Snippet Preview', 'wp-seopress'),
                'type'        => 'seopress-social-preview',
                'label_block' => true,
                'separator'   => 'none',
                'network'     => 'google',
                'title'       => $title ? $title : '',
                'description' => $desc ? $desc : '',
                'link'        => get_permalink($post_id),
                'post_id'     => $post_id,
                'origin'      => is_singular() ? 'post' : 'term',
                'post_type'   => get_post_status($post_id),
            ]
        );

        $document->end_controls_section();
    }

    /**
     * Add advanced section.
     *
     * @param \Elementor\Core\Base\Document $document
     * @param int                           $post_id
     *
     * @return void
     */
    private function _add_advanced_section($document, $post_id) {
        $document->start_controls_section(
            '_seopress_advanced_settings',
            [
                'label' => __('SEO Advanced', 'wp-seopress'),
                'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $robots_index       = get_post_meta($post_id, '_seopress_robots_index', true);
        $robots_follow      = get_post_meta($post_id, '_seopress_robots_follow', true);
        $robots_imageindex  = get_post_meta($post_id, '_seopress_robots_imageindex', true);
        $robots_archive     = get_post_meta($post_id, '_seopress_robots_archive', true);
        $robots_snippet     = get_post_meta($post_id, '_seopress_robots_snippet', true);
        $robots_canonical   = get_post_meta($post_id, '_seopress_robots_canonical', true);
        $robots_primary_cat = get_post_meta($post_id, '_seopress_robots_primary_cat', true);
        $robots_breadcrumbs = get_post_meta($post_id, '_seopress_robots_breadcrumbs', true);

        $document->add_control(
            '_seopress_robots_index',
            [
                'label'       => __('Don\'t display this page in search engine results / XML - HTML sitemaps (noindex)', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SWITCHER,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => 'yes' === $robots_index ? 'yes' : '',
            ]
        );

        $document->add_control(
            '_seopress_robots_follow',
            [
                'label'       => __('Don\'t follow links for this page (nofollow)', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SWITCHER,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => 'yes' === $robots_follow ? 'yes' : '',
            ]
        );

        $document->add_control(
            '_seopress_robots_imageindex',
            [
                'label'       => __('Don\'t index images for this page (noimageindex)', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SWITCHER,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => 'yes' === $robots_imageindex ? 'yes' : '',
            ]
        );

        $document->add_control(
            '_seopress_robots_archive',
            [
                'label'       => __('Don\'t display a "Cached" link in the Google search results (noarchive)', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SWITCHER,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => 'yes' === $robots_archive ? 'yes' : '',
            ]
        );

        $document->add_control(
            '_seopress_robots_snippet',
            [
                'label'       => __('Don\'t display a description in search results for this page (nosnippet)', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SWITCHER,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => 'yes' === $robots_snippet ? 'yes' : '',
            ]
        );

        $document->add_control(
            '_seopress_robots_canonical',
            [
                'label'       => __('Canonical URL', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $robots_canonical ? $robots_canonical : '',
            ]
        );

        global $typenow;
        global $pagenow;
        if (('post' == $typenow || 'product' == $typenow) && ('post.php' == $pagenow || 'post-new.php' == $pagenow)) {
            $cats = get_categories();

            if ('product' == $typenow) {
                $cats = get_the_terms($post_id, 'product_cat');
            }

            if ( ! empty($cats)) {
                $options = [];

                foreach ($cats as $category) {
                    $options[$category->term_id] = $category->name;
                }
                $options['none'] = __('None (will disable this feature)', 'wp-seopress');
            }

            if ( ! empty($options)) {
                $document->add_control(
                    '_seopress_robots_primary_cat',
                    [
                        'label'       => __('Select a primary category', 'wp-seopress'),
                        'description' => __('Set the category that gets used in the %category% permalink and in our breadcrumbs if you have multiple categories.', 'wp-seopress'),
                        'type'        => \Elementor\Controls_Manager::SELECT,
                        'label_block' => true,
                        'separator'   => 'none',
                        'options'     => $options,
                        'default'     => $robots_primary_cat ? (int) $robots_primary_cat : 'none',
                    ]
                );
            }
        }

        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $document->add_control(
                '_seopress_robots_breadcrumbs',
                [
                    'label'       => __('Custom breadcrumbs', 'wp-seopress'),
                    'description' => __('Enter a custom value, useful if your title is too long', 'wp-seopress'),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'separator'   => 'none',
                    'default'     => $robots_breadcrumbs ? $robots_breadcrumbs : '',
                ]
            );
        }

        $document->end_controls_section();
    }

    /**
     * Add social section.
     *
     * @param \Elementor\Core\Base\Document $document
     * @param int                           $post_id
     *
     * @return void
     */
    private function _add_social_section($document, $post_id) {
        $document->start_controls_section(
            '_seopress_social_settings',
            [
                'label' => __('SEO Social', 'wp-seopress'),
                'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $fb_title      = get_post_meta($post_id, '_seopress_social_fb_title', true);
        $fb_desc       = get_post_meta($post_id, '_seopress_social_fb_desc', true);
        $fb_image      = get_post_meta($post_id, '_seopress_social_fb_img', true);
        $twitter_title = get_post_meta($post_id, '_seopress_social_twitter_title', true);
        $twitter_desc  = get_post_meta($post_id, '_seopress_social_twitter_desc', true);
        $twitter_image = get_post_meta($post_id, '_seopress_social_twitter_img', true);

        $default_preview_title = get_the_title($post_id);
        $default_preview_desc  = substr(strip_tags(get_the_content(null, true, $post_id)), 0, 140);

        $document->add_control(
            '_seopress_social_note',
            [
                //'label' => __( 'Important Note', 'wp-seopress' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __('<p class="elementor-control-field-description"><span class="dashicons dashicons-external"></span><a href="https://developers.facebook.com/tools/debug/sharing/?q=' . get_permalink(get_the_id()) . '" target="_blank">Ask Facebook to update its cache</a></p>', 'wp-seopress'),
                //'content_classes' => 'your-class',
            ]
        );

        $document->add_control(
            '_seopress_social_note_2',
            [
                //'label' => __( 'Important Note', 'wp-seopress' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __('<p class="elementor-control-field-description"><strong>Did you know?</strong> LinkedIn, Instagram and Pinterest use the same social metadata as Facebook. Twitter does the same if no Twitter cards tags are defined below.</p>', 'wp-seopress'),
                //'content_classes' => 'your-class',
            ]
        );

        $document->add_control(
            '_seopress_social_fb_title',
            [
                'label'       => __('Facebook Title', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $fb_title ? $fb_title : '',
            ]
        );

        $document->add_control(
            '_seopress_social_fb_desc',
            [
                'label'       => __('Facebook description', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $fb_desc ? $fb_desc : '',
            ]
        );

        $document->add_control(
            '_seopress_social_fb_img',
            [
                'label'       => __('Facebook Thumbnail', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::MEDIA,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => [
                    'url' => $fb_image ? $fb_image : '',
                ],
            ]
        );

        $document->add_control(
            'social_preview_facebook',
            [
                'label'       => __('Facebook Preview', 'wp-seopress'),
                'type'        => 'seopress-social-preview',
                'label_block' => true,
                'separator'   => 'none',
                'network'     => 'facebook',
                'image'       => $fb_image ? $fb_image : '',
                'title'       => $fb_title ? $fb_title : $default_preview_title,
                'description' => $fb_desc ? $fb_desc : $default_preview_desc,
            ]
        );

        $document->add_control(
            '_seopress_social_twitter_title',
            [
                'label'       => __('Twitter Title', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $twitter_title ? $twitter_title : '',
            ]
        );

        $document->add_control(
            '_seopress_social_twitter_desc',
            [
                'label'       => __('Twitter description', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $twitter_desc ? $twitter_desc : '',
            ]
        );

        $document->add_control(
            '_seopress_social_twitter_img',
            [
                'label'       => __('Twitter Thumbnail', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::MEDIA,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => [
                    'url' => $twitter_image ? $twitter_image : '',
                ],
            ]
        );

        $document->add_control(
            'social_preview_twitter',
            [
                'label'       => __('Twitter Preview', 'wp-seopress'),
                'type'        => 'seopress-social-preview',
                'label_block' => true,
                'separator'   => 'none',
                'network'     => 'twitter',
                'image'       => $twitter_image ? $twitter_image : '',
                'title'       => $twitter_title ? $twitter_title : $default_preview_title,
                'description' => $twitter_desc ? $twitter_desc : $default_preview_desc,
            ]
        );

        $document->end_controls_section();
    }

    /**
     * Add redirection section.
     *
     * @param \Elementor\Core\Base\Document $document
     * @param int                           $post_id
     *
     * @return void
     */
    private function _add_redirection_section($document, $post_id) {
        $document->start_controls_section(
            'seopress_redirection_settings',
            [
                'label' => __('SEO Redirection', 'wp-seopress'),
                'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $redirections_enabled = get_post_meta($post_id, '_seopress_redirections_enabled', true);
        $redirections_type    = get_post_meta($post_id, '_seopress_redirections_type', true);
        $redirections_value   = get_post_meta($post_id, '_seopress_redirections_value', true);

        $document->add_control(
            '_seopress_redirections_enabled',
            [
                'label'       => __('Enable redirection?', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SWITCHER,
                'label_block' => false,
                'separator'   => 'none',
                'default'     => 'yes' === $redirections_enabled ? 'yes' : '',
            ]
        );

        $document->add_control(
            '_seopress_redirections_type',
            [
                'label'       => __('URL redirection', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'separator'   => 'none',
                'options'     => [
                    301 => __('301 Moved Permanently', 'wp-seopress'),
                    302 => __('302 Found / Moved Temporarily', 'wp-seopress'),
                    307 => __('307 Moved Temporarily', 'wp-seopress')
                ],
                'default' => $redirections_type ? (int) $redirections_type : 301,
            ]
        );

        $document->add_control(
            '_seopress_redirections_value',
            [
                'label'       => __('Enter your new URL in absolute (e.g. https://www.example.com/)', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $redirections_value ? $redirections_value : '',
            ]
        );

        $document->end_controls_section();
    }

    /**
     * Add Content analysis section.
     *
     * @param \Elementor\Core\Base\Document $document
     * @param int                           $post_id
     *
     * @return void
     */
    private function _add_content_analysis_section($document, $post_id) {
        $document->start_controls_section(
            'seopress_content_analysis_settings',
            [
                'label' => __('SEO Content Analysis', 'wp-seopress'),
                'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $keywords = get_post_meta($post_id, '_seopress_analysis_target_kw', true);

        $document->add_control(
            '_seopress_analysis_note',
            [
                //'label' => __( 'Important Note', 'wp-seopress' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __('<p class="elementor-control-field-description">Enter a few keywords for analysis to help you write optimized content.</p><p class="elementor-control-field-description"><strong>Did you know?</strong> Writing content for your users is the most important thing! If it doesn‘t feel natural, your visitors will leave your site, Google will know it and your ranking will be affected.</p>', 'wp-seopress'),
                //'content_classes' => 'your-class',
            ]
        );

        $document->add_control(
            '_seopress_analysis_target_kw',
            [
                'label'       => __('Target keywords', 'wp-seopress'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => __('Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them', 'wp-seopress'),
                'label_block' => true,
                'separator'   => 'none',
                'default'     => $keywords ? $keywords : '',
            ]
        );

        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $document->add_control(
                'seopress_google_suggest_kw',
                [
                    'label'       => __('Google suggestions', 'wp-seopress'),
                    'type'        => 'seopress-google-suggestions',
                    'label_block' => true,
                    'separator'   => 'none',
                ]
            );
        }

        $document->add_control(
            'seopress_content_analyses',
            [
                'label'       => '',
                'type'        => 'seopress-content-analysis',
                'description' => __('To get the most accurate analysis, save your post first. We analyze all of your source code as a search engine would.', 'wp-seopress'),
                'label_block' => true,
                'separator'   => 'none',
            ]
        );

        $document->end_controls_section();
    }

    /**
     * Before saving of the values in elementor.
     *
     * @param array $data
     *
     * @return void
     */
    public function on_save(\Elementor\Core\Base\Document $document, $data) {
        $settings = ! empty($data['settings']) ? $data['settings'] : [];

        if (empty($settings)) {
            return;
        }

        $post_id = $document->get_main_id();

        if ( ! $post_id) {
            return;
        }

        $seopress_settings = array_filter(
            $settings,
            function ($key) {
                return in_array($key, $this->get_allowed_meta_keys(), true);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (empty($seopress_settings)) {
            return;
        }

        if (isset($seopress_settings['_seopress_social_fb_img'])) {
            $seopress_settings['_seopress_social_fb_img'] = $seopress_settings['_seopress_social_fb_img']['url'];
        }

        if (isset($seopress_settings['_seopress_social_twitter_img'])) {
            $seopress_settings['_seopress_social_twitter_img'] = $seopress_settings['_seopress_social_twitter_img']['url'];
        }

        $seopress_settings = array_map('sanitize_text_field', $seopress_settings);

        $post_id = wp_update_post(
            [
                'ID'         => $post_id,
                'meta_input' => $seopress_settings,
            ]
        );

        if (is_wp_error($post_id)) {
            throw new \Exception($post_id->get_error_message());
        }
    }

    /**
     * Save seopress meta to elementor.
     *
     * @param int $post_id
     *
     * @return void
     */
    public function on_seopress_meta_save($post_id) {
        if ( ! class_exists('\Elementor\Core\Settings\Manager')) {
            return;
        }

        if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $meta = get_post_meta($post_id);

            $seopress_meta = array_filter(
                $meta,
                function ($key) {
                    return in_array($key, $this->get_allowed_meta_keys(), true);
                },
                ARRAY_FILTER_USE_KEY
            );

            if (empty($seopress_meta)) {
                return;
            }

            $settings = [];

            foreach ($seopress_meta as $key => $sm) {
                $settings[$key] = maybe_unserialize( ! empty($sm[0]) ? $sm[0] : '');
            }

            $seo_data['settings'] = $settings;

            $page_settings = get_metadata('post', $post_id, \Elementor\Core\Settings\Page\Manager::META_KEY, true);
            $settings      = array_merge($page_settings, $settings);

            remove_action('seopress/page-builders/elementor/save_meta', [$this, 'on_seopress_meta_save'], 99);
            $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
            $page_settings_manager->ajax_before_save_settings($settings, $post_id);
            $page_settings_manager->save_settings($settings, $post_id);
            add_action('seopress/page-builders/elementor/save_meta', [$this, 'on_seopress_meta_save'], 99);
        }
    }

    public function get_allowed_meta_keys() {
        return seopress_get_meta_helper()->get_meta_fields();
    }
}
