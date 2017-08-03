<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Restrict SEO metaboxes to user roles
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_advanced_security_metaboxe_role_hook_option() {
    $seopress_advanced_security_metaboxe_role_hook_option = get_option("seopress_advanced_option_name");
    if ( ! empty ( $seopress_advanced_security_metaboxe_role_hook_option ) ) {
        foreach ($seopress_advanced_security_metaboxe_role_hook_option as $key => $seopress_advanced_security_metaboxe_role_hook_value)
            $options[$key] = $seopress_advanced_security_metaboxe_role_hook_value;
         if (isset($seopress_advanced_security_metaboxe_role_hook_option['seopress_advanced_security_metaboxe_role'])) { 
            return $seopress_advanced_security_metaboxe_role_hook_option['seopress_advanced_security_metaboxe_role'];
         }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox in Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_display_seo_metaboxe() {
    add_action('add_meta_boxes','seopress_init_metabox');
    function seopress_init_metabox(){
        foreach (seopress_get_post_types() as $key => $value) {
            add_meta_box('seopress_cpt', __('SEO','wp-seopress'), 'seopress_cpt', $key, 'normal', 'high');
        }
        add_meta_box('seopress_cpt', __('SEO','wp-seopress'), 'seopress_cpt', 'seopress_404', 'normal', 'high');
    }

    function seopress_cpt($post){
        global $typenow;
        $seopress_titles_title                  = get_post_meta($post->ID,'_seopress_titles_title',true);
        $seopress_titles_desc                   = get_post_meta($post->ID,'_seopress_titles_desc',true);
        $seopress_robots_index                  = get_post_meta($post->ID,'_seopress_robots_index',true);
        $seopress_robots_follow                 = get_post_meta($post->ID,'_seopress_robots_follow',true);
        $seopress_robots_odp                    = get_post_meta($post->ID,'_seopress_robots_odp',true);
        $seopress_robots_imageindex             = get_post_meta($post->ID,'_seopress_robots_imageindex',true);
        $seopress_robots_archive                = get_post_meta($post->ID,'_seopress_robots_archive',true);
        $seopress_robots_snippet                = get_post_meta($post->ID,'_seopress_robots_snippet',true);
        $seopress_robots_canonical              = get_post_meta($post->ID,'_seopress_robots_canonical',true);
        $seopress_social_fb_title               = get_post_meta($post->ID,'_seopress_social_fb_title',true);
        $seopress_social_fb_desc                = get_post_meta($post->ID,'_seopress_social_fb_desc',true);
        $seopress_social_fb_img                 = get_post_meta($post->ID,'_seopress_social_fb_img',true);    
        $seopress_social_twitter_title          = get_post_meta($post->ID,'_seopress_social_twitter_title',true);
        $seopress_social_twitter_desc           = get_post_meta($post->ID,'_seopress_social_twitter_desc',true);
        $seopress_social_twitter_img            = get_post_meta($post->ID,'_seopress_social_twitter_img',true);
        $seopress_redirections_enabled          = get_post_meta($post->ID,'_seopress_redirections_enabled',true);
        $seopress_redirections_type             = get_post_meta($post->ID,'_seopress_redirections_type',true);
        $seopress_redirections_value            = get_post_meta($post->ID,'_seopress_redirections_value',true);
        $seopress_news_disabled                 = get_post_meta($post->ID,'_seopress_news_disabled',true);
        $seopress_news_genres                   = get_post_meta($post->ID,'_seopress_news_genres',true);
        $seopress_news_keyboard                 = get_post_meta($post->ID,'_seopress_news_keyboard',true);

        function seopress_titles_title($seopress_titles_title) {
            if ($seopress_titles_title !='') {
                return $seopress_titles_title;
            } else {
                return get_the_title().' - '.get_bloginfo('name');
            }
        }
        
        function seopress_titles_single_desc_option() {
            global $post;
            $seopress_get_current_cpt = get_post_type($post);
        
            $seopress_titles_single_desc_option = get_option("seopress_titles_option_name");
            if ( ! empty ( $seopress_titles_single_desc_option ) ) {
                foreach ($seopress_titles_single_desc_option as $key => $seopress_titles_single_desc_value)
                    $options[$key] = $seopress_titles_single_desc_value;
                    if (isset($seopress_titles_single_desc_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['description'])) {
                        return $seopress_titles_single_desc_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['description'];
                    }
            }
        }
        
        function seopress_titles_desc($seopress_titles_desc) {
            if ($seopress_titles_desc !='') {
                return $seopress_titles_desc;
            } else {
                global $post;
                if (seopress_titles_single_desc_option() !='') {
                    return seopress_titles_single_desc_option();
                } elseif ( has_excerpt( $post->ID ) ) {
                    // This post has excerpt
                    return substr(wp_strip_all_tags($post->post_excerpt, true), 0, 160);
                } else {
                    // This post has no excerpt
                    return substr(wp_strip_all_tags($post->post_content, true), 0, 160);
                }          
            }
        }

        function seopress_titles_single_cpt_date_option() {
            global $post;
            $seopress_get_current_cpt = get_post_type($post);

            $seopress_titles_single_cpt_date_option = get_option("seopress_titles_option_name");
            if ( ! empty ( $seopress_titles_single_cpt_date_option ) ) {
                foreach ($seopress_titles_single_cpt_date_option as $key => $seopress_titles_single_cpt_date_value)
                    $options[$key] = $seopress_titles_single_cpt_date_value;
                 if (isset($seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'])) { 
                    return $seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'];
                 }
            }
        };

        function seopress_display_date_snippet() {
            if (seopress_titles_single_cpt_date_option()) {
                return '<div class="snippet-date">'.get_the_date('M j, Y').' - </div>';
            }
        }

        function seopress_redirections_value($seopress_redirections_value) {
            if ($seopress_redirections_value !='') {
                return $seopress_redirections_value;
            }
        }

        echo '<div id="seopress-tabs">';
             echo'<ul>';
                    if ("seopress_404" != $typenow) {
                        echo '<li><a href="#tabs-1"><span class="dashicons dashicons-editor-table"></span>'. __( 'Titles settings', 'wp-seopress' ) .'</a></li>
                        <li><a href="#tabs-2"><span class="dashicons dashicons-admin-generic"></span>'. __( 'Advanced', 'wp-seopress' ) .'</a></li>
                        <li><a href="#tabs-3"><span class="dashicons dashicons-share"></span>'. __( 'Social', 'wp-seopress' ) .'</a></li>';
                    }
                    echo '<li><a href="#tabs-4"><span class="dashicons dashicons-admin-links"></span>'. __( 'Redirection', 'wp-seopress' ) .'</a></li>';
                    if (is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
                        if ("seopress_404" != $typenow) {
                            echo '<li><a href="#tabs-5"><span class="dashicons dashicons-admin-post"></span>'. __( 'Google News', 'wp-seopress-pro' ) .'</a></li>';
                        }
                    }
                echo '</ul>';
                
                if ("seopress_404" != $typenow) {
                echo '<div id="tabs-1">
                    <div class="box-left">
                        <p>
                            <label for="seopress_titles_title_meta">'. __( 'Title', 'wp-seopress' ) .'</label>
                            <input id="seopress_titles_title_meta" type="text" name="seopress_titles_title" placeholder="'.__('Enter your title','wp-seopress').'" value="'.$seopress_titles_title.'" />
                        </p> 
                        <div class="wrap-seopress-counters">
                            <div id="seopress_titles_title_counters"></div>
                            '.__('(maximum recommended limit)','wp-seopress').'
                        </div>
                        <p>
                            <label for="seopress_titles_desc_meta">'. __( 'Meta description', 'wp-seopress' ) .'</label>
                            <textarea id="seopress_titles_desc_meta" style="width:100%" name="seopress_titles_desc" placeholder="'.__('Enter your meta description','wp-seopress').'" value="'.$seopress_titles_desc.'">'.$seopress_titles_desc.'</textarea>
                        </p>
                        <div class="wrap-seopress-counters">
                            <div id="seopress_titles_desc_counters"></div>
                            '.__('(maximum recommended limit)','wp-seopress').'
                        </div>
                    </div>
                    <div class="box-right">
                        <div class="google-snippet-preview">
                            <h3>'.__('Google Snippet Preview','wp-seopress').'</h3>
                            <p>'.__('This is what your page will look like in Google search results','wp-seopress').'</p>
                            <div class="snippet-title">'.seopress_titles_title($seopress_titles_title).'</div>
                            <div class="snippet-title-custom" style="display:none"></div>
                            <div class="snippet-title-default" style="display:none">'.get_the_title().' - '.get_bloginfo('name').'</div>
                            <div class="snippet-permalink">'.get_permalink().'</div>';
        echo                seopress_display_date_snippet();
        echo                '<div class="snippet-description">'.seopress_titles_desc($seopress_titles_desc).'...</div>
                            <div class="snippet-description-custom" style="display:none"></div>
                            <div class="snippet-description-default" style="display:none">'.seopress_titles_desc($seopress_titles_desc).'</div>';
                    echo '</div>
                    </div>
                </div>
                <div id="tabs-2">
                    <p>
                        <label for="seopress_robots_index_meta">
                            <input type="checkbox" name="seopress_robots_index" id="seopress_robots_index_meta" value="yes" '. checked( $seopress_robots_index, 'yes', false ) .' />
                                '. __( 'noindex', 'wp-seopress' ) .'
                        </label><span class="dashicons dashicons-info" title="'.esc_html(__('Do not display all pages of the site in Google search results and do not display "Cached" links in search results.','wp-seopress')).'"></span>
                    </p>
                    <p>
                        <label for="seopress_robots_follow_meta">
                            <input type="checkbox" name="seopress_robots_follow" id="seopress_robots_follow_meta" value="yes" '. checked( $seopress_robots_follow, 'yes', false ) .' />
                                '. __( 'nofollow', 'wp-seopress' ) .'
                        </label><span class="dashicons dashicons-info" title="'.esc_html(__('Do not follow links for all pages.','wp-seopress')).'"></span>
                    </p>
                    <p>
                        <label for="seopress_robots_odp_meta">
                            <input type="checkbox" name="seopress_robots_odp" id="seopress_robots_odp_meta" value="yes" '. checked( $seopress_robots_odp, 'yes', false ) .' />
                                '. __( 'noodp', 'wp-seopress' ) .'
                        </label><span class="dashicons dashicons-info" title="'.esc_html(__('Do not use Open Directory project metadata for titles or excerpts for all pages.','wp-seopress')).'"></span>
                    </p>
                    <p>
                        <label for="seopress_robots_imageindex_meta">
                            <input type="checkbox" name="seopress_robots_imageindex" id="seopress_robots_imageindex_meta" value="yes" '. checked( $seopress_robots_imageindex, 'yes', false ) .' />
                                '. __( 'noimageindex', 'wp-seopress' ) .'
                        </label><span class="dashicons dashicons-info" title="'.esc_html(__('Do not index images from the entire site.','wp-seopress')).'"></span>
                    </p>
                    <p>
                        <label for="seopress_robots_archive_meta">
                            <input type="checkbox" name="seopress_robots_archive" id="seopress_robots_archive_meta" value="yes" '. checked( $seopress_robots_archive, 'yes', false ) .' />
                                '. __( 'noarchive', 'wp-seopress' ) .'
                        </label><span class="dashicons dashicons-info" title="'.esc_html(__('Do not display a "Cached" link in the Google search results.','wp-seopress')).'"></span>
                    </p>
                    <p>
                        <label for="seopress_robots_snippet_meta">
                            <input type="checkbox" name="seopress_robots_snippet" id="seopress_robots_snippet_meta" value="yes" '. checked( $seopress_robots_snippet, 'yes', false ) .' />
                                '. __( 'nosnippet', 'wp-seopress' ) .'
                        </label><span class="dashicons dashicons-info" title="'.esc_html(__('Do not display a description in the Google search results for all pages.','wp-seopress')).'"></span>
                    </p>
                    <p>
                        <label for="seopress_robots_canonical_meta">'. __( 'Canonical URL', 'wp-seopress' ) .'</label>
                        <input id="seopress_robots_canonical_meta" type="text" name="seopress_robots_canonical" placeholder="'.__('Default value: ','wp-seopress').get_permalink().'" value="'.$seopress_robots_canonical.'" />
                    </p>
                </div>
                <div id="tabs-3">
                    <span class="dashicons dashicons-facebook-alt"></span>
                    <p>
                        <label for="seopress_social_fb_title_meta">'. __( 'Facebook Title', 'wp-seopress' ) .'</label>
                        <input id="seopress_social_fb_title_meta" type="text" name="seopress_social_fb_title" placeholder="'.__('Enter your Facebook title','wp-seopress').'" value="'.$seopress_social_fb_title.'" />
                    </p>
                    <p>
                        <label for="seopress_social_fb_desc_meta">'. __( 'Facebook description', 'wp-seopress' ) .'</label>
                        <textarea id="seopress_social_fb_desc_meta" name="seopress_social_fb_desc" placeholder="'.__('Enter your Facebook description','wp-seopress').'" value="'.$seopress_social_fb_desc.'">'.$seopress_social_fb_desc.'</textarea>
                    </p> 
                    <p>
                        <label for="seopress_social_fb_img_meta">'. __( 'Facebook Thumbnail', 'wp-seopress' ) .'</label>
                        <span class="advise">'. __('Minimum size: 200x200px', 'wp-seopress') .'</span>
                        <input id="seopress_social_fb_img_meta" type="text" name="seopress_social_fb_img" placeholder="'.__('Select your default thumbnail','wp-seopress').'" value="'.$seopress_social_fb_img.'" />
                        <input id="seopress_social_fb_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />
                    </p>
                    <br/>
                    <span class="dashicons dashicons-twitter"></span>
                    <p>
                        <label for="seopress_social_twitter_title_meta">'. __( 'Twitter Title', 'wp-seopress' ) .'</label>
                        <input id="seopress_social_twitter_title_meta" type="text" name="seopress_social_twitter_title" placeholder="'.__('Enter your Twitter title','wp-seopress').'" value="'.$seopress_social_twitter_title.'" />
                    </p>
                    <p>
                        <label for="seopress_social_twitter_desc_meta">'. __( 'Twitter description', 'wp-seopress' ) .'</label>
                        <textarea id="seopress_social_twitter_desc_meta" name="seopress_social_twitter_desc" placeholder="'.__('Enter your Twitter description','wp-seopress').'" value="'.$seopress_social_twitter_desc.'">'.$seopress_social_twitter_desc.'</textarea>
                    </p> 
                    <p>
                        <label for="seopress_social_twitter_img_meta">'. __( 'Twitter Thumbnail', 'wp-seopress' ) .'</label>
                        <span class="advise">'. __('Minimum size: 160x160px', 'wp-seopress') .'</span>
                        <input id="seopress_social_twitter_img_meta" type="text" name="seopress_social_twitter_img" placeholder="'.__('Select your default thumbnail','wp-seopress').'" value="'.$seopress_social_twitter_img.'" />
                        <input id="seopress_social_twitter_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />
                    </p>
                </div>';
                }

                echo '<div id="tabs-4">
                    <p>
                        <label for="seopress_redirections_enabled_meta" id="seopress_redirections_enabled">
                            <input type="checkbox" name="seopress_redirections_enabled" id="seopress_redirections_enabled_meta" value="yes" '. checked( $seopress_redirections_enabled, 'yes', false ) .' />
                                '. __( 'Enable redirection?', 'wp-seopress' ) .'
                        </label>
                    </p>
                    <p>
                        <label for="seopress_redirections_value_meta">'. __( 'URL redirection', 'wp-seopress' ) .'</label>
                        <select name="seopress_redirections_type">
                            <option ' . selected( '301', $seopress_redirections_type, false ) . ' value="301">'. __( '301 Moved Permanently', 'wp-seopress' ) .'</option>
                            <option ' . selected( '302', $seopress_redirections_type, false ) . ' value="302">'. __( '302 Found (HTTP 1.1) / Moved Temporarily (HTTP 1.0)', 'wp-seopress' ) .'</option>
                            <option ' . selected( '307', $seopress_redirections_type, false ) . ' value="307">'. __( '307 Moved Temporarily (HTTP 1.1 Only)', 'wp-seopress' ) .'</option>
                        </select>
                        <input id="seopress_redirections_value_meta" type="text" name="seopress_redirections_value" placeholder="'.__('Enter your new URL','wp-seopress').'" value="'.$seopress_redirections_value.'" />
                        <br><br>';
                        if ($seopress_redirections_value !='') {     
        echo                '<a href="'.seopress_redirections_value($seopress_redirections_value).'" id="seopress_redirections_value_default" class="button" target="_blank">'.__('Test your URL','wp-seopress').'</a>';
                        } 
        echo            '<a href="" id="seopress_redirections_value_live" class="button" target="_blank" style="display: none">'.__('Test your URL','wp-seopress').'</a>
                    </p>
                </div>';
        if (is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
            if ("seopress_404" != $typenow) { 
                echo '<div id="tabs-5">
                        <p>
                            <label for="seopress_news_disabled_meta" id="seopress_news_disabled">
                                <input type="checkbox" name="seopress_news_disabled" id="seopress_news_disabled_meta" value="yes" '. checked( $seopress_news_disabled, 'yes', false ) .' />
                                    '. __( 'Exclude this post from Google News Sitemap?', 'wp-seopress' ) .'
                            </label>
                        </p>
                        <p>
                            <label for="seopress_news_genres_meta">'. __( 'Google News Genres', 'wp-seopress' ) .'</label>
                            <select name="seopress_news_genres">
                                <option ' . selected( 'none', $seopress_news_genres, false ) . ' value="none">'. __( 'None', 'wp-seopress' ) .'</option>
                                <option ' . selected( 'pressrelease', $seopress_news_genres, false ) . ' value="pressrelease">'. __( 'Press Release', 'wp-seopress' ) .'</option>
                                <option ' . selected( 'satire', $seopress_news_genres, false ) . ' value="satire">'. __( 'Satire', 'wp-seopress' ) .'</option>
                                <option ' . selected( 'blog', $seopress_news_genres, false ) . ' value="blog">'. __( 'Blog', 'wp-seopress' ) .'</option>
                                <option ' . selected( 'oped', $seopress_news_genres, false ) . ' value="oped">'. __( 'OpEd', 'wp-seopress' ) .'</option>
                                <option ' . selected( 'opinion', $seopress_news_genres, false ) . ' value="opinion">'. __( 'Opinion', 'wp-seopress' ) .'</option>
                                <option ' . selected( 'usergenerated', $seopress_news_genres, false ) . ' value="usergenerated">'. __( 'UserGenerated', 'wp-seopress' ) .'</option>
                            </select>
                        </p>
                        <p>
                            <label for="seopress_news_keyboard_meta" id="seopress_news_keyboard">
                                '. __( 'Google News Keywords <em>(max recommended limit: 12)</em>', 'wp-seopress' ) .'</label>
                                <input id="seopress_news_keyboard_meta" type="text" name="seopress_news_keyboard" placeholder="'.__('Enter your Google News Keywords','wp-seopress').'" value="'.$seopress_news_keyboard.'" />
                        </p>
                    </div>';
                }
            }
            echo '</div>
        ';  
    }

    add_action('save_post','seopress_save_metabox');
    function seopress_save_metabox($post_id){
        if ( 'attachment' !== get_post_type($post_id)) {
            if(isset($_POST['seopress_titles_title'])){
              update_post_meta($post_id, '_seopress_titles_title', esc_html($_POST['seopress_titles_title']));
            }
            if(isset($_POST['seopress_titles_desc'])){
              update_post_meta($post_id, '_seopress_titles_desc', esc_html($_POST['seopress_titles_desc']));
            }
            if( isset( $_POST[ 'seopress_robots_index' ] ) ) {
                update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_robots_index', '' );
            }
            if( isset( $_POST[ 'seopress_robots_follow' ] ) ) {
                update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_robots_follow', '' );
            }
            if( isset( $_POST[ 'seopress_robots_odp' ] ) ) {
                update_post_meta( $post_id, '_seopress_robots_odp', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_robots_odp', '' );
            }
            if( isset( $_POST[ 'seopress_robots_imageindex' ] ) ) {
                update_post_meta( $post_id, '_seopress_robots_imageindex', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_robots_imageindex', '' );
            }
            if( isset( $_POST[ 'seopress_robots_archive' ] ) ) {
                update_post_meta( $post_id, '_seopress_robots_archive', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_robots_archive', '' );
            }
            if( isset( $_POST[ 'seopress_robots_snippet' ] ) ) {
                update_post_meta( $post_id, '_seopress_robots_snippet', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_robots_snippet', '' );
            }
            if(isset($_POST['seopress_robots_canonical'])){
                update_post_meta($post_id, '_seopress_robots_canonical', esc_html($_POST['seopress_robots_canonical']));
            }
            if(isset($_POST['seopress_social_fb_title'])){
                update_post_meta($post_id, '_seopress_social_fb_title', esc_html($_POST['seopress_social_fb_title']));
            }
            if(isset($_POST['seopress_social_fb_desc'])){
                update_post_meta($post_id, '_seopress_social_fb_desc', esc_html($_POST['seopress_social_fb_desc']));
            }
            if(isset($_POST['seopress_social_fb_img'])){
                update_post_meta($post_id, '_seopress_social_fb_img', esc_html($_POST['seopress_social_fb_img']));
            }
            if(isset($_POST['seopress_social_twitter_title'])){
                update_post_meta($post_id, '_seopress_social_twitter_title', esc_html($_POST['seopress_social_twitter_title']));
            }
            if(isset($_POST['seopress_social_twitter_desc'])){
                update_post_meta($post_id, '_seopress_social_twitter_desc', esc_html($_POST['seopress_social_twitter_desc']));
            }
            if(isset($_POST['seopress_social_twitter_img'])){
                update_post_meta($post_id, '_seopress_social_twitter_img', esc_html($_POST['seopress_social_twitter_img']));
            }         
            if(isset($_POST['seopress_redirections_type'])){
                update_post_meta($post_id, '_seopress_redirections_type', $_POST['seopress_redirections_type']);
            }     
            if(isset($_POST['seopress_redirections_value'])){
                update_post_meta($post_id, '_seopress_redirections_value', esc_html($_POST['seopress_redirections_value']));
            }
            if( isset( $_POST[ 'seopress_redirections_enabled' ] ) ) {
                update_post_meta( $post_id, '_seopress_redirections_enabled', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_redirections_enabled', '' );
            }
            if( isset( $_POST[ 'seopress_news_disabled' ] ) ) {
                update_post_meta( $post_id, '_seopress_news_disabled', 'yes' );
            } else {
                delete_post_meta( $post_id, '_seopress_news_disabled', '' );
            }
            if(isset($_POST['seopress_news_genres'])){
                update_post_meta($post_id, '_seopress_news_genres', $_POST['seopress_news_genres']);
            }     
            if(isset($_POST['seopress_news_keyboard'])){
                update_post_meta($post_id, '_seopress_news_keyboard', esc_html($_POST['seopress_news_keyboard']));
            }
        }
    }
}

if (is_user_logged_in()) {
    global $wp_roles;
        
    //Get current user role
    if(isset(wp_get_current_user()->roles[0])) {
        $seopress_user_role = wp_get_current_user()->roles[0];

        //If current user role matchs values from Security settings then apply
        if (function_exists('seopress_advanced_security_metaboxe_role_hook_option') && seopress_advanced_security_metaboxe_role_hook_option() !='') {
            if( array_key_exists( $seopress_user_role, seopress_advanced_security_metaboxe_role_hook_option())) {
                //do nothing
            } else {
                echo seopress_display_seo_metaboxe();
            }
        } else {
            echo seopress_display_seo_metaboxe();
        }
    }   
}
