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
<h3>
    <?php _e('SEOPress metaboxes', 'wp-seopress'); ?>
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

<h3>
    <?php _e('SEOPress settings pages', 'wp-seopress'); ?>
</h3>

<p>
    <?php _e('Check a user role to allow it to edit a specific settings page.', 'wp-seopress'); ?>
</p>

<?php
}
