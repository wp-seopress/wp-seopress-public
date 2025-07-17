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
    <?php esc_attr_e('Enable XML Sitemap', 'wp-seopress'); ?>
    <?php echo wp_kses_post(seopress_tooltip_link(esc_url($docs['sitemaps']['xml']), esc_attr__('Guide to enable XML Sitemaps - new window', 'wp-seopress'))); ?>
</label>


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
    <?php esc_attr_e('Enable Image Sitemap (standard images, image galleries, featured image, WooCommerce product images)', 'wp-seopress'); ?>
    <?php echo wp_kses_post(seopress_tooltip_link(esc_url($docs['sitemaps']['image']), esc_attr__('Guide to enable XML image sitemap - new window', 'wp-seopress'))); ?>
</label>


<p class="description">
    <?php esc_attr_e('Images in XML sitemaps are visible only from the source code.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_xml_sitemap_img_enable'])) {
        esc_attr($options['seopress_xml_sitemap_img_enable']);
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
    <?php esc_attr_e('Enable Author Sitemap', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php esc_attr_e('Make sure to enable author archive from SEO, titles and metas, archives tab.', 'wp-seopress'); ?>
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
    <?php esc_attr_e('Enable HTML Sitemap', 'wp-seopress'); ?>
    <?php echo wp_kses_post(seopress_tooltip_link(esc_url($docs['sitemaps']['html']), esc_attr__('Guide to enable a HTML Sitemap - new window', 'wp-seopress'))); ?>
</label>


<?php if (isset($options['seopress_xml_sitemap_html_enable'])) {
        esc_attr($options['seopress_xml_sitemap_html_enable']);
    }
}

function seopress_xml_sitemap_post_types_list_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_post_types_list']);

    $postTypes = seopress_get_service('WordPressData')->getPostTypes();

    $postTypes = array_filter($postTypes, 'is_post_type_viewable');

    $postTypes[] = get_post_type_object('attachment');

    $postTypes = apply_filters( 'seopress_sitemaps_cpt', $postTypes );

    foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
        ?>
<h3>
    <?php echo esc_html($seopress_cpt_value->labels->name); ?>
    <code>[<?php echo esc_html($seopress_cpt_value->name); ?>]</code>
</h3>

<!--List all post types-->
<div class="seopress_wrap_single_cpt">

    <?php
        $options = get_option('seopress_xml_sitemap_option_name');
        $check   = isset($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
        ?>

    <label
        for="seopress_xml_sitemap_post_types_list_include[<?php echo esc_attr($seopress_cpt_key); ?>]">
        <input
            id="seopress_xml_sitemap_post_types_list_include[<?php echo esc_attr($seopress_cpt_key); ?>]"
            name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_post_types_list][<?php echo esc_attr($seopress_cpt_key); ?>][include]"
            type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>
        <?php esc_attr_e('Include', 'wp-seopress'); ?>
    </label>

    <?php if ('attachment' == $seopress_cpt_value->name) { ?>
    <div class="seopress-notice is-warning is-inline">
        <p>
            <?php echo wp_kses_post(__('You should never include <strong>attachment</strong> post type in your sitemap. Be careful if you checked this.', 'wp-seopress')); ?>
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

    $taxonomies = seopress_get_service('WordPressData')->getTaxonomies();

    $taxonomies = array_filter($taxonomies, 'is_taxonomy_viewable');

    $taxonomies = apply_filters( 'seopress_sitemaps_tax', $taxonomies );

    foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) { ?>
<h3>
    <?php echo esc_html($seopress_tax_value->labels->name); ?>
    <code>[<?php echo esc_html($seopress_tax_value->name); ?>]</code>
</h3>

<!--List all taxonomies-->
<div class="seopress_wrap_single_tax">

    <?php $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']); ?>

    <label
        for="seopress_xml_sitemap_taxonomies_list_include[<?php echo esc_attr($seopress_tax_key); ?>]">
        <input
            id="seopress_xml_sitemap_taxonomies_list_include[<?php echo esc_attr($seopress_tax_key); ?>]"
            name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_taxonomies_list][<?php echo esc_attr($seopress_tax_key); ?>][include]"
            type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>
        <?php esc_attr_e('Include', 'wp-seopress'); ?>
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
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_mapping]" placeholder="' . esc_html__('e.g. 2, 28, 68', 'wp-seopress') . '" aria-label="' . esc_html__('Enter a post, page or custom post type ID(s) to display the sitemap', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_xml_sitemap_html_exclude_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');
    $check   = isset($options['seopress_xml_sitemap_html_exclude']) ? $options['seopress_xml_sitemap_html_exclude'] : null;

    printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_exclude]" placeholder="' . esc_html__('e.g. 13, 8, 38', 'wp-seopress') . '" aria-label="' . esc_html__('Exclude some Posts, Pages, Custom Post Types or Terms IDs', 'wp-seopress') . '" value="%s"/>',
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
        value="DESC"><?php esc_attr_e('DESC (descending order from highest to lowest values (3, 2, 1; c, b, a))', 'wp-seopress'); ?>
    </option>
    <option <?php if ('ASC' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="ASC"><?php esc_attr_e('ASC (ascending order from lowest to highest values (1, 2, 3; a, b, c))', 'wp-seopress'); ?>
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
        value="date"><?php esc_attr_e('Default (date)', 'wp-seopress'); ?>
    </option>
    <option <?php if ('title' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="title"><?php esc_attr_e('Post Title', 'wp-seopress'); ?>
    </option>
    <option <?php if ('modified' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="modified"><?php esc_attr_e('Modified date', 'wp-seopress'); ?>
    </option>
    <option <?php if ('ID' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="ID"><?php esc_attr_e('Post ID', 'wp-seopress'); ?>
    </option>
    <option <?php if ('menu_order' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="menu_order"><?php esc_attr_e('Menu order', 'wp-seopress'); ?>
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
    <?php esc_attr_e('Disable date after each post, page, post type?', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_xml_sitemap_html_date'])) {
        esc_attr($options['seopress_xml_sitemap_html_date']);
    }
}

function seopress_xml_sitemap_html_no_hierarchy_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_html_no_hierarchy']); ?>


<label for="seopress_xml_sitemap_html_no_hierarchy">
    <input id="seopress_xml_sitemap_html_no_hierarchy" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_no_hierarchy]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php esc_attr_e('Do not display posts and products by categories / product categories?', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_xml_sitemap_html_no_hierarchy'])) {
        esc_attr($options['seopress_xml_sitemap_html_no_hierarchy']);
    }
}

function seopress_xml_sitemap_html_post_type_archive_callback()
{
    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_html_post_type_archive']); ?>


<label for="seopress_xml_sitemap_html_post_type_archive">
    <input id="seopress_xml_sitemap_html_post_type_archive" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_post_type_archive]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php esc_attr_e('Do not display post type archive links', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php 
    // translator: %1$s is_archive, %2$s true
    echo wp_kses_post(sprintf(esc_attr__('Only post types registered with the %1$s argument set to %2$s will be displayed.', 'wp-seopress'), '<code>is_archive</code>', '<code>true</code>')); ?>
</p>

<?php if (isset($options['seopress_xml_sitemap_html_post_type_archive'])) {
        esc_attr($options['seopress_xml_sitemap_html_post_type_archive']);
    }
}