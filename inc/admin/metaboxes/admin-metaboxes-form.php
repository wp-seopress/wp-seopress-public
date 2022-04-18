<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

global $typenow;
global $pagenow;

function seopress_redirections_value($seopress_redirections_value) {
    if ('' != $seopress_redirections_value) {
        return $seopress_redirections_value;
    }
}

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

if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) { ?>
<tr id="term-seopress" class="form-field">
    <th scope="row">
        <h2>
            <?php _e('SEO', 'wp-seopress'); ?>
        </h2>
    </th>
    <td>
        <div id="seopress_cpt">
            <div class="inside">
                <?php } ?>
                <div id="seopress-tabs"
                    data-home-id="<?php echo $data_attr['isHomeId']; ?>"
                    data-term-id="<?php echo $data_attr['termId']; ?>"
                    data_id="<?php echo $data_attr['current_id']; ?>"
                    data_origin="<?php echo $data_attr['origin']; ?>"
                    data_tax="<?php echo $data_attr['data_tax']; ?>">
                    <?php if ('seopress_404' != $typenow) {
    $seo_tabs['title-tab']    = '<li><a href="#tabs-1">' . __('Titles settings', 'wp-seopress') . '</a></li>';
    $seo_tabs['social-tab']   = '<li><a href="#tabs-2">' . __('Social', 'wp-seopress') . '</a></li>';
    $seo_tabs['advanced-tab'] = '<li><a href="#tabs-3">' . __('Advanced', 'wp-seopress') . '<span id="sp-advanced-alert"></span></a></li>';
}

        $seo_tabs['redirect-tab'] = '<li><a href="#tabs-4">' . __('Redirection', 'wp-seopress') . '</a></li>';

        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('news')) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        $seo_tabs['news-tab'] = '<li><a href="#tabs-5">' . __('Google News', 'wp-seopress') . '</a></li>';
                    }
                }
            }
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('xml-sitemap') && function_exists('seopress_xml_sitemap_video_enable_option') && '1' == seopress_xml_sitemap_video_enable_option()) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        $seo_tabs['video-tab'] = '<li><a href="#tabs-6">' . __('Video Sitemap', 'wp-seopress') . '</a></li>';
                    }
                }
            }
        }

        $seo_tabs = apply_filters('seopress_metabox_seo_tabs', $seo_tabs);

        if ( ! empty($seo_tabs)) { ?>
                    <ul>
                        <?php foreach ($seo_tabs as $tab) {
            echo $tab;
        } ?>
                    </ul>
                    <?php }
                    if ('seopress_404' != $typenow) {
                        if (array_key_exists('title-tab', $seo_tabs)) { ?>
                    <div id="tabs-1">
                        <?php if (is_plugin_active('woocommerce/woocommerce.php') && function_exists('wc_get_page_id')) {
                            $shop_page_id = wc_get_page_id('shop');
                            if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                                if ($post && absint($shop_page_id) === absint($post->ID)) { ?>
                        <p class="notice notice-info">
                            <?php printf(__('This is your <strong>Shop page</strong>. Go to <a href="%s"><strong>SEO > Titles & Metas > Archives > Products</strong></a> to edit your title and meta description.', 'wp-seopress'), admin_url('admin.php?page=seopress-titles')); ?>
                        </p>
                        <?php }
                            }
                        } ?>
                        <div class="box-left">
                            <p>
                                <label for="seopress_titles_title_meta">
                                    <?php
                                    _e('Title', 'wp-seopress');
                                    echo seopress_tooltip(__('Meta title', 'wp-seopress'), __('Titles are critical to give users a quick insight into the content of a result and why it’s relevant to their query. It\'s often the primary piece of information used to decide which result to click on, so it\'s important to use high-quality titles on your web pages.', 'wp-seopress'), esc_html('<title>My super title</title>'));
                                ?>
                                </label>
                                <input id="seopress_titles_title_meta" type="text" name="seopress_titles_title"
                                    class="components-text-control__input"
                                    placeholder="<?php esc_html_e('Enter your title', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Title', 'wp-seopress'); ?>"
                                    value="<?php echo $seopress_titles_title; ?>" />
                            </p>
                            <div class="sp-progress">
                                <div id="seopress_titles_title_counters_progress" class="sp-progress-bar"
                                    role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0"
                                    aria-valuemax="100">1%</div>
                            </div>
                            <div class="wrap-seopress-counters">
                                <div id="seopress_titles_title_pixel"></div>
                                <strong><?php _e(' / 568 pixels - ', 'wp-seopress'); ?></strong>
                                <div id="seopress_titles_title_counters"></div>
                                <?php _e(' (maximum recommended limit)', 'wp-seopress'); ?>
                            </div>

                            <div class="wrap-tags">
                                <?php if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) { ?>
                                <button type="button" class="<?php echo seopress_btn_secondary_classes(); ?> tag-title" id="seopress-tag-single-title" data-tag="%%term_title%%"><span
                                        class="dashicons dashicons-plus-alt2"></span><?php _e('Term Title', 'wp-seopress'); ?></button>
                                <?php } else { ?>
                                <button type="button" class="<?php echo seopress_btn_secondary_classes(); ?> tag-title" id="seopress-tag-single-title" data-tag="%%post_title%%"><span
                                        class="dashicons dashicons-plus-alt2"></span><?php _e('Post Title', 'wp-seopress'); ?></button>
                                <?php } ?>
                                <button type="button" class="<?php echo seopress_btn_secondary_classes(); ?> tag-title" id="seopress-tag-single-site-title" data-tag="%%sitetitle%%">
                                    <span class="dashicons dashicons-plus-alt2"></span><?php _e('Site Title', 'wp-seopress'); ?></button>
                                <button type="button" class="<?php echo seopress_btn_secondary_classes(); ?> tag-title" id="seopress-tag-single-sep" data-tag="%%sep%%"><span
                                        class="dashicons dashicons-plus-alt2"></span><?php _e('Separator', 'wp-seopress'); ?></button>

                                <?php echo seopress_render_dyn_variables('tag-title'); ?>
                            </div>
                            <p>
                                <label for="seopress_titles_desc_meta">
                                    <?php
                                _e('Meta description', 'wp-seopress');
                                echo seopress_tooltip(__('Meta description', 'wp-seopress'), __('A meta description tag should generally inform and interest users with a short, relevant summary of what a particular page is about. <br>They are like a pitch that convince the user that the page is exactly what they\'re looking for. <br>There\'s no limit on how long a meta description can be, but the search result snippets are truncated as needed, typically to fit the device width.', 'wp-seopress'), esc_html('<meta name="description" content="my super meta description" />'));
                            ?>
                                </label>
                                <textarea id="seopress_titles_desc_meta" rows="4" name="seopress_titles_desc"
                                    class="components-text-control__input"
                                    placeholder="<?php esc_html_e('Enter your meta description', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Meta description', 'wp-seopress'); ?>"><?php echo $seopress_titles_desc; ?></textarea>
                            </p>
                            <div class="sp-progress">
                                <div id="seopress_titles_desc_counters_progress" class="sp-progress-bar"
                                    role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0"
                                    aria-valuemax="100">1%</div>
                            </div>
                            <div class="wrap-seopress-counters">
                                <div id="seopress_titles_desc_pixel"></div>
                                <strong><?php _e(' / 940 pixels - ', 'wp-seopress'); ?></strong>
                                <div id="seopress_titles_desc_counters"></div>
                                <?php _e(' (maximum recommended limit)', 'wp-seopress'); ?>
                            </div>
                            <div class="wrap-tags">
                                <?php if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) { ?>
                                <button type="button" class="<?php echo seopress_btn_secondary_classes(); ?> tag-title" id="seopress-tag-single-excerpt" data-tag="%%_category_description%%">
                                    <span class="dashicons dashicons-plus-alt2"></span><?php _e('Category / term description', 'wp-seopress'); ?>
                                </button>
                                <?php } else { ?>
                                <button type="button" class="<?php echo seopress_btn_secondary_classes(); ?> tag-title" id="seopress-tag-single-excerpt" data-tag="%%post_excerpt%%">
                                    <span class="dashicons dashicons-plus-alt2"></span><?php _e('Post Excerpt', 'wp-seopress'); ?>
                                </button>
                                <?php }
                    echo seopress_render_dyn_variables('tag-description');
                ?>
                            </div>
                        </div>

                        <?php
                    $toggle_preview = 1;
                    $toggle_preview = apply_filters('seopress_toggle_mobile_preview', $toggle_preview);
                ?>

                        <div class="box-right">
                            <div class="google-snippet-preview mobile-preview">
                                <h3>
                                    <?php
                                _e('Google Snippet Preview', 'wp-seopress');
                                echo seopress_tooltip(__('Snippet Preview', 'wp-seopress'), __('The Google preview is a simulation. <br>There is no reliable preview because it depends on the screen resolution, the device used, the expression sought, and Google. <br>There is not one snippet for one URL but several. <br>All the data in this overview comes directly from your source code. <br>This is what the crawlers will see.', 'wp-seopress'), null);
                            ?>
                                </h3>
                                <p><?php _e('This is what your page will look like in Google search results. You have to publish your post to get the Google Snippet Preview.', 'wp-seopress'); ?>
                                </p>
                                <div class="wrap-toggle-preview">
                                    <p>
                                        <span class="dashicons dashicons-smartphone"></span>
                                        <?php _e('Mobile Preview', 'wp-seopress'); ?>
                                        <input type="checkbox" name="toggle-preview" id="toggle-preview" class="toggle"
                                            data-toggle="<?php echo $toggle_preview; ?>">
                                        <label for="toggle-preview"></label>
                                    </p>
                                </div>
                                <?php
                global $tag;

                $gp_title       = '';
                $gp_permalink   = '';
                if (get_the_title()) {
                    $gp_title       = '<div class="snippet-title-default" style="display:none">' . get_the_title() . ' - ' . get_bloginfo('name') . '</div>';
                    $gp_permalink   = '<div class="snippet-permalink">' . htmlspecialchars(urldecode(get_permalink())) . '</div>';
                } elseif ($tag) {
                    if (false === is_wp_error(get_term_link($tag))) {
                        $gp_title       = '<div class="snippet-title-default" style="display:none">' . $tag->name . ' - ' . get_bloginfo('name') . '</div>';
                        $gp_permalink   = '<div class="snippet-permalink">' . htmlspecialchars(urldecode(get_term_link($tag))) . '</div>';
                    }
                }

                $siteicon = '<div class="snippet-favicon"><img aria-hidden="true" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC" width="16" alt="favicon"></div>';
                if (get_site_icon_url(32)) {
                    $siteicon = '<div class="snippet-favicon"><img aria-hidden="true" height="16" src="' . get_site_icon_url(32) . '" width="16" alt="favicon"/></div>';
                } ?>

                                <div class="wrap-snippet">
                                    <div class="wrap-m-icon-permalink"><?php echo $siteicon . $gp_permalink; ?>
                                    </div>
                                    <div class="snippet-title"></div>
                                    <div class="snippet-title-custom" style="display:none"></div>

                                    <?php
                echo $gp_title;
                echo $gp_permalink;

                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    echo seopress_display_date_snippet();
                } ?>
                                    <div class="snippet-description">...</div>
                                    <div class="snippet-description-custom" style="display:none"></div>
                                    <div class="snippet-description-default" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }
                        if (array_key_exists('advanced-tab', $seo_tabs)) { ?>
                    <div id="tabs-3">
                        <span class="sp-section"><?php _e('Meta robots settings', 'wp-seopress'); ?></span>
                        <p class="description">
                            <?php $url = admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_single');
                                /* translators: %s: link to plugin settings page */
                                printf(__('You cannot uncheck a parameter? This is normal, and it‘s most likely defined in the <a href="%s">global settings of the plugin.</a>', 'wp-seopress'), $url);
                            ?>
                        </p>
                        <p>
                            <label for="seopress_robots_index_meta">
                                <input type="checkbox" name="seopress_robots_index" id="seopress_robots_index_meta"
                                    value="yes" <?php echo checked($seopress_robots_index, 'yes', false); ?>
                                <?php echo $disabled['robots_index']; ?>/>
                                <?php
                                    _e('Do not display this page in search engine results / XML - HTML sitemaps <strong>(noindex)</strong>', 'wp-seopress');
                                    echo seopress_tooltip(__('"noindex" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noindex". <br>Search engines will not index this URL in the search results.', 'wp-seopress'), esc_html('<meta name="robots" content="noindex" />'));
                                ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_robots_follow_meta">
                                <input type="checkbox" name="seopress_robots_follow" id="seopress_robots_follow_meta"
                                    value="yes" <?php echo checked($seopress_robots_follow, 'yes', false); ?>
                                <?php echo $disabled['robots_follow']; ?>/>
                                <?php
                                    _e('Do not follow links for this page <strong>(nofollow)</strong>', 'wp-seopress');
                                    echo seopress_tooltip(__('"nofollow" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "nofollow". <br>Search engines will not follow links from this URL.', 'wp-seopress'), esc_html('<meta name="robots" content="nofollow" />'));
                                ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_robots_odp_meta">
                                <input type="checkbox" name="seopress_robots_odp" id="seopress_robots_odp_meta"
                                    value="yes" <?php echo checked($seopress_robots_odp, 'yes', false); ?>
                                <?php echo $disabled['robots_odp']; ?>/>
                                <?php _e('Do not use Open Directory project metadata for titles or excerpts for this page <strong>(noodp)</strong>', 'wp-seopress'); ?>
                                <?php echo seopress_tooltip(__('"noodp" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noodp". <br>Note that Google and Yahoo have stopped considering this tag since the closing of DMOZ directory.', 'wp-seopress'), esc_html('<meta name="robots" content="noodp" />')); ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_robots_imageindex_meta">
                                <input type="checkbox" name="seopress_robots_imageindex"
                                    id="seopress_robots_imageindex_meta" value="yes" <?php echo checked($seopress_robots_imageindex, 'yes', false); ?>
                                <?php echo $disabled['imageindex']; ?>/>
                                <?php _e('Do not index images for this page <strong>(noimageindex)</strong>', 'wp-seopress'); ?>
                                <?php echo seopress_tooltip(__('"noimageindex" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noimageindex". <br> Note that your images can always be indexed if they are linked from other pages.', 'wp-seopress'), esc_html('<meta name="google" content="noimageindex" />')); ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_robots_archive_meta">
                                <input type="checkbox" name="seopress_robots_archive" id="seopress_robots_archive_meta"
                                    value="yes" <?php echo checked($seopress_robots_archive, 'yes', false); ?>
                                <?php echo $disabled['archive']; ?>/>
                                <?php _e('Do not display a "Cached" link in the Google search results <strong>(noarchive)</strong>', 'wp-seopress'); ?>
                                <?php echo seopress_tooltip(__('"noarchive" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noarchive".', 'wp-seopress'), esc_html('<meta name="robots" content="noarchive" />')); ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_robots_snippet_meta">
                                <input type="checkbox" name="seopress_robots_snippet" id="seopress_robots_snippet_meta"
                                    value="yes" <?php echo checked($seopress_robots_snippet, 'yes', false); ?>
                                <?php echo $disabled['snippet']; ?>/>
                                <?php _e('Do not display a description in search results for this page <strong>(nosnippet)</strong>', 'wp-seopress'); ?>
                                <?php echo seopress_tooltip(__('"nosnippet" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "nosnippet".', 'wp-seopress'), esc_html('<meta name="robots" content="nosnippet" />')); ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_robots_canonical_meta">
                                <?php
                                    _e('Canonical URL', 'wp-seopress');
                                    echo seopress_tooltip(__('Canonical URL', 'wp-seopress'), __('A canonical URL is the URL of the page that Google thinks is most representative from a set of duplicate pages on your site. <br>For example, if you have URLs for the same page (for example: example.com?dress=1234 and example.com/dresses/1234), Google chooses one as canonical. <br>Note that the pages do not need to be absolutely identical; minor changes in sorting or filtering of list pages do not make the page unique (for example, sorting by price or filtering by item color). The canonical can be in a different domain than a duplicate.', 'wp-seopress'), esc_html('<link rel="canonical" href="https://www.example.com/my-post-url/" />'));
                                ?>
                            </label>
                            <input id="seopress_robots_canonical_meta" type="text" name="seopress_robots_canonical"
                                class="components-text-control__input"
                                placeholder="<?php esc_html_e('Default value: ', 'wp-seopress') . htmlspecialchars(urldecode(get_permalink())); ?>"
                                aria-label="<?php _e('Canonical URL', 'wp-seopress'); ?>"
                                value="<?php echo $seopress_robots_canonical; ?>" />
                        </p>

                        <?php if (('post' == $typenow || 'product' == $typenow) && ('post.php' == $pagenow || 'post-new.php' == $pagenow)) { ?>
                        <p>
                            <label for="seopress_robots_primary_cat"><?php _e('Select a primary category', 'wp-seopress'); ?></label>
                            <span class="description"><?php _e('Set the category that gets used in the %category% permalink and in our breadcrumbs if you have multiple categories.', 'wp-seopress'); ?>
                        </p>
                        <select id="seopress_robots_primary_cat" name="seopress_robots_primary_cat">

                            <?php $cats = get_categories();

                    if ('product' == $typenow) {
                        $cats = get_the_terms($post, 'product_cat');
                    }
                    if ( ! empty($cats)) { ?>
                            <option <?php echo selected('none', $seopress_robots_primary_cat, false); ?>
                                value="none"><?php _e('None (will disable this feature)', 'wp-seopress'); ?>
                            </option>
                            <?php foreach ($cats as $category) { ?>
                            <option <?php echo selected($category->term_id, $seopress_robots_primary_cat, false); ?>
                                value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?>
                            </option>
                            <?php }
                        } ?>
                        </select>
                        </p>
                        <?php }
                        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
                        <p>
                            <label for="seopress_robots_breadcrumbs_meta"><?php _e('Custom breadcrumbs', 'wp-seopress'); ?></label>
                            <span class="description"><?php _e('Enter a custom value, useful if your title is too long', 'wp-seopress'); ?></span>
                        </p>
                        <p>
                            <input id="seopress_robots_breadcrumbs_meta" type="text" name="seopress_robots_breadcrumbs"
                                class="components-text-control__input"
                                placeholder="<?php esc_html_e(sprintf(__('Current breadcrumbs: %s', 'wp-seopress'), $data_attr['title'])); ?>"
                                aria-label="<?php _e('Custom breadcrumbs', 'wp-seopress'); ?>"
                                value="<?php echo $seopress_robots_breadcrumbs; ?>" />
                        </p>
                        <?php } ?>
                    </div>
                    <?php }
                        if (array_key_exists('social-tab', $seo_tabs)) { ?>
                    <div id="tabs-2">
                        <div class="box-left">
                            <p class="description-alt desc-fb">
                                <svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true"
                                    focusable="false">
                                    <path
                                        d="M12 15.8c-3.7 0-6.8-3-6.8-6.8s3-6.8 6.8-6.8c3.7 0 6.8 3 6.8 6.8s-3.1 6.8-6.8 6.8zm0-12C9.1 3.8 6.8 6.1 6.8 9s2.4 5.2 5.2 5.2c2.9 0 5.2-2.4 5.2-5.2S14.9 3.8 12 3.8zM8 17.5h8V19H8zM10 20.5h4V22h-4z">
                                    </path>
                                </svg>
                                <?php _e('LinkedIn, Instagram, WhatsApp and Pinterest use the same social metadata as Facebook. Twitter does the same if no Twitter cards tags are defined below.', 'wp-seopress'); ?>
                            </p>
                            <p>
                                <span class="dashicons dashicons-facebook-alt"></span>
                            </p>
                            <p>
                                <span class="dashicons dashicons-external"></span>
                                <a href="https://developers.facebook.com/tools/debug/sharing/?q=<?php echo get_permalink(get_the_id()); ?>"
                                    target="_blank">
                                    <?php _e('Ask Facebook to update its cache', 'wp-seopress'); ?>
                                </a>
                            </p>
                            <p>
                                <label for="seopress_social_fb_title_meta"><?php _e('Facebook Title', 'wp-seopress'); ?></label>
                                <input id="seopress_social_fb_title_meta" type="text" name="seopress_social_fb_title"
                                    class="components-text-control__input"
                                    placeholder="<?php esc_html_e('Enter your Facebook title', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Facebook Title', 'wp-seopress'); ?>"
                                    value="<?php echo $seopress_social_fb_title; ?>" />
                            </p>
                            <p>
                                <label for="seopress_social_fb_desc_meta"><?php _e('Facebook description', 'wp-seopress'); ?></label>
                                <textarea id="seopress_social_fb_desc_meta" name="seopress_social_fb_desc"
                                    class="components-text-control__input"
                                    placeholder="<?php esc_html_e('Enter your Facebook description', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Facebook description', 'wp-seopress'); ?>"><?php echo $seopress_social_fb_desc; ?></textarea>
                            </p>
                            <p>
                                <label for="seopress_social_fb_img_meta">
                                    <?php _e('Facebook Thumbnail', 'wp-seopress'); ?>
                                </label>
                                <input id="seopress_social_fb_img_meta" type="text" name="seopress_social_fb_img"
                                    class="components-text-control__input seopress_social_fb_img_meta"
                                    placeholder="<?php esc_html_e('Select your default thumbnail', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Facebook Thumbnail', 'wp-seopress'); ?>"
                                    value="<?php echo $seopress_social_fb_img; ?>" />
                            </p>
                            <p class="description">
                                <?php _e('Minimum size: 200x200px, ideal ratio 1.91:1, 8Mb max. (eg: 1640x856px or 3280x1712px for retina screens)', 'wp-seopress'); ?>
                            </p>
                            <p>
                                <input type="hidden" name="seopress_social_fb_img_attachment_id" id="seopress_social_fb_img_attachment_id" value="<?php echo esc_html($seopress_social_fb_img_attachment_id); ?>">
                                <input type="hidden" name="seopress_social_fb_img_width" id="seopress_social_fb_img_width" value="<?php echo esc_html($seopress_social_fb_img_width); ?>">
                                <input type="hidden" name="seopress_social_fb_img_height" id="seopress_social_fb_img_height" value="<?php echo esc_html($seopress_social_fb_img_height); ?>">

                                <input id="seopress_social_fb_img_upload"
                                    class="<?php echo seopress_btn_secondary_classes(); ?>"
                                    type="button"
                                    value="<?php _e('Upload an Image', 'wp-seopress'); ?>" />
                            </p>
                        </div>
                        <div class="box-right">
                            <div class="facebook-snippet-preview">
                                <h3>
                                    <?php _e('Facebook Preview', 'wp-seopress'); ?>
                                </h3>
                                <?php if ('1' == seopress_get_toggle_option('social')) { ?>
                                <p>
                                    <?php _e('This is what your post will look like in Facebook. You have to publish your post to get the Facebook Preview.', 'wp-seopress'); ?>
                                </p>
                                <?php } else { ?>
                                <p class="notice notice-error">
                                    <?php _e('The Social Networks feature is disabled. Still seing informations from the FB Preview? You probably have social tags added by your theme or a plugin.', 'wp-seopress'); ?>
                                </p>
                                <?php } ?>
                                <div class="facebook-snippet-box">
                                    <div class="snippet-fb-img-alert alert1" style="display:none">
                                        <p class="notice notice-error"><?php _e('File type not supported by Facebook. Please choose another image.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-fb-img-alert alert2" style="display:none">
                                        <p class="notice notice-error"><?php _e('Minimun size for Facebook is <strong>200x200px</strong>. Please choose another image.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-fb-img-alert alert3" style="display:none">
                                        <p class="notice notice-error"><?php _e('File error. Please choose another image.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-fb-img-alert alert4" style="display:none">
                                        <p class="notice notice-info"><?php _e('Your image ratio is: ', 'wp-seopress'); ?><span></span>.
                                            <?php _e('The closer to 1.91 the better.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-fb-img-alert alert5" style="display:none">
                                        <p class="notice notice-error"><?php _e('File URL is not valid.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-fb-img"><img src="" width="524" height="274" alt=""
                                            aria-label="" /><span class="seopress_social_fb_img_upload"></span></div>
                                    <div class="snippet-fb-img-custom" style="display:none"><img src="" width="524"
                                            height="274" alt="" aria-label="" /><span class="seopress_social_fb_img_upload"></span></div>
                                    <div class="snippet-fb-img-default" style="display:none"><img src="" width="524"
                                            height="274" alt="" aria-label="" /><span class="seopress_social_fb_img_upload"></span></div>
                                    <div class="facebook-snippet-text">
                                        <div class="snippet-meta">
                                            <div class="snippet-fb-url"></div>
                                            <div class="fb-sep">|</div>
                                            <div class="fb-by"><?php _e('By ', 'wp-seopress'); ?>
                                            </div>
                                            <div class="snippet-fb-site-name"></div>
                                        </div>
                                        <div class="title-desc">
                                            <div class="snippet-fb-title"></div>
                                            <div class="snippet-fb-title-custom" style="display:none"></div>
                                            <?php global $tag;
                                            if (get_the_title()) { ?>
                                            <div class="snippet-fb-title-default" style="display:none"><?php the_title(); ?> -
                                                <?php bloginfo('name'); ?>
                                            </div>
                                            <?php } elseif ($tag) { ?>
                                            <div class="snippet-fb-title-default" style="display:none"><?php echo $tag->name; ?> -
                                                <?php bloginfo('name'); ?>
                                            </div>
                                            <?php } ?>
                                            <div class="snippet-fb-description">...</div>
                                            <div class="snippet-fb-description-custom" style="display:none"></div>
                                            <div class="snippet-fb-description-default" style="display:none"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="box-left">
                            <p>
                                <span class="dashicons dashicons-twitter"></span>
                            </p>

                            <p>
                                <span class="dashicons dashicons-external"></span>
                                <a href="https://cards-dev.twitter.com/validator" target="_blank">
                                    <?php _e('Preview your Twitter card using the official validator', 'wp-seopress'); ?>
                                </a>
                            </p>
                            <p>
                                <label for="seopress_social_twitter_title_meta"><?php _e('Twitter Title', 'wp-seopress'); ?></label>
                                <input id="seopress_social_twitter_title_meta" type="text"
                                    class="components-text-control__input" name="seopress_social_twitter_title"
                                    placeholder="<?php esc_html_e('Enter your Twitter title', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Twitter Title', 'wp-seopress'); ?>"
                                    value="<?php echo $seopress_social_twitter_title; ?>" />
                            </p>
                            <p>
                                <label for="seopress_social_twitter_desc_meta"><?php _e('Twitter description', 'wp-seopress'); ?></label>
                                <textarea id="seopress_social_twitter_desc_meta" name="seopress_social_twitter_desc"
                                    class="components-text-control__input"
                                    placeholder="<?php esc_html_e('Enter your Twitter description', 'wp-seopress'); ?>"
                                    aria-label="<?php _e('Twitter description', 'wp-seopress'); ?>"><?php echo $seopress_social_twitter_desc; ?></textarea>
                            </p>
                            <p>
                                <label for="seopress_social_twitter_img_meta"><?php _e('Twitter Thumbnail', 'wp-seopress'); ?></label>
                                <input id="seopress_social_twitter_img_meta" type="text"
                                    class="components-text-control__input seopress_social_twitter_img_meta" name="seopress_social_twitter_img"
                                    placeholder="<?php esc_html_e('Select your default thumbnail', 'wp-seopress'); ?>"
                                    value="<?php echo $seopress_social_twitter_img; ?>" />
                            </p>
                            <p class="description">
                                <?php _e('Minimum size: 144x144px (300x157px with large card enabled), ideal ratio 1:1 (2:1 with large card), 5Mb max.', 'wp-seopress'); ?>
                            </p>
                            <p>
                                <input type="hidden" name="seopress_social_twitter_img_attachment_id" id="seopress_social_twitter_img_attachment_id" value="<?php echo esc_html($seopress_social_twitter_img_attachment_id); ?>">
                                <input type="hidden" name="seopress_social_twitter_img_width" id="seopress_social_twitter_img_width" value="<?php echo esc_html($seopress_social_twitter_img_width); ?>">
                                <input type="hidden" name="seopress_social_twitter_img_height" id="seopress_social_twitter_img_height" value="<?php echo esc_html($seopress_social_twitter_img_height); ?>">

                                <input id="seopress_social_twitter_img_upload"
                                    class="<?php echo seopress_btn_secondary_classes(); ?>"
                                    type="button"
                                    aria-label="<?php _e('Twitter Thumbnail', 'wp-seopress'); ?>"
                                    value="<?php _e('Upload an Image', 'wp-seopress'); ?>" />
                            </p>
                        </div>
                        <div class="box-right">
                            <div class="twitter-snippet-preview">
                                <h3><?php _e('Twitter Preview', 'wp-seopress'); ?>
                                </h3>
                                <?php if ('1' == seopress_get_toggle_option('social')) { ?>
                                <p><?php _e('This is what your post will look like in Twitter. You have to publish your post to get the Twitter Preview.', 'wp-seopress'); ?>
                                </p>
                                <?php } else { ?>
                                <p class="notice notice-error"><?php _e('The Social Networks feature is disabled. Still seing informations from the Twitter Preview? You probably have social tags added by your theme or a plugin.', 'wp-seopress'); ?>
                                </p>
                                <?php } ?>
                                <div class="twitter-snippet-box">
                                    <div class="snippet-twitter-img-alert alert1" style="display:none">
                                        <p class="notice notice-error"><?php _e('File type not supported by Twitter. Please choose another image.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-twitter-img-alert alert2" style="display:none">
                                        <p class="notice notice-error"><?php _e('Minimun size for Twitter is <strong>144x144px</strong>. Please choose another image.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-twitter-img-alert alert3" style="display:none">
                                        <p class="notice notice-error"><?php _e('File error. Please choose another image.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-twitter-img-alert alert4" style="display:none">
                                        <p class="notice notice-info"><?php _e('Your image ratio is: ', 'wp-seopress'); ?><span></span>.
                                            <?php _e('The closer to 1 the better (with large card, 2 is better).', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-twitter-img-alert alert5" style="display:none">
                                        <p class="notice notice-error"><?php _e('File URL is not valid.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                    <div class="snippet-twitter-img"><img src="" width="524" height="274" alt=""
                                            aria-label="" /><span class="seopress_social_twitter_img_upload"></span></div>
                                    <div class="snippet-twitter-img-custom" style="display:none"><img src="" width="600"
                                            height="314" alt="" aria-label="" /><span class="seopress_social_twitter_img_upload"></span></div>
                                    <div class="snippet-twitter-img-default" style="display:none"><img src=""
                                            width="600" height="314" alt="" aria-label="" /><span class="seopress_social_twitter_img_upload"></span></div>

                                    <div class="twitter-snippet-text">
                                        <div class="title-desc">
                                            <div class="snippet-twitter-title"></div>
                                            <div class="snippet-twitter-title-custom" style="display:none"></div>
                                            <?php global $tag;
                                            if (get_the_title()) { ?>
                                            <div class="snippet-twitter-title-default" style="display:none"><?php the_title(); ?> -
                                                <?php bloginfo('name'); ?>
                                            </div>
                                            <?php } elseif ($tag) { ?>
                                            <div class="snippet-twitter-title-default" style="display:none"><?php echo $tag->name; ?> -
                                                <?php bloginfo('name'); ?>
                                            </div>
                                            <?php } ?>
                                            <div class="snippet-twitter-description">...</div>
                                            <div class="snippet-twitter-description-custom" style="display:none"></div>
                                            <div class="snippet-twitter-description-default" style="display:none"></div>
                                        </div>
                                        <div class="snippet-meta">
                                            <div class="snippet-twitter-url"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }
                    }

                    if (array_key_exists('redirect-tab', $seo_tabs)) {

                        ?>
                    <div id="tabs-4">
                        <p>
                            <label for="seopress_redirections_enabled_meta" id="seopress_redirections_enabled">
                                <input type="checkbox" name="seopress_redirections_enabled"
                                    id="seopress_redirections_enabled_meta" value="yes" <?php echo checked($seopress_redirections_enabled, 'yes', false); ?>
                                />
                                <?php _e('Enable redirection?', 'wp-seopress'); ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_redirections_enabled_regex_meta" id="seopress_redirections_enabled_regex">
                                <input type="checkbox" name="seopress_redirections_enabled_regex"
                                    id="seopress_redirections_enabled_regex_meta" value="yes" <?php echo checked($seopress_redirections_enabled_regex, 'yes', false); ?>
                                />
                                <?php _e('Regex?', 'wp-seopress'); ?>
                            </label>
                        </p>
                        <p>
                            <label for="seopress_redirections_logged_status"><?php _e('Select a login status: ', 'wp-seopress'); ?></label>

                            <select id="seopress_redirections_logged_status" name="seopress_redirections_logged_status">
                                <option <?php echo selected('both', $seopress_redirections_logged_status); ?>
                                    value="both"><?php _e('All', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('only_logged_in', $seopress_redirections_logged_status); ?>
                                    value="only_logged_in"><?php _e('Only Logged In', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('only_not_logged_in', $seopress_redirections_logged_status); ?>
                                    value="only_not_logged_in"><?php _e('Only Not Logged In', 'wp-seopress'); ?>
                                </option>
                            </select>
                        </p>
                        <p>

                            <label for="seopress_redirections_type"><?php _e('Select a redirection type: ', 'wp-seopress'); ?></label>

                            <select id="seopress_redirections_type" name="seopress_redirections_type">
                                <option <?php echo selected('301', $seopress_redirections_type, false); ?>
                                    value="301"><?php _e('301 Moved Permanently', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('302', $seopress_redirections_type, false); ?>
                                    value="302"><?php _e('302 Found / Moved Temporarily', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('307', $seopress_redirections_type, false); ?>
                                    value="307"><?php _e('307 Moved Temporarily', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('410', $seopress_redirections_type, false); ?>
                                    value="410"><?php _e('410 Gone', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('451', $seopress_redirections_type, false); ?>
                                    value="451"><?php _e('451 Unavailable For Legal Reasons', 'wp-seopress'); ?>
                                </option>
                            </select>
                        </p>
                        <p>
                            <label for="seopress_redirections_value_meta"><?php _e('URL redirection', 'wp-seopress'); ?></label>
                            <input id="seopress_redirections_value_meta" type="text" name="seopress_redirections_value"
                                class="components-text-control__input js-seopress_redirections_value_meta"
                                placeholder="<?php esc_html_e('Enter your new URL in absolute (eg: https://www.example.com/)', 'wp-seopress'); ?>"
                                aria-label="<?php _e('URL redirection', 'wp-seopress'); ?>"
                                value="<?php echo $seopress_redirections_value; ?>" />

                        </p>

                        <script>
                            document.addEventListener('DOMContentLoaded', function(){

                                var cache = {};
                                jQuery( ".js-seopress_redirections_value_meta" ).autocomplete({
                                    source: async function( request, response ) {
                                        var term = request.term;
                                        if ( term in cache ) {
                                            response( cache[ term ] );
                                            return;
                                        }

                                        const dataResponse = await fetch("<?php echo rest_url(); ?>seopress/v1/search-url?url=" + term)
                                        const data = await dataResponse.json();

                                        cache[ term ] = data.map(item => {
                                            return {
                                                label: item.post_title + " (" + item.guid + ")",
                                                value: item.guid
                                            }
                                        });
                                        response( cache[term] );
                                    },

                                    minLength: 3,
                                });


                            })
                        </script>
                        <?php if ('seopress_404' == $typenow) { ?>
                        <p>
                            <label for="seopress_redirections_param_meta"><?php _e('Query parameters', 'wp-seopress'); ?></label>
                            <select name="seopress_redirections_param">
                                <option <?php echo selected('exact_match', $seopress_redirections_param, false); ?>
                                    value="exact_match"><?php _e('Exactly match all parameters', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('without_param', $seopress_redirections_param, false); ?>
                                    value="without_param"><?php _e('Exclude all parameters', 'wp-seopress'); ?>
                                </option>
                                <option <?php echo selected('with_ignored_param', $seopress_redirections_param, false); ?>
                                    value="with_ignored_param"><?php _e('Exclude all parameters and pass them to the redirection', 'wp-seopress'); ?>
                                </option>
                            </select>
                        </p>
                        <?php } ?>
                        <p>
                            <?php if ('yes' == $seopress_redirections_enabled) {
                        $status_code = ['410', '451'];
                        if ('' != $seopress_redirections_value || in_array($seopress_redirections_type, $status_code)) {
                            if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                                if ('seopress_404' == $typenow) {
                                    $href = get_home_url() . '/' . get_the_title();
                                } else {
                                    $href = get_the_permalink();
                                }
                            } elseif ('term.php' == $pagenow) {
                                $href = get_term_link($term);
                            } else {
                                $href = get_the_permalink();
                            } ?>
                            <a href="<?php echo $href; ?>"
                                id="seopress_redirections_value_default"
                                class="<?php echo seopress_btn_secondary_classes(); ?>"
                                target="_blank">
                                <?php _e('Test your URL', 'wp-seopress'); ?>
                            </a>
                            <?php
                        }
                    }

                if ((function_exists('seopress_mu_white_label_help_links_option') && '1' !== seopress_mu_white_label_help_links_option()) || (function_exists('seopress_white_label_help_links_option') && '1' !== seopress_white_label_help_links_option())) {
                    $docs = seopress_get_docs_links(); ?>
                            <span class="seopress-help dashicons dashicons-external"></span>
                            <a href="<?php echo $docs['redirects']['enable']; ?>"
                                target="_blank" class="seopress-help seopress-doc">
                                <?php _e('Need help with your redirections? Read our guide.', 'wp-seopress'); ?>
                            </a>
                            <?php
                } ?>
                        </p>
                    </div>
                    <?php }
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('news')) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        if (array_key_exists('news-tab', $seo_tabs)) { ?>
                    <div id="tabs-5">
                        <p>
                            <label for="seopress_news_disabled_meta" id="seopress_news_disabled">
                                <input type="checkbox" name="seopress_news_disabled" id="seopress_news_disabled_meta"
                                    value="yes" <?php echo checked($seopress_news_disabled, 'yes', false); ?>
                                />
                                <?php _e('Exclude this post from Google News Sitemap?', 'wp-seopress'); ?>
                            </label>
                        </p>
                    </div>
                    <?php }
                    }
                }
            }
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('xml-sitemap') && function_exists('seopress_xml_sitemap_video_enable_option') && '1' == seopress_xml_sitemap_video_enable_option()) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        //Init $seopress_video array if empty
                        if (empty($seopress_video)) {
                            $seopress_video = ['0' => ['']];
                        }

                        $count = $seopress_video[0];

                        $total = '';
                        if (is_array($count)) {
                            end($count);
                            $total = key($count);
                        }

                        if (array_key_exists('video-tab', $seo_tabs)) { ?>
                    <div id="tabs-6">
                        <p>
                            <label for="seopress_video_disabled_meta" id="seopress_video_disabled">
                                <input type="checkbox" name="seopress_video_disabled" id="seopress_video_disabled_meta"
                                    value="yes" <?php echo checked($seopress_video_disabled, 'yes', false); ?>
                                />
                                <?php _e('Exclude this post from Video Sitemap?', 'wp-seopress'); ?>
                            </label>
                            <span class="description"><?php _e('If your post is set to noindex, it will be automatically excluded from the sitemap.', 'wp-seopress'); ?></span>
                        </p>
                        <div id="wrap-videos"
                            data-count="<?php echo $total; ?>">
                            <?php foreach ($seopress_video[0] as $key => $value) {
                            $check_url             = isset($seopress_video[0][$key]['url']) ? esc_attr($seopress_video[0][$key]['url']) : null;
                            $check_internal_video  = isset($seopress_video[0][$key]['internal_video']) ? esc_attr($seopress_video[0][$key]['internal_video']) : null;
                            $check_title           = isset($seopress_video[0][$key]['title']) ? esc_attr($seopress_video[0][$key]['title']) : null;
                            $check_desc            = isset($seopress_video[0][$key]['desc']) ? esc_attr($seopress_video[0][$key]['desc']) : null;
                            $check_thumbnail       = isset($seopress_video[0][$key]['thumbnail']) ? esc_attr($seopress_video[0][$key]['thumbnail']) : null;
                            $check_duration        = isset($seopress_video[0][$key]['duration']) ? esc_attr($seopress_video[0][$key]['duration']) : null;
                            $check_rating          = isset($seopress_video[0][$key]['rating']) ? esc_attr($seopress_video[0][$key]['rating']) : null;
                            $check_view_count      = isset($seopress_video[0][$key]['view_count']) ? esc_attr($seopress_video[0][$key]['view_count']) : null;
                            $check_view_count      = isset($seopress_video[0][$key]['view_count']) ? esc_attr($seopress_video[0][$key]['view_count']) : null;
                            $check_tag             = isset($seopress_video[0][$key]['tag']) ? esc_attr($seopress_video[0][$key]['tag']) : null;
                            $check_cat             = isset($seopress_video[0][$key]['cat']) ? esc_attr($seopress_video[0][$key]['cat']) : null;
                            $check_family_friendly = isset($seopress_video[0][$key]['family_friendly']) ? esc_attr($seopress_video[0][$key]['family_friendly']) : null; ?>

                            <div class="video">
                                <h3 class="accordion-section-title" tabindex="0"><?php _e('Video ', 'wp-seopress'); ?><?php echo $check_title; ?>
                                </h3>
                                <div class="accordion-section-content">
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][url_meta]"><?php _e('Video URL (required)', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][url_meta]"
                                            type="text" class="components-text-control__input"
                                            name="seopress_video[<?php echo $key; ?>][url]"
                                            placeholder="<?php esc_html_e('Enter your video URL', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video URL', 'wp-seopress'); ?>"
                                            value="<?php echo $check_url; ?>" />
                                    </p>
                                    <p class="internal_video">
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][internal_video_meta]"
                                            id="seopress_video[<?php echo $key; ?>][internal_video]">
                                            <input type="checkbox"
                                                name="seopress_video[<?php echo $key; ?>][internal_video]"
                                                id="seopress_video[<?php echo $key; ?>][internal_video_meta]"
                                                value="yes" <?php echo checked($check_internal_video, 'yes', false); ?>
                                            />
                                            <?php _e('NOT an external video (eg: video hosting on YouTube, Vimeo, Wistia...)? Check this if your video is hosting on this server.', 'wp-seopress'); ?>
                                        </label>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][title_meta]"><?php _e('Video Title (required)', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][title_meta]"
                                            type="text" class="components-text-control__input"
                                            name="seopress_video[<?php echo $key; ?>][title]"
                                            placeholder="<?php esc_html_e('Enter your video title', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video title', 'wp-seopress'); ?>"
                                            value="<?php echo $check_title; ?>" />
                                        <span class="description"><?php _e('Default: title tag, if not available, post title.', 'wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][desc_meta]"><?php _e('Video Description (required)', 'wp-seopress'); ?></label>
                                        <textarea
                                            id="seopress_video[<?php echo $key; ?>][desc_meta]"
                                            name="seopress_video[<?php echo $key; ?>][desc]"
                                            class="components-text-control__input"
                                            placeholder="<?php esc_html_e('Enter your video description', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video description', 'wp-seopress'); ?>"><?php echo $check_desc; ?></textarea>
                                        <span class="description"><?php _e('2048 characters max.; default: meta description. If not available, use the beginning of the post content.', 'wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][thumbnail_meta]"><?php _e('Video Thumbnail (required)', 'wp-seopress'); ?></label>

                                        <input
                                            id="seopress_video[<?php echo $key; ?>][thumbnail_meta]"
                                            class="seopress_video_thumbnail_meta components-text-control__input"
                                            type="text"
                                            name="seopress_video[<?php echo $key; ?>][thumbnail]"
                                            placeholder="<?php esc_html_e('Select your video thumbnail', 'wp-seopress'); ?>"
                                            value="<?php echo $check_thumbnail; ?>" />


                                        <input
                                            class="<?php echo seopress_btn_secondary_classes(); ?> seopress_video_thumbnail_upload seopress_media_upload"
                                            type="button"
                                            aria-label="<?php _e('Video Thumbnail', 'wp-seopress'); ?>"
                                            value="<?php _e('Upload an Image', 'wp-seopress'); ?>" />
                                        <span class="description">
                                            <?php _e('Minimum size: 160x90px (1920x1080 max), JPG, PNG or GIF formats. Default: your post featured image.', 'wp-seopress'); ?>
                                        </span>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][duration_meta]"><?php _e('Video Duration (recommended)', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][duration_meta]"
                                            type="number" step="1" min="0" max="28800"
                                            name="seopress_video[<?php echo $key; ?>][duration]"
                                            placeholder="<?php esc_html_e('Duration in seconds', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video duration', 'wp-seopress'); ?>"
                                            value="<?php echo $check_duration; ?>" />
                                        <span class="description"><?php _e('The duration of the video in seconds. Value must be between 0 and 28800 (8 hours).', 'wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][rating_meta]"><?php _e('Video Rating', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][rating_meta]"
                                            type="number" step="0.1" min="0" max="5"
                                            name="seopress_video[<?php echo $key; ?>][rating]"
                                            placeholder="<?php esc_html_e('Video rating', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video rating', 'wp-seopress'); ?>"
                                            value="<?php echo $check_rating; ?>" />
                                        <span class="description"><?php _e('Allowed values are float numbers in the range 0.0 to 5.0.', 'wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][view_count_meta]"><?php _e('View count', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][view_count_meta]"
                                            type="number"
                                            name="seopress_video[<?php echo $key; ?>][view_count]"
                                            placeholder="<?php esc_html_e('Number of views', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('View count', 'wp-seopress'); ?>"
                                            value="<?php echo $check_view_count; ?>" />
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][tag_meta]"><?php _e('Video tags', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][tag_meta]"
                                            type="text" class="components-text-control__input"
                                            name="seopress_video[<?php echo $key; ?>][tag]"
                                            placeholder="<?php esc_html_e('Enter your video tags', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video tags', 'wp-seopress'); ?>"
                                            value="<?php echo $check_tag; ?>" />
                                        <span class="description"><?php _e('32 tags max., separate tags with commas. Default: target keywords + post tags if available.', 'wp-seopress'); ?></span>
                                    </p>
                                    <p>
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][cat_meta]"><?php _e('Video categories', 'wp-seopress'); ?></label>
                                        <input
                                            id="seopress_video[<?php echo $key; ?>][cat_meta]"
                                            type="text" class="components-text-control__input"
                                            name="seopress_video[<?php echo $key; ?>][cat]"
                                            placeholder="<?php esc_html_e('Enter your video categories', 'wp-seopress'); ?>"
                                            aria-label="<?php _e('Video categories', 'wp-seopress'); ?>"
                                            value="<?php echo $check_cat; ?>" />
                                        <span class="description"><?php _e('256 characters max., usually a video will belong to a single category, separate categories with commas. Default: first post category if available.', 'wp-seopress'); ?></span>
                                    </p>
                                    <p class="family-friendly">
                                        <label
                                            for="seopress_video[<?php echo $key; ?>][family_friendly_meta]"
                                            id="seopress_video[<?php echo $key; ?>][family_friendly]">
                                            <input type="checkbox"
                                                name="seopress_video[<?php echo $key; ?>][family_friendly]"
                                                id="seopress_video[<?php echo $key; ?>][family_friendly_meta]"
                                                value="yes" <?php echo checked($check_family_friendly, 'yes', false); ?>
                                            />
                                            <?php _e('NOT family friendly?', 'wp-seopress'); ?>
                                        </label>
                                        <span class="description"><?php _e('The video will be available only to users with SafeSearch turned off.', 'wp-seopress'); ?></span>
                                    </p>
                                    <p><a href="#"
                                            class="remove-video components-button editor-post-trash is-tertiary is-destructive"><?php _e('Remove video', 'wp-seopress'); ?></a>
                                    </p>
                                </div>
                            </div>
                            <?php
                        } ?>
                        </div>
                        <p>
                            <a href="#" id="add-video"
                                class="add-video <?php echo seopress_btn_secondary_classes(); ?>">
                                <?php _e('Add video', 'wp-seopress'); ?>
                            </a>
                        </p>
                    </div>
                    <?php }
                    }
                }
            }
        } ?>
                </div>

                <?php
                 if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) { ?>
            </div>
        </div>
    </td>
</tr>
<?php } ?>
<input type="hidden" id="seo_tabs" name="seo_tabs"
    value="<?php echo htmlspecialchars(json_encode(array_keys($seo_tabs))); ?>">
