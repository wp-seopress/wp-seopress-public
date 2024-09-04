<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_advanced_advanced()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Advanced', 'wp-seopress'); ?>
        </h2>
    </div>

    <div class="seopress-sub-tabs">
        <a href="#seopress-advanced-crawling"><?php esc_attr_e('Crawling Optimization', 'wp-seopress'); ?></a> |
        <a href="#seopress-advanced-search-engines"><?php esc_attr_e('Search engines validation', 'wp-seopress'); ?></a>
    </div>

    <p>
        <?php esc_attr_e('Advanced SEO options for advanced users.', 'wp-seopress'); ?>
    </p>

<?php
}

function seopress_print_section_info_advanced_advanced_crawling()
{ ?>
    <hr>

    <h3 id="seopress-advanced-crawling">
        <?php esc_attr_e('Crawling Optimization', 'wp-seopress'); ?>
    </h3>

    <p><?php esc_attr_e('Clean your source code to improve performance and your crawl budget.', 'wp-seopress'); ?></p>

<?php
}

function seopress_print_section_info_advanced_advanced_search_engines()
{ ?>
    <hr>

    <h3 id="seopress-advanced-search-engines">
        <?php esc_attr_e('Search engines validation', 'wp-seopress'); ?>
    </h3>

    <p><?php esc_attr_e('Easily validate your site with search engines webmaster tools.', 'wp-seopress'); ?></p>

<?php
}

function seopress_print_section_info_advanced_appearance()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Appearance', 'wp-seopress'); ?>
        </h2>
    </div>

    <div class="seopress-sub-tabs">
        <a href="#seopress-advanced-metaboxes"><?php esc_attr_e('Metaboxes', 'wp-seopress'); ?></a> |
        <a href="#seopress-advanced-adminbar"><?php esc_attr_e('Admin bar', 'wp-seopress'); ?></a> |
        <a href="#seopress-advanced-seo-dashboard"><?php esc_attr_e('SEO Dashboard', 'wp-seopress'); ?></a> |
        <a href="#seopress-advanced-columns"><?php esc_attr_e('Columns', 'wp-seopress'); ?></a>
    </div>

    <p>
        <?php esc_attr_e('Customize the plugin to fit your needs.', 'wp-seopress'); ?>
    </p>

<?php
}

function seopress_print_section_info_advanced_security()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Security', 'wp-seopress'); ?>
        </h2>
    </div>

    <div class="seopress-sub-tabs">
        <a href="#seopress-security-metaboxes"><?php esc_attr_e('SEO metaboxes', 'wp-seopress'); ?></a> |
        <a href="#seopress-security-settings"><?php esc_attr_e('SEO settings pages', 'wp-seopress'); ?></a>
    </div>

    <p>
        <?php esc_attr_e('Control access to SEO settings and metaboxes by user roles.', 'wp-seopress'); ?>
    </p>

    <hr>

    <h3 id="seopress-security-metaboxes">
        <?php esc_attr_e('SEO metaboxes', 'wp-seopress'); ?>
    </h3>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('Check a user role to <strong>PREVENT</strong> it to edit a specific metabox.', 'wp-seopress')); ?>
        </p>
    </div>

<?php
}

function seopress_print_section_info_advanced_security_roles()
{
?>

    <hr>

    <h3 id="seopress-security-settings">
        <?php esc_attr_e('SEO settings pages', 'wp-seopress'); ?>
    </h3>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('Check a user role to <strong>ALLOW</strong> it to edit a specific settings page.', 'wp-seopress')); ?>
        </p>
    </div>

<?php
}

function seopress_print_section_info_advanced_appearance_col()
{ ?>
    <hr>

    <h3 id="seopress-advanced-columns">
        <?php esc_attr_e('Columns', 'wp-seopress'); ?>
    </h3>

    <p><?php esc_attr_e('Customize the SEO columns.', 'wp-seopress'); ?></p>

<?php
}

function seopress_print_section_info_advanced_appearance_metabox()
{ ?>
    <hr>

    <h3 id="seopress-advanced-metaboxes">
        <?php esc_attr_e('Metaboxes', 'wp-seopress'); ?>
    </h3>

    <p><?php esc_attr_e('Edit your SEO metadata directly from your favorite page builder.', 'wp-seopress'); ?></p>

    <?php if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
        <a class="wrap-yt-embed" href="https://www.youtube.com/watch?v=sf0ocG7vQMM" target="_blank" title="<?php esc_attr_e('Watch the universal SEO metabox overview video - Open in a new window', 'wp-seopress'); ?>">
            <img src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/yt-universal-metabox.webp'); ?>" alt="<?php esc_attr_e('Universal SEO metabox video thumbnail', 'wp-seopress'); ?>" width="500" />
        </a>
    <?php }
}

function seopress_print_section_info_advanced_appearance_dashboard()
{ ?>
    <hr>

    <h3 id="seopress-advanced-seo-dashboard">
        <?php esc_attr_e('SEO Dashboard', 'wp-seopress'); ?>
    </h3>

    <p><?php esc_attr_e('Click the dedicated icon from the toolbar to customize the SEO dashboard.', 'wp-seopress'); ?></p>

<?php
}

function seopress_print_section_info_advanced_appearance_admin_bar()
{ ?>
    <hr>

    <h3 id="seopress-advanced-adminbar">
        <?php esc_attr_e('Admin bar', 'wp-seopress'); ?>
    </h3>

    <p><?php esc_attr_e('The admin bar appears on the top of your pages when logged in to your WP admin.', 'wp-seopress'); ?></p>

<?php
}
