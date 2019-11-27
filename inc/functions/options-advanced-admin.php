<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//MANDATORY for using is_plugin_active
include_once(ABSPATH.'wp-admin/includes/plugin.php');

//Admin notices
//=================================================================================================
//License notice
if (get_option( 'seopress_pro_license_status' ) !='valid' && is_plugin_active('wp-seopress-pro/seopress-pro.php') && !is_multisite()) {
    function seopress_notice_license() {
        $class = 'notice notice-error';
        $message = '<strong>'.__( 'Welcome to SEOPress PRO!', 'wp-seopress' ).'</strong>';
        $message .= '<p>'.__( 'Please activate your license to receive automatic updates and get premium support.', 'wp-seopress' ).'</p>';
        $message .= '<a class="button button-primary" href="'.admin_url( 'admin.php?page=seopress-license' ).'">'.__('Activate License', 'wp-seopress').'</a>';
        if (seopress_get_locale() =='fr') {
            $sp_license_guide = 'https://www.seopress.org/fr/support/guides/activer-licence-seopress-pro/';
        } else {
            $sp_license_guide = 'https://www.seopress.org/support/guides/activate-seopress-pro-license/';
        }
        $message .= '<a href="'.$sp_license_guide.'" target="_blank" style="vertical-align: middle;line-height: 28px;margin: 0 0 0 5px;">'.__('Need help?', 'wp-seopress').'</a>';

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
    }
    add_action( 'admin_notices', 'seopress_notice_license' );
}

//Permalinks notice
global $pagenow;
if (isset($pagenow) && $pagenow == 'options-permalink.php') {
    function seopress_notice_permalinks() {
        $class = 'notice notice-warning';
        $message = '<strong>'.__( 'WARNING', 'wp-seopress' ).'</strong>';
        $message .= '<p>'.__( 'Do NOT change your permalink structure on a production site. Changing URLs can severely damage your SEO.', 'wp-seopress' ).'</p>';

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
    }
    add_action( 'admin_notices', 'seopress_notice_permalinks' );

    if (get_option('permalink_structure') =='') { //If default permalink
        function seopress_notice_no_rewrite_url() {
            $class = 'notice notice-warning';
            $message = '<strong>'.__( 'WARNING', 'wp-seopress' ).'</strong>';
            $message .= '<p>'.__( 'URL rewriting is NOT enabled on your site. Select a permalink structure that is optimized for SEO (NOT Plain).', 'wp-seopress' ).'</p>';

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
        }
        add_action( 'admin_notices', 'seopress_notice_no_rewrite_url' );
    }
}

//Advanced
//=================================================================================================
//Automatic title on media file
function seopress_advanced_advanced_image_auto_title_editor_option() {
    $seopress_advanced_advanced_image_auto_title_editor_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_image_auto_title_editor_option ) ) {
        foreach ($seopress_advanced_advanced_image_auto_title_editor_option as $key => $seopress_advanced_advanced_image_auto_title_editor_value)
            $options[$key] = $seopress_advanced_advanced_image_auto_title_editor_value;
         if (isset($seopress_advanced_advanced_image_auto_title_editor_option['seopress_advanced_advanced_image_auto_title_editor'])) { 
            return $seopress_advanced_advanced_image_auto_title_editor_option['seopress_advanced_advanced_image_auto_title_editor'];
         }
    }
}

//Automatic alt text on media file
function seopress_advanced_advanced_image_auto_alt_editor_option() {
    $seopress_advanced_advanced_image_auto_alt_editor_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_image_auto_alt_editor_option ) ) {
        foreach ($seopress_advanced_advanced_image_auto_alt_editor_option as $key => $seopress_advanced_advanced_image_auto_alt_editor_value)
            $options[$key] = $seopress_advanced_advanced_image_auto_alt_editor_value;
         if (isset($seopress_advanced_advanced_image_auto_alt_editor_option['seopress_advanced_advanced_image_auto_alt_editor'])) { 
            return $seopress_advanced_advanced_image_auto_alt_editor_option['seopress_advanced_advanced_image_auto_alt_editor'];
         }
    }
}

//Automatic caption on media file
function seopress_advanced_advanced_image_auto_caption_editor_option() {
    $seopress_advanced_advanced_image_auto_caption_editor_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_image_auto_caption_editor_option ) ) {
        foreach ($seopress_advanced_advanced_image_auto_caption_editor_option as $key => $seopress_advanced_advanced_image_auto_caption_editor_value)
            $options[$key] = $seopress_advanced_advanced_image_auto_caption_editor_value;
         if (isset($seopress_advanced_advanced_image_auto_caption_editor_option['seopress_advanced_advanced_image_auto_caption_editor'])) { 
            return $seopress_advanced_advanced_image_auto_caption_editor_option['seopress_advanced_advanced_image_auto_caption_editor'];
         }
    }
}

//Automatic desc on media file
function seopress_advanced_advanced_image_auto_desc_editor_option() {
    $seopress_advanced_advanced_image_auto_desc_editor_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_image_auto_desc_editor_option ) ) {
        foreach ($seopress_advanced_advanced_image_auto_desc_editor_option as $key => $seopress_advanced_advanced_image_auto_desc_editor_value)
            $options[$key] = $seopress_advanced_advanced_image_auto_desc_editor_value;
         if (isset($seopress_advanced_advanced_image_auto_desc_editor_option['seopress_advanced_advanced_image_auto_desc_editor'])) { 
            return $seopress_advanced_advanced_image_auto_desc_editor_option['seopress_advanced_advanced_image_auto_desc_editor'];
         }
    }
}

if (seopress_advanced_advanced_image_auto_title_editor_option() !='' ||
seopress_advanced_advanced_image_auto_alt_editor_option() !='' ||
seopress_advanced_advanced_image_auto_caption_editor_option() !='' ||
seopress_advanced_advanced_image_auto_desc_editor_option() !='') {
    add_action( 'add_attachment', 'seopress_auto_image_attr' );
    function seopress_auto_image_attr( $post_ID ) {
        if (wp_attachment_is_image($post_ID)) {
            $img_attr = get_post( $post_ID )->post_title;

            // Sanitize the title: remove hyphens, underscores & extra spaces:
            $img_attr = preg_replace( '%\s*[-_\s]+\s*%', ' ', $img_attr);

            // Sanitize the title: capitalize first letter of every word (other letters lower case)
            $img_attr = ucwords( strtolower( $img_attr ) );

            $img_attr = apply_filters('seopress_auto_image_title', $img_attr);
        
            // Create an array with the image meta (Title, Caption, Description) to be updated
            $img_attr_array = array('ID'=>$post_ID); // Image (ID) to be updated

            if (seopress_advanced_advanced_image_auto_title_editor_option() !='') {
                $img_attr_array['post_title'] = $img_attr; // Set image Title
            }

            if (seopress_advanced_advanced_image_auto_caption_editor_option() !='') {
                $img_attr_array['post_excerpt'] = $img_attr; // Set image Caption
            }

            if (seopress_advanced_advanced_image_auto_desc_editor_option() !='') {
                $img_attr_array['post_content'] = $img_attr; // Set image Desc
            }

            $img_attr_array = apply_filters('seopress_auto_image_attr', $img_attr_array);
            

            // Set the image Alt-Text
            if (seopress_advanced_advanced_image_auto_alt_editor_option() !='') {
                update_post_meta( $post_ID, '_wp_attachment_image_alt', $img_attr );
            }

            // Set the image meta (e.g. Title, Excerpt, Content)
            if (seopress_advanced_advanced_image_auto_title_editor_option() !='' || seopress_advanced_advanced_image_auto_caption_editor_option() !='' || seopress_advanced_advanced_image_auto_desc_editor_option() !='') {
                wp_update_post( $img_attr_array );
            }
        }
    }
}

//Metaboxe position
function seopress_advanced_appearance_metaboxe_position_option() {
    $seopress_advanced_appearance_metaboxe_position_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_metaboxe_position_option ) ) {
        foreach ($seopress_advanced_appearance_metaboxe_position_option as $key => $seopress_advanced_appearance_metaboxe_position_value)
            $options[$key] = $seopress_advanced_appearance_metaboxe_position_value;
         if (isset($seopress_advanced_appearance_metaboxe_position_option['seopress_advanced_appearance_metaboxe_position'])) { 
            return $seopress_advanced_appearance_metaboxe_position_option['seopress_advanced_appearance_metaboxe_position'];
         }
    }
}

//Columns in post types
function seopress_advanced_appearance_title_col_option() {
    $seopress_advanced_appearance_title_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_title_col_option ) ) {
        foreach ($seopress_advanced_appearance_title_col_option as $key => $seopress_advanced_appearance_title_col_value)
            $options[$key] = $seopress_advanced_appearance_title_col_value;
         if (isset($seopress_advanced_appearance_title_col_option['seopress_advanced_appearance_title_col'])) { 
            return $seopress_advanced_appearance_title_col_option['seopress_advanced_appearance_title_col'];
         }
    }
}
function seopress_advanced_appearance_meta_desc_col_option() {
    $seopress_advanced_appearance_meta_desc_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_meta_desc_col_option ) ) {
        foreach ($seopress_advanced_appearance_meta_desc_col_option as $key => $seopress_advanced_appearance_meta_desc_col_value)
            $options[$key] = $seopress_advanced_appearance_meta_desc_col_value;
         if (isset($seopress_advanced_appearance_meta_desc_col_option['seopress_advanced_appearance_meta_desc_col'])) { 
            return $seopress_advanced_appearance_meta_desc_col_option['seopress_advanced_appearance_meta_desc_col'];
         }
    }
}
function seopress_advanced_appearance_redirect_url_col_option() {
    $seopress_advanced_appearance_redirect_url_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_redirect_url_col_option ) ) {
        foreach ($seopress_advanced_appearance_redirect_url_col_option as $key => $seopress_advanced_appearance_redirect_url_col_value)
            $options[$key] = $seopress_advanced_appearance_redirect_url_col_value;
         if (isset($seopress_advanced_appearance_redirect_url_col_option['seopress_advanced_appearance_redirect_url_col'])) { 
            return $seopress_advanced_appearance_redirect_url_col_option['seopress_advanced_appearance_redirect_url_col'];
         }
    }
}
function seopress_advanced_appearance_redirect_enable_col_option() {
    $seopress_advanced_appearance_redirect_enable_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_redirect_enable_col_option ) ) {
        foreach ($seopress_advanced_appearance_redirect_enable_col_option as $key => $seopress_advanced_appearance_redirect_enable_col_value)
            $options[$key] = $seopress_advanced_appearance_redirect_enable_col_value;
         if (isset($seopress_advanced_appearance_redirect_enable_col_option['seopress_advanced_appearance_redirect_enable_col'])) { 
            return $seopress_advanced_appearance_redirect_enable_col_option['seopress_advanced_appearance_redirect_enable_col'];
         }
    }
}
function seopress_advanced_appearance_canonical_option() {
    $seopress_advanced_appearance_canonical_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_canonical_option ) ) {
        foreach ($seopress_advanced_appearance_canonical_option as $key => $seopress_advanced_appearance_canonical_value)
            $options[$key] = $seopress_advanced_appearance_canonical_value;
         if (isset($seopress_advanced_appearance_canonical_option['seopress_advanced_appearance_canonical'])) { 
            return $seopress_advanced_appearance_canonical_option['seopress_advanced_appearance_canonical'];
         }
    }
}
function seopress_advanced_appearance_target_kw_col_option() {
    $seopress_advanced_appearance_target_kw_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_target_kw_col_option ) ) {
        foreach ($seopress_advanced_appearance_target_kw_col_option as $key => $seopress_advanced_appearance_target_kw_col_value)
            $options[$key] = $seopress_advanced_appearance_target_kw_col_value;
         if (isset($seopress_advanced_appearance_target_kw_col_option['seopress_advanced_appearance_target_kw_col'])) { 
            return $seopress_advanced_appearance_target_kw_col_option['seopress_advanced_appearance_target_kw_col'];
         }
    }
}
function seopress_advanced_appearance_noindex_col_option() {
    $seopress_advanced_appearance_noindex_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_noindex_col_option ) ) {
        foreach ($seopress_advanced_appearance_noindex_col_option as $key => $seopress_advanced_appearance_noindex_col_value)
            $options[$key] = $seopress_advanced_appearance_noindex_col_value;
         if (isset($seopress_advanced_appearance_noindex_col_option['seopress_advanced_appearance_noindex_col'])) { 
            return $seopress_advanced_appearance_noindex_col_option['seopress_advanced_appearance_noindex_col'];
         }
    }
}
function seopress_advanced_appearance_nofollow_col_option() {
    $seopress_advanced_appearance_nofollow_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_nofollow_col_option ) ) {
        foreach ($seopress_advanced_appearance_nofollow_col_option as $key => $seopress_advanced_appearance_nofollow_col_value)
            $options[$key] = $seopress_advanced_appearance_nofollow_col_value;
         if (isset($seopress_advanced_appearance_nofollow_col_option['seopress_advanced_appearance_nofollow_col'])) { 
            return $seopress_advanced_appearance_nofollow_col_option['seopress_advanced_appearance_nofollow_col'];
         }
    }
}
function seopress_advanced_appearance_words_col_option() {
    $seopress_advanced_appearance_words_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_words_col_option ) ) {
        foreach ($seopress_advanced_appearance_words_col_option as $key => $seopress_advanced_appearance_words_col_value)
            $options[$key] = $seopress_advanced_appearance_words_col_value;
         if (isset($seopress_advanced_appearance_words_col_option['seopress_advanced_appearance_words_col'])) { 
            return $seopress_advanced_appearance_words_col_option['seopress_advanced_appearance_words_col'];
         }
    }
}
function seopress_advanced_appearance_w3c_col_option() {
    $seopress_advanced_appearance_w3c_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_w3c_col_option ) ) {
        foreach ($seopress_advanced_appearance_w3c_col_option as $key => $seopress_advanced_appearance_w3c_col_value)
            $options[$key] = $seopress_advanced_appearance_w3c_col_value;
         if (isset($seopress_advanced_appearance_w3c_col_option['seopress_advanced_appearance_w3c_col'])) { 
            return $seopress_advanced_appearance_w3c_col_option['seopress_advanced_appearance_w3c_col'];
         }
    }
}
function seopress_advanced_appearance_ps_col_option() {
    $seopress_advanced_appearance_ps_col_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_ps_col_option ) ) {
        foreach ($seopress_advanced_appearance_ps_col_option as $key => $seopress_advanced_appearance_ps_col_value)
            $options[$key] = $seopress_advanced_appearance_ps_col_value;
         if (isset($seopress_advanced_appearance_ps_col_option['seopress_advanced_appearance_ps_col'])) { 
            return $seopress_advanced_appearance_ps_col_option['seopress_advanced_appearance_ps_col'];
         }
    }
}

if (seopress_advanced_appearance_title_col_option() !='' || seopress_advanced_appearance_meta_desc_col_option() !='' || seopress_advanced_appearance_redirect_enable_col_option() !='' || seopress_advanced_appearance_redirect_url_col_option() !='' ||
    seopress_advanced_appearance_canonical_option() !='' || seopress_advanced_appearance_target_kw_col_option() !='' || seopress_advanced_appearance_noindex_col_option() !='' || seopress_advanced_appearance_nofollow_col_option() !='' || seopress_advanced_appearance_words_col_option() !='' || seopress_advanced_appearance_w3c_col_option() !='' || seopress_advanced_appearance_ps_col_option() !='') {
    function seopress_add_columns() {
        foreach (seopress_get_post_types() as $key => $value) {
            add_filter('manage_'.$key.'_posts_columns', 'seopress_title_columns');
            add_action('manage_'.$key.'_posts_custom_column', 'seopress_title_display_column', 10, 2);
            if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' )) {
                add_filter('manage_edit-'.$key.'_columns', 'seopress_title_columns');
            }
        }

        function seopress_title_columns($columns) {
            if(seopress_advanced_appearance_title_col_option() !='') {
                $columns['seopress_title'] = __('Title tag', 'wp-seopress');
            }
            if(seopress_advanced_appearance_meta_desc_col_option() !='') {
                $columns['seopress_desc'] = __('Meta Desc.', 'wp-seopress');
            }
            if(seopress_advanced_appearance_redirect_enable_col_option() !='') {
                $columns['seopress_redirect_enable'] = __('Redirect?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_redirect_url_col_option() !='') {
                $columns['seopress_redirect_url'] = __('Redirect URL', 'wp-seopress');
            }
            if(seopress_advanced_appearance_canonical_option() !='') {
                $columns['seopress_canonical'] = __('Canonical', 'wp-seopress');
            }
            if(seopress_advanced_appearance_target_kw_col_option() !='') {
                $columns['seopress_tkw'] = __('Target Kw', 'wp-seopress');
            }
            if(seopress_advanced_appearance_noindex_col_option() !='') {
                $columns['seopress_noindex'] = __('Noindex?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_nofollow_col_option() !='') {
                $columns['seopress_nofollow'] = __('Nofollow?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_words_col_option() !='') {
                $columns['seopress_words'] = __('Count words?', 'wp-seopress');
            }
            if(seopress_advanced_appearance_w3c_col_option() !='') {
                $columns['seopress_w3c'] = __('W3C check', 'wp-seopress');
            }
            if(seopress_advanced_appearance_ps_col_option() !='') {
                $columns['seopress_ps'] = __('Page Speed', 'wp-seopress');
            }
            return $columns;
        }

        function seopress_title_display_column($column, $post_id) {
            switch ( $column ) {
                case 'seopress_title' :
                    echo '<div id="seopress_title-' . $post_id . '">'.get_post_meta($post_id, "_seopress_titles_title", true).'</div>';
                    break;
                
                case 'seopress_desc';
                    echo '<div id="seopress_desc-' . $post_id . '">'.get_post_meta($post_id, "_seopress_titles_desc", true).'</div>';
                    break;

                case 'seopress_redirect_enable';
                    if (get_post_meta($post_id, "_seopress_redirections_enabled", true) =='yes') {
                        echo '<div id="seopress_redirect_enable-' . $post_id . '"><span class="dashicons dashicons-yes"></span></div>';
                    }
                    break;
                case 'seopress_redirect_url';
                    echo '<div id="seopress_redirect_url-' . $post_id . '">'.get_post_meta($post_id, "_seopress_redirections_value", true).'</div>';
                    break;

                case 'seopress_canonical';
                    echo '<div id="seopress_canonical-' . $post_id . '">'.get_post_meta($post_id, "_seopress_robots_canonical", true).'</div>';
                    break;

                case 'seopress_tkw' :
                    echo '<div id="seopress_tkw-' . $post_id . '">'.get_post_meta($post_id, "_seopress_analysis_target_kw", true).'</div>';
                    break;

                case 'seopress_noindex' :
                    if (get_post_meta($post_id, "_seopress_robots_index", true) =='yes') {
                    	echo '<span class="dashicons dashicons-yes"></span>';
                    }
                    break;
                
                case 'seopress_nofollow' :
                    if (get_post_meta($post_id, "_seopress_robots_follow", true) =='yes') {
                    	echo '<span class="dashicons dashicons-yes"></span>';
                    }
                    break;

                case 'seopress_words' :
                    if (get_the_content() !='') {
                        $seopress_analysis_data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses(get_the_content())), $matches);

                        echo $seopress_analysis_data['words_counter'];
                    }
                    break;

                case 'seopress_w3c' :
                    echo '<a class="seopress-button" href="https://validator.w3.org/nu/?doc='.get_the_permalink().'" title="'.__('Check code quality of this page','wp-seopress').'" target="_blank"><span class="dashicons dashicons-clipboard"></span></a>';
                    break;

                case 'seopress_ps' :
                    echo '<div class="seopress-request-page-speed seopress-button" data_permalink="'.get_the_permalink().'" title="'.__('Analyze this page with Google Page Speed','wp-seopress').'"><span class="dashicons dashicons-dashboard"></span></div>';
                    break;
                
                default :
                    break;
            }
        }
    }
    add_action('init', 'seopress_add_columns', 999);
    
    //Sortable columns
    foreach (seopress_get_post_types() as $key => $value) {
    	add_filter( 'manage_edit-'.$key.'_sortable_columns' , 'seopress_admin_sortable_columns' );
    }
    
    function seopress_admin_sortable_columns($columns) {
    	$columns['seopress_noindex'] = 'seopress_noindex';
    	return $columns;
    }
    
    add_filter( 'pre_get_posts', 'seopress_admin_sort_columns_by');
    function seopress_admin_sort_columns_by( $query ) {
    	if( ! is_admin() ) {
    		return;
    	} else {
	    	$orderby = $query->get('orderby');
	    	if( 'seopress_noindex' == $orderby ) {
	    		$query->set('meta_key', '_seopress_robots_index');
	    		$query->set('orderby','meta_value');
	    	}
    	}
    }
}

//Remove Genesis SEO Metaboxe
function seopress_advanced_appearance_genesis_seo_metaboxe_hook_option() {
	$seopress_advanced_appearance_genesis_seo_metaboxe_hook_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_appearance_genesis_seo_metaboxe_hook_option ) ) {
		foreach ($seopress_advanced_appearance_genesis_seo_metaboxe_hook_option as $key => $seopress_advanced_appearance_genesis_seo_metaboxe_hook_value)
			$options[$key] = $seopress_advanced_appearance_genesis_seo_metaboxe_hook_value;
		 if (isset($seopress_advanced_appearance_genesis_seo_metaboxe_hook_option['seopress_advanced_appearance_genesis_seo_metaboxe'])) { 
		 	return $seopress_advanced_appearance_genesis_seo_metaboxe_hook_option['seopress_advanced_appearance_genesis_seo_metaboxe'];
		 }
	}
}

if (seopress_advanced_appearance_genesis_seo_metaboxe_hook_option() !='') {
	function seopress_advanced_appearance_genesis_seo_metaboxe_hook() {
		remove_action( 'admin_menu', 'genesis_add_inpost_seo_box' );
	}
	add_action('init', 'seopress_advanced_appearance_genesis_seo_metaboxe_hook', 999);
}

//Remove Genesis SEO Menu Link
function seopress_advanced_appearance_genesis_seo_menu_option() {
    $seopress_advanced_appearance_genesis_seo_menu_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_appearance_genesis_seo_menu_option ) ) {
        foreach ($seopress_advanced_appearance_genesis_seo_menu_option as $key => $seopress_advanced_appearance_genesis_seo_menu_value)
            $options[$key] = $seopress_advanced_appearance_genesis_seo_menu_value;
         if (isset($seopress_advanced_appearance_genesis_seo_menu_option['seopress_advanced_appearance_genesis_seo_menu'])) { 
            return $seopress_advanced_appearance_genesis_seo_menu_option['seopress_advanced_appearance_genesis_seo_menu'];
         }
    }
}

if (seopress_advanced_appearance_genesis_seo_menu_option() !='') {
    function seopress_advanced_appearance_genesis_seo_menu_hook() {
        remove_theme_support( 'genesis-seo-settings-menu' );
    }
    add_action('init', 'seopress_advanced_appearance_genesis_seo_menu_hook', 999);
}

//Bulk actions
//noindex
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_noindex' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_noindex' );
}

if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_noindex' );
}

function seopress_bulk_actions_noindex($bulk_actions) {
	$bulk_actions['seopress_noindex'] = __( 'Enable noindex', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_noindex_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_noindex_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_noindex_handler', 10, 3 );
}

function seopress_bulk_action_noindex_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_noindex' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post/term
        update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
		update_term_meta( $post_id, '_seopress_robots_index', 'yes' );
	}
	$redirect_to = add_query_arg( 'bulk_noindex_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_noindex_admin_notice' );

function seopress_bulk_action_noindex_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_noindex_posts'] ) ) {
		$noindex_count = intval( $_REQUEST['bulk_noindex_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to noindex.',
						'%s posts to noindex.',
						$noindex_count,
						'wp-seopress'
						) . '</p></div>', $noindex_count );
	}
}

//index
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_index' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_index' );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_index' );
}

function seopress_bulk_actions_index($bulk_actions) {
	$bulk_actions['seopress_index'] = __( 'Enable index', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_index_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_index_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_index_handler', 10, 3 );
}

function seopress_bulk_action_index_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_index' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
        delete_post_meta( $post_id, '_seopress_robots_index', '' );
		delete_term_meta( $post_id, '_seopress_robots_index', '' );
	}
	$redirect_to = add_query_arg( 'bulk_index_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_index_admin_notice' );

function seopress_bulk_action_index_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_index_posts'] ) ) {
		$index_count = intval( $_REQUEST['bulk_index_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to index.',
						'%s posts to index.',
						$index_count,
						'wp-seopress'
						) . '</p></div>', $index_count );
	}
}

//nofollow
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_nofollow' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_nofollow' );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_nofollow' );
}

function seopress_bulk_actions_nofollow($bulk_actions) {
	$bulk_actions['seopress_nofollow'] = __( 'Enable nofollow', 'wp-seopress');
	return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_nofollow_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_nofollow_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_nofollow_handler', 10, 3 );
}

function seopress_bulk_action_nofollow_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_nofollow' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
        update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
		update_term_meta( $post_id, '_seopress_robots_follow', 'yes' );
	}
	$redirect_to = add_query_arg( 'bulk_nofollow_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_nofollow_admin_notice' );

function seopress_bulk_action_nofollow_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_nofollow_posts'] ) ) {
		$nofollow_count = intval( $_REQUEST['bulk_nofollow_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to nofollow.',
						'%s posts to nofollow.',
						$nofollow_count,
						'wp-seopress'
						) . '</p></div>', $nofollow_count );
	}
}

//follow
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_follow' );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_follow' );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'bulk_actions-edit-product', 'seopress_bulk_actions_follow' );
}

function seopress_bulk_actions_follow($bulk_actions) {
	$bulk_actions['seopress_follow'] = __( 'Enable follow', 'wp-seopress');
	return $bulk_actions;
}

foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_follow_handler', 10, 3 );
}
foreach (seopress_get_taxonomies() as $key => $value) {
	add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_follow_handler', 10, 3 );
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_filter( 'handle_bulk_actions-edit-product', 'seopress_bulk_action_follow_handler', 10, 3 );
}

function seopress_bulk_action_follow_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'seopress_follow' ) {
		return $redirect_to;
	}
	foreach ( $post_ids as $post_id ) {
		// Perform action for each post.
        delete_post_meta( $post_id, '_seopress_robots_follow', '' );
		delete_term_meta( $post_id, '_seopress_robots_follow', '' );
	}
	$redirect_to = add_query_arg( 'bulk_follow_posts', count( $post_ids ), $redirect_to );
	return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_follow_admin_notice' );

function seopress_bulk_action_follow_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_follow_posts'] ) ) {
		$follow_count = intval( $_REQUEST['bulk_follow_posts'] );
		printf( '<div id="message" class="updated fade"><p>' .
				_n( '%s post to follow.',
						'%s posts to follow.',
						$follow_count,
						'wp-seopress'
						) . '</p></div>', $follow_count );
	}
}

//enable 301
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_redirect_enable' );
}

function seopress_bulk_actions_redirect_enable($bulk_actions) {
    $bulk_actions['seopress_enable'] = __( 'Enable redirection', 'wp-seopress');
    return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_redirect_enable_handler', 10, 3 );
}

function seopress_bulk_action_redirect_enable_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'seopress_enable' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        // Perform action for each post.
        update_post_meta( $post_id, '_seopress_redirections_enabled', 'yes' );
    }
    $redirect_to = add_query_arg( 'bulk_enable_redirects_posts', count( $post_ids ), $redirect_to );
    return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_redirect_enable_admin_notice' );

function seopress_bulk_action_redirect_enable_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_enable_redirects_posts'] ) ) {
        $enable_count = intval( $_REQUEST['bulk_enable_redirects_posts'] );
        printf( '<div id="message" class="updated fade"><p>' .
                _n( '%s redirections enabled.',
                        '%s redirections enabled.',
                        $enable_count,
                        'wp-seopress'
                        ) . '</p></div>', $enable_count );
    }
}

//disable 301
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'bulk_actions-edit-'.$key, 'seopress_bulk_actions_redirect_disable' );
}

function seopress_bulk_actions_redirect_disable($bulk_actions) {
    $bulk_actions['seopress_disable'] = __( 'Disable redirection', 'wp-seopress');
    return $bulk_actions;
}
foreach (seopress_get_post_types() as $key => $value) {
    add_filter( 'handle_bulk_actions-edit-'.$key, 'seopress_bulk_action_redirect_disable_handler', 10, 3 );
}

function seopress_bulk_action_redirect_disable_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'seopress_disable' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        // Perform action for each post.
        update_post_meta( $post_id, '_seopress_redirections_enabled', '' );
    }
    $redirect_to = add_query_arg( 'bulk_disable_redirects_posts', count( $post_ids ), $redirect_to );
    return $redirect_to;
}

add_action( 'admin_notices', 'seopress_bulk_action_redirect_disable_admin_notice' );
function seopress_bulk_action_redirect_disable_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_disable_redirects_posts'] ) ) {
        $enable_count = intval( $_REQUEST['bulk_disable_redirects_posts'] );
        printf( '<div id="message" class="updated fade"><p>' .
                _n( '%s redirections disabled.',
                        '%s redirections disabled.',
                        $enable_count,
                        'wp-seopress'
                        ) . '</p></div>', $enable_count );
    }
}

//Quick Edit
add_action( 'quick_edit_custom_box', 'seopress_bulk_quick_edit_custom_box', 10, 2 );
function seopress_bulk_quick_edit_custom_box($column_name) {
 	static $printNonce = TRUE;
    if ( $printNonce ) {
    	$printNonce = FALSE;
        wp_nonce_field( plugin_basename( __FILE__ ), 'seopress_title_edit_nonce' );
    }

    ?>
    <div class="wp-clearfix"></div>
    <fieldset class="inline-edit-col-left inline-edit-book">
    	<div class="inline-edit-col column-<?php echo $column_name; ?>">
	        
	        <?php 
	        	switch ( $column_name ) {
	         	case 'seopress_title':
	            ?>	
	            	<h4><?php _e('SEO','wp-seopress'); ?></h4>
	            	<label class="inline-edit-group">
		            	<span class="title"><?php _e('Title tag','wp-seopress'); ?></span>
		        		<span class="input-text-wrap"><input type="text" name="seopress_title" /></span>
	        		</label>
	        		<?php
	            break;
	            case 'seopress_desc':
                ?>  
                    <label class="inline-edit-group">
                        <span class="title"><?php _e('Meta description','wp-seopress'); ?></span>
                        <span class="input-text-wrap"><textarea cols="18" rows="1" name="seopress_desc" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false"></textarea></span>
                    </label>
                    <?php
                break;
                case 'seopress_tkw':
                ?>  
                    <label class="inline-edit-group">
                        <span class="title"><?php _e('Target keywords','wp-seopress'); ?></span>
                        <span class="input-text-wrap"><input type="text" name="seopress_tkw" /></span>
                    </label>
                    <?php
                break;
                case 'seopress_canonical':
	            ?>	
	            	<label class="inline-edit-group">
		            	<span class="title"><?php _e('Canonical','wp-seopress'); ?></span>
		        		<span class="input-text-wrap"><input type="text" name="seopress_canonical" /></span>
		        	</label>
		        	<?php
                break;
                default :
                break;
	        	}
	        ?>
      	</div>
    </fieldset>
    <?php
}

add_action('save_post','seopress_bulk_quick_edit_save_post', 10, 2);
function seopress_bulk_quick_edit_save_post($post_id) {

    // don't save if Elementor library
    if ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'elementor_library') {
        return $post_id;
    }

    // don't save for autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return $post_id;

    // dont save for revisions
    if ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'revision') {
        return $post_id;
    }

	if (!current_user_can('edit_posts', $post_id)) {
        return;
    }

    $_REQUEST += array("seopress_title_edit_nonce" => '');

    if (!wp_verify_nonce($_REQUEST["seopress_title_edit_nonce"], plugin_basename( __FILE__ ))) {
        return;
    }
    if (isset($_REQUEST['seopress_title'])) {
        update_post_meta($post_id, '_seopress_titles_title', esc_html($_REQUEST['seopress_title']));
    }
    if (isset($_REQUEST['seopress_desc'])) {
        update_post_meta($post_id, '_seopress_titles_desc', esc_html($_REQUEST['seopress_desc']));
    }
    if (isset($_REQUEST['seopress_tkw'])) {
        update_post_meta($post_id, '_seopress_analysis_target_kw', esc_html($_REQUEST['seopress_tkw']));
    }
    if (isset($_REQUEST['seopress_canonical'])) {
        update_post_meta($post_id, '_seopress_robots_canonical', esc_html($_REQUEST['seopress_canonical']));
    }
}

//WP Editor on taxonomy description field
function seopress_advanced_advanced_tax_desc_editor_option() {
    $seopress_advanced_advanced_tax_desc_editor_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_advanced_tax_desc_editor_option ) ) {
        foreach ($seopress_advanced_advanced_tax_desc_editor_option as $key => $seopress_advanced_advanced_tax_desc_editor_value)
            $options[$key] = $seopress_advanced_advanced_tax_desc_editor_value;
         if (isset($seopress_advanced_advanced_tax_desc_editor_option['seopress_advanced_advanced_tax_desc_editor'])) { 
            return $seopress_advanced_advanced_tax_desc_editor_option['seopress_advanced_advanced_tax_desc_editor'];
         }
    }
}
if (seopress_advanced_advanced_tax_desc_editor_option() !='' && current_user_can( 'publish_posts' )) {
    
    function seopress_tax_desc_wp_editor_init() {
        global $pagenow;
        if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
            remove_filter( 'pre_term_description', 'wp_filter_kses' );
            remove_filter( 'term_description', 'wp_kses_data' );

            //Disallow HTML Tags
            if ( !current_user_can( 'unfiltered_html' ) ) {
                add_filter( 'pre_term_description', 'wp_kses_post' );
                add_filter( 'term_description', 'wp_kses_post' );
            }

            //Allow HTML Tags
            add_filter( 'term_description', 'wptexturize' );
            add_filter( 'term_description', 'convert_smilies' );
            add_filter( 'term_description', 'convert_chars' );
            add_filter( 'term_description', 'wpautop' );

        }
    }
    add_action('init', 'seopress_tax_desc_wp_editor_init', 100);
    
    function seopress_tax_desc_wp_editor($tag) {
        global $pagenow;
        if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {

            $content = '';

            if ($pagenow == 'term.php') {
                $editor_id = 'description';
            } elseif($pagenow == 'edit-tags.php') {
                $editor_id = 'tag-description';
            }

            ?>

            <tr class="form-field term-description-wrap">
                <th scope="row"><label for="description"><?php _e( 'Description' ); ?></label></th>
                <td>
                    <?php
                    $settings = array(
                        'textarea_name' => 'description',
                        'textarea_rows' => 10,
                    );
                    wp_editor( htmlspecialchars_decode( $tag->description ), 'html-tag-description', $settings );
                    ?>
                    <p class="description"><?php _e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>
                </td>
                <script type="text/javascript">
                    // Remove default description field
                    jQuery('textarea#description').closest('.form-field').remove();
                </script>
            </tr>

            <?php
        }
    }
    $seopress_get_taxonomies = seopress_get_taxonomies();
    foreach ($seopress_get_taxonomies as $key => $value) {
        add_action($key.'_edit_form_fields', 'seopress_tax_desc_wp_editor', 9, 1);
    }
}
