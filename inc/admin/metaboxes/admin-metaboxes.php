<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Restrict SEO metaboxes to user roles
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_advanced_security_metaboxe_role_hook_option()
{
    $seopress_advanced_security_metaboxe_role_hook_option = get_option('seopress_advanced_option_name');
    if (! empty($seopress_advanced_security_metaboxe_role_hook_option)) {
        foreach ($seopress_advanced_security_metaboxe_role_hook_option as $key => $seopress_advanced_security_metaboxe_role_hook_value) {
            $options[$key] = $seopress_advanced_security_metaboxe_role_hook_value;
        }
        if (isset($seopress_advanced_security_metaboxe_role_hook_option['seopress_advanced_security_metaboxe_role'])) {
            return $seopress_advanced_security_metaboxe_role_hook_option['seopress_advanced_security_metaboxe_role'];
        }
    }
}

function seopress_advanced_security_metaboxe_ca_role_hook_option()
{
    $seopress_advanced_security_metaboxe_ca_role_hook_option = get_option('seopress_advanced_option_name');
    if (! empty($seopress_advanced_security_metaboxe_ca_role_hook_option)) {
        foreach ($seopress_advanced_security_metaboxe_ca_role_hook_option as $key => $seopress_advanced_security_metaboxe_ca_role_hook_value) {
            $options[$key] = $seopress_advanced_security_metaboxe_ca_role_hook_value;
        }
        if (isset($seopress_advanced_security_metaboxe_ca_role_hook_option['seopress_advanced_security_metaboxe_ca_role'])) {
            return $seopress_advanced_security_metaboxe_ca_role_hook_option['seopress_advanced_security_metaboxe_ca_role'];
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Check global settings
///////////////////////////////////////////////////////////////////////////////////////////////////
if (! function_exists('seopress_titles_single_cpt_noindex_option')) {
    function seopress_titles_single_cpt_noindex_option()
    {
        global $post;
        $seopress_get_current_cpt = get_post_type($post);

        $seopress_titles_single_cpt_noindex_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_single_cpt_noindex_option)) {
            foreach ($seopress_titles_single_cpt_noindex_option as $key => $seopress_titles_single_cpt_noindex_value) {
                $options[$key] = $seopress_titles_single_cpt_noindex_value;
            }
            if (isset($seopress_titles_single_cpt_noindex_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['noindex'])) {
                return $seopress_titles_single_cpt_noindex_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['noindex'];
            }
        }
    }
}

if (! function_exists('seopress_titles_noindex_option')) {
    function seopress_titles_noindex_option()
    {
        $seopress_titles_noindex_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_noindex_option)) {
            foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value) {
                $options[$key] = $seopress_titles_noindex_value;
            }
            if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) {
                return $seopress_titles_noindex_option['seopress_titles_noindex'];
            }
        }
    }
}

if (! function_exists('seopress_titles_single_cpt_nofollow_option')) {
    function seopress_titles_single_cpt_nofollow_option()
    {
        global $post;
        $seopress_get_current_cpt = get_post_type($post);

        $seopress_titles_single_cpt_nofollow_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_single_cpt_nofollow_option)) {
            foreach ($seopress_titles_single_cpt_nofollow_option as $key => $seopress_titles_single_cpt_nofollow_value) {
                $options[$key] = $seopress_titles_single_cpt_nofollow_value;
            }
            if (isset($seopress_titles_single_cpt_nofollow_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['nofollow'])) {
                return $seopress_titles_single_cpt_nofollow_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['nofollow'];
            }
        }
    }
}

if (! function_exists('seopress_titles_nofollow_option')) {
    function seopress_titles_nofollow_option()
    {
        $seopress_titles_nofollow_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_nofollow_option)) {
            foreach ($seopress_titles_nofollow_option as $key => $seopress_titles_nofollow_value) {
                $options[$key] = $seopress_titles_nofollow_value;
            }
            if (isset($seopress_titles_nofollow_option['seopress_titles_nofollow'])) {
                return $seopress_titles_nofollow_option['seopress_titles_nofollow'];
            }
        }
    }
}

if (! function_exists('seopress_titles_noodp_option')) {
    function seopress_titles_noodp_option()
    {
        $seopress_titles_noodp_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_noodp_option)) {
            foreach ($seopress_titles_noodp_option as $key => $seopress_titles_noodp_value) {
                $options[$key] = $seopress_titles_noodp_value;
            }
            if (isset($seopress_titles_noodp_option['seopress_titles_noodp'])) {
                return $seopress_titles_noodp_option['seopress_titles_noodp'];
            }
        }
    }
}

if (! function_exists('seopress_titles_noarchive_option')) {
    function seopress_titles_noarchive_option()
    {
        $seopress_titles_noarchive_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_noarchive_option)) {
            foreach ($seopress_titles_noarchive_option as $key => $seopress_titles_noarchive_value) {
                $options[$key] = $seopress_titles_noarchive_value;
            }
            if (isset($seopress_titles_noarchive_option['seopress_titles_noarchive'])) {
                return $seopress_titles_noarchive_option['seopress_titles_noarchive'];
            }
        }
    }
}

if (! function_exists('seopress_titles_nosnippet_option')) {
    function seopress_titles_nosnippet_option()
    {
        $seopress_titles_nosnippet_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_nosnippet_option)) {
            foreach ($seopress_titles_nosnippet_option as $key => $seopress_titles_nosnippet_value) {
                $options[$key] = $seopress_titles_nosnippet_value;
            }
            if (isset($seopress_titles_nosnippet_option['seopress_titles_nosnippet'])) {
                return $seopress_titles_nosnippet_option['seopress_titles_nosnippet'];
            }
        }
    }
}

if (! function_exists('seopress_titles_noimageindex_option')) {
    function seopress_titles_noimageindex_option()
    {
        $seopress_titles_noimageindex_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_noimageindex_option)) {
            foreach ($seopress_titles_noimageindex_option as $key => $seopress_titles_noimageindex_value) {
                $options[$key] = $seopress_titles_noimageindex_value;
            }
            if (isset($seopress_titles_noimageindex_option['seopress_titles_noimageindex'])) {
                return $seopress_titles_noimageindex_option['seopress_titles_noimageindex'];
            }
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox in Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_titles_single_cpt_date_option()
{
    global $post;
    $seopress_get_current_cpt = get_post_type($post);

    $seopress_titles_single_cpt_date_option = get_option('seopress_titles_option_name');
    if (! empty($seopress_titles_single_cpt_date_option)) {
        foreach ($seopress_titles_single_cpt_date_option as $key => $seopress_titles_single_cpt_date_value) {
            $options[$key] = $seopress_titles_single_cpt_date_value;
        }
        if (isset($seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'])) {
            return $seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'];
        }
    }
}

function seopress_display_date_snippet()
{
    if (seopress_titles_single_cpt_date_option()) {
        return '<div class="snippet-date">' . get_the_modified_date('M j, Y') . ' - </div>';
    }
}

function seopress_metaboxes_init()
{
    global $typenow;
    global $pagenow;

    $data_attr             = [];
    $data_attr['data_tax'] = '';
    $data_attr['termId']   = '';

    if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
        $data_attr['current_id'] = get_the_id();
        $data_attr['origin']     = 'post';
        $data_attr['title']      = get_the_title($data_attr['current_id']);
    } elseif ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
        global $tag;
        $data_attr['current_id'] = $tag->term_id;
        $data_attr['termId']     = $tag->term_id;
        $data_attr['origin']     = 'term';
        $data_attr['data_tax']   = $tag->taxonomy;
        $data_attr['title']      = $tag->name;
    }

    $data_attr['isHomeId'] = get_option('page_on_front');
    if ('0' === $data_attr['isHomeId']) {
        $data_attr['isHomeId'] = '';
    }

    return $data_attr;
}

function seopress_display_seo_metaboxe()
{
    add_action('add_meta_boxes', 'seopress_init_metabox');
    function seopress_init_metabox()
    {
        if (function_exists('seopress_advanced_appearance_metaboxe_position_option')) {
            $seopress_advanced_appearance_metaboxe_position_option = seopress_advanced_appearance_metaboxe_position_option();
        } else {
            $seopress_advanced_appearance_metaboxe_position_option = 'default';
        }

        if (function_exists('seopress_get_post_types')) {
            $seopress_get_post_types = seopress_get_service('WordPressData')->getPostTypes();

            $seopress_get_post_types = apply_filters('seopress_metaboxe_seo', $seopress_get_post_types);

            if (! empty($seopress_get_post_types) && ! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
                foreach ($seopress_get_post_types as $key => $value) {
                    add_meta_box('seopress_cpt', __('SEO', 'wp-seopress'), 'seopress_cpt', $key, 'normal', $seopress_advanced_appearance_metaboxe_position_option);
                }
            }
            add_meta_box('seopress_cpt', __('SEO', 'wp-seopress'), 'seopress_cpt', 'seopress_404', 'normal', $seopress_advanced_appearance_metaboxe_position_option);
        }
    }

    function seopress_cpt($post)
    {
        global $typenow;
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_nonce_field(plugin_basename(__FILE__), 'seopress_cpt_nonce');

        //init
        $disabled = [];

        wp_enqueue_script('seopress-cpt-tabs-js', SEOPRESS_ASSETS_DIR . '/js/seopress-metabox' . $prefix . '.js', ['jquery-ui-tabs'], SEOPRESS_VERSION);

        if ('seopress_404' != $typenow) {
            wp_enqueue_script('jquery-ui-accordion');

            //Tagify
            wp_enqueue_script('seopress-tagify-js', SEOPRESS_ASSETS_DIR . '/js/tagify.min.js', ['jquery'], SEOPRESS_VERSION, true);
            wp_register_style('seopress-tagify', SEOPRESS_ASSETS_DIR . '/css/tagify.min.css', [], SEOPRESS_VERSION);
            wp_enqueue_style('seopress-tagify');

            //Register Google Snippet Preview / Content Analysis JS
            wp_enqueue_script('seopress-cpt-counters-js', SEOPRESS_ASSETS_DIR . '/js/seopress-counters' . $prefix . '.js', ['jquery', 'jquery-ui-tabs', 'jquery-ui-accordion'], SEOPRESS_VERSION, true);

            //If Gutenberg ON
            if (function_exists('get_current_screen')) {
                $get_current_screen = get_current_screen();
                if (isset($get_current_screen->is_block_editor)) {
                    if ($get_current_screen->is_block_editor) {
                        wp_enqueue_script('seopress-block-editor-js', SEOPRESS_ASSETS_DIR . '/js/seopress-block-editor' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, true);
                    }
                }
            }

            wp_enqueue_script('seopress-cpt-video-sitemap-js', SEOPRESS_ASSETS_DIR . '/js/seopress-sitemap-video' . $prefix . '.js', ['jquery', 'jquery-ui-accordion'], SEOPRESS_VERSION);

            $seopress_real_preview = [
                'seopress_nonce'         => wp_create_nonce('seopress_real_preview_nonce'), // @deprecated 4.4.0
                'seopress_real_preview'  => admin_url('admin-ajax.php'), // @deprecated 4.4.0
                'i18n'                   => ['progress'  => __('Analysis in progress...', 'wp-seopress')],
                'ajax_url'               => admin_url('admin-ajax.php'),
                'get_preview_meta_title' => wp_create_nonce('get_preview_meta_title'),
            ];
            wp_localize_script('seopress-cpt-counters-js', 'seopressAjaxRealPreview', $seopress_real_preview);

            wp_enqueue_script('seopress-media-uploader-js', SEOPRESS_ASSETS_DIR . '/js/seopress-media-uploader' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, false);
            wp_enqueue_media();
        }

        $seopress_titles_title                  = get_post_meta($post->ID, '_seopress_titles_title', true);
        $seopress_titles_desc                   = get_post_meta($post->ID, '_seopress_titles_desc', true);

        $disabled['robots_index'] ='';
        if (seopress_titles_single_cpt_noindex_option() || seopress_titles_noindex_option() || true === post_password_required($post->ID)) {
            $seopress_robots_index              = 'yes';
            $disabled['robots_index']           = 'disabled';
        } else {
            $seopress_robots_index              = get_post_meta($post->ID, '_seopress_robots_index', true);
        }

        $disabled['robots_follow'] ='';
        if (seopress_titles_single_cpt_nofollow_option() || seopress_titles_nofollow_option()) {
            $seopress_robots_follow             = 'yes';
            $disabled['robots_follow']          = 'disabled';
        } else {
            $seopress_robots_follow             = get_post_meta($post->ID, '_seopress_robots_follow', true);
        }

        $disabled['robots_odp'] ='';
        if (seopress_titles_noodp_option()) {
            $seopress_robots_odp                = 'yes';
            $disabled['robots_odp']             = 'disabled';
        } else {
            $seopress_robots_odp                = get_post_meta($post->ID, '_seopress_robots_odp', true);
        }

        $disabled['archive'] ='';
        if (seopress_titles_noarchive_option()) {
            $seopress_robots_archive            = 'yes';
            $disabled['archive']                = 'disabled';
        } else {
            $seopress_robots_archive            = get_post_meta($post->ID, '_seopress_robots_archive', true);
        }

        $disabled['snippet'] ='';
        if (seopress_titles_nosnippet_option()) {
            $seopress_robots_snippet            = 'yes';
            $disabled['snippet']                = 'disabled';
        } else {
            $seopress_robots_snippet            = get_post_meta($post->ID, '_seopress_robots_snippet', true);
        }

        $disabled['imageindex'] ='';
        if (seopress_titles_noimageindex_option()) {
            $seopress_robots_imageindex         = 'yes';
            $disabled['imageindex']             = 'disabled';
        } else {
            $seopress_robots_imageindex         = get_post_meta($post->ID, '_seopress_robots_imageindex', true);
        }

        $seopress_robots_canonical              = get_post_meta($post->ID, '_seopress_robots_canonical', true);
        $seopress_robots_primary_cat            = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $seopress_robots_breadcrumbs            = get_post_meta($post->ID, '_seopress_robots_breadcrumbs', true);
        }
        $seopress_social_fb_title               = get_post_meta($post->ID, '_seopress_social_fb_title', true);
        $seopress_social_fb_desc                = get_post_meta($post->ID, '_seopress_social_fb_desc', true);
        $seopress_social_fb_img                 = get_post_meta($post->ID, '_seopress_social_fb_img', true);
        $seopress_social_fb_img_attachment_id   = get_post_meta($post->ID, '_seopress_social_fb_img_attachment_id', true);
        $seopress_social_fb_img_width           = get_post_meta($post->ID, '_seopress_social_fb_img_width', true);
        $seopress_social_fb_img_height          = get_post_meta($post->ID, '_seopress_social_fb_img_height', true);
        $seopress_social_twitter_title          = get_post_meta($post->ID, '_seopress_social_twitter_title', true);
        $seopress_social_twitter_desc           = get_post_meta($post->ID, '_seopress_social_twitter_desc', true);
        $seopress_social_twitter_img            = get_post_meta($post->ID, '_seopress_social_twitter_img', true);
        $seopress_social_twitter_img_attachment_id            = get_post_meta($post->ID, '_seopress_social_twitter_img_attachment_id', true);
        $seopress_social_twitter_img_width      = get_post_meta($post->ID, '_seopress_social_twitter_img_width', true);
        $seopress_social_twitter_img_height     = get_post_meta($post->ID, '_seopress_social_twitter_img_height', true);
        $seopress_redirections_enabled          = get_post_meta($post->ID, '_seopress_redirections_enabled', true);
        $seopress_redirections_enabled_regex    = get_post_meta($post->ID, '_seopress_redirections_enabled_regex', true);
        $seopress_redirections_logged_status   = get_post_meta($post->ID, '_seopress_redirections_logged_status', true);
        $seopress_redirections_type             = get_post_meta($post->ID, '_seopress_redirections_type', true);
        $seopress_redirections_value            = get_post_meta($post->ID, '_seopress_redirections_value', true);
        $seopress_redirections_param            = get_post_meta($post->ID, '_seopress_redirections_param', true);
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $seopress_news_disabled                 = get_post_meta($post->ID, '_seopress_news_disabled', true);
            $seopress_video_disabled                = get_post_meta($post->ID, '_seopress_video_disabled', true);
            $seopress_video                         = get_post_meta($post->ID, '_seopress_video');
        }

        require_once dirname(dirname(__FILE__)) . '/admin-dyn-variables-helper.php'; //Dynamic variables

        require_once dirname(__FILE__) . '/admin-metaboxes-form.php'; //Metaboxe HTML
    }

    add_action('save_post', 'seopress_save_metabox', 10, 2);
    function seopress_save_metabox($post_id, $post)
    {
        //Nonce
        if (! isset($_POST['seopress_cpt_nonce']) || ! wp_verify_nonce($_POST['seopress_cpt_nonce'], plugin_basename(__FILE__))) {
            return $post_id;
        }

        //Post type object
        $post_type = get_post_type_object($post->post_type);

        //Check permission
        if (! current_user_can($post_type->cap->edit_post, $post_id)) {
            return $post_id;
        }

        if ('attachment' !== get_post_type($post_id)) {
            $seo_tabs = [];
            $seo_tabs = json_decode(stripslashes(htmlspecialchars_decode($_POST['seo_tabs'])));

            if (in_array('title-tab', $seo_tabs)) {
                if (isset($_POST['seopress_titles_title'])) {
                    update_post_meta($post_id, '_seopress_titles_title', esc_html($_POST['seopress_titles_title']));
                }
                if (isset($_POST['seopress_titles_desc'])) {
                    update_post_meta($post_id, '_seopress_titles_desc', esc_html($_POST['seopress_titles_desc']));
                }
            }
            if (in_array('advanced-tab', $seo_tabs)) {
                if (isset($_POST['seopress_robots_index'])) {
                    update_post_meta($post_id, '_seopress_robots_index', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_robots_index', '');
                }
                if (isset($_POST['seopress_robots_follow'])) {
                    update_post_meta($post_id, '_seopress_robots_follow', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_robots_follow', '');
                }
                if (isset($_POST['seopress_robots_odp'])) {
                    update_post_meta($post_id, '_seopress_robots_odp', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_robots_odp', '');
                }
                if (isset($_POST['seopress_robots_imageindex'])) {
                    update_post_meta($post_id, '_seopress_robots_imageindex', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_robots_imageindex', '');
                }
                if (isset($_POST['seopress_robots_archive'])) {
                    update_post_meta($post_id, '_seopress_robots_archive', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_robots_archive', '');
                }
                if (isset($_POST['seopress_robots_snippet'])) {
                    update_post_meta($post_id, '_seopress_robots_snippet', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_robots_snippet', '');
                }
                if (isset($_POST['seopress_robots_canonical'])) {
                    update_post_meta($post_id, '_seopress_robots_canonical', esc_html($_POST['seopress_robots_canonical']));
                }
                if (isset($_POST['seopress_robots_primary_cat'])) {
                    update_post_meta($post_id, '_seopress_robots_primary_cat', esc_html($_POST['seopress_robots_primary_cat']));
                }
                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    if (isset($_POST['seopress_robots_breadcrumbs'])) {
                        update_post_meta($post_id, '_seopress_robots_breadcrumbs', esc_html($_POST['seopress_robots_breadcrumbs']));
                    }
                }
            }
            if (in_array('social-tab', $seo_tabs)) {
                if (isset($_POST['seopress_social_fb_title'])) {
                    update_post_meta($post_id, '_seopress_social_fb_title', esc_html($_POST['seopress_social_fb_title']));
                }
                if (isset($_POST['seopress_social_fb_desc'])) {
                    update_post_meta($post_id, '_seopress_social_fb_desc', esc_html($_POST['seopress_social_fb_desc']));
                }
                if (isset($_POST['seopress_social_fb_img'])) {
                    update_post_meta($post_id, '_seopress_social_fb_img', esc_html($_POST['seopress_social_fb_img']));
                }
                if (isset($_POST['seopress_social_fb_img_attachment_id'])) {
                    update_post_meta($post_id, '_seopress_social_fb_img_attachment_id', esc_html($_POST['seopress_social_fb_img_attachment_id']));
                }
                if (isset($_POST['seopress_social_fb_img_width'])) {
                    update_post_meta($post_id, '_seopress_social_fb_img_width', esc_html($_POST['seopress_social_fb_img_width']));
                }
                if (isset($_POST['seopress_social_fb_img_height'])) {
                    update_post_meta($post_id, '_seopress_social_fb_img_height', esc_html($_POST['seopress_social_fb_img_height']));
                }
                if (isset($_POST['seopress_social_twitter_title'])) {
                    update_post_meta($post_id, '_seopress_social_twitter_title', esc_html($_POST['seopress_social_twitter_title']));
                }
                if (isset($_POST['seopress_social_twitter_desc'])) {
                    update_post_meta($post_id, '_seopress_social_twitter_desc', esc_html($_POST['seopress_social_twitter_desc']));
                }
                if (isset($_POST['seopress_social_twitter_img'])) {
                    update_post_meta($post_id, '_seopress_social_twitter_img', esc_html($_POST['seopress_social_twitter_img']));
                }
                if (isset($_POST['seopress_social_twitter_img_attachment_id'])) {
                    update_post_meta($post_id, '_seopress_social_twitter_img_attachment_id', esc_html($_POST['seopress_social_twitter_img_attachment_id']));
                }
                if (isset($_POST['seopress_social_twitter_img_width'])) {
                    update_post_meta($post_id, '_seopress_social_twitter_img_width', esc_html($_POST['seopress_social_twitter_img_width']));
                }
                if (isset($_POST['seopress_social_twitter_img_height'])) {
                    update_post_meta($post_id, '_seopress_social_twitter_img_height', esc_html($_POST['seopress_social_twitter_img_height']));
                }
            }
            if (in_array('redirect-tab', $seo_tabs)) {
                if (isset($_POST['seopress_redirections_type'])) {
                    update_post_meta($post_id, '_seopress_redirections_type', $_POST['seopress_redirections_type']);
                }
                if (isset($_POST['seopress_redirections_value'])) {
                    update_post_meta($post_id, '_seopress_redirections_value', esc_html($_POST['seopress_redirections_value']));
                }
                if (isset($_POST['seopress_redirections_param'])) {
                    update_post_meta($post_id, '_seopress_redirections_param', esc_html($_POST['seopress_redirections_param']));
                }
                if (isset($_POST['seopress_redirections_enabled'])) {
                    update_post_meta($post_id, '_seopress_redirections_enabled', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_redirections_enabled', '');
                }
                if (isset($_POST['seopress_redirections_enabled_regex'])) {
                    update_post_meta($post_id, '_seopress_redirections_enabled_regex', 'yes');
                } else {
                    delete_post_meta($post_id, '_seopress_redirections_enabled_regex');
                }
                if (isset($_POST['seopress_redirections_logged_status'])) {
                    update_post_meta($post_id, '_seopress_redirections_logged_status', $_POST['seopress_redirections_logged_status']);

                } else {
                    delete_post_meta($post_id, '_seopress_redirections_logged_status');
                }
            }
            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                if (in_array('news-tab', $seo_tabs)) {
                    if (isset($_POST['seopress_news_disabled'])) {
                        update_post_meta($post_id, '_seopress_news_disabled', 'yes');
                    } else {
                        delete_post_meta($post_id, '_seopress_news_disabled', '');
                    }
                }
                if (in_array('video-tab', $seo_tabs)) {
                    if (isset($_POST['seopress_video_disabled'])) {
                        update_post_meta($post_id, '_seopress_video_disabled', 'yes');
                    } else {
                        delete_post_meta($post_id, '_seopress_video_disabled', '');
                    }
                    if (isset($_POST['seopress_video'])) {
                        update_post_meta($post_id, '_seopress_video', $_POST['seopress_video']);
                    }
                }
            }
            if (did_action('elementor/loaded')) {
                $elementor = get_post_meta($post_id, '_elementor_page_settings', true);

                if (! empty($elementor)) {
                    if (isset($_POST['seopress_titles_title'])) {
                        $elementor['_seopress_titles_title'] = esc_html($_POST['seopress_titles_title']);
                    }
                    if (isset($_POST['seopress_titles_desc'])) {
                        $elementor['_seopress_titles_desc'] = esc_html($_POST['seopress_titles_desc']);
                    }
                    if (isset($_POST['seopress_robots_index'])) {
                        $elementor['_seopress_robots_index'] = 'yes';
                    } else {
                        $elementor['_seopress_robots_index'] = '';
                    }
                    if (isset($_POST['seopress_robots_follow'])) {
                        $elementor['_seopress_robots_follow'] = 'yes';
                    } else {
                        $elementor['_seopress_robots_follow'] = '';
                    }
                    if (isset($_POST['seopress_robots_odp'])) {
                        $elementor['_seopress_robots_odp'] = 'yes';
                    } else {
                        $elementor['_seopress_robots_odp'] = '';
                    }
                    if (isset($_POST['seopress_robots_imageindex'])) {
                        $elementor['_seopress_robots_imageindex'] = 'yes';
                    } else {
                        $elementor['_seopress_robots_imageindex'] = '';
                    }
                    if (isset($_POST['seopress_robots_archive'])) {
                        $elementor['_seopress_robots_archive'] = 'yes';
                    } else {
                        $elementor['_seopress_robots_archive'] = '';
                    }
                    if (isset($_POST['seopress_robots_snippet'])) {
                        $elementor['_seopress_robots_snippet'] = 'yes';
                    } else {
                        $elementor['_seopress_robots_snippet'] = '';
                    }
                    if (isset($_POST['seopress_robots_canonical'])) {
                        $elementor['_seopress_robots_canonical'] = esc_html($_POST['seopress_robots_canonical']);
                    }
                    if (isset($_POST['seopress_robots_primary_cat'])) {
                        $elementor['_seopress_robots_primary_cat'] = esc_html($_POST['seopress_robots_primary_cat']);
                    }
                    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                        if (isset($_POST['seopress_robots_breadcrumbs'])) {
                            $elementor['_seopress_robots_breadcrumbs'] = esc_html($_POST['seopress_robots_breadcrumbs']);
                        }
                    }
                    if (isset($_POST['seopress_social_fb_title'])) {
                        $elementor['_seopress_social_fb_title'] = esc_html($_POST['seopress_social_fb_title']);
                    }
                    if (isset($_POST['seopress_social_fb_desc'])) {
                        $elementor['_seopress_social_fb_desc'] = esc_html($_POST['seopress_social_fb_desc']);
                    }
                    if (isset($_POST['seopress_social_fb_img'])) {
                        $elementor['_seopress_social_fb_img'] = esc_html($_POST['seopress_social_fb_img']);
                    }
                    if (isset($_POST['seopress_social_twitter_title'])) {
                        $elementor['_seopress_social_twitter_title'] = esc_html($_POST['seopress_social_twitter_title']);
                    }
                    if (isset($_POST['seopress_social_twitter_desc'])) {
                        $elementor['_seopress_social_twitter_desc'] = esc_html($_POST['seopress_social_twitter_desc']);
                    }
                    if (isset($_POST['seopress_social_twitter_img'])) {
                        $elementor['_seopress_social_twitter_img'] = esc_html($_POST['seopress_social_twitter_img']);
                    }
                    if (isset($_POST['seopress_redirections_type'])) {
                        $elementor['_seopress_redirections_type'] = esc_html($_POST['seopress_redirections_type']);
                    }
                    if (isset($_POST['seopress_redirections_value'])) {
                        $elementor['_seopress_redirections_value'] = esc_html($_POST['seopress_redirections_value']);
                    }
                    if (isset($_POST['seopress_redirections_param'])) {
                        $elementor['_seopress_redirections_param'] = esc_html($_POST['seopress_redirections_param']);
                    }
                    if (isset($_POST['seopress_redirections_enabled'])) {
                        $elementor['_seopress_redirections_enabled'] = 'yes';
                    } else {
                        $elementor['_seopress_redirections_enabled'] = '';
                    }
                    update_post_meta($post_id, '_elementor_page_settings', $elementor);
                }
            }
        }
    }
}

function seopress_display_ca_metaboxe()
{
    add_action('add_meta_boxes', 'seopress_init_ca_metabox');
    function seopress_init_ca_metabox()
    {
        if (function_exists('seopress_advanced_appearance_metaboxe_position_option')) {
            $seopress_advanced_appearance_metaboxe_position_option = seopress_advanced_appearance_metaboxe_position_option();
        } else {
            $seopress_advanced_appearance_metaboxe_position_option = 'default';
        }
        if (function_exists('seopress_get_post_types')) {
            $seopress_get_post_types = seopress_get_service('WordPressData')->getPostTypes();

            $seopress_get_post_types = apply_filters('seopress_metaboxe_content_analysis', $seopress_get_post_types);

            if (! empty($seopress_get_post_types) && ! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
                foreach ($seopress_get_post_types as $key => $value) {
                    add_meta_box('seopress_content_analysis', __('Content analysis', 'wp-seopress'), 'seopress_content_analysis', $key, 'normal', $seopress_advanced_appearance_metaboxe_position_option);
                }
            }
        }
    }

    function seopress_content_analysis($post)
    {
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_nonce_field(plugin_basename(__FILE__), 'seopress_content_analysis_nonce');

        //Tagify
        wp_enqueue_script('seopress-tagify-js', SEOPRESS_ASSETS_DIR . '/js/tagify.min.js', ['jquery'], SEOPRESS_VERSION, true);
        wp_register_style('seopress-tagify', SEOPRESS_ASSETS_DIR . '/css/tagify.min.css', [], SEOPRESS_VERSION);
        wp_enqueue_style('seopress-tagify');

        wp_enqueue_script('seopress-cpt-counters-js', SEOPRESS_ASSETS_DIR . '/js/seopress-counters' . $prefix . '.js', ['jquery', 'jquery-ui-tabs', 'jquery-ui-accordion'], SEOPRESS_VERSION);
        $seopress_real_preview = [
            'seopress_nonce'         => wp_create_nonce('seopress_real_preview_nonce'),
            'seopress_real_preview'  => admin_url('admin-ajax.php'),
            'i18n'                   => ['progress' => __('Analysis in progress...', 'wp-seopress')],
            'ajax_url'               => admin_url('admin-ajax.php'),
            'get_preview_meta_title' => wp_create_nonce('get_preview_meta_title'),
        ];
        wp_localize_script('seopress-cpt-counters-js', 'seopressAjaxRealPreview', $seopress_real_preview);

        $seopress_analysis_target_kw            = get_post_meta($post->ID, '_seopress_analysis_target_kw', true);
        $seopress_analysis_data                 = get_post_meta($post->ID, '_seopress_analysis_data', true);
        $seopress_titles_title                  = get_post_meta($post->ID, '_seopress_titles_title', true);
        $seopress_titles_desc                   = get_post_meta($post->ID, '_seopress_titles_desc', true);

        if (seopress_titles_single_cpt_noindex_option() || seopress_titles_noindex_option() || true === post_password_required($post->ID)) {
            $seopress_robots_index              = 'yes';
        } else {
            $seopress_robots_index              = get_post_meta($post->ID, '_seopress_robots_index', true);
        }

        if (seopress_titles_single_cpt_nofollow_option() || seopress_titles_nofollow_option()) {
            $seopress_robots_follow             = 'yes';
        } else {
            $seopress_robots_follow             = get_post_meta($post->ID, '_seopress_robots_follow', true);
        }

        if (seopress_titles_noodp_option()) {
            $seopress_robots_odp                = 'yes';
        } else {
            $seopress_robots_odp                = get_post_meta($post->ID, '_seopress_robots_odp', true);
        }

        if (seopress_titles_noarchive_option()) {
            $seopress_robots_archive            = 'yes';
        } else {
            $seopress_robots_archive            = get_post_meta($post->ID, '_seopress_robots_archive', true);
        }

        if (seopress_titles_nosnippet_option()) {
            $seopress_robots_snippet            = 'yes';
        } else {
            $seopress_robots_snippet            = get_post_meta($post->ID, '_seopress_robots_snippet', true);
        }

        if (seopress_titles_noimageindex_option()) {
            $seopress_robots_imageindex         = 'yes';
        } else {
            $seopress_robots_imageindex         = get_post_meta($post->ID, '_seopress_robots_imageindex', true);
        }

        require_once dirname(__FILE__) . '/admin-metaboxes-content-analysis-form.php'; //Metaboxe HTML
    }

    add_action('save_post', 'seopress_save_ca_metabox', 10, 2);
    function seopress_save_ca_metabox($post_id, $post)
    {
        //Nonce
        if (! isset($_POST['seopress_content_analysis_nonce']) || ! wp_verify_nonce($_POST['seopress_content_analysis_nonce'], plugin_basename(__FILE__))) {
            return $post_id;
        }

        //Post type object
        $post_type = get_post_type_object($post->post_type);

        //Check permission
        if (! current_user_can($post_type->cap->edit_post, $post_id)) {
            return $post_id;
        }

        if ('attachment' !== get_post_type($post_id)) {
            if (isset($_POST['seopress_analysis_target_kw'])) {
                update_post_meta($post_id, '_seopress_analysis_target_kw', esc_html($_POST['seopress_analysis_target_kw']));
            }

            if (did_action('elementor/loaded')) {
                $elementor = get_post_meta($post_id, '_elementor_page_settings', true);

                if (! empty($elementor)) {
                    if (isset($_POST['seopress_analysis_target_kw'])) {
                        $elementor['_seopress_analysis_target_kw'] = esc_html($_POST['seopress_analysis_target_kw']);
                    }
                    update_post_meta($post_id, '_elementor_page_settings', $elementor);
                }
            }
        }
    }

    //Save metabox values in elementor
    add_action('save_post', 'seopress_update_elementor_fields', 999, 2);
    function seopress_update_elementor_fields($post_id, $post)
    {
        do_action('seopress/page-builders/elementor/save_meta', $post_id);
    }
}

if (is_user_logged_in()) {
    if (is_super_admin()) {
        echo seopress_display_seo_metaboxe();
        echo seopress_display_ca_metaboxe();
    } else {
        global $wp_roles;
        $user = wp_get_current_user();
        //Get current user role
        if (isset($user->roles) && is_array($user->roles) && ! empty($user->roles)) {
            $seopress_user_role = current($user->roles);

            //If current user role matchs values from Security settings then apply -- SEO Metaboxe
            if (function_exists('seopress_advanced_security_metaboxe_role_hook_option') && '' != seopress_advanced_security_metaboxe_role_hook_option()) {
                if (array_key_exists($seopress_user_role, seopress_advanced_security_metaboxe_role_hook_option())) {
                    //do nothing
                } else {
                    echo seopress_display_seo_metaboxe();
                }
            } else {
                echo seopress_display_seo_metaboxe();
            }

            //If current user role matchs values from Security settings then apply -- SEO Content Analysis
            if (function_exists('seopress_advanced_security_metaboxe_ca_role_hook_option') && '' != seopress_advanced_security_metaboxe_ca_role_hook_option()) {
                if (array_key_exists($seopress_user_role, seopress_advanced_security_metaboxe_ca_role_hook_option())) {
                    //do nothing
                } else {
                    echo seopress_display_ca_metaboxe();
                }
            } else {
                echo seopress_display_ca_metaboxe();
            }
        }
    }
}
