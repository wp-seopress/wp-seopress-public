<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Titles & metas
function seopress_titles_sep_callback()
{
    $options = get_option('seopress_titles_option_name');
    $check   = isset($options['seopress_titles_sep']) ? $options['seopress_titles_sep'] : null; ?>

<input type="text" id="seopress_titles_sep" name="seopress_titles_option_name[seopress_titles_sep]"
    placeholder="<?php esc_html_e('Enter your separator, eg: "-"', 'wp-seopress'); ?>"
    aria-label="<?php _e('Separator', 'wp-seopress'); ?>"
    value="<?php esc_html_e($check); ?>" />

<p class="description">
    <?php _e('Use this separator with %%sep%% in your title and meta description.', 'wp-seopress'); ?>
</p>

<?php
}

function seopress_titles_home_site_title_callback()
{
    $options = get_option('seopress_titles_option_name');
    $check   = isset($options['seopress_titles_home_site_title']) ? $options['seopress_titles_home_site_title'] : null; ?>

<input type="text" id="seopress_titles_home_site_title"
    name="seopress_titles_option_name[seopress_titles_home_site_title]"
    placeholder="<?php esc_html_e('My awesome website', 'wp-seopress'); ?>"
    aria-label="<?php _e('Site title', 'wp-seopress'); ?>"
    value="<?php esc_html_e($check); ?>" />

<div class="wrap-tags">
    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-title" data-tag="%%sitetitle%%">
        <span class="dashicons dashicons-plus-alt2"></span>
        <?php _e('Site Title', 'wp-seopress'); ?>
    </button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-sep" data-tag="%%sep%%">
        <span class="dashicons dashicons-plus-alt2"></span>
        <?php _e('Separator', 'wp-seopress'); ?>
    </button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-desc" data-tag="%%tagline%%">
        <span class="dashicons dashicons-plus-alt2"></span>
        <?php _e('Tagline', 'wp-seopress'); ?>
    </button>

    <?php echo seopress_render_dyn_variables('tag-title');
}

function seopress_titles_home_site_desc_callback()
{
    $options = get_option('seopress_titles_option_name');
    $check   = isset($options['seopress_titles_home_site_desc']) ? $options['seopress_titles_home_site_desc'] : null; ?>

    <textarea id="seopress_titles_home_site_desc" name="seopress_titles_option_name[seopress_titles_home_site_desc]"
        placeholder="<?php esc_html_e('This is a cool website about Wookiees', 'wp-seopress'); ?>"
        aria-label="<?php _e('Meta description', 'wp-seopress'); ?>"><?php esc_html_e($check); ?></textarea>

    <div class="wrap-tags">
        <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-meta-desc" data-tag="%%tagline%%">
            <span class="dashicons dashicons-plus-alt2"></span>
            <?php _e('Tagline', 'wp-seopress'); ?>
        </button>

        <?php echo seopress_render_dyn_variables('tag-description');

    if (get_option('page_for_posts')) { ?>
        <p>
            <a
                href="<?php echo admin_url('post.php?post=' . get_option('page_for_posts') . '&action=edit'); ?>">
                <?php _e('Looking to edit your blog page?', 'wp-seopress'); ?>
            </a>
        </p>
        <?php }
}

//Single CPT
function seopress_titles_single_titles_callback()
{
    echo seopress_get_empty_templates('cpt', 'title');
    echo seopress_get_empty_templates('cpt', 'description');

    $docs = seopress_get_docs_links();

    $postTypes = seopress_get_service('WordPressData')->getPostTypes();
    foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
        ?>
        <h3>
            <?php echo $seopress_cpt_value->labels->name; ?>
            <em>
                <small>[<?php echo $seopress_cpt_value->name; ?>]</small>
            </em>
            <!--Single on/off CPT-->
            <div class="seopress_wrap_single_cpt">

                <?php $options = get_option('seopress_titles_option_name');
        $check             = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['enable']) ? $options['seopress_titles_single_titles'][$seopress_cpt_key]['enable'] : null; ?>

                <input
                    id="seopress_titles_single_cpt_enable[<?php echo $seopress_cpt_key; ?>]"
                    data-id=<?php echo $seopress_cpt_key; ?>
                name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo $seopress_cpt_key; ?>][enable]" class="toggle"
                type="checkbox"
                <?php if ('1' == $check) { ?>
                checked="yes" data-toggle="0"
                <?php } else { ?>
                data-toggle="1"
                <?php } ?>
                value="1"/>

                <label
                    for="seopress_titles_single_cpt_enable[<?php echo $seopress_cpt_key; ?>]">
                    <?php _e('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress'); ?>
                </label>

                <?php if ('1' == $check) { ?>
                <span id="titles-state-default" class="feature-state">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e('Click to display any SEO metaboxes / columns for this post type', 'wp-seopress'); ?>
                </span>
                <span id="titles-state" class="feature-state feature-state-off">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress'); ?>
                </span>
                <?php } else { ?>
                <span id="titles-state-default" class="feature-state">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress'); ?>
                </span>
                <span id="titles-state" class="feature-state feature-state-off">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e('Click to display any SEO metaboxes / columns for this post type', 'wp-seopress'); ?>
                </span>
                <?php }

        $toggle_txt_on  = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes / columns for this post type', 'wp-seopress');
        $toggle_txt_off = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress'); ?>
                <script>
                    jQuery(document).ready(function($) {
                        $('input[data-id=<?php echo $seopress_cpt_key; ?>]')
                            .on('click', function() {
                                $(this).attr('data-toggle', $(this).attr('data-toggle') == '1' ? '0' : '1');
                                if ($(this).attr('data-toggle') == '1') {
                                    $(this).next().next('.feature-state').html(
                                        '<?php echo $toggle_txt_off; ?>'
                                    );
                                } else {
                                    $(this).next().next('.feature-state').html(
                                        '<?php echo $toggle_txt_on; ?>'
                                    );
                                }
                            });
                    });
                </script>

                <?php if (isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['enable'])) {
            esc_attr($options['seopress_titles_single_titles'][$seopress_cpt_key]['enable']);
        } ?>

            </div>
        </h3>


        <!--Single Title CPT-->
        <div class="seopress_wrap_single_cpt">
            <p>
                <?php _e('Title template', 'wp-seopress'); ?>
            </p>

            <?php
         $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['title']) ? $options['seopress_titles_single_titles'][$seopress_cpt_key]['title'] : null; ?>
            <script>
                jQuery(document).ready(function($) {
                    $('#seopress-tag-single-title-<?php echo $seopress_cpt_key; ?>')
                        .click(function() {
                            $('#seopress_titles_single_titles_<?php echo $seopress_cpt_key; ?>')
                                .val(sp_get_field_length($(
                                    '#seopress_titles_single_titles_<?php echo $seopress_cpt_key; ?>'
                                )) + $(
                                    '#seopress-tag-single-title-<?php echo $seopress_cpt_key; ?>'
                                ).attr('data-tag'));
                        });
                    $('#seopress-tag-sep-<?php echo $seopress_cpt_key; ?>')
                        .click(function() {
                            $('#seopress_titles_single_titles_<?php echo $seopress_cpt_key; ?>')
                                .val(sp_get_field_length($(
                                    '#seopress_titles_single_titles_<?php echo $seopress_cpt_key; ?>'
                                )) + $(
                                    '#seopress-tag-sep-<?php echo $seopress_cpt_key; ?>'
                                ).attr('data-tag'));
                        });
                    $('#seopress-tag-single-sitetitle-<?php echo $seopress_cpt_key; ?>')
                        .click(function() {
                            $('#seopress_titles_single_titles_<?php echo $seopress_cpt_key; ?>')
                                .val(sp_get_field_length($(
                                    '#seopress_titles_single_titles_<?php echo $seopress_cpt_key; ?>'
                                )) + $(
                                    '#seopress-tag-single-sitetitle-<?php echo $seopress_cpt_key; ?>'
                                ).attr('data-tag'));
                        });
                });
            </script>

            <?php printf(
            '<input type="text" id="seopress_titles_single_titles_' . $seopress_cpt_key . '" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][title]" value="%s"/>',
            esc_html($check)
        ); ?>

            <div class="wrap-tags">
                <button type="button" class="btn btnSecondary tag-title"
                    id="seopress-tag-single-title-<?php echo $seopress_cpt_key; ?>"
                    data-tag="%%post_title%%">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Post Title', 'wp-seopress'); ?>
                </button>

                <button type="button" class="btn btnSecondary tag-title"
                    id="seopress-tag-sep-<?php echo $seopress_cpt_key; ?>"
                    data-tag="%%sep%%">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Separator', 'wp-seopress'); ?>
                </button>

                <button type="button" class="btn btnSecondary tag-title"
                    id="seopress-tag-single-sitetitle-<?php echo $seopress_cpt_key; ?>"
                    data-tag="%%sitetitle%%">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Site Title', 'wp-seopress'); ?>
                </button>

                <?php
            echo seopress_render_dyn_variables('tag-title'); ?>
            </div>

            <!--Single Meta Description CPT-->
            <div class="seopress_wrap_single_cpt">
                <p>
                    <?php _e('Meta description template', 'wp-seopress'); ?>
                </p>

                <?php
        $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['description']) ? $options['seopress_titles_single_titles'][$seopress_cpt_key]['description'] : null; ?>

                <script>
                    jQuery(document).ready(function($) {
                        $('#seopress-tag-single-desc-<?php echo $seopress_cpt_key; ?>')
                            .click(function() {
                                $('#seopress_titles_single_desc_<?php echo $seopress_cpt_key; ?>')
                                    .val(sp_get_field_length($(
                                        '#seopress_titles_single_desc_<?php echo $seopress_cpt_key; ?>'
                                    )) + $(
                                        '#seopress-tag-single-desc-<?php echo $seopress_cpt_key; ?>'
                                    ).attr('data-tag'));
                            });
                    });
                </script>

                <?php printf(
            '<textarea id="seopress_titles_single_desc_' . $seopress_cpt_key . '" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][description]">%s</textarea>',
            esc_html($check)
        ); ?>
                <div class="wrap-tags">
                    <button type="button" class="btn btnSecondary tag-title"
                        id="seopress-tag-single-desc-<?php echo $seopress_cpt_key; ?>"
                        data-tag="%%post_excerpt%%">
                        <span class="dashicons dashicons-plus-alt2"></span>
                        <?php _e('Post excerpt', 'wp-seopress'); ?>
                    </button>
                    <?php
            echo seopress_render_dyn_variables('tag-description'); ?>
                </div>
            </div>

            <!--Single No-Index CPT-->
            <div class="seopress_wrap_single_cpt">

                <?php
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']); ?>

                <label
                    for="seopress_titles_single_cpt_noindex[<?php echo $seopress_cpt_key; ?>]">
                    <input
                        id="seopress_titles_single_cpt_noindex[<?php echo $seopress_cpt_key; ?>]"
                        name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo $seopress_cpt_key; ?>][noindex]"
                        type="checkbox" <?php if ('1' == $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>

                    <?php _e('Do not display this single post type in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                </label>

                <?php $cpt_in_sitemap = seopress_get_service('SitemapOption')->getPostTypesList();

        if ('1' == $check && isset($cpt_in_sitemap[$seopress_cpt_key]) && '1' === $cpt_in_sitemap[$seopress_cpt_key]['include']) { ?>
                <div class="seopress-notice is-error">
                    <p>
                        <?php _e('This custom post type is <strong>NOT</strong> excluded from your XML sitemaps despite the fact that it is set to <strong>NOINDEX</strong>. We recommend that you check this out.', 'wp-seopress'); ?>
                    </p>
                </div>
                <?php }

        if (isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
            esc_attr($options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);
        } ?>

            </div>

            <!--Single No-Follow CPT-->
            <div class="seopress_wrap_single_cpt">

                <?php
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']); ?>

                <label
                    for="seopress_titles_single_cpt_nofollow[<?php echo $seopress_cpt_key; ?>]">
                    <input
                        id="seopress_titles_single_cpt_nofollow[<?php echo $seopress_cpt_key; ?>]"
                        name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo $seopress_cpt_key; ?>][nofollow]"
                        type="checkbox" <?php if ('1' == $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>

                    <?php _e('Do not follow links for this single post type <strong>(nofollow)</strong>', 'wp-seopress'); ?>
                </label>

                <?php if (isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow'])) {
            esc_attr($options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);
        } ?>

            </div>

            <!--Single Published / modified date CPT-->
            <div class="seopress_wrap_single_cpt">

                <?php $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['date']); ?>

                <label
                    for="seopress_titles_single_cpt_date[<?php echo $seopress_cpt_key; ?>]">
                    <input
                        id="seopress_titles_single_cpt_date[<?php echo $seopress_cpt_key; ?>]"
                        name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo $seopress_cpt_key; ?>][date]"
                        type="checkbox" <?php if ('1' == $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>

                    <?php _e('Display date in Google search results by adding <code>article:published_time</code> and <code>article:modified_time</code> meta?', 'wp-seopress'); ?>
                </label>

                <p class="description">
                    <?php _e('Unchecking this doesn\'t prevent Google to display post date in search results.', 'wp-seopress'); ?>
                </p>

                <?php if (isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['date'])) {
            esc_attr($options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);
        } ?>

            </div>

            <!--Single meta thumbnail CPT-->
            <div class="seopress_wrap_single_cpt">

                <?php $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['thumb_gcs']); ?>

                <label
                    for="seopress_titles_single_cpt_thumb_gcs[<?php echo $seopress_cpt_key; ?>]">
                    <input
                        id="seopress_titles_single_cpt_thumb_gcs[<?php echo $seopress_cpt_key; ?>]"
                        name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo $seopress_cpt_key; ?>][thumb_gcs]"
                        type="checkbox" <?php if ('1' == $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>

                    <?php _e('Display post thumbnail in Google Custom Search results?', 'wp-seopress'); ?>
                </label>

                <p class="description">
                    <?php printf(__('This option does not apply to traditional search results. <a href="%s" target="_blank">Learn more</a>', 'wp-seopress'), $docs['titles']['thumbnail']); ?><span
                        class="dashicons dashicons-external"></span>
                </p>

                <?php if (isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['thumb_gcs'])) {
            esc_attr($options['seopress_titles_single_titles'][$seopress_cpt_key]['thumb_gcs']);
        } ?>

            </div>
            <?php
        if (empty($options['seopress_titles_single_titles'][$seopress_cpt_key]['title'])) {
            $t[] = $seopress_cpt_key;
        }
    }
}

//BuddyPress Groups
function seopress_titles_bp_groups_title_callback()
{
    if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
        $options = get_option('seopress_titles_option_name'); ?>
            <h3>
                <?php _e('BuddyPress groups', 'wp-seopress'); ?>
            </h3>

            <p>
                <?php _e('Title template', 'wp-seopress'); ?>
            </p>

            <?php $check = isset($options['seopress_titles_bp_groups_title']) ? $options['seopress_titles_bp_groups_title'] : null; ?>

            <input id="seopress_titles_bp_groups_title" type="text"
                name="seopress_titles_option_name[seopress_titles_bp_groups_title]"
                value="<?php esc_html_e($check); ?>" />

            <div class="wrap-tags">
                <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-post-title-bd-groups" data-tag="%%post_title%%">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Post Title', 'wp-seopress'); ?>
                </button>
                <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-sep-bd-groups" data-tag="%%sep%%">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Separator', 'wp-seopress'); ?>
                </button>

                <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-title-bd-groups" data-tag="%%sitetitle%%">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Site Title', 'wp-seopress'); ?>
                </button>

                <?php
        echo seopress_render_dyn_variables('tag-title');
    }
}

function seopress_titles_bp_groups_desc_callback()
{
    if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
        $options = get_option('seopress_titles_option_name'); ?>
                <p>
                    <?php _e('Meta description template', 'wp-seopress'); ?>
                </p>

                <?php $check = isset($options['seopress_titles_bp_groups_desc']) ? $options['seopress_titles_bp_groups_desc'] : null; ?>

                <textarea
                    name="seopress_titles_option_name[seopress_titles_bp_groups_desc]"><?php esc_html_e($check); ?></textarea>

                <?php
    }
}

function seopress_titles_bp_groups_noindex_callback()
{
    if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_bp_groups_noindex']); ?>

                <label for="seopress_titles_bp_groups_noindex">
                    <input id="seopress_titles_bp_groups_noindex"
                        name="seopress_titles_option_name[seopress_titles_bp_groups_noindex]" type="checkbox" <?php if ('1' == $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>

                    <?php _e('Do not display BuddyPress groups in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                </label>

                <?php if (isset($options['seopress_titles_bp_groups_noindex'])) {
            esc_attr($options['seopress_titles_bp_groups_noindex']);
        }
    }
}

//Taxonomies
function seopress_titles_tax_titles_callback()
{
    echo seopress_get_empty_templates('tax', 'title');
    echo seopress_get_empty_templates('tax', 'description');

    foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) { ?>
                <h3>
                    <?php echo $seopress_tax_value->labels->name; ?>
                    <em>
                        <small>[<?php echo $seopress_tax_value->name; ?>]</small>
                    </em>
                </h3>

                <!--Single on/off Tax-->
                <div class="seopress_wrap_tax">
                    <?php
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['enable']) ? $options['seopress_titles_tax_titles'][$seopress_tax_key]['enable'] : null;
        ?>
                    <input
                        id="seopress_titles_tax_titles_enable[<?php echo $seopress_tax_key; ?>]"
                        data-id=<?php echo $seopress_tax_key; ?>
                    name="seopress_titles_option_name[seopress_titles_tax_titles][<?php echo $seopress_tax_key; ?>][enable]"
                    class="toggle" type="checkbox"
                    <?php if ('1' == $check) { ?>
                    checked="yes" data-toggle="0"
                    <?php } else { ?>
                    data-toggle="1"
                    <?php } ?>
                    value="1"/>

                    <label
                        for="seopress_titles_tax_titles_enable[<?php echo $seopress_tax_key; ?>]">
                        <?php _e('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress'); ?>
                    </label>

                    <?php
        if ('1' == $check) { ?>
                    <span id="titles-state-default" class="feature-state">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Click to display any SEO metaboxes for this taxonomy', 'wp-seopress'); ?>
                    </span>
                    <span id="titles-state" class="feature-state feature-state-off">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress'); ?>
                    </span>
                    <?php } else { ?>
                    <span id="titles-state-default" class="feature-state">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress'); ?>
                    </span>
                    <span id="titles-state" class="feature-state feature-state-off">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Click to display any SEO metaboxes for this taxonomy', 'wp-seopress'); ?>
                    </span>
                    <?php }

        $toggle_txt_on  = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes for this taxonomy', 'wp-seopress');
        $toggle_txt_off = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress');
?>
                    <script>
                        jQuery(document).ready(function($) {
                            $(' input[data-id=<?php echo $seopress_tax_key; ?>]')
                                .on('click',
                                    function() {
                                        $(this).attr('data-toggle', $(this).attr('data-toggle') == '1' ? '0' :
                                            '1');
                                        if ($(this).attr('data-toggle') == '1') {
                                            $(this).next().next('.feature-state').html(
                                                '<?php echo $toggle_txt_off; ?>'
                                            );
                                        } else {
                                            $(this).next().next('.feature-state').html(
                                                '<?php echo $toggle_txt_on; ?>'
                                            );
                                        }
                                    });
                        });
                    </script>

                    <?php if (isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['enable'])) {
    esc_attr($options['seopress_titles_tax_titles'][$seopress_tax_key]['enable']);
} ?>

                </div>

                <!--Tax Title-->
                <?php
            $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['title']) ? $options['seopress_titles_tax_titles'][$seopress_tax_key]['title'] : null;
        ?>

                <div class="seopress_wrap_tax">
                    <p>
                        <?php _e('Title template', 'wp-seopress'); ?>
                    </p>

                    <script>
                        jQuery(document).ready(function($) {
                            $(' #seopress-tag-tax-title-<?php echo $seopress_tax_key; ?>')
                                .click(function() {
                                    $('#seopress_titles_tax_titles_<?php echo $seopress_tax_key; ?>')
                                        .val(sp_get_field_length($(
                                            '#seopress_titles_tax_titles_<?php echo $seopress_tax_key; ?>'
                                        )) + $(
                                            '#seopress-tag-tax-title-<?php echo $seopress_tax_key; ?>'
                                        ).attr('data-tag'));
                                });
                            $('#seopress-tag-sep-<?php echo $seopress_tax_key; ?>')
                                .click(function() {
                                    $('#seopress_titles_tax_titles_<?php echo $seopress_tax_key; ?>')
                                        .val(sp_get_field_length($(
                                            '#seopress_titles_tax_titles_<?php echo $seopress_tax_key; ?>'
                                        )) + $(
                                            '#seopress-tag-sep-<?php echo $seopress_tax_key; ?>'
                                        ).attr('data-tag'));
                                });
                            $('#seopress-tag-tax-sitetitle-<?php echo $seopress_tax_key; ?>')
                                .click(function() {
                                    $('#seopress_titles_tax_titles_<?php echo $seopress_tax_key; ?>')
                                        .val(sp_get_field_length($(
                                            '#seopress_titles_tax_titles_<?php echo $seopress_tax_key; ?>'
                                        )) + $(
                                            '#seopress-tag-tax-sitetitle-<?php echo $seopress_tax_key; ?>'
                                        ).attr('data-tag'));
                                });
                        });
                    </script>

                    <?php printf(
            '<input type="text" id="seopress_titles_tax_titles_' . $seopress_tax_key . '" name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][title]" value="%s"/>',
            esc_html($check)
        );

        if ('category' == $seopress_tax_key) { ?>
                    <div class=" wrap-tags">
                        <span
                            id="seopress-tag-tax-title-<?php echo $seopress_tax_key; ?>"
                            data-tag="%%_category_title%%" class="btn btnSecondary tag-title">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            <?php _e('Category Title', 'wp-seopress'); ?>
                        </span>
                        <?php } elseif ('post_tag' == $seopress_tax_key) { ?>
                        <div class="wrap-tags">
                            <button type="button" class="btn btnSecondary tag-title"
                                id="seopress-tag-tax-title-<?php echo $seopress_tax_key; ?>"
                                data-tag="%%tag_title%%">
                                <span class="dashicons dashicons-plus-alt2"></span>
                                <?php _e('Tag Title', 'wp-seopress'); ?>
                            </button>
                            <?php } else { ?>
                            <div class="wrap-tags">
                                <button type="button" class="btn btnSecondary tag-title"
                                    id="seopress-tag-tax-title-<?php echo $seopress_tax_key; ?>"
                                    data-tag="%%term_title%%">
                                    <span class="dashicons dashicons-plus-alt2"></span>
                                    <?php _e('Term Title', 'wp-seopress'); ?>
                                </button>
                                <?php } ?>

                                <button type="button" class="btn btnSecondary tag-title"
                                    id="seopress-tag-sep-<?php echo $seopress_tax_key; ?>"
                                    data-tag="%%sep%%">
                                    <span class="dashicons dashicons-plus-alt2"></span>
                                    <?php _e('Separator', 'wp-seopress'); ?>
                                </button>

                                <button type="button" class="btn btnSecondary tag-title"
                                    id="seopress-tag-tax-sitetitle-<?php echo $seopress_tax_key; ?>"
                                    data-tag="%%sitetitle%%">
                                    <span class="dashicons dashicons-plus-alt2"></span>
                                    <?php _e('Site Title', 'wp-seopress'); ?>
                                </button>

                                <?php echo seopress_render_dyn_variables('tag-title'); ?>
                            </div>

                            <!--Tax Meta Description-->
                            <div class="seopress_wrap_tax">
                                <?php $check2 = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['description']) ? $options['seopress_titles_tax_titles'][$seopress_tax_key]['description'] : null; ?>

                                <p>
                                    <?php _e('Meta description template', 'wp-seopress'); ?>
                                </p>

                                <script>
                                    jQuery(document).ready(function($) {
                                        $('#seopress-tag-tax-desc-<?php echo $seopress_tax_key; ?>')
                                            .click(function() {
                                                $('#seopress_titles_tax_desc_<?php echo $seopress_tax_key; ?>')
                                                    .val(
                                                        sp_get_field_length($(
                                                            '#seopress_titles_tax_desc_<?php echo $seopress_tax_key; ?>'
                                                        )) + $(
                                                            '#seopress-tag-tax-desc-<?php echo $seopress_tax_key; ?>'
                                                        )
                                                        .attr('data-tag'));
                                            });
                                    });
                                </script>

                                <?php
        printf(
            '<textarea id="seopress_titles_tax_desc_' . $seopress_tax_key . '" name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][description]">%s</textarea>',
            esc_html($check2)
        );
?>
                                <?php if ('category' == $seopress_tax_key) { ?>
                                <div class="wrap-tags">
                                    <button type="button" class="btn btnSecondary tag-title"
                                        id="seopress-tag-tax-desc-<?php echo $seopress_tax_key; ?>"
                                        data-tag="%%_category_description%%">
                                        <span class="dashicons dashicons-plus-alt2"></span>
                                        <?php _e('Category Description', 'wp-seopress'); ?>
                                    </button>
                                    <?php } elseif ('post_tag' == $seopress_tax_key) { ?>
                                    <div class="wrap-tags">
                                        <button type="button" class="btn btnSecondary tag-title"
                                            id="seopress-tag-tax-desc-<?php echo $seopress_tax_key; ?>"
                                            data-tag="%%tag_description%%">
                                            <span class="dashicons dashicons-plus-alt2"></span>
                                            <?php _e('Tag Description', 'wp-seopress'); ?>
                                        </button>
                                        <?php } else { ?>
                                        <div class="wrap-tags">
                                            <button type="button" class="btn btnSecondary tag-title"
                                                id="seopress-tag-tax-desc-<?php echo $seopress_tax_key; ?>"
                                                data-tag="%%term_description%%">
                                                <span class="dashicons dashicons-plus-alt2"></span>
                                                <?php _e('Term Description', 'wp-seopress'); ?>
                                            </button>
                                            <?php } echo seopress_render_dyn_variables('tag-description'); ?>
                                        </div>

                                        <!--Tax No-Index-->
                                        <div class="seopress_wrap_tax">

                                            <?php $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']); ?>


                                            <label
                                                for="seopress_titles_tax_noindex[<?php echo $seopress_tax_key; ?>]">
                                                <input
                                                    id="seopress_titles_tax_noindex[<?php echo $seopress_tax_key; ?>]"
                                                    name="seopress_titles_option_name[seopress_titles_tax_titles][<?php echo $seopress_tax_key; ?>][noindex]"
                                                    type="checkbox" <?php if ('1' == $check) { ?>
                                                checked="yes"
                                                <?php } ?>
                                                value="1"/>
                                                <?php _e('Do not display this taxonomy archive in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                            </label>

                                            <?php $tax_in_sitemap = seopress_get_service('SitemapOption')->getTaxonomiesList();

        if ('1' == $check && isset($tax_in_sitemap[$seopress_tax_key]) && '1' === $tax_in_sitemap[$seopress_tax_key]['include']) { ?>
                                            <div class="seopress-notice is-error">
                                                <p>
                                                    <?php _e('This custom taxonomy is <strong>NOT</strong> excluded from your XML sitemaps despite the fact that it is set to <strong>NOINDEX</strong>. We recommend that you check this out.', 'wp-seopress'); ?>
                                                </p>
                                            </div>
                                            <?php }

        if (isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'])) {
            esc_attr($options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);
        } ?>

                                        </div>

                                        <!--Tax No-Follow-->
                                        <div class="seopress_wrap_tax">

                                            <?php
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);
        ?>


                                            <label
                                                for="seopress_titles_tax_nofollow[<?php echo $seopress_tax_key; ?>]">
                                                <input
                                                    id="seopress_titles_tax_nofollow[<?php echo $seopress_tax_key; ?>]"
                                                    name="seopress_titles_option_name[seopress_titles_tax_titles][<?php echo $seopress_tax_key; ?>][nofollow]"
                                                    type="checkbox" <?php if ('1' == $check) { ?>
                                                checked="yes"
                                                <?php } ?>
                                                value="1"/>
                                                <?php _e('Do not follow links for this taxonomy archive <strong>(nofollow)</strong>', 'wp-seopress'); ?>
                                            </label>

                                            <?php if (isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow'])) {
            esc_attr($options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);
        } ?>

                                        </div>
                                        <?php
    }
}

//Archives
function seopress_titles_archives_titles_callback()
{
    $options = get_option('seopress_titles_option_name');

    $postTypes = seopress_get_service('WordPressData')->getPostTypes();
    foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
        if (! in_array($seopress_cpt_key, ['post', 'page'])) {
            $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['title']) ? $options['seopress_titles_archive_titles'][$seopress_cpt_key]['title'] : null; ?>
                                        <h3><?php echo $seopress_cpt_value->labels->name; ?>
                                            <em><small>[<?php echo $seopress_cpt_value->name; ?>]</small></em>

                                            <?php if (get_post_type_archive_link($seopress_cpt_value->name)) { ?>
                                            <span class="link-archive">
                                                <span class="dashicons dashicons-external"></span>
                                                <a href="<?php echo get_post_type_archive_link($seopress_cpt_value->name); ?>"
                                                    target="_blank">
                                                    <?php _e('See archive', 'wp-seopress'); ?>
                                                </a>
                                            </span>
                                            <?php } ?>
                                        </h3>

                                        <!--Archive Title CPT-->
                                        <div class="seopress_wrap_archive_cpt">
                                            <p>
                                                <?php _e('Title template', 'wp-seopress'); ?>
                                            </p>

                                            <script>
                                                jQuery(document).ready(function($) {
                                                    $('#seopress-tag-archive-title-<?php echo $seopress_cpt_key; ?>')
                                                        .click(
                                                            function() {
                                                                $('#seopress_titles_archive_titles_<?php echo $seopress_cpt_key; ?>')
                                                                    .val(sp_get_field_length($(
                                                                        '#seopress_titles_archive_titles_<?php echo $seopress_cpt_key; ?>'
                                                                    )) + $(
                                                                        '#seopress-tag-archive-title-<?php echo $seopress_cpt_key; ?>'
                                                                    ).attr('data-tag'));
                                                            });
                                                    $('#seopress-tag-archive-sep-<?php echo $seopress_cpt_key; ?>')
                                                        .click(
                                                            function() {
                                                                $('#seopress_titles_archive_titles_<?php echo $seopress_cpt_key; ?>')
                                                                    .val(sp_get_field_length($(
                                                                        '#seopress_titles_archive_titles_<?php echo $seopress_cpt_key; ?>'
                                                                    )) + $(
                                                                        '#seopress-tag-archive-sep-<?php echo $seopress_cpt_key; ?>'
                                                                    ).attr('data-tag'));
                                                            });
                                                    $('#seopress-tag-archive-sitetitle-<?php echo $seopress_cpt_key; ?>')
                                                        .click(function() {
                                                            $('#seopress_titles_archive_titles_<?php echo $seopress_cpt_key; ?>')
                                                                .val(sp_get_field_length($(
                                                                    '#seopress_titles_archive_titles_<?php echo $seopress_cpt_key; ?>'
                                                                )) + $(
                                                                    '#seopress-tag-archive-sitetitle-<?php echo $seopress_cpt_key; ?>'
                                                                ).attr('data-tag'));
                                                        });
                                                });
                                            </script>

                                            <?php printf(
                '<input type="text" id="seopress_titles_archive_titles_' . $seopress_cpt_key . '"
                                        name="seopress_titles_option_name[seopress_titles_archive_titles][' . $seopress_cpt_key . '][title]"
                                        value="%s" />',
                esc_html($check)
            ); ?>

                                            <div class="wrap-tags"><button type="button" class="btn btnSecondary tag-title"
                                                    id="seopress-tag-archive-title-<?php echo $seopress_cpt_key; ?>"
                                                    data-tag="%%cpt_plural%%"><span
                                                        class="dashicons dashicons-plus-alt2"></span><?php _e('Post Type Archive Name', 'wp-seopress'); ?></button>

                                                <button type="button" class="btn btnSecondary tag-title"
                                                    id="seopress-tag-archive-sep-<?php echo $seopress_cpt_key; ?>"
                                                    data-tag="%%sep%%"><span
                                                        class="dashicons dashicons-plus-alt2"></span><?php _e('Separator', 'wp-seopress'); ?></button>

                                                <button type="button" class="btn btnSecondary tag-title"
                                                    id="seopress-tag-archive-sitetitle-<?php echo $seopress_cpt_key; ?>"
                                                    data-tag="%%sitetitle%%"><span
                                                        class="dashicons dashicons-plus-alt2"></span><?php _e('Site Title', 'wp-seopress'); ?></button>

                                                <?php echo seopress_render_dyn_variables('tag-title'); ?>

                                            </div>

                                            <!--Archive Meta Description CPT-->
                                            <div class="seopress_wrap_archive_cpt">

                                                <p>
                                                    <?php _e('Meta description template', 'wp-seopress'); ?>
                                                </p>

                                                <?php $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['description']) ? $options['seopress_titles_archive_titles'][$seopress_cpt_key]['description'] : null; ?>

                                                <script>
                                                    jQuery(document).ready(function($) {
                                                        $('#seopress-tag-archive-desc-<?php echo $seopress_cpt_key; ?>')
                                                            .click(
                                                                function() {
                                                                    $('#seopress_titles_archive_desc_<?php echo $seopress_cpt_key; ?>')
                                                                        .val(sp_get_field_length($(
                                                                            '#seopress_titles_archive_desc_<?php echo $seopress_cpt_key; ?>'
                                                                        )) + $(
                                                                            '#seopress-tag-archive-desc-<?php echo $seopress_cpt_key; ?>'
                                                                        ).attr('data-tag'));
                                                                });
                                                        $('#seopress-tag-archive-desc-sep-<?php echo $seopress_cpt_key; ?>')
                                                            .click(
                                                                function() {
                                                                    $('#seopress_titles_archive_desc_<?php echo $seopress_cpt_key; ?>')
                                                                        .val(sp_get_field_length($(
                                                                            '#seopress_titles_archive_desc_<?php echo $seopress_cpt_key; ?>'
                                                                        )) + $(
                                                                            '#seopress-tag-archive-desc-sep-<?php echo $seopress_cpt_key; ?>'
                                                                        ).attr('data-tag'));
                                                                });
                                                        $('#seopress-tag-archive-desc-sitetitle-<?php echo $seopress_cpt_key; ?>')
                                                            .click(function() {
                                                                $('#seopress_titles_archive_desc_<?php echo $seopress_cpt_key; ?>')
                                                                    .val(sp_get_field_length($(
                                                                        '#seopress_titles_archive_desc_<?php echo $seopress_cpt_key; ?>'
                                                                    )) + $(
                                                                        '#seopress-tag-archive-desc-sitetitle-<?php echo $seopress_cpt_key; ?>'
                                                                    ).attr('data-tag'));
                                                            });
                                                    });
                                                </script>

                                                <?php printf(
                '<textarea name="seopress_titles_option_name[seopress_titles_archive_titles][' . $seopress_cpt_key . '][description]">%s</textarea>',
                esc_html($check)
            ); ?>
                                                <div class="wrap-tags">
                                                    <?php echo seopress_render_dyn_variables('tag-description'); ?>
                                                </div>

                                                <!--Archive No-Index CPT-->
                                                <div class="seopress_wrap_archive_cpt">
                                                    <?php
            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']); ?>


                                                    <label
                                                        for="seopress_titles_archive_cpt_noindex[<?php echo $seopress_cpt_key; ?>]">
                                                        <input
                                                            id="seopress_titles_archive_cpt_noindex[<?php echo $seopress_cpt_key; ?>]"
                                                            name="seopress_titles_option_name[seopress_titles_archive_titles][<?php echo $seopress_cpt_key; ?>][noindex]"
                                                            type="checkbox" <?php if ('1' == $check) { ?>
                                                        checked="yes"
                                                        <?php } ?>
                                                        value="1"/>
                                                        <?php _e('Do not display this post type archive in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                                    </label>

                                                    <?php if (isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'])) {
                esc_attr($options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']);
            } ?>

                                                </div>

                                                <!--Archive No-Follow CPT-->
                                                <div class="seopress_wrap_archive_cpt">

                                                    <?php
            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow']); ?>


                                                    <label
                                                        for="seopress_titles_archive_cpt_nofollow[<?php echo $seopress_cpt_key; ?>]">
                                                        <input
                                                            id="seopress_titles_archive_cpt_nofollow[<?php echo $seopress_cpt_key; ?>]"
                                                            name="seopress_titles_option_name[seopress_titles_archive_titles][<?php echo $seopress_cpt_key; ?>][nofollow]"
                                                            type="checkbox" <?php if ('1' == $check) { ?>
                                                        checked="yes"
                                                        <?php } ?>
                                                        value="1"/>
                                                        <?php _e('Do not follow links for this post type archive <strong>(nofollow)</strong>', 'wp-seopress'); ?>
                                                    </label>

                                                    <?php if (isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow'])) {
                esc_attr($options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow']);
            } ?>

                                                </div>
                                                <?php
        }
    }
}

function seopress_titles_archives_author_title_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>
                                                <h3>
                                                    <?php _e('Author archives', 'wp-seopress'); ?>
                                                </h3>

                                                <p>
                                                    <?php _e('Title template', 'wp-seopress'); ?>
                                                </p>

                                                <?php $check = isset($options['seopress_titles_archives_author_title']) ? $options['seopress_titles_archives_author_title'] : null; ?>

                                                <input id="seopress_titles_archive_post_author" type="text"
                                                    name="seopress_titles_option_name[seopress_titles_archives_author_title]"
                                                    value="<?php esc_html_e($check); ?>" />

                                                <div class="wrap-tags">
                                                    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-post-author" data-tag="%%post_author%%">
                                                        <span class="dashicons dashicons-plus-alt2"></span>
                                                        <?php _e('Post author', 'wp-seopress'); ?>
                                                    </button>
                                                    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-sep-author" data-tag="%%sep%%">
                                                        <span class="dashicons dashicons-plus-alt2"></span>
                                                        <?php _e('Separator', 'wp-seopress'); ?>
                                                    </button>

                                                    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-title-author" data-tag="%%sitetitle%%">
                                                        <span class="dashicons dashicons-plus-alt2"></span>
                                                        <?php _e('Site Title', 'wp-seopress'); ?>
                                                    </button>

                                                    <?php
        echo seopress_render_dyn_variables('tag-title');
}

function seopress_titles_archives_author_desc_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>
                                                    <p>
                                                        <?php _e('Meta description template', 'wp-seopress'); ?>
                                                    </p>

                                                    <?php $check = isset($options['seopress_titles_archives_author_desc']) ? $options['seopress_titles_archives_author_desc'] : null; ?>

                                                    <textarea
                                                        name="seopress_titles_option_name[seopress_titles_archives_author_desc]"><?php esc_html_e($check); ?></textarea>

                                                    <?php
}

function seopress_titles_archives_author_noindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_archives_author_noindex']); ?>


                                                    <label for="seopress_titles_archives_author_noindex">
                                                        <input id="seopress_titles_archives_author_noindex"
                                                            name="seopress_titles_option_name[seopress_titles_archives_author_noindex]"
                                                            type="checkbox" <?php if ('1' == $check) { ?>
                                                        checked="yes"
                                                        <?php } ?>
                                                        value="1"/>
                                                        <?php _e('Do not display author archives in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                                    </label>

                                                    <?php if (isset($options['seopress_titles_archives_author_noindex'])) {
        esc_attr($options['seopress_titles_archives_author_noindex']);
    }
}

function seopress_titles_archives_author_disable_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_archives_author_disable']); ?>

                                                    <label for="seopress_titles_archives_author_disable">
                                                        <input id="seopress_titles_archives_author_disable"
                                                            name="seopress_titles_option_name[seopress_titles_archives_author_disable]"
                                                            type="checkbox" <?php if ('1' == $check) { ?>
                                                        checked="yes"
                                                        <?php } ?>
                                                        value="1"/>
                                                        <?php _e('Disable author archives', 'wp-seopress'); ?>
                                                    </label>

                                                    <?php if (isset($options['seopress_titles_archives_author_disable'])) {
        esc_attr($options['seopress_titles_archives_author_disable']);
    }
}

function seopress_titles_archives_date_title_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>
                                                    <h3>
                                                        <?php _e('Date archives', 'wp-seopress'); ?>
                                                    </h3>

                                                    <p>
                                                        <?php _e('Title template', 'wp-seopress'); ?>
                                                    </p>

                                                    <?php $check = isset($options['seopress_titles_archives_date_title']) ? $options['seopress_titles_archives_date_title'] : null; ?>

                                                    <input id="seopress_titles_archives_date_title" type="text"
                                                        name="seopress_titles_option_name[seopress_titles_archives_date_title]"
                                                        value="<?php esc_html_e($check); ?>" />

                                                    <div class="wrap-tags">
                                                        <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-archive-date" data-tag="%%archive_date%%">
                                                            <span class="dashicons dashicons-plus-alt2"></span>
                                                            <?php _e('Date archives', 'wp-seopress'); ?>
                                                        </button>
                                                        <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-sep-date" data-tag="%%sep%%">
                                                            <span class="dashicons dashicons-plus-alt2"></span>
                                                            <?php _e('Separator', 'wp-seopress'); ?>
                                                        </button>
                                                        <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-title-date" data-tag="%%sitetitle%%">
                                                            <span class="dashicons dashicons-plus-alt2"></span>
                                                            <?php _e('Site Title', 'wp-seopress'); ?>
                                                        </button>
                                                        <?php
    echo seopress_render_dyn_variables('tag-title');
}

function seopress_titles_archives_date_desc_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>

                                                        <p>
                                                            <?php _e('Meta description template', 'wp-seopress'); ?>
                                                        </p>

                                                        <?php $check = isset($options['seopress_titles_archives_date_desc']) ? $options['seopress_titles_archives_date_desc'] : null; ?>

                                                        <textarea
                                                            name="seopress_titles_option_name[seopress_titles_archives_date_desc]"><?php esc_html_e($check); ?></textarea>

                                                        <?php
}

function seopress_titles_archives_date_noindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_archives_date_noindex']); ?>

                                                        <label for="seopress_titles_archives_date_noindex">
                                                            <input id="seopress_titles_archives_date_noindex"
                                                                name="seopress_titles_option_name[seopress_titles_archives_date_noindex]"
                                                                type="checkbox" <?php if ('1' == $check) { ?>
                                                            checked="yes"
                                                            <?php } ?>
                                                            value="1"/>
                                                            <?php _e('Do not display date archives in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                                        </label>

                                                        <?php if (isset($options['seopress_titles_archives_date_noindex'])) {
        esc_attr($options['seopress_titles_archives_date_noindex']);
    }
}

function seopress_titles_archives_date_disable_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_archives_date_disable']); ?>


                                                        <label for="seopress_titles_archives_date_disable">
                                                            <input id="seopress_titles_archives_date_disable"
                                                                name="seopress_titles_option_name[seopress_titles_archives_date_disable]"
                                                                type="checkbox" <?php if ('1' == $check) { ?>
                                                            checked="yes"
                                                            <?php } ?>
                                                            value="1"/>
                                                            <?php _e('Disable date archives', 'wp-seopress'); ?>
                                                        </label>

                                                        <?php if (isset($options['seopress_titles_archives_date_disable'])) {
        esc_attr($options['seopress_titles_archives_date_disable']);
    }
}

function seopress_titles_archives_search_title_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>
                                                        <h3>
                                                            <?php _e('Search archives', 'wp-seopress'); ?>
                                                        </h3>

                                                        <p>
                                                            <?php _e('Title template', 'wp-seopress'); ?>
                                                        </p>

                                                        <?php $check = isset($options['seopress_titles_archives_search_title']) ? $options['seopress_titles_archives_search_title'] : null; ?>

                                                        <input id="seopress_titles_archives_search_title" type="text"
                                                            name="seopress_titles_option_name[seopress_titles_archives_search_title]"
                                                            value="<?php esc_html_e($check); ?>" />

                                                        <div class="wrap-tags">
                                                            <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-search-keywords" data-tag="%%search_keywords%%">
                                                                <span class="dashicons dashicons-plus-alt2"></span>
                                                                <?php _e('Search Keywords', 'wp-seopress'); ?>
                                                            </button>

                                                            <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-sep-search" data-tag="%%sep%%">
                                                                <span class="dashicons dashicons-plus-alt2"></span>
                                                                <?php _e('Separator', 'wp-seopress'); ?>
                                                            </button>

                                                            <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-title-search" data-tag="%%sitetitle%%">
                                                                <span class="dashicons dashicons-plus-alt2"></span>
                                                                <?php _e('Site Title', 'wp-seopress'); ?>
                                                            </button>
                                                            <?php
    echo seopress_render_dyn_variables('tag-title');
}

function seopress_titles_archives_search_desc_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>
                                                            <p>
                                                                <?php _e('Meta description template', 'wp-seopress'); ?>
                                                            </p>

                                                            <?php $check = isset($options['seopress_titles_archives_search_desc']) ? $options['seopress_titles_archives_search_desc'] : null; ?>

                                                            <textarea
                                                                name="seopress_titles_option_name[seopress_titles_archives_search_desc]"><?php esc_html_e($check); ?></textarea>

                                                            <?php
}

function seopress_titles_archives_search_title_noindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_archives_search_title_noindex']); ?>


                                                            <label for="seopress_titles_archives_search_title_noindex">
                                                                <input
                                                                    id="seopress_titles_archives_search_title_noindex"
                                                                    name="seopress_titles_option_name[seopress_titles_archives_search_title_noindex]"
                                                                    type="checkbox" <?php if ('1' == $check) { ?>
                                                                checked="yes"
                                                                <?php } ?>
                                                                value="1"/>
                                                                <?php _e('Do not display search archives in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                                            </label>

                                                            <?php if (isset($options['seopress_titles_archives_search_title_noindex'])) {
        esc_attr($options['seopress_titles_archives_search_title_noindex']);
    }
}

function seopress_titles_archives_404_title_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>
                                                            <h3>
                                                                <?php _e('404 archives', 'wp-seopress'); ?>
                                                            </h3>

                                                            <p>
                                                                <?php _e('Title template', 'wp-seopress'); ?>
                                                            </p>

                                                            <?php $check = isset($options['seopress_titles_archives_404_title']) ? $options['seopress_titles_archives_404_title'] : null; ?>

                                                            <input id="seopress_titles_archives_404_title" type="text"
                                                                name="seopress_titles_option_name[seopress_titles_archives_404_title]"
                                                                value="<?php esc_html_e($check); ?>" />

                                                            <div class="wrap-tags">
                                                                <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-site-title-404" data-tag="%%sitetitle%%">
                                                                    <span class="dashicons dashicons-plus-alt2"></span>
                                                                    <?php _e('Site Title', 'wp-seopress'); ?>
                                                                </button>
                                                                <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-sep-404" data-tag="%%sep%%">
                                                                    <span class="dashicons dashicons-plus-alt2"></span>
                                                                    <?php _e('Separator', 'wp-seopress'); ?>
                                                                </button>
                                                                <?php
    echo seopress_render_dyn_variables('tag-title');
}

function seopress_titles_archives_404_desc_callback()
{
    $options = get_option('seopress_titles_option_name'); ?>

                                                                <p>
                                                                    <label for="seopress_titles_archives_404_desc">
                                                                        <?php _e('Meta description template', 'wp-seopress'); ?>
                                                                    </label>
                                                                </p>

                                                                <?php $check = isset($options['seopress_titles_archives_404_desc']) ? $options['seopress_titles_archives_404_desc'] : null; ?>

                                                                <textarea id="seopress_titles_archives_404_desc"
                                                                    name="seopress_titles_option_name[seopress_titles_archives_404_desc]"><?php esc_html_e($check); ?></textarea>

                                                                <?php
}

//Advanced
function seopress_titles_noindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_noindex']); ?>


                                                                <label for="seopress_titles_noindex">
                                                                    <input id="seopress_titles_noindex"
                                                                        name="seopress_titles_option_name[seopress_titles_noindex]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('noindex', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Do not display all pages of the site in Google search results and do not display "Cached" links in search results.', 'wp-seopress'); ?>
                                                                </p>

                                                                <p class="description">
                                                                    <?php printf(__('Check also the <strong>"Search engine visibility"</strong> setting from the <a href="%s">WordPress Reading page</a>.', 'wp-seopress'), admin_url('options-reading.php')); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_noindex'])) {
        esc_attr($options['seopress_titles_noindex']);
    }
}

function seopress_titles_nofollow_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_nofollow']); ?>


                                                                <label for="seopress_titles_nofollow">
                                                                    <input id="seopress_titles_nofollow"
                                                                        name="seopress_titles_option_name[seopress_titles_nofollow]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('nofollow', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Do not follow links for all pages.', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_nofollow'])) {
        esc_attr($options['seopress_titles_nofollow']);
    }
}

function seopress_titles_noodp_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_noodp']); ?>


                                                                <label for="seopress_titles_noodp">
                                                                    <input id="seopress_titles_noodp"
                                                                        name="seopress_titles_option_name[seopress_titles_noodp]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('noodp', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Do not use Open Directory project metadata for titles or excerpts for all pages.', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_noodp'])) {
        esc_attr($options['seopress_titles_noodp']);
    }
}

function seopress_titles_noimageindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_noimageindex']); ?>


                                                                <label for="seopress_titles_noimageindex">
                                                                    <input id="seopress_titles_noimageindex"
                                                                        name="seopress_titles_option_name[seopress_titles_noimageindex]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('noimageindex', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Do not index images from the entire site.', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_noimageindex'])) {
        esc_attr($options['seopress_titles_noimageindex']);
    }
}

function seopress_titles_noarchive_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_noarchive']); ?>

                                                                <label for="seopress_titles_noarchive">
                                                                    <input id="seopress_titles_noarchive"
                                                                        name="seopress_titles_option_name[seopress_titles_noarchive]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('noarchive', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Do not display a "Cached" link in the Google search results.', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_noarchive'])) {
        esc_attr($options['seopress_titles_noarchive']);
    }
}

function seopress_titles_nosnippet_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_nosnippet']); ?>


                                                                <label for="seopress_titles_nosnippet">
                                                                    <input id="seopress_titles_nosnippet"
                                                                        name="seopress_titles_option_name[seopress_titles_nosnippet]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('nosnippet', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Do not display a description in the Google search results for all pages.', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_nosnippet'])) {
        esc_attr($options['seopress_titles_nosnippet']);
    }
}

function seopress_titles_nositelinkssearchbox_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_nositelinkssearchbox']); ?>


                                                                <label for="seopress_titles_nositelinkssearchbox">
                                                                    <input id="seopress_titles_nositelinkssearchbox"
                                                                        name="seopress_titles_option_name[seopress_titles_nositelinkssearchbox]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('nositelinkssearchbox', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('Prevents Google to display a sitelinks searchbox in search results. Enable this option will remove the "Website" schema from your source code.', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_nositelinkssearchbox'])) {
        esc_attr($options['seopress_titles_nositelinkssearchbox']);
    }
}

function seopress_titles_paged_rel_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_paged_rel']); ?>


                                                                <label for="seopress_titles_paged_rel">
                                                                    <input id="seopress_titles_paged_rel"
                                                                        name="seopress_titles_option_name[seopress_titles_paged_rel]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('Add rel next/prev link in head of paginated archive pages', 'wp-seopress'); ?>
                                                                </label>

                                                                <?php if (isset($options['seopress_titles_paged_rel'])) {
        esc_attr($options['seopress_titles_paged_rel']);
    }
}

function seopress_titles_paged_noindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_paged_noindex']); ?>

                                                                <label for="seopress_titles_paged_noindex">

                                                                    <input id="seopress_titles_paged_noindex"
                                                                        name="seopress_titles_option_name[seopress_titles_paged_noindex]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('Add a "noindex" meta robots for all paginated archive pages', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('eg: https://example.com/category/my-category/page/2/', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_paged_noindex'])) {
        esc_attr($options['seopress_titles_paged_noindex']);
    }
}

function seopress_titles_attachments_noindex_callback()
{
    $options = get_option('seopress_titles_option_name');

    $check = isset($options['seopress_titles_attachments_noindex']); ?>


                                                                <label for="seopress_titles_attachments_noindex">
                                                                    <input id="seopress_titles_attachments_noindex"
                                                                        name="seopress_titles_option_name[seopress_titles_attachments_noindex]"
                                                                        type="checkbox" <?php if ('1' == $check) { ?>
                                                                    checked="yes"
                                                                    <?php } ?>
                                                                    value="1"/>
                                                                    <?php _e('Add a "noindex" meta robots for all attachment pages', 'wp-seopress'); ?>
                                                                </label>

                                                                <p class="description">
                                                                    <?php _e('eg: https://example.com/my-media-attachment-page', 'wp-seopress'); ?>
                                                                </p>

                                                                <?php if (isset($options['seopress_titles_attachments_noindex'])) {
        esc_attr($options['seopress_titles_attachments_noindex']);
    }
}
