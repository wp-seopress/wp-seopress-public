<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

global $typenow;
global $pagenow;
$data_tax = '';

if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
    $current_id = get_the_id();
    $origin = 'post';

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
    }

    function seopress_display_date_snippet() {
        if (seopress_titles_single_cpt_date_option()) {
            return '<div class="snippet-date">'.get_the_date('M j, Y').' - </div>';
        }
    }
} elseif ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
    global $tag;
    $current_id = $tag->term_id;
    $origin = 'term';
    $data_tax = $tag->taxonomy;
}

function seopress_redirections_value($seopress_redirections_value) {
    if ($seopress_redirections_value !='') {
        return $seopress_redirections_value;
    }
}

if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
    echo '
        <tr id="term-seopress" class="form-field">
            <th scope="row"><h2>'.__('SEO','wp-seopress').'</h2></th>
            <td>
                <div id="seopress_cpt">
                    <div class="inside">';
}

echo '<div id="seopress-tabs" data_id="'.$current_id.'" data_origin="'.$origin.'" data_tax="'.$data_tax.'">';
     echo'<ul>';
            if ("seopress_404" != $typenow) {
                echo '<li><a href="#tabs-1"><span class="dashicons dashicons-editor-table"></span>'. __( 'Titles settings', 'wp-seopress' ) .'</a></li>
                <li><a href="#tabs-2"><span class="dashicons dashicons-admin-generic"></span>'. __( 'Advanced', 'wp-seopress' ) .'</a></li>
                <li><a href="#tabs-3"><span class="dashicons dashicons-share"></span>'. __( 'Social', 'wp-seopress' ) .'</a></li>';
            }
            echo '<li><a href="#tabs-4"><span class="dashicons dashicons-admin-links"></span>'. __( 'Redirection', 'wp-seopress' ) .'</a></li>';
            if (is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
                if (function_exists('seopress_get_toggle_news_option') && seopress_get_toggle_news_option() =='1') {
                    if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                        if ("seopress_404" != $typenow) {
                            echo '<li><a href="#tabs-5"><span class="dashicons dashicons-admin-post"></span>'. __( 'Google News', 'wp-seopress' ) .'</a></li>';
                        }
                    }
                }
                if (function_exists('seopress_get_toggle_xml_sitemap_option') && seopress_get_toggle_xml_sitemap_option() =='1') {
                    if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                        if ("seopress_404" != $typenow) {
                            echo '<li><a href="#tabs-6"><span class="dashicons dashicons-format-video"></span>'. __( 'Video Sitemap', 'wp-seopress' ) .'</a></li>';
                        }
                    }
                }
            }
        echo '</ul>';
        
        if ("seopress_404" != $typenow) {
        echo '<div id="tabs-1">';
            if (is_plugin_active( 'woocommerce/woocommerce.php' )) {
                $shop_page_id = wc_get_page_id( 'shop' );
                if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                    if ( $post && absint( $shop_page_id ) === absint( $post->ID ) ) {
                        echo '<p class="notice notice-info">'.__('This is your <strong>Shop page</strong>. Go to <strong>SEO > Titles & Metas > Archives > Products</strong> ','wp-seopress').' <a href="'.admin_url( 'admin.php?page=seopress-titles' ).'">'.__('to edit your title and meta description','wp-seopress').'</a></p>';
                    }
                }
            }
        echo '<div class="box-left">
                <p style="margin-bottom:0">
                    <label for="seopress_titles_title_meta">'. __( 'Title', 'wp-seopress' ) .'</label>
                    <input id="seopress_titles_title_meta" type="text" name="seopress_titles_title" placeholder="'.esc_html__('Enter your title','wp-seopress').'" aria-label="'.__('Title','wp-seopress').'" value="'.$seopress_titles_title.'" />
                </p> 
                <div class="wrap-seopress-counters">
                    <div id="seopress_titles_title_pixel"></div>
                    <strong>'.__(' / 568 pixels - ','wp-seopress').'</strong>
                    <div id="seopress_titles_title_counters"></div>
                    '.__(' (maximum recommended limit)','wp-seopress').'
                </div>
                
                <div class="wrap-tags">
                    <span id="seopress-tag-single-title" data-tag="%%post_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Post Title','wp-seopress').'</span>

                    <span id="seopress-tag-single-site-title" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Site Title','wp-seopress').'</span>

                    <span id="seopress-tag-single-sep" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Separator','wp-seopress').'</span>
                </div>

                <p style="margin-bottom:0">
                    <label for="seopress_titles_desc_meta">'. __( 'Meta description', 'wp-seopress' ) .'</label>
                    <textarea id="seopress_titles_desc_meta" style="width:100%" rows="8" name="seopress_titles_desc" placeholder="'.esc_html__('Enter your meta description','wp-seopress').'" aria-label="'.__('Meta description','wp-seopress').'" value="'.$seopress_titles_desc.'">'.$seopress_titles_desc.'</textarea>
                </p>
                <div class="wrap-seopress-counters">
                    <div id="seopress_titles_desc_pixel"></div>
                    <strong>'.__(' / 940 pixels - ','wp-seopress').'</strong>
                    <div id="seopress_titles_desc_counters"></div>
                    '.__(' (maximum recommended limit)','wp-seopress').'
                </div>';
                if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
                    echo '<div class="wrap-tags">
                        <span id="seopress-tag-single-excerpt" data-tag="%%_category_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Category / term description','wp-seopress').'</span>
                    </div>';
                } else {
                    echo '<div class="wrap-tags">
                        <span id="seopress-tag-single-excerpt" data-tag="%%post_excerpt%%" class="tag-title"><span class="dashicons dashicons-plus"></span>'.__('Post Excerpt','wp-seopress').'</span>
                    </div>';
                }
            echo '</div>
            <div class="box-right">
                <div class="google-snippet-preview">
                    <h3>'.__('Google Snippet Preview','wp-seopress').'</h3>
                    <p>'.__('This is what your page will look like in Google search results. You have to publish your post to get the Google Snippet Preview.','wp-seopress').'</p>
                    <div class="snippet-title"></div>
                    <div class="snippet-title-custom" style="display:none"></div>';
                global $tag;
                if (get_the_title()) {
                    echo '<div class="snippet-title-default" style="display:none">'.get_the_title().' - '.get_bloginfo('name').'</div>
                    <div class="snippet-permalink">'.htmlspecialchars(urldecode(get_permalink())).'</div>';
                } elseif ($tag) {
                    echo '<div class="snippet-title-default" style="display:none">'.$tag->name.' - '.get_bloginfo('name').'</div>';
                    echo '<div class="snippet-permalink">'.htmlspecialchars(urldecode(get_term_link($tag))).'</div>';
                }

                if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
echo                seopress_display_date_snippet();
                }
echo               '<div class="snippet-description">...</div>
                    <div class="snippet-description-custom" style="display:none"></div>
                    <div class="snippet-description-default" style="display:none"></div>';
            echo '</div>
            </div>
        </div>
        <div id="tabs-2">
            <p>
                <label for="seopress_robots_index_meta">
                    <input type="checkbox" name="seopress_robots_index" id="seopress_robots_index_meta" value="yes" '. checked( $seopress_robots_index, 'yes', false ) .' '.$disabled['robots_index'].'/>
                        '. __( 'Do not display this page in search engine results / XML - HTML sitemaps <strong>(noindex)</strong>', 'wp-seopress' ) .'
                </label>
            </p>
            <p>
                <label for="seopress_robots_follow_meta">
                    <input type="checkbox" name="seopress_robots_follow" id="seopress_robots_follow_meta" value="yes" '. checked( $seopress_robots_follow, 'yes', false ) .' '.$disabled['robots_follow'].'/>
                        '. __( 'Do not follow links for this page <strong>(nofollow)</strong>', 'wp-seopress' ) .'
                </label>
            </p>
            <p>
                <label for="seopress_robots_odp_meta">
                    <input type="checkbox" name="seopress_robots_odp" id="seopress_robots_odp_meta" value="yes" '. checked( $seopress_robots_odp, 'yes', false ) .' '.$disabled['robots_odp'].'/>
                        '. __( 'Do not use Open Directory project metadata for titles or excerpts for this page <strong>(noodp)</strong>', 'wp-seopress' ) .'
                </label>
            </p>
            <p>
                <label for="seopress_robots_imageindex_meta">
                    <input type="checkbox" name="seopress_robots_imageindex" id="seopress_robots_imageindex_meta" value="yes" '. checked( $seopress_robots_imageindex, 'yes', false ) .' '.$disabled['imageindex'].'/>
                        '. __( 'Do not index images for this page <strong>(noimageindex)</strong>', 'wp-seopress' ) .'
                </label>
            </p>
            <p>
                <label for="seopress_robots_archive_meta">
                    <input type="checkbox" name="seopress_robots_archive" id="seopress_robots_archive_meta" value="yes" '. checked( $seopress_robots_archive, 'yes', false ) .' '.$disabled['archive'].'/>
                        '. __( 'Do not display a "Cached" link in the Google search results <strong>(noarchive)</strong>', 'wp-seopress' ) .'
                </label>
            </p>
            <p>
                <label for="seopress_robots_snippet_meta">
                    <input type="checkbox" name="seopress_robots_snippet" id="seopress_robots_snippet_meta" value="yes" '. checked( $seopress_robots_snippet, 'yes', false ) .' '.$disabled['snippet'].'/>
                        '. __( 'Do not display a description in search results for this page <strong>(nosnippet)</strong>', 'wp-seopress' ) .'
                </label>
            </p>
            <p class="description">
                '.__('You cannot uncheck a parameter? This is normal, and it\'s most likely defined in the global settings of the extension.','wp-seopress').'
            </p>
            <p>
                <label for="seopress_robots_canonical_meta">'. __( 'Canonical URL', 'wp-seopress' ) .'</label>
                <input id="seopress_robots_canonical_meta" type="text" name="seopress_robots_canonical" placeholder="'.esc_html__('Default value: ','wp-seopress').htmlspecialchars(urldecode(get_permalink())).'" aria-label="'.__('Canonical URL','wp-seopress').'" value="'.$seopress_robots_canonical.'" />
                <span class="sp-tooltip"><span class="dashicons dashicons-editor-help"></span>
                  <span class="sp-tooltiptext">'.__('A canonical URL is the URL of the page that Google thinks is most representative from a set of duplicate pages on your site. For example, if you have URLs for the same page (for example: example.com?dress=1234 and example.com/dresses/1234), Google chooses one as canonical. Note that the pages do not need to be absolutely identical; minor changes in sorting or filtering of list pages do not make the page unique (for example, sorting by price or filtering by item color).
                The canonical can be in a different domain than a duplicate.','wp-seopress').'</span>
                </span>
            </p>';

            if ($typenow =='post' && ($pagenow == 'post.php' || $pagenow == 'post-new.php')) {
                echo '<p>
                    <label for="seopress_robots_primary_cat_meta">'. __( 'Select a primary category', 'wp-seopress' ) .'</label>
                    <span class="description">'.__('Set the category that gets used in the %category% permalink if you have multiple categories.','wp-seopress').'</p>
                    <select name="seopress_robots_primary_cat">';

                    $cats = get_categories();
                    if (!empty($cats)) {
                        echo '<option '. selected( 'none', $seopress_robots_primary_cat, false ).' value="none">'.__('None (will disable this feature)','wp-seopress').'</option>';
                        foreach ($cats as $category) {
                            echo '<option '.selected( $category->term_id, $seopress_robots_primary_cat, false ).' value="'.$category->term_id.'">'. $category->name .'</option>';
                        }
                    }
                    echo '</select>
                </p>';
            }
            if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    echo '<p>
                        <label for="seopress_robots_breadcrumbs_meta">'. __( 'Custom breadcrumbs', 'wp-seopress' ) .'</label>
                        <input id="seopress_robots_breadcrumbs_meta" type="text" name="seopress_robots_breadcrumbs" placeholder="'.esc_html__('Enter a custom value, useful if your title is too long','wp-seopress').'" aria-label="'.__('Custom breadcrumbs','wp-seopress').'" value="'.$seopress_robots_breadcrumbs.'" />
                    </p>';
                }
            }
        echo '</div>
        <div id="tabs-3">
            <span class="dashicons dashicons-facebook-alt"></span>
            <br><br>
            <span class="dashicons dashicons-external"></span><a href="https://developers.facebook.com/tools/debug/sharing/?q='.get_permalink(get_the_id()).'" target="_blank">'.__('Ask Facebook to update his cache','wp-seopress').'</a>
            <p>
                <label for="seopress_social_fb_title_meta">'. __( 'Facebook Title', 'wp-seopress' ) .'</label>
                <input id="seopress_social_fb_title_meta" type="text" name="seopress_social_fb_title" placeholder="'.esc_html__('Enter your Facebook title','wp-seopress').'" aria-label="'.__('Facebook Title','wp-seopress').'" value="'.$seopress_social_fb_title.'" />
            </p>
            <p>
                <label for="seopress_social_fb_desc_meta">'. __( 'Facebook description', 'wp-seopress' ) .'</label>
                <textarea id="seopress_social_fb_desc_meta" name="seopress_social_fb_desc" placeholder="'.esc_html__('Enter your Facebook description','wp-seopress').'" aria-label="'.__('Facebook description','wp-seopress').'" value="'.$seopress_social_fb_desc.'">'.$seopress_social_fb_desc.'</textarea>
            </p> 
            <p>
                <label for="seopress_social_fb_img_meta">'. __( 'Facebook Thumbnail', 'wp-seopress' ) .'</label>
                <input id="seopress_social_fb_img_meta" type="text" name="seopress_social_fb_img" placeholder="'.esc_html__('Select your default thumbnail','wp-seopress').'" aria-label="'.__('Facebook Thumbnail','wp-seopress').'" value="'.$seopress_social_fb_img.'" />
                <span class="advise">'.__('Minimum size: 200x200px, ideal ratio 1.91:1, 8Mb max. (eg: 1640x856px or 3280x1712px for retina screens)', 'wp-seopress').'</span>
                <input id="seopress_social_fb_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />
            </p>
            <br/>
            <span class="dashicons dashicons-twitter"></span>
            <br><br>
            <span class="dashicons dashicons-external"></span><a href="https://cards-dev.twitter.com/validator" target="_blank">'.__('Preview your Twitter card using the official validator','wp-seopress').'</a>
            <p>
                <label for="seopress_social_twitter_title_meta">'. __( 'Twitter Title', 'wp-seopress' ) .'</label>
                <input id="seopress_social_twitter_title_meta" type="text" name="seopress_social_twitter_title" placeholder="'.esc_html__('Enter your Twitter title','wp-seopress').'" aria-label="'.__('Twitter Title','wp-seopress').'" value="'.$seopress_social_twitter_title.'" />
            </p>
            <p>
                <label for="seopress_social_twitter_desc_meta">'. __( 'Twitter description', 'wp-seopress' ) .'</label>
                <textarea id="seopress_social_twitter_desc_meta" name="seopress_social_twitter_desc" placeholder="'.esc_html__('Enter your Twitter description','wp-seopress').'" aria-label="'.__('Twitter description','wp-seopress').'" value="'.$seopress_social_twitter_desc.'">'.$seopress_social_twitter_desc.'</textarea>
            </p> 
            <p>
                <label for="seopress_social_twitter_img_meta">'. __( 'Twitter Thumbnail', 'wp-seopress' ) .'</label>
                <input id="seopress_social_twitter_img_meta" type="text" name="seopress_social_twitter_img" placeholder="'.esc_html__('Select your default thumbnail','wp-seopress').'" value="'.$seopress_social_twitter_img.'" />
                <span class="advise">'. __('Minimum size: 144x144px (300x157px with large card enabled), ideal ratio 1:1 (2:1 with large card), 5Mb max.', 'wp-seopress') .'</span>
                <input id="seopress_social_twitter_img_upload" class="button" type="button" aria-label="'.__('Twitter Thumbnail','wp-seopress').'" value="'.__('Upload an Image','wp-seopress').'" />
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
                    <option ' . selected( '302', $seopress_redirections_type, false ) . ' value="302">'. __( '302 Found / Moved Temporarily', 'wp-seopress' ) .'</option>
                    <option ' . selected( '307', $seopress_redirections_type, false ) . ' value="307">'. __( '307 Moved Temporarily', 'wp-seopress' ) .'</option>
                    <option ' . selected( '410', $seopress_redirections_type, false ) . ' value="410">'. __( '410 Gone', 'wp-seopress' ) .'</option>
                    <option ' . selected( '451', $seopress_redirections_type, false ) . ' value="451">'. __( '451 Unavailable For Legal Reasons', 'wp-seopress' ) .'</option>
                </select>
                <input id="seopress_redirections_value_meta" type="text" name="seopress_redirections_value" placeholder="'.esc_html__('Enter your new URL in absolute (eg: https://www.example.com/)','wp-seopress').'" aria-label="'.__('URL redirection','wp-seopress').'" value="'.$seopress_redirections_value.'" />
                <br><br>
            </p>';
            if ("seopress_404" == $typenow) {
            echo '<p>
                <label for="seopress_redirections_param_meta">'. __( 'Query parameters', 'wp-seopress' ) .'</label>
                <select name="seopress_redirections_param">
                    <option ' . selected( 'exact_match', $seopress_redirections_param, false ) . ' value="exact_match">'. __( 'Exactly match all parameters', 'wp-seopress' ) .'</option>
                    <option ' . selected( 'without_param', $seopress_redirections_param, false ) . ' value="without_param">'. __( 'Exclude all parameters', 'wp-seopress' ) .'</option>
                    <option ' . selected( 'with_ignored_param', $seopress_redirections_param, false ) . ' value="with_ignored_param">'. __( 'Exclude all parameters and pass them to the redirection', 'wp-seopress' ) .'</option>
                </select></p>';
            }
            echo '<p>';                
                if ($seopress_redirections_enabled =='yes') {
                    $status_code = array('410','451');
                    if ($seopress_redirections_value !='' || in_array($seopress_redirections_type, $status_code)) {
                        if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                            if ( 'seopress_404' == $typenow ) {                      
                                echo '<a href="'.get_home_url().'/'.get_the_title().'" id="seopress_redirections_value_default" class="button" target="_blank">'.__('Test your URL','wp-seopress').'</a>';
                            } else {
                                echo '<a href="'.get_permalink().'" id="seopress_redirections_value_default" class="button" target="_blank">'.__('Test your URL','wp-seopress').'</a>';
                            }
                        } elseif ( $pagenow == 'term.php' ) {
                            echo '<a href="'.get_term_link($term).'" id="seopress_redirections_value_default" class="button" target="_blank">'.__('Test your URL','wp-seopress').'</a>';
                        } else {
                            echo '<a href="'.get_permalink().'" id="seopress_redirections_value_default" class="button" target="_blank">'.__('Test your URL','wp-seopress').'</a>';
                        }
                    }
                }

                if (function_exists('seopress_get_locale')) {
                    if (seopress_get_locale() =='fr') {
                        $seopress_docs_link['support']['redirection'] = 'https://www.seopress.org/fr/support/guides/activer-redirections-301-surveillance-404/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                    } else {
                        $seopress_docs_link['support']['redirection'] = 'https://www.seopress.org/support/guides/redirections/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                    }
                }
                ?>
                <span class="dashicons dashicons-external"></span>
                <a href="<?php echo $seopress_docs_link['support']['redirection']; ?>" target="_blank" class="seopress-doc"><?php _e('Need help with your redirections? Read our guide.','wp-seopress'); ?></a>
                <?php echo 
            '</p>
        </div>';
    if (is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
        if (function_exists('seopress_get_toggle_news_option') && seopress_get_toggle_news_option() =='1') {
            if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                if ("seopress_404" != $typenow) { 
                    echo '<div id="tabs-5">
                        <p>
                            <label for="seopress_news_disabled_meta" id="seopress_news_disabled">
                                <input type="checkbox" name="seopress_news_disabled" id="seopress_news_disabled_meta" value="yes" '. checked( $seopress_news_disabled, 'yes', false ) .' />
                                    '. __( 'Exclude this post from Google News Sitemap?', 'wp-seopress' ) .'
                            </label>
                        </p>
                    </div>';
                }
            }
        }
        if (function_exists('seopress_get_toggle_xml_sitemap_option') && seopress_get_toggle_xml_sitemap_option() =='1') {
            if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
                if ("seopress_404" != $typenow) {

                    //Init $seopress_video array if empty
                    if (empty($seopress_video)) {
                        $seopress_video = array('0' => array(''));
                    }

                    $count = $seopress_video[0];
                    end($count);
                    $total = key($count);

                    echo '<div id="tabs-6">
                        <p>
                            <label for="seopress_video_disabled_meta" id="seopress_video_disabled">
                                <input type="checkbox" name="seopress_video_disabled" id="seopress_video_disabled_meta" value="yes" '. checked( $seopress_video_disabled, 'yes', false ) .' />
                                    '. __( 'Exclude this post from Video Sitemap?', 'wp-seopress' ) .'
                            </label>
                            <span class="advise">'. __('If your post is set to noindex, it will be automatically excluded from the sitemap.', 'wp-seopress') .'</span>
                        </p>
                        <div id="wrap-videos" data-count="'.$total.'">';
                            foreach ($seopress_video[0] as $key => $value) {

                                $check_url = isset($seopress_video[0][$key]["url"]) ? $seopress_video[0][$key]["url"] : NULL;
                                $check_internal_video = isset($seopress_video[0][$key]["internal_video"]) ? $seopress_video[0][$key]["internal_video"] : NULL;
                                $check_title = isset($seopress_video[0][$key]["title"]) ? $seopress_video[0][$key]["title"] : NULL;
                                $check_desc = isset($seopress_video[0][$key]["desc"]) ? $seopress_video[0][$key]["desc"] : NULL;
                                $check_thumbnail = isset($seopress_video[0][$key]["thumbnail"]) ? $seopress_video[0][$key]["thumbnail"] : NULL;
                                $check_duration = isset($seopress_video[0][$key]["duration"]) ? $seopress_video[0][$key]["duration"] : NULL;
                                $check_rating = isset($seopress_video[0][$key]["rating"]) ? $seopress_video[0][$key]["rating"] : NULL;
                                $check_view_count = isset($seopress_video[0][$key]["view_count"]) ? $seopress_video[0][$key]["view_count"] : NULL;
                                $check_view_count = isset($seopress_video[0][$key]["view_count"]) ? $seopress_video[0][$key]["view_count"] : NULL;
                                $check_tag = isset($seopress_video[0][$key]["tag"]) ? $seopress_video[0][$key]["tag"] : NULL;
                                $check_cat = isset($seopress_video[0][$key]["cat"]) ? $seopress_video[0][$key]["cat"] : NULL;
                                $check_family_friendly = isset($seopress_video[0][$key]["family_friendly"]) ? $seopress_video[0][$key]["family_friendly"] : NULL;

                            echo '<div class="video">
                                    <h3 class="accordion-section-title" tabindex="0">'.__('Video ','wp-seopress').$check_title.'</h3>
                                    <div class="accordion-section-content">
                                        <div class="inside">
                                            <p>
                                                <label for="seopress_video['.$key.'][url_meta]">'. __( 'Video URL (required)', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][url_meta]" type="text" name="seopress_video['.$key.'][url]" placeholder="'.esc_html__('Enter your video URL','wp-seopress').'" aria-label="'.__('Video URL','wp-seopress').'" value="'.$check_url.'" />
                                            </p>
                                            <p class="internal_video">
                                                <label for="seopress_video['.$key.'][internal_video_meta]" id="seopress_video['.$key.'][internal_video]">
                                                    <input type="checkbox" name="seopress_video['.$key.'][internal_video]" id="seopress_video['.$key.'][internal_video_meta]" value="yes" '. checked( $check_internal_video, 'yes', false ) .' />
                                                        '. __( 'NOT an external video (eg: video hosting on YouTube, Vimeo, Wistia...)? Check this if your video is hosting on this server.', 'wp-seopress' ) .'
                                                </label>
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][title_meta]">'. __( 'Video Title (required)', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][title_meta]" type="text" name="seopress_video['.$key.'][title]" placeholder="'.esc_html__('Enter your video title','wp-seopress').'" aria-label="'.__('Video title','wp-seopress').'" value="'.$check_title.'" />
                                                <span class="advise">'. __('Default: title tag, if not available, post title.', 'wp-seopress') .'</span>
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][desc_meta]">'. __( 'Video Description (required)', 'wp-seopress' ) .'</label>
                                                <textarea id="seopress_video['.$key.'][desc_meta]" name="seopress_video['.$key.'][desc]" placeholder="'.esc_html__('Enter your video description','wp-seopress').'" aria-label="'.__('Video description','wp-seopress').'" value="'.$check_desc.'">'.$check_desc.'</textarea>
                                                <span class="advise">'. __('2048 characters max.; default: meta description. If not available, use the beginning of the post content.', 'wp-seopress') .'</span>
                                            </p> 
                                            <p>
                                                <label for="seopress_video['.$key.'][thumbnail_meta]">'. __( 'Video Thumbnail (required)', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][thumbnail_meta]" class="seopress_video_thumbnail_meta" type="text" name="seopress_video['.$key.'][thumbnail]" placeholder="'.esc_html__('Select your video thumbnail','wp-seopress').'" value="'.$check_thumbnail.'" />
                                                <input class="button seopress_video_thumbnail_upload" type="button" aria-label="'.__('Video Thumbnail','wp-seopress').'" value="'.__('Upload an Image','wp-seopress').'" />
                                                <span class="advise">'. __('Minimum size: 160x90px (1920x1080 max), JPG, PNG or GIF formats. Default: your post featured image.', 'wp-seopress') .'</span>
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][duration_meta]">'. __( 'Video Duration (recommended)', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][duration_meta]" type="number" step="1" min="0" max="28800" name="seopress_video['.$key.'][duration]" placeholder="'.esc_html__('Duration in seconds','wp-seopress').'" aria-label="'.__('Video duration','wp-seopress').'" value="'.$check_duration.'" />
                                                <span class="advise">'. __('The duration of the video in seconds. Value must be between 0 and 28800 (8 hours).', 'wp-seopress') .'</span>
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][rating_meta]">'. __( 'Video Rating', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][rating_meta]" type="number" step="0.1" min="0" max="5" name="seopress_video['.$key.'][rating]" placeholder="'.esc_html__('Video rating','wp-seopress').'" aria-label="'.__('Video rating','wp-seopress').'" value="'.$check_rating.'" />
                                                <span class="advise">'. __('Allowed values are float numbers in the range 0.0 to 5.0.', 'wp-seopress') .'</span>
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][view_count_meta]">'. __( 'View count', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][view_count_meta]" type="number" name="seopress_video['.$key.'][view_count]" placeholder="'.esc_html__('Number of views','wp-seopress').'" aria-label="'.__('View count','wp-seopress').'" value="'.$check_view_count.'" />
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][tag_meta]">'. __( 'Video tags', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][tag_meta]" type="text" name="seopress_video['.$key.'][tag]" placeholder="'.esc_html__('Enter your video tags','wp-seopress').'" aria-label="'.__('Video tags','wp-seopress').'" value="'.$check_tag.'" />
                                                <span class="advise">'. __('32 tags max., separate tags with commas. Default: target keywords + post tags if available.', 'wp-seopress') .'</span>
                                            </p>
                                            <p>
                                                <label for="seopress_video['.$key.'][cat_meta]">'. __( 'Video categories', 'wp-seopress' ) .'</label>
                                                <input id="seopress_video['.$key.'][cat_meta]" type="text" name="seopress_video['.$key.'][cat]" placeholder="'.esc_html__('Enter your video categories','wp-seopress').'" aria-label="'.__('Video categories','wp-seopress').'" value="'.$check_cat.'" />
                                                <span class="advise">'. __('256 characters max., usually a video will belong to a single category, separate categories with commas. Default: first post category if available.', 'wp-seopress') .'</span>
                                            </p>
                                            <p class="family-friendly">
                                                <label for="seopress_video['.$key.'][family_friendly_meta]" id="seopress_video['.$key.'][family_friendly]">
                                                    <input type="checkbox" name="seopress_video['.$key.'][family_friendly]" id="seopress_video['.$key.'][family_friendly_meta]" value="yes" '. checked( $check_family_friendly, 'yes', false ) .' />
                                                        '. __( 'NOT family friendly?', 'wp-seopress' ) .'
                                                </label>
                                                <span class="advise">'. __('The video will be available only to users with SafeSearch turned off.', 'wp-seopress') .'</span>
                                            </p>
                                            <p><a href="#" class="remove-video button">'.__('Remove video','wp-seopress').'</a></p>
                                        </div>
                                    </div>
                                </div>
                            ';
                        }
                   echo '</div>
                   <p><a href="#" id="add-video" class="add-video button button-primary">'.__('Add video','wp-seopress').'</a></p>
                   </div>';
                }
            }
        }
    }
    echo '</div>';

if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
                echo '</div>';
            echo '</div>';
        echo '</td>';
    echo '</tr>';
}
