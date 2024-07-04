<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_titles()
{
    $docs = seopress_get_docs_links(); ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Home', 'wp-seopress'); ?>
    </h2>
</div>

<div class="seopress-notice">
    <p>
        <?php _e('Title and meta description are used by search engines to generate the snippet of your site in search results page.', 'wp-seopress'); ?>
    </p>
</div>

<p>
    <?php _e('Customize your title & meta description for homepage.', 'wp-seopress'); ?>
</p>

<span class="dashicons dashicons-external seopress-help"></span>
<a class="seopress-help" href="<?php echo $docs['titles']['wrong_meta']; ?>"
    target="_blank">
    <?php _e('Wrong meta title / description in SERP?', 'wp-seopress'); ?></a>

<script>
    function sp_get_field_length(e) {
        if (e.val().length > 0) {
            meta = e.val() + ' ';
        } else {
            meta = e.val();
        }
        return meta;
    }
</script>

<?php
}

function print_section_info_single()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Post Types', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Customize your titles & metas for Single Custom Post Types.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php
            /* translators: %1$s: <code>public => true</code>, %2$s: <code>show_ui => true</code>*/
            printf(__('Only post types registered with the %1$s and %2$s arguments will be listed here.', 'wp-seopress'), '<code>public => true</code>', '<code>show_ui => true</code>');
        ?>
    </p>
</div>

<?php
}

function print_section_info_advanced()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Advanced', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Customize your metas for all pages.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_tax()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Taxonomies', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Customize your metas for all taxonomies archives.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php
            /* translators: %1$s: <code>public => true</code>, %2$s: <code>show_ui => true</code>*/
            printf(__('Only taxonomies registered with the %1$s and %2$s arguments will be listed here.', 'wp-seopress'), '<code>public => true</code>', '<code>show_ui => true</code>');
        ?>
    </p>
</div>

<?php
}

function print_section_info_archives()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Archives', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Customize your metas for all archives.', 'wp-seopress'); ?>
</p>

<?php
}
