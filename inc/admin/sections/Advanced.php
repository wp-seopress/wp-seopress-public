<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_advanced_advanced()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Advanced', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Advanced SEO options for advanced users.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_advanced_appearance()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Appearance', 'wp-seopress'); ?>
    </h2>
</div>

<div class="seopress-sub-tabs">
    <a href="#seopress-advanced-metaboxes"><?php _e('Metaboxes', 'wp-seopress'); ?></a> |
    <a href="#seopress-advanced-adminbar"><?php _e('Admin bar', 'wp-seopress'); ?></a> |
    <a href="#seopress-advanced-seo-dashboard"><?php _e('SEO Dashboard', 'wp-seopress'); ?></a> |
    <a href="#seopress-advanced-columns"><?php _e('Columns', 'wp-seopress'); ?></a> |
    <a href="#seopress-advanced-misc"><?php _e('Misc', 'wp-seopress'); ?></a>
</div>

<p>
    <?php _e('Customize the plugin to fit your needs.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_advanced_security()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Security', 'wp-seopress'); ?>
    </h2>
</div>

<div class="seopress-sub-tabs">
    <a href="#seopress-security-metaboxes"><?php _e('SEO metaboxes', 'wp-seopress'); ?></a> |
    <a href="#seopress-security-settings"><?php _e('SEO settings pages', 'wp-seopress'); ?></a>
</div>

<p>
    <?php _e('Control access to SEO settings and metaboxes by user roles.', 'wp-seopress'); ?>
</p>

<hr>

<h3 id="seopress-security-metaboxes">
    <?php _e('SEO metaboxes', 'wp-seopress'); ?>
</h3>

<p>
    <?php _e('Check a user role to prevent it to edit a specific metabox.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_advanced_security_roles()
{
    ?>

<hr>

<h3 id="seopress-security-settings">
    <?php _e('SEO settings pages', 'wp-seopress'); ?>
</h3>

<p>
    <?php _e('Check a user role to allow it to edit a specific settings page.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_advanced_appearance_col()
{ ?>
<hr>

<h3 id="seopress-advanced-columns">
    <?php _e('Columns', 'wp-seopress'); ?>
</h3>

<p><?php _e('Customize the SEO columns.','wp-seopress'); ?></p>

<?php
}

function print_section_info_advanced_appearance_metabox()
{ ?>
<hr>

<h3 id="seopress-advanced-metaboxes">
    <?php _e('Metaboxes', 'wp-seopress'); ?>
</h3>

<p><?php _e('Edit your SEO metadata directly from your favorite page builder.','wp-seopress'); ?></p>

    <?php if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
        <a class="wrap-yt-embed" href="https://www.youtube.com/watch?v=sf0ocG7vQMM" target="_blank" title="<?php _e('Watch the universal SEO metabox overview video - Open in a new window', 'wp-seopress'); ?>">
            <img src="<?php echo SEOPRESS_ASSETS_DIR . '/img/yt-universal-metabox.webp'; ?>" alt="<?php _e('Universal SEO metabox video thumbnail','wp-seopress'); ?>" width="500" />
        </a>
    <?php }
}

function print_section_info_advanced_appearance_dashboard()
{ ?>
<hr>

<h3 id="seopress-advanced-seo-dashboard">
    <?php _e('SEO Dashboard', 'wp-seopress'); ?>
</h3>

<p><?php _e('Click the dedicated icon from the toolbar to customize the SEO dashboard.','wp-seopress'); ?></p>

<?php
}

function print_section_info_advanced_appearance_admin_bar()
{ ?>
<hr>

<h3 id="seopress-advanced-adminbar">
    <?php _e('Admin bar', 'wp-seopress'); ?>
</h3>

<p><?php _e('The admin bar appears on the top of your pages when logged in to your WP admin.','wp-seopress'); ?></p>

<?php
}

function print_section_info_advanced_appearance_misc()
{ ?>
<hr>

<h3 id="seopress-advanced-misc">
    <?php _e('Misc', 'wp-seopress'); ?>
</h3>

<?php
}
