<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_social_knowledge()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Knowledge Graph', 'wp-seopress'); ?>
    </h2>
</div>

<p>
    <?php _e('Configure Google Knowledge Graph. This will print a schema for search engines on your homepage.', 'wp-seopress'); ?>
</p>

<p class="seopress-help">
    <span class="dashicons dashicons-external"></span>
    <a href="https://developers.google.com/search/docs/guides/enhance-site" target="_blank">
        <?php _e('Learn more on Google official website.', 'wp-seopress'); ?>
    </a>
</p>

<?php
}

function print_section_info_social_accounts()
{
    ?>

<div class="sp-section-header">
    <h2>
        <?php _e('Your social accounts', 'wp-seopress'); ?>
    </h2>
</div>

<div class="seopress-notice">
    <p>
        <?php _e('Link your site with your social accounts.', 'wp-seopress'); ?>
    <p>
        <?php _e('Use markup on your website to add your social profile information to a Google Knowledge panel.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php _e('Knowledge panels prominently display your social profile information in some Google Search results.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php _e('Filling in these fields does not guarantee the display of this data in search results.', 'wp-seopress'); ?>
    </p>
</div>

<?php
}

function print_section_info_social_facebook()
{
    $docs  = seopress_get_docs_links(); ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Facebook (Open Graph)', 'wp-seopress'); ?>
    </h2>
</div>

<p>
    <?php _e('Manage Open Graph data. These metatags will be used by Facebook, Pinterest, LinkedIn, WhatsApp... when a user shares a link on its own social network. Increase your click-through rate by providing relevant information such as an attractive image.', 'wp-seopress'); ?>
    <?php echo seopress_tooltip_link($docs['social']['og'], __('Manage Facebook Open Graph and Twitter Cards metas - new window', 'wp-seopress')); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php _e('We generate the <strong>og:image</strong> meta in this order:', 'wp-seopress'); ?>
    </p>

    <ol>
        <li>
            <?php _e('Custom OG Image from SEO metabox', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('Post thumbnail / Product category thumbnail (Featured image)', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('First image of your post content', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('Global OG Image set in SEO > Social > Open Graph', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('Site icon from the Customizer', 'wp-seopress'); ?>
        </li>
    </ol>
</div>

<?php
}

function print_section_info_social_twitter()
{
    $docs  = seopress_get_docs_links(); ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Twitter (Twitter card)', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Manage your Twitter card.', 'wp-seopress'); ?>
    <?php echo seopress_tooltip_link($docs['social']['og'], __('Manage Facebook Open Graph and Twitter Cards metas - new window', 'wp-seopress')); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php _e('We generate the <strong>twitter:image</strong> meta in this order:', 'wp-seopress'); ?>
    </p>

    <ol>
        <li>
            <?php _e('Custom Twitter image from SEO metabox', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('Post thumbnail / Product category thumbnail (Featured image)', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('First image of your post content', 'wp-seopress'); ?>
        </li>
        <li>
            <?php _e('Global Twitter:image set in SEO > Social > Twitter Card', 'wp-seopress'); ?>
        </li>
    </ol>
</div>

<?php
}
