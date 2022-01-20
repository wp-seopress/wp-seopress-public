<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_advanced_advanced_replytocom_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_replytocom']); ?>

<label for="seopress_advanced_advanced_replytocom">
    <input id="seopress_advanced_advanced_replytocom"
        name="seopress_advanced_option_name[seopress_advanced_advanced_replytocom]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Remove ?replytocom link in source code', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_replytocom'])) {
        esc_attr($options['seopress_advanced_advanced_replytocom']);
    }
}

function seopress_advanced_advanced_tax_desc_editor_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_tax_desc_editor']); ?>

<label for="seopress_advanced_advanced_tax_desc_editor">
    <input id="seopress_advanced_advanced_tax_desc_editor"
        name="seopress_advanced_option_name[seopress_advanced_advanced_tax_desc_editor]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Add TINYMCE editor to term description', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_tax_desc_editor'])) {
        esc_attr($options['seopress_advanced_advanced_tax_desc_editor']);
    }
}

function seopress_advanced_advanced_category_url_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_category_url']); ?>

<label for="seopress_advanced_advanced_category_url">
    <input id="seopress_advanced_advanced_category_url"
        name="seopress_advanced_option_name[seopress_advanced_advanced_category_url]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php
    $category_base = '/category/';
    if (get_option('category_base')) {
        $category_base = '/' . get_option('category_base');
    }

    printf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), $category_base); ?>
</label>

<div class="seopress-notice">
    <p>
        <?php _e('You have to flush your permalinks each time you change this setting.', 'wp-seopress'); ?>
    </p>
</div>

<?php
    if (isset($options['seopress_advanced_advanced_category_url'])) {
        esc_attr($options['seopress_advanced_advanced_category_url']);
    }
}

function seopress_advanced_advanced_product_cat_url_callback() {
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_product_cat_url']);

        ?>

    <label for="seopress_advanced_advanced_product_cat_url">
        <input id="seopress_advanced_advanced_product_cat_url"
            name="seopress_advanced_option_name[seopress_advanced_advanced_product_cat_url]" type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>

        <?php
        $category_base = get_option('woocommerce_permalinks');
        $category_base = $category_base['category_base'];

        if ('' != $category_base) {
            $category_base = '/' . $category_base . '/';
        } else {
            $category_base = '/product-category/';
        }

        printf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), $category_base); ?>

    </label>

    <div class="seopress-notice">
        <p>
            <?php _e('You have to flush your permalinks each time you change this setting.', 'wp-seopress'); ?>
        </p>
        <p>
            <?php _e('Make sure you don\'t have indentical URLs after activating this option to prevent conflicts.', 'wp-seopress'); ?>
        </p>
    </div>

    <?php
        if (isset($options['seopress_advanced_advanced_product_cat_url'])) {
            esc_attr($options['seopress_advanced_advanced_product_cat_url']);
        }
    } else { ?>
        <div class="seopress-notice is-warning">
            <p>
                <?php _e('You need to enable <strong>WooCommerce</strong> to apply these settings.', 'wp-seopress'); ?>
            </p>
        </div>
        <?php
    }
}

function seopress_advanced_advanced_trailingslash_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_trailingslash']); ?>


<label for="seopress_advanced_advanced_trailingslash">
    <input id="seopress_advanced_advanced_trailingslash"
        name="seopress_advanced_option_name[seopress_advanced_advanced_trailingslash]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Disable trailing slash for metas', 'wp-seopress'); ?>
</label>

<div class="seopress-notice">
    <p>
        <?php _e('You must check this box if the structure of your permalinks <strong>DOES NOT</strong> contain a slash at the end (eg: /%postname%)', 'wp-seopress'); ?>
    </p>
</div>

<?php
    if (isset($options['seopress_advanced_advanced_trailingslash'])) {
        esc_attr($options['seopress_advanced_advanced_trailingslash']);
    }
}

function seopress_advanced_advanced_wp_generator_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_wp_generator']); ?>

<label for="seopress_advanced_advanced_wp_generator">
    <input id="seopress_advanced_advanced_wp_generator"
        name="seopress_advanced_option_name[seopress_advanced_advanced_wp_generator]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Remove WordPress meta generator in source code', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_wp_generator'])) {
        esc_attr($options['seopress_advanced_advanced_wp_generator']);
    }
}

function seopress_advanced_advanced_hentry_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_hentry']); ?>

<label for="seopress_advanced_advanced_hentry">
    <input id="seopress_advanced_advanced_hentry"
        name="seopress_advanced_option_name[seopress_advanced_advanced_hentry]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove hentry post class to prevent Google from seeing this as structured data (schema)', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_hentry'])) {
        esc_attr($options['seopress_advanced_advanced_hentry']);
    }
}

function seopress_advanced_advanced_comments_author_url_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_comments_author_url']); ?>

<label for="seopress_advanced_advanced_comments_author_url">
    <input id="seopress_advanced_advanced_comments_author_url"
        name="seopress_advanced_option_name[seopress_advanced_advanced_comments_author_url]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove comment author URL in comments if the website is filled from profile page', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_comments_author_url'])) {
        esc_attr($options['seopress_advanced_advanced_comments_author_url']);
    }
}

function seopress_advanced_advanced_comments_website_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_comments_website']); ?>

<label for="seopress_advanced_advanced_comments_website">
    <input id="seopress_advanced_advanced_comments_website"
        name="seopress_advanced_option_name[seopress_advanced_advanced_comments_website]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove website field from comment form to reduce spam', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_comments_website'])) {
        esc_attr($options['seopress_advanced_advanced_comments_website']);
    }
}

function seopress_advanced_advanced_comments_form_link_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_comments_form_link']); ?>

<label for="seopress_advanced_advanced_comments_form_link">
    <input id="seopress_advanced_advanced_comments_form_link"
        name="seopress_advanced_option_name[seopress_advanced_advanced_comments_form_link]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Prevent search engines to follow / index the link to the comments form (<em>eg: https://www.example.com/my-blog-post/#respond</em>)', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_comments_form_link'])) {
        esc_attr($options['seopress_advanced_advanced_comments_form_link']);
    }
}

function seopress_advanced_advanced_wp_shortlink_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_wp_shortlink']); ?>

<label for="seopress_advanced_advanced_wp_shortlink">
    <input id="seopress_advanced_advanced_wp_shortlink"
        name="seopress_advanced_option_name[seopress_advanced_advanced_wp_shortlink]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove WordPress shortlink meta tag in source code (eg:', 'wp-seopress'); ?>
    <em>
        <?php echo esc_attr('<link rel="shortlink" href="https://www.example.com/"/>'); ?>
    </em>)
</label>

<?php if (isset($options['seopress_advanced_advanced_wp_shortlink'])) {
        esc_attr($options['seopress_advanced_advanced_wp_shortlink']);
    }
}

function seopress_advanced_advanced_wp_wlw_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_wp_wlw']); ?>

<label for="seopress_advanced_advanced_wp_wlw">
    <input id="seopress_advanced_advanced_wp_wlw"
        name="seopress_advanced_option_name[seopress_advanced_advanced_wp_wlw]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove Windows Live Writer meta tag in source code (eg:', 'wp-seopress'); ?>
    <em>
        <?php echo esc_attr('<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="https://www.example.com/wp-includes/wlwmanifest.xml" />'); ?>
    </em>)
</label>

<?php if (isset($options['seopress_advanced_advanced_wp_wlw'])) {
        esc_attr($options['seopress_advanced_advanced_wp_wlw']);
    }
}

function seopress_advanced_advanced_wp_rsd_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_wp_rsd']); ?>

<label for="seopress_advanced_advanced_wp_rsd">
    <input id="seopress_advanced_advanced_wp_rsd"
        name="seopress_advanced_option_name[seopress_advanced_advanced_wp_rsd]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove Really Simple Discovery meta tag in source code (eg:', 'wp-seopress'); ?>
    <em>
        <?php echo esc_attr('<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://www.example.com/xmlrpc.php?rsd" />'); ?>
    </em>)
</label>

<p class="description">
    <?php _e('WordPress Site Health feature will return a HTTPS warning if you enable this option. This is a false positive of course.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_wp_rsd'])) {
        esc_attr($options['seopress_advanced_advanced_wp_rsd']);
    }
}

function seopress_advanced_advanced_google_callback() {
    $options = get_option('seopress_advanced_option_name');
    $check   = isset($options['seopress_advanced_advanced_google']) ? $options['seopress_advanced_advanced_google'] : null;

    printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_google]" placeholder="' . esc_html__('Enter Google meta value site verification', 'wp-seopress') . '" aria-label="' . __('Google site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
); ?>
<p class="description">
    <?php _e('If your site is already verified in <strong>Google Search Console</strong>, you can leave this field empty.', 'wp-seopress'); ?>
</p>

<?php
}

function seopress_advanced_advanced_bing_callback() {
    $options = get_option('seopress_advanced_option_name');
    $check   = isset($options['seopress_advanced_advanced_bing']) ? $options['seopress_advanced_advanced_bing'] : null;

    printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_bing]" placeholder="' . esc_html__('Enter Bing meta value site verification', 'wp-seopress') . '" aria-label="' . __('Bing site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
); ?>
<p class="description">
    <?php _e('If your site is already verified in <strong>Bing Webmaster tools</strong>, you can leave this field empty.', 'wp-seopress'); ?>
</p>

<?php
}

function seopress_advanced_advanced_pinterest_callback() {
    $options = get_option('seopress_advanced_option_name');
    $check   = isset($options['seopress_advanced_advanced_pinterest']) ? $options['seopress_advanced_advanced_pinterest'] : null;

    printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_pinterest]" placeholder="' . esc_html__('Enter Pinterest meta value site verification', 'wp-seopress') . '" aria-label="' . __('Pinterest site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
);
}

function seopress_advanced_advanced_yandex_callback() {
    $options = get_option('seopress_advanced_option_name');
    $check   = isset($options['seopress_advanced_advanced_yandex']) ? $options['seopress_advanced_advanced_yandex'] : null;

    printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_yandex]" aria-label="' . __('Yandex site verification', 'wp-seopress') . '" placeholder="' . esc_html__('Enter Yandex meta value site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
);
}

function seopress_advanced_appearance_adminbar_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_adminbar']); ?>

<label for="seopress_advanced_appearance_adminbar">
    <input id="seopress_advanced_appearance_adminbar"
        name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove SEO from Admin Bar in backend and frontend', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_adminbar'])) {
        esc_attr($options['seopress_advanced_appearance_adminbar']);
    }
}

function seopress_advanced_appearance_universal_metabox_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_universal_metabox']); ?>

<label for="seopress_advanced_appearance_universal_metabox">
    <input id="seopress_advanced_appearance_universal_metabox"
        name="seopress_advanced_option_name[seopress_advanced_appearance_universal_metabox]"
        type="checkbox"
        <?php checked($check, "1"); ?>
        value="1"/>

    <?php _e('Enable the universal SEO metabox for the Block Editor (Gutenberg)', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_adminbar'])) {
        esc_attr($options['seopress_advanced_appearance_adminbar']);
    }
}

function seopress_advanced_appearance_universal_metabox_disable_callback() {
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
    $options = get_option('seopress_advanced_option_name');

    if(!$options){
        $check = "1";
    }
    else{
        $check = isset($options['seopress_advanced_appearance_universal_metabox_disable']) && $options['seopress_advanced_appearance_universal_metabox_disable'] === '1' ? true : false;
    }


    ?>

<label for="seopress_advanced_appearance_universal_metabox_disable">
    <input id="seopress_advanced_appearance_universal_metabox_disable"
        name="seopress_advanced_option_name[seopress_advanced_appearance_universal_metabox_disable]"
        type="checkbox"
        <?php checked($check, "1"); ?>
        value="1"/>

    <?php _e('Disable the universal SEO metabox', 'wp-seopress'); ?>
</label>

<p class="description">
    <a class="seopress-help" href="<?php echo $docs['universal']['introduction']; ?>" target="_blank">
        <?php _e('Learn more about how we interface with all page builders to optimize your productivity','wp-seopress'); ?>
        <span class="seopress-help dashicons dashicons-external"></span>
    </a>
</p>

<?php if (isset($options['seopress_advanced_appearance_adminbar'])) {
        esc_attr($options['seopress_advanced_appearance_adminbar']);
    }
}

function seopress_advanced_appearance_adminbar_noindex_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_adminbar_noindex']); ?>

<label for="seopress_advanced_appearance_adminbar_noindex">
    <input id="seopress_advanced_appearance_adminbar_noindex"
        name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar_noindex]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove noindex item from Admin Bar in backend and frontend', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_adminbar_noindex'])) {
        esc_attr($options['seopress_advanced_appearance_adminbar_noindex']);
    }
}

function seopress_advanced_appearance_metaboxe_position_callback() {
    $options = get_option('seopress_advanced_option_name');

    $selected = isset($options['seopress_advanced_appearance_metaboxe_position']) ? $options['seopress_advanced_appearance_metaboxe_position'] : null; ?>

<select id="seopress_advanced_appearance_metaboxe_position"
    name="seopress_advanced_option_name[seopress_advanced_appearance_metaboxe_position]">
    <option <?php if ('high' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="high"><?php _e('High priority (top)', 'wp-seopress'); ?>
    </option>
    <option <?php if ('default' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="default"><?php _e('Normal priority (default)', 'wp-seopress'); ?>
    </option>
    <option <?php if ('low' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="low"><?php _e('Low priority', 'wp-seopress'); ?>
    </option>
</select>

<?php if (isset($options['seopress_advanced_appearance_metaboxe_position'])) {
        esc_attr($options['seopress_advanced_appearance_metaboxe_position']);
    }
}

function seopress_advanced_appearance_schema_default_tab_callback() {
    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        $options = get_option('seopress_advanced_option_name');

        $selected = isset($options['seopress_advanced_appearance_schema_default_tab']) ? $options['seopress_advanced_appearance_schema_default_tab'] : null; ?>

<select id="seopress_advanced_appearance_schema_default_tab"
    name="seopress_advanced_option_name[seopress_advanced_appearance_schema_default_tab]">
    <option <?php if ('automatic' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="automatic"><?php _e('Automatic tab (default)', 'wp-seopress'); ?>
    </option>
    <option <?php if ('manual' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="manual"><?php _e('Manual tab', 'wp-seopress'); ?>
    </option>
</select>

<?php if (isset($options['seopress_advanced_appearance_schema_default_tab'])) {
            esc_attr($options['seopress_advanced_appearance_schema_default_tab']);
        }
    }
}

function seopress_advanced_appearance_notifications_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_notifications']); ?>

<label for="seopress_advanced_appearance_notifications">
    <input id="seopress_advanced_appearance_notifications"
        name="seopress_advanced_option_name[seopress_advanced_appearance_notifications]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Hide Notifications Center in SEO Dashboard page', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_notifications'])) {
        esc_attr($options['seopress_advanced_appearance_notifications']);
    }
}

function seopress_advanced_appearance_news_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_news']); ?>

<label for="seopress_advanced_appearance_news">
    <input id="seopress_advanced_appearance_news"
        name="seopress_advanced_option_name[seopress_advanced_appearance_news]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Hide SEO News in SEO Dashboard page', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_news'])) {
        esc_attr($options['seopress_advanced_appearance_news']);
    }
}

function seopress_advanced_appearance_seo_tools_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_seo_tools']); ?>

<label for="seopress_advanced_appearance_seo_tools">
    <input id="seopress_advanced_appearance_seo_tools"
        name="seopress_advanced_option_name[seopress_advanced_appearance_seo_tools]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Hide Site Overview in SEO Dashboard page', 'wp-seopress'); ?></label>

<?php if (isset($options['seopress_advanced_appearance_seo_tools'])) {
        esc_attr($options['seopress_advanced_appearance_seo_tools']);
    }
}

function seopress_advanced_appearance_title_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_title_col']); ?>

<label for="seopress_advanced_appearance_title_col">
    <input id="seopress_advanced_appearance_title_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_title_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Add title column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_title_col'])) {
        esc_attr($options['seopress_advanced_appearance_title_col']);
    }
}

function seopress_advanced_appearance_meta_desc_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_meta_desc_col']); ?>

<label for="seopress_advanced_appearance_meta_desc_col">
    <input id="seopress_advanced_appearance_meta_desc_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_meta_desc_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Add meta description column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_meta_desc_col'])) {
        esc_attr($options['seopress_advanced_appearance_meta_desc_col']);
    }
}

function seopress_advanced_appearance_redirect_enable_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_redirect_enable_col']); ?>

<label for="seopress_advanced_appearance_redirect_enable_col">
    <input id="seopress_advanced_appearance_redirect_enable_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_redirect_enable_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Add redirection enable column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_redirect_enable_col'])) {
        esc_attr($options['seopress_advanced_appearance_redirect_enable_col']);
    }
}

function seopress_advanced_appearance_redirect_url_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_redirect_url_col']); ?>

<label for="seopress_advanced_appearance_redirect_url_col">
    <input id="seopress_advanced_appearance_redirect_url_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_redirect_url_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Add redirection URL column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_redirect_url_col'])) {
        esc_attr($options['seopress_advanced_appearance_redirect_url_col']);
    }
}

function seopress_advanced_appearance_canonical_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_canonical']); ?>

<label for="seopress_advanced_appearance_canonical">
    <input id="seopress_advanced_appearance_canonical"
        name="seopress_advanced_option_name[seopress_advanced_appearance_canonical]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Add canonical URL column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_canonical'])) {
        esc_attr($options['seopress_advanced_appearance_canonical']);
    }
}

function seopress_advanced_appearance_target_kw_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_target_kw_col']); ?>

<label for="seopress_advanced_appearance_target_kw_col">
    <input id="seopress_advanced_appearance_target_kw_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_target_kw_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Add target keyword column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_target_kw_col'])) {
        esc_attr($options['seopress_advanced_appearance_target_kw_col']);
    }
}

function seopress_advanced_appearance_noindex_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_noindex_col']); ?>

<label for="seopress_advanced_appearance_noindex_col">
    <input id="seopress_advanced_appearance_noindex_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_noindex_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Display noindex status', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_noindex_col'])) {
        esc_attr($options['seopress_advanced_appearance_noindex_col']);
    }
}

function seopress_advanced_appearance_nofollow_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_nofollow_col']); ?>

<label for="seopress_advanced_appearance_nofollow_col">
    <input id="seopress_advanced_appearance_nofollow_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_nofollow_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Display nofollow status', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_nofollow_col'])) {
        esc_attr($options['seopress_advanced_appearance_nofollow_col']);
    }
}

function seopress_advanced_appearance_words_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_words_col']); ?>

<label for="seopress_advanced_appearance_words_col">
    <input id="seopress_advanced_appearance_words_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_words_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Display total number of words in content', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_words_col'])) {
        esc_attr($options['seopress_advanced_appearance_words_col']);
    }
}

function seopress_advanced_appearance_ps_col_callback() {
    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_ps_col']); ?>

<label for="seopress_advanced_appearance_ps_col">
    <input id="seopress_advanced_appearance_ps_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_ps_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Display Page Speed column to check performances', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_ps_col'])) {
            esc_attr($options['seopress_advanced_appearance_ps_col']);
        }
    }
}

function seopress_advanced_appearance_insights_col_callback() {
    if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_insights_col']); ?>
<label for="seopress_advanced_appearance_insights_col">
    <input id="seopress_advanced_appearance_insights_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_insights_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Display SEO Insights column to check rankings', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_insights_col'])) {
            esc_attr($options['seopress_advanced_appearance_insights_col']);
        }
    }
}

function seopress_advanced_appearance_score_col_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_score_col']); ?>

<label for="seopress_advanced_appearance_score_col">
    <input id="seopress_advanced_appearance_score_col"
        name="seopress_advanced_option_name[seopress_advanced_appearance_score_col]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Display Content Analysis results column ("Good" or "Should be improved")', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_score_col'])) {
        esc_attr($options['seopress_advanced_appearance_score_col']);
    }
}

function seopress_advanced_appearance_ca_metaboxe_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_ca_metaboxe']); ?>

<label for="seopress_advanced_appearance_ca_metaboxe">
    <input id="seopress_advanced_appearance_ca_metaboxe"
        name="seopress_advanced_option_name[seopress_advanced_appearance_ca_metaboxe]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove Content Analysis Metabox', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_ca_metaboxe'])) {
        esc_attr($options['seopress_advanced_appearance_ca_metaboxe']);
    }
}

function seopress_advanced_appearance_genesis_seo_metaboxe_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_genesis_seo_metaboxe']); ?>

<label for="seopress_advanced_appearance_genesis_seo_metaboxe">
    <input id="seopress_advanced_appearance_genesis_seo_metaboxe"
        name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_metaboxe]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove Genesis SEO Metabox', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_genesis_seo_metaboxe'])) {
        esc_attr($options['seopress_advanced_appearance_genesis_seo_metaboxe']);
    }
}

function seopress_advanced_appearance_genesis_seo_menu_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_genesis_seo_menu']); ?>

<label for="seopress_advanced_appearance_genesis_seo_menu">
    <input id="seopress_advanced_appearance_genesis_seo_menu"
        name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_menu]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove Genesis SEO link in WP Admin Menu', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_genesis_seo_menu'])) {
        esc_attr($options['seopress_advanced_appearance_genesis_seo_menu']);
    }
}

function seopress_advanced_appearance_advice_schema_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_appearance_advice_schema']); ?>

<label for="seopress_advanced_appearance_advice_schema">
    <input id="seopress_advanced_appearance_advice_schema"
        name="seopress_advanced_option_name[seopress_advanced_appearance_advice_schema]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Remove the advice if None schema selected', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_advice_schema'])) {
        esc_attr($options['seopress_advanced_appearance_advice_schema']);
    }
}

function seopress_advanced_security_metaboxe_role_callback() {
    $docs  = seopress_get_docs_links();

    $options = get_option('seopress_advanced_option_name');

    global $wp_roles;

    if ( ! isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    } ?>

<div class="wrap-user-roles">

    <?php foreach ($wp_roles->get_names() as $key => $value) {
        $check = isset($options['seopress_advanced_security_metaboxe_role'][$key]); ?>

    <p>

        <input
            id="seopress_advanced_security_metaboxe_role_<?php echo $key; ?>"
            name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_role][<?php echo $key; ?>]"
            type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>

        <label
            for="seopress_advanced_security_metaboxe_role_<?php echo $key; ?>">
            <strong><?php echo $value; ?></strong> (<em><?php echo translate_user_role($value,  'default'); ?>)</em>
        </label>

    </p>

    <?php if (isset($options['seopress_advanced_security_metaboxe_role'][$key])) {
            esc_attr($options['seopress_advanced_security_metaboxe_role'][$key]);
        }
    } ?>
</div>
<?php echo seopress_tooltip_link($docs['security']['metaboxe_seo'], __('Hook to filter structured data types metabox call by post type - new window', 'wp-seopress')); ?>

<?php
}

function seopress_advanced_security_metaboxe_ca_role_callback() {
    $docs    = seopress_get_docs_links();
    $options = get_option('seopress_advanced_option_name');

    global $wp_roles;

    if ( ! isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    } ?>

<div class="wrap-user-roles">

    <?php foreach ($wp_roles->get_names() as $key => $value) {
        $check = isset($options['seopress_advanced_security_metaboxe_ca_role'][$key]); ?>

    <p>
        <label
            for="seopress_advanced_security_metaboxe_ca_role_<?php echo $key; ?>">
            <input
                id="seopress_advanced_security_metaboxe_ca_role_<?php echo $key; ?>"
                name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_ca_role][<?php echo $key; ?>]"
                type="checkbox" <?php if ('1' == $check) { ?>
            checked="yes"
            <?php } ?>
            value="1"/>

            <strong><?php echo $value; ?></strong> (<em><?php echo translate_user_role($value,  'default'); ?>)</em>
        </label>
    </p>

    <?php if (isset($options['seopress_advanced_security_metaboxe_ca_role'][$key])) {
            esc_attr($options['seopress_advanced_security_metaboxe_ca_role'][$key]);
        }
    } ?>
</div>

<?php echo seopress_tooltip_link($docs['security']['metaboxe_ca'], __('Hook to filter structured data types metabox call by post type - new window', 'wp-seopress')); ?>

<?php
}
