<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Noindex alert?
function seopress_advanced_appearance_adminbar_noindex_option() {
    $seopress_advanced_appearance_adminbar_noindex_option = get_option('seopress_advanced_option_name');
    if ( ! empty($seopress_advanced_appearance_adminbar_noindex_option)) {
        foreach ($seopress_advanced_appearance_adminbar_noindex_option as $key => $seopress_advanced_appearance_adminbar_noindex_value) {
            $options[$key] = $seopress_advanced_appearance_adminbar_noindex_value;
        }
        if (isset($seopress_advanced_appearance_adminbar_noindex_option['seopress_advanced_appearance_adminbar_noindex'])) {
            return $seopress_advanced_appearance_adminbar_noindex_option['seopress_advanced_appearance_adminbar_noindex'];
        }
    }
}

/**
 * Admin bar customization.
 */
function seopress_admin_bar_links() {
    if (current_user_can(seopress_capability('manage_options', 'admin_bar'))) {
        if (function_exists('seopress_advanced_appearance_adminbar_option') && '1' != seopress_advanced_appearance_adminbar_option()) {
            global $wp_admin_bar;

            $title = '<span class="ab-icon icon-seopress-seopress"></span> ' . __('SEO', 'wp-seopress');
            $title = apply_filters('seopress_adminbar_icon', $title);

            $noindex = '';
            if ('1' != seopress_advanced_appearance_adminbar_noindex_option()) {
                if ('1' == seopress_global_noindex_option() || '1' != get_option('blog_public')) {
                    $noindex .= '<a class="wrap-seopress-noindex" href="' . admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_advanced') . '">';
                    $noindex .= '<span class="ab-icon dashicons dashicons-hidden"></span>';
                    $noindex .= __('noindex is on!', 'wp-seopress');
                    $noindex .= '</a>';
                }
                $noindex = apply_filters('seopress_adminbar_noindex', $noindex);
            }

            // Adds a new top level admin bar link and a submenu to it
            $wp_admin_bar->add_menu([
                'parent'	=> false,
                'id'		   => 'seopress_custom_top_level',
                'title'		=> $title . $noindex,
                'href'		 => admin_url('admin.php?page=seopress-option'),
            ]);

            //noindex/nofollow per CPT
            if (function_exists('get_current_screen') && null != get_current_screen()) {
                if (get_current_screen()->post_type || get_current_screen()->taxonomy) {
                    $robots = '';

                    $options = get_option('seopress_titles_option_name');

                    if (get_current_screen()->taxonomy) {
                        $noindex  = isset($options['seopress_titles_single_titles'][get_current_screen()->taxonomy]['noindex']);
                        $nofollow = isset($options['seopress_titles_single_titles'][get_current_screen()->taxonomy]['nofollow']);
                    } else {
                        $noindex  = isset($options['seopress_titles_single_titles'][get_current_screen()->post_type]['noindex']);
                        $nofollow = isset($options['seopress_titles_single_titles'][get_current_screen()->post_type]['nofollow']);
                    }

                    if (get_current_screen()->taxonomy) {
                        /* translators: %s taxonomy name */
                        $robots .= '<span class="wrap-seopress-cpt-seo">' . sprintf(__('SEO for "%s"', 'wp-seopress'), get_current_screen()->taxonomy) . '</span>';
                    } else {
                        /* translators: %s custom post type name */
                        $robots .= '<span class="wrap-seopress-cpt-seo">' . sprintf(__('SEO for "%s"', 'wp-seopress'), get_current_screen()->post_type) . '</span>';
                    }
                    $robots .= '<span class="wrap-seopress-cpt-noindex">';

                    if (true === $noindex) {
                        $robots .= '<span class="ab-icon dashicons dashicons-marker on"></span>';
                        $robots .= __('noindex is on!', 'wp-seopress');
                    } else {
                        $robots .= '<span class="ab-icon dashicons dashicons-marker off"></span>';
                        $robots .= __('noindex is off.', 'wp-seopress');
                    }

                    $robots .= '</span>';

                    $robots .= '<span class="wrap-seopress-cpt-nofollow">';

                    if (true === $nofollow) {
                        $robots .= '<span class="ab-icon dashicons dashicons-marker on"></span>';
                        $robots .= __('nofollow is on!', 'wp-seopress');
                    } else {
                        $robots .= '<span class="ab-icon dashicons dashicons-marker off"></span>';
                        $robots .= __('nofollow is off.', 'wp-seopress');
                    }

                    $robots .= '</span>';

                    $wp_admin_bar->add_menu([
                        'parent'	=> 'seopress_custom_top_level',
                        'id'		   => 'seopress_custom_sub_menu_meta_robots',
                        'title'		=> $robots,
                        'href'		 => admin_url('admin.php?page=seopress-titles'),
                    ]);
                }
            }

            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_titles',
                'title'		=> __('Titles & Metas', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-titles'),
            ]);
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_xml_sitemap',
                'title'		=> __('XML / HTML Sitemap', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-xml-sitemap'),
            ]);
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_social',
                'title'		=> __('Social Networks', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-social'),
            ]);
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_google_analytics',
                'title'		=> __('Analytics', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-google-analytics'),
            ]);
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_instant_indexing',
                'title'		=> __('Instant Indexing', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-instant-indexing'),
            ]);
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_advanced',
                'title'		=> __('Advanced', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-advanced'),
            ]);
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                $wp_admin_bar->add_menu([
                    'parent'	=> 'seopress_custom_top_level',
                    'id'		   => 'seopress_custom_sub_menu_insights',
                    'title'		=> __('Insights', 'wp-seopress'),
                    'href'		 => admin_url('admin.php?page=seopress-insights'),
                ]);
            }
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_import_export',
                'title'		=> __('Tools', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-import-export'),
            ]);
            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                if ('1' == seopress_get_toggle_option('bot')) {
                    $wp_admin_bar->add_menu([
                        'parent'	=> 'seopress_custom_top_level',
                        'id'		   => 'seopress_custom_sub_menu_bot',
                        'title'		=> __('BOT', 'wp-seopress'),
                        'href'		 => admin_url('admin.php?page=seopress-bot-batch'),
                    ]);
                }
                $wp_admin_bar->add_menu([
                    'parent'	=> 'seopress_custom_top_level',
                    'id'		   => 'seopress_custom_sub_menu_license',
                    'title'		=> __('License', 'wp-seopress'),
                    'href'		 => admin_url('admin.php?page=seopress-license'),
                ]);
                $wp_admin_bar->add_menu([
                    'parent'	=> 'seopress_custom_top_level',
                    'id'		   => 'seopress_custom_sub_menu_pro',
                    'title'		=> __('PRO', 'wp-seopress'),
                    'href'		 => admin_url('admin.php?page=seopress-pro-page'),
                ]);
                if ('1' == seopress_get_toggle_option('rich-snippets')) {
                    $wp_admin_bar->add_menu([
                        'parent'	=> 'seopress_custom_top_level',
                        'id'		   => 'seopress_custom_sub_menu_schemas',
                        'title'		=> __('Schemas', 'wp-seopress'),
                        'href'		 => admin_url('edit.php?post_type=seopress_schemas'),
                    ]);
                }
                if ('1' == seopress_get_toggle_option('404')) {
                    $wp_admin_bar->add_menu([
                        'parent'	=> 'seopress_custom_top_level',
                        'id'		   => 'seopress_custom_sub_menu_404',
                        'title'		=> __('Redirections', 'wp-seopress'),
                        'href'		 => admin_url('edit.php?post_type=seopress_404'),
                    ]);
                }
                if ('1' == seopress_get_toggle_option('bot')) {
                    $wp_admin_bar->add_menu([
                        'parent'	=> 'seopress_custom_top_level',
                        'id'		   => 'seopress_custom_sub_menu_broken_links',
                        'title'		=> __('Broken Links', 'wp-seopress'),
                        'href'		 => admin_url('edit.php?post_type=seopress_bot'),
                    ]);
                }
            }
            $wp_admin_bar->add_menu([
                'parent'	=> 'seopress_custom_top_level',
                'id'		   => 'seopress_custom_sub_menu_wizard',
                'title'		=> __('Configuration wizard', 'wp-seopress'),
                'href'		 => admin_url('admin.php?page=seopress-setup'),
            ]);
        }
    }
}
add_action('admin_bar_menu', 'seopress_admin_bar_links', 99);
