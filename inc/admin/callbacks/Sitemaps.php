<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_xml_sitemap_general_enable_callback()
{
    $docs  = seopress_get_docs_links();

    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_general_enable']); ?>


<label for="seopress_xml_sitemap_general_enable">
    <input id="seopress_xml_sitemap_general_enable"
        name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_general_enable]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Enable XML Sitemap', 'wp-seopress'); ?>
</label>

<?php echo seopress_tooltip_link($docs['sitemaps']['xml'], __('Guide to enable XML Sitemaps - new window', 'wp-seopress')); ?>

<?php if (isset($options['seopress_xml_sitemap_general_enable'])) {
        esc_attr($options['seopress_xml_sitemap_general_enable']);
    }
}

function seopress_xml_sitemap_img_enable_callback()
{
    $docs  = seopress_get_docs_links();

    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_img_enable']); ?>


<label for="seopress_xml_sitemap_img_enable">
    <input id="seopress_xml_sitemap_img_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_img_enable]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Enable Image Sitemaps (standard images, image galleries, featured image, WooCommerce product images)', 'wp-seopress'); ?>
</label>

<?php echo seopress_tooltip_link($docs['sitemaps']['image'], __('Guide to enable XML image sitemaps - new window', 'wp-seopress')); ?>

<p class="description">
    <?php _e('Images in XML sitemaps are visible only from the source code.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_xml_sitemap_img_enable'])) {
        esc_attr($options['seopress_xml_sitemap_img_enable']);
    }
}

function seopress_xml_sitemap_video_enable_callback()
{
    $docs  = seopress_get_docs_links();

    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_video_enable']); ?>


<label for="seopress_xml_sitemap_video_enable">
    <input id="seopress_xml_sitemap_video_enable"
        name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_video_enable]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Enable Video Sitemaps', 'wp-seopress'); ?>
</label>

<?php echo seopress_tooltip_link($docs['sitemaps']['video'], __('Guide to enable XML video sitemaps - new window', 'wp-seopress')); ?>

<?php printf('<p class="description">' . __('Your video sitemap is empty? Read our guide to learn more about <a href="%s" target="_blank">adding videos to your sitemap.</a>', 'wp-seopress') . '</p>', $docs['sitemaps']['video']); ?>

<?php if (isset($options['seopress_xml_sitemap_video_enable'])) {
            esc_attr($options['seopress_xml_sitemap_video_enable']);
        }
    }
}

function seopress_xml_sitemap_author_enable_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_author_enable']); ?>


<label for="seopress_xml_sitemap_author_enable">
    <input id="seopress_xml_sitemap_author_enable"
        name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_author_enable]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Enable Author Sitemap', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e('Make sure to enable author archive from SEO, titles and metas, archives tab.</a>', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_xml_sitemap_author_enable'])) {
        esc_attr($options['seopress_xml_sitemap_author_enable']);
    }
}

function seopress_xml_sitemap_html_enable_callback()
{
    $docs  = seopress_get_docs_links();

    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_html_enable']); ?>


<label for="seopress_xml_sitemap_html_enable">
    <input id="seopress_xml_sitemap_html_enable"
        name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_enable]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Enable HTML Sitemap', 'wp-seopress'); ?>
</label>

<?php echo seopress_tooltip_link($docs['sitemaps']['html'], __('Guide to enable a HTML Sitemap - new window', 'wp-seopress')); ?>

<?php if (isset($options['seopress_xml_sitemap_html_enable'])) {
        esc_attr($options['seopress_xml_sitemap_html_enable']);
    }
}

function seopress_xml_sitemap_post_types_list_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_post_types_list']);

    global $wp_post_types;

    $args = [
        'show_ui' => true,
        'public'  => true,
    ];

    $output       = 'objects'; // names or objects, note names is the default
    $operator     = 'and'; // 'and' or 'or'

    $post_types = get_post_types($args, $output, $operator);

    foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) { ?>
<h3>
    <?php echo $seopress_cpt_value->labels->name; ?>
    <em><small>[<?php echo $seopress_cpt_value->name; ?>]</small></em>
</h3>

<!--List all post types-->
<div class="seopress_wrap_single_cpt">

    <?php
        $options = get_option('seopress_xml_sitemap_option_name');
        $check   = isset($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
        ?>

    <label
        for="seopress_xml_sitemap_post_types_list_include[<?php echo $seopress_cpt_key; ?>]">
        <input
            id="seopress_xml_sitemap_post_types_list_include[<?php echo $seopress_cpt_key; ?>]"
            name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_post_types_list][<?php echo $seopress_cpt_key; ?>][include]"
            type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>
        <?php _e('Include', 'wp-seopress'); ?>
    </label>

    <?php if ('attachment' == $seopress_cpt_value->name) { ?>
    <div class="seopress-notice is-warning">
        <p class="description">
            <?php _e('You should never include <strong>attachment</strong> post type in your sitemap. Be careful if you checked this.', 'wp-seopress'); ?>
        </p>
    </div>
    <?php } ?>

    <?php
        if (isset($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'])) {
            esc_attr($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
        }
        ?>
</div>
<?php
    }
}

function seopress_xml_sitemap_taxonomies_list_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_taxonomies_list']);

    $args = [
        'show_ui' => true,
        'public'  => true,
    ];

    $output         = 'objects'; // or objects
    $operator       = 'and'; // 'and' or 'or'
    $taxonomies     = get_taxonomies($args, $output, $operator);

    foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) { ?>
<h3>
    <?php echo $seopress_tax_value->labels->name; ?>
    <em><small>[<?php echo $seopress_tax_value->name; ?>]</small></em>
</h3>

<!--List all taxonomies-->
<div class="seopress_wrap_single_tax">

    <?php $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']); ?>

    <label
        for="seopress_xml_sitemap_taxonomies_list_include[<?php echo $seopress_tax_key; ?>]">
        <input
            id="seopress_xml_sitemap_taxonomies_list_include[<?php echo $seopress_tax_key; ?>]"
            name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_taxonomies_list][<?php echo $seopress_tax_key; ?>][include]"
            type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>
        <?php _e('Include', 'wp-seopress'); ?>
    </label>

    <?php if (isset($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'])) {
            esc_attr($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);
        } ?>
</div>

<?php
    }
}

function seopress_xml_sitemap_html_mapping_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');
    $check   = isset($options['seopress_xml_sitemap_html_mapping']) ? $options['seopress_xml_sitemap_html_mapping'] : null;

    printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_mapping]" placeholder="' . esc_html__('eg: 2, 28, 68', 'wp-seopress') . '" aria-label="' . __('Enter a post, page or custom post type ID(s) to display the sitemap', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    ); ?>

<p>
    <?php _e('You can also use this shortcode:', 'wp-seopress'); ?>
</p>

<pre>[seopress_html_sitemap]</pre>

<p><?php _e('To include specific custom post types, use the CPT attribute:', 'wp-seopress'); ?>
</p>

<pre>[seopress_html_sitemap cpt="post,product"]</pre>

<?php
}

function seopress_xml_sitemap_html_exclude_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');
    $check   = isset($options['seopress_xml_sitemap_html_exclude']) ? $options['seopress_xml_sitemap_html_exclude'] : null;

    printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_exclude]" placeholder="' . esc_html__('eg: 13, 8, 38', 'wp-seopress') . '" aria-label="' . __('Exclude some Posts, Pages, Custom Post Types or Terms IDs', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_xml_sitemap_html_order_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $selected = isset($options['seopress_xml_sitemap_html_order']) ? $options['seopress_xml_sitemap_html_order'] : null; ?>

<select id="seopress_xml_sitemap_html_order" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_order]">
    <option <?php if ('DESC' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="DESC"><?php _e('DESC (descending order from highest to lowest values (3, 2, 1; c, b, a))', 'wp-seopress'); ?>
    </option>
    <option <?php if ('ASC' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="ASC"><?php _e('ASC (ascending order from lowest to highest values (1, 2, 3; a, b, c))', 'wp-seopress'); ?>
    </option>
</select>

<?php if (isset($options['seopress_xml_sitemap_html_order'])) {
        esc_attr($options['seopress_xml_sitemap_html_order']);
    }
}

function seopress_xml_sitemap_html_orderby_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $selected = isset($options['seopress_xml_sitemap_html_orderby']) ? $options['seopress_xml_sitemap_html_orderby'] : null; ?>

<select id="seopress_xml_sitemap_html_orderby"
    name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_orderby]">
    <option <?php if ('date' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="date"><?php _e('Default (date)', 'wp-seopress'); ?>
    </option>
    <option <?php if ('title' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="title"><?php _e('Post Title', 'wp-seopress'); ?>
    </option>
    <option <?php if ('modified' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="modified"><?php _e('Modified date', 'wp-seopress'); ?>
    </option>
    <option <?php if ('ID' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="ID"><?php _e('Post ID', 'wp-seopress'); ?>
    </option>
    <option <?php if ('menu_order' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="menu_order"><?php _e('Menu order', 'wp-seopress'); ?>
    </option>
</select>

<?php if (isset($options['seopress_xml_sitemap_html_orderby'])) {
        esc_attr($options['seopress_xml_sitemap_html_orderby']);
    }
}

function seopress_xml_sitemap_html_date_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_html_date']); ?>


<label for="seopress_xml_sitemap_html_date">
    <input id="seopress_xml_sitemap_html_date" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_date]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Disable date after each post, page, post type?', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_xml_sitemap_html_date'])) {
        esc_attr($options['seopress_xml_sitemap_html_date']);
    }
}

function seopress_xml_sitemap_html_archive_links_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_html_archive_links']); ?>


<label for="seopress_xml_sitemap_html_archive_links">
    <input id="seopress_xml_sitemap_html_archive_links"
        name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_archive_links]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Remove links from archive pages (eg: Products)', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_xml_sitemap_html_archive_links'])) {
        esc_attr($options['seopress_xml_sitemap_html_archive_links']);
    }
}
