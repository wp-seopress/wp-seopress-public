<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox in Custom Taxonomy
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_display_seo_term_metaboxe() {
    add_action('init', 'seopress_init_term_metabox', 11);

    function seopress_init_term_metabox() {
        $seopress_get_taxonomies = seopress_get_service('WordPressData')->getTaxonomies();
        $seopress_get_taxonomies = apply_filters('seopress_metaboxe_term_seo', $seopress_get_taxonomies);

        if ( ! empty($seopress_get_taxonomies)) {
            if (!empty(seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition())) {
                switch (seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition()) {
                    case 'high':
                        $priority = 1;
                        break;
                    case 'default':
                        $priority = 10;
                        break;
                    case 'low':
                        $priority = 100;
                        break;
                    default:
                        $priority = 10;
                        break;
                }
            } else {
                $priority = 10;
            }

            $priority = apply_filters('seopress_metaboxe_term_seo_priority', $priority);

            foreach ($seopress_get_taxonomies as $key => $value) {
                add_action($key . '_edit_form', 'seopress_tax', $priority, 2); //Edit term page
                add_action('edit_' . $key,   'seopress_tax_save_term', $priority, 2); //Edit save term
            }
        }
    }

    function seopress_tax($term) {
        wp_nonce_field(plugin_basename(__FILE__), 'seopress_cpt_nonce');

        global $typenow;
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        //init
        $disabled = [];

        wp_enqueue_script('seopress-cpt-tabs-js', SEOPRESS_ASSETS_DIR . '/js/seopress-metabox' . $prefix . '.js', ['jquery-ui-tabs'], SEOPRESS_VERSION);

        if ('seopress_404' != $typenow) {
            //Tagify
            wp_enqueue_script('seopress-tagify-js', SEOPRESS_ASSETS_DIR . '/js/tagify' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, true);
            wp_register_style('seopress-tagify', SEOPRESS_ASSETS_DIR . '/css/tagify' . $prefix . '.css', [], SEOPRESS_VERSION);
            wp_enqueue_style('seopress-tagify');

            //Register Google Snippet Preview / Content Analysis JS
            wp_enqueue_script('seopress-cpt-counters-js', SEOPRESS_ASSETS_DIR . '/js/seopress-counters' . $prefix . '.js', ['jquery', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-autocomplete'], SEOPRESS_VERSION);

            $seopress_real_preview = [
                'seopress_nonce'         => wp_create_nonce('seopress_real_preview_nonce'),
                'seopress_real_preview'  => admin_url('admin-ajax.php'),
                'i18n'                   => ['progress' => __('Analysis in progress...', 'wp-seopress')],
                'ajax_url'               => admin_url('admin-ajax.php'),
                'get_preview_meta_title' => wp_create_nonce('get_preview_meta_title'),
            ];
            wp_localize_script('seopress-cpt-counters-js', 'seopressAjaxRealPreview', $seopress_real_preview);

            wp_enqueue_script('seopress-media-uploader-js', SEOPRESS_ASSETS_DIR . '/js/seopress-media-uploader' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, false);
            wp_enqueue_media();
        }

        $seopress_titles_title             = get_term_meta($term->term_id, '_seopress_titles_title', true);
        $seopress_titles_desc              = get_term_meta($term->term_id, '_seopress_titles_desc', true);

        $disabled['robots_index'] ='';
        if (seopress_get_service('TitleOption')->getTaxNoIndex() || seopress_get_service('TitleOption')->getTitleNoIndex()) {
            $seopress_robots_index              = 'yes';
            $disabled['robots_index']           = 'disabled';
        } else {
            $seopress_robots_index              = get_term_meta($term->term_id, '_seopress_robots_index', true);
        }

        $disabled['robots_follow'] ='';
        if (seopress_get_service('TitleOption')->getTaxNoFollow() || seopress_get_service('TitleOption')->getTitleNoFollow()) {
            $seopress_robots_follow             = 'yes';
            $disabled['robots_follow']          = 'disabled';
        } else {
            $seopress_robots_follow             = get_term_meta($term->term_id, '_seopress_robots_follow', true);
        }

        $disabled['archive'] ='';
        if (seopress_get_service('TitleOption')->getTitleNoArchive()) {
            $seopress_robots_archive            = 'yes';
            $disabled['archive']                = 'disabled';
        } else {
            $seopress_robots_archive            = get_term_meta($term->term_id, '_seopress_robots_archive', true);
        }

        $disabled['snippet'] ='';
        if (seopress_get_service('TitleOption')->getTitleNoSnippet()) {
            $seopress_robots_snippet            = 'yes';
            $disabled['snippet']                = 'disabled';
        } else {
            $seopress_robots_snippet            = get_term_meta($term->term_id, '_seopress_robots_snippet', true);
        }

        $disabled['imageindex'] ='';
        if (seopress_get_service('TitleOption')->getTitleNoImageIndex()) {
            $seopress_robots_imageindex         = 'yes';
            $disabled['imageindex']             = 'disabled';
        } else {
            $seopress_robots_imageindex         = get_term_meta($term->term_id, '_seopress_robots_imageindex', true);
        }

        $seopress_robots_canonical                  = get_term_meta($term->term_id, '_seopress_robots_canonical', true);
        $seopress_social_fb_title                   = get_term_meta($term->term_id, '_seopress_social_fb_title', true);
        $seopress_social_fb_desc                    = get_term_meta($term->term_id, '_seopress_social_fb_desc', true);
        $seopress_social_fb_img                     = get_term_meta($term->term_id, '_seopress_social_fb_img', true);
        $seopress_social_fb_img_attachment_id       = get_term_meta($term->term_id, '_seopress_social_fb_img_attachment_id', true);
        $seopress_social_fb_img_width               = get_term_meta($term->term_id, '_seopress_social_fb_img_width', true);
        $seopress_social_fb_img_height              = get_term_meta($term->term_id, '_seopress_social_fb_img_height', true);
        $seopress_social_twitter_title              = get_term_meta($term->term_id, '_seopress_social_twitter_title', true);
        $seopress_social_twitter_desc               = get_term_meta($term->term_id, '_seopress_social_twitter_desc', true);
        $seopress_social_twitter_img                = get_term_meta($term->term_id, '_seopress_social_twitter_img', true);
        $seopress_social_twitter_img_attachment_id  = get_term_meta($term->term_id, '_seopress_social_twitter_img_attachment_id', true);
        $seopress_social_twitter_img_width          = get_term_meta($term->term_id, '_seopress_social_twitter_img_width', true);
        $seopress_social_twitter_img_height         = get_term_meta($term->term_id, '_seopress_social_twitter_img_height', true);
        $seopress_redirections_enabled              = get_term_meta($term->term_id, '_seopress_redirections_enabled', true);
        $seopress_redirections_logged_status        = get_term_meta($term->term_id, '_seopress_redirections_logged_status', true);
        $seopress_redirections_type                 = get_term_meta($term->term_id, '_seopress_redirections_type', true);
        $seopress_redirections_value                = get_term_meta($term->term_id, '_seopress_redirections_value', true);

        require_once dirname(dirname(__FILE__)) . '/admin-dyn-variables-helper.php'; //Dynamic variables
        require_once dirname(__FILE__) . '/admin-metaboxes-form.php'; //Metaboxe HTML
    }

    function seopress_tax_save_term($term_id) {
        //Nonce
        if ( ! isset($_POST['seopress_cpt_nonce']) || ! wp_verify_nonce($_POST['seopress_cpt_nonce'], plugin_basename(__FILE__))) {
            return $term_id;
        }

        //Taxonomy object
        $taxonomy = get_taxonomy(get_current_screen()->taxonomy);

        //Check permission
        if ( ! current_user_can($taxonomy->cap->edit_terms, $term_id)) {
            return $term_id;
        }

        $seo_tabs = [];
        $seo_tabs = json_decode(stripslashes(htmlspecialchars_decode($_POST['seo_tabs'])));

        if (in_array('title-tab', $seo_tabs)) {
            if (!empty($_POST['seopress_titles_title'])) {
                update_term_meta($term_id, '_seopress_titles_title', esc_html($_POST['seopress_titles_title']));
            } else {
                delete_term_meta($term_id, '_seopress_titles_title');
            }
            if (!empty($_POST['seopress_titles_desc'])) {
                update_term_meta($term_id, '_seopress_titles_desc', esc_html($_POST['seopress_titles_desc']));
            } else {
                delete_term_meta($term_id, '_seopress_titles_desc');
            }
        }
        if (in_array('advanced-tab', $seo_tabs)) {
            if (isset($_POST['seopress_robots_index'])) {
                update_term_meta($term_id, '_seopress_robots_index', 'yes');
            } else {
                delete_term_meta($term_id, '_seopress_robots_index', '');
            }
            if (isset($_POST['seopress_robots_follow'])) {
                update_term_meta($term_id, '_seopress_robots_follow', 'yes');
            } else {
                delete_term_meta($term_id, '_seopress_robots_follow', '');
            }
            if (isset($_POST['seopress_robots_imageindex'])) {
                update_term_meta($term_id, '_seopress_robots_imageindex', 'yes');
            } else {
                delete_term_meta($term_id, '_seopress_robots_imageindex', '');
            }
            if (isset($_POST['seopress_robots_archive'])) {
                update_term_meta($term_id, '_seopress_robots_archive', 'yes');
            } else {
                delete_term_meta($term_id, '_seopress_robots_archive', '');
            }
            if (isset($_POST['seopress_robots_snippet'])) {
                update_term_meta($term_id, '_seopress_robots_snippet', 'yes');
            } else {
                delete_term_meta($term_id, '_seopress_robots_snippet', '');
            }
            if (!empty($_POST['seopress_robots_canonical'])) {
                update_term_meta($term_id, '_seopress_robots_canonical', esc_html($_POST['seopress_robots_canonical']));
            } else {
                delete_term_meta($term_id, '_seopress_robots_canonical');
            }
        }

        if (in_array('social-tab', $seo_tabs)) {
            //Facebook
            if (!empty($_POST['seopress_social_fb_title'])) {
                update_term_meta($term_id, '_seopress_social_fb_title', esc_html($_POST['seopress_social_fb_title']));
            } else {
                delete_term_meta($term_id, '_seopress_social_fb_title');
            }
            if (!empty($_POST['seopress_social_fb_desc'])) {
                update_term_meta($term_id, '_seopress_social_fb_desc', esc_html($_POST['seopress_social_fb_desc']));
            } else {
                delete_term_meta($term_id, '_seopress_social_fb_desc');
            }
            if (!empty($_POST['seopress_social_fb_img'])) {
                update_term_meta($term_id, '_seopress_social_fb_img', esc_html($_POST['seopress_social_fb_img']));
            }
            if (!empty($_POST['seopress_social_fb_img_attachment_id']) && !empty($_POST['seopress_social_fb_img'])) {
                update_term_meta($term_id, '_seopress_social_fb_img_attachment_id', esc_html($_POST['seopress_social_fb_img_attachment_id']));
            } else {
                delete_term_meta($term_id, '_seopress_social_fb_img_attachment_id');
            }
            if (!empty($_POST['seopress_social_fb_img_width']) && !empty($_POST['seopress_social_fb_img'])) {
                update_term_meta($term_id, '_seopress_social_fb_img_width', esc_html($_POST['seopress_social_fb_img_width']));
            } else {
                delete_term_meta($term_id, '_seopress_social_fb_img_width');
            }
            if (!empty($_POST['seopress_social_fb_img_height']) && !empty($_POST['seopress_social_fb_img'])) {
                update_term_meta($term_id, '_seopress_social_fb_img_height', esc_html($_POST['seopress_social_fb_img_height']));
            } else {
                delete_term_meta($term_id, '_seopress_social_fb_img_height');
            }

            //Twitter
            if (!empty($_POST['seopress_social_twitter_title'])) {
                update_term_meta($term_id, '_seopress_social_twitter_title', esc_html($_POST['seopress_social_twitter_title']));
            } else {
                delete_term_meta($term_id, '_seopress_social_twitter_title');
            }
            if (!empty($_POST['seopress_social_twitter_desc'])) {
                update_term_meta($term_id, '_seopress_social_twitter_desc', esc_html($_POST['seopress_social_twitter_desc']));
            } else {
                delete_term_meta($term_id, '_seopress_social_twitter_desc');
            }
            if (!empty($_POST['seopress_social_twitter_img'])) {
                update_term_meta($term_id, '_seopress_social_twitter_img', esc_html($_POST['seopress_social_twitter_img']));
            }
        }
        if (in_array('redirect-tab', $seo_tabs)) {
            if (isset($_POST['seopress_redirections_type'])) {
                update_term_meta($term_id, '_seopress_redirections_type', $_POST['seopress_redirections_type']);
            }
            if (isset($_POST['seopress_redirections_logged_status'])) {
                update_term_meta($term_id, '_seopress_redirections_logged_status', $_POST['seopress_redirections_logged_status']);
            }
            if (!empty($_POST['seopress_redirections_value'])) {
                update_term_meta($term_id, '_seopress_redirections_value', esc_html($_POST['seopress_redirections_value']));
            } else {
                delete_term_meta($term_id, '_seopress_redirections_value');
            }
            if (isset($_POST['seopress_redirections_enabled'])) {
                update_term_meta($term_id, '_seopress_redirections_enabled', 'yes');
            } else {
                delete_term_meta($term_id, '_seopress_redirections_enabled', '');
            }
        }

        do_action('seopress_seo_metabox_term_save', $term_id, $_POST);
    }
}

if (is_user_logged_in()) {
    if (is_super_admin()) {
        echo seopress_display_seo_term_metaboxe();
    } else {
        global $wp_roles;

        //Get current user role
        if (isset(wp_get_current_user()->roles[0])) {
            $seopress_user_role = wp_get_current_user()->roles[0];

            //If current user role matchs values from Security settings then apply
            if (!empty(seopress_get_service('AdvancedOption')->getSecurityMetaboxRole())) {
                if (array_key_exists($seopress_user_role, seopress_get_service('AdvancedOption')->getSecurityMetaboxRole())) {
                    //do nothing
                } else {
                    echo seopress_display_seo_term_metaboxe();
                }
            } else {
                echo seopress_display_seo_term_metaboxe();
            }
        }
    }
}
