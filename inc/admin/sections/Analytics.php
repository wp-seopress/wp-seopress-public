<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_google_analytics_enable()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('General', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Link your Google Analytics to your website. The tracking code will be automatically added to your site.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_google_analytics_gdpr()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Cookie bar / GDPR', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Manage user consent for GDPR and customize your cookie bar easily.', 'wp-seopress'); ?>
</p>

<p>
    <?php _e('Works with <strong>Google Analytics</strong> and <strong>Matomo</strong>.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_google_analytics_features()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Tracking', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Configure your Google Analytics tracking code.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_google_analytics_events()
{
    $docs = seopress_get_docs_links();
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Events', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Track events in Google Analytics.', 'wp-seopress'); ?>
</p>

<p class="seopress-help description">
    <span class="dashicons dashicons-external"></span>
    <a href="<?php echo $docs['analytics']['events']; ?>" target="_blank">
        <?php _e('Learn how to track events with Google Analytics','wp-seopress'); ?>
    </a>
</p>
<?php
}

function print_section_info_google_analytics_custom_dimensions()
{
    $docs = seopress_get_docs_links(); ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Custom Dimensions', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Configure your Google Analytics custom dimensions.', 'wp-seopress'); ?>
</p>
<p>
    <?php _e('Custom dimensions and custom metrics are like the default dimensions and metrics in your Analytics account, except you create them yourself.', 'wp-seopress'); ?>
</p>
<p>
    <?php _e('Use them to collect and analyze data that Analytics doesn\'t automatically track.', 'wp-seopress'); ?>
</p>
<p>
    <?php _e('Please note that you also have to setup your custom dimensions in your Google Analytics account. More info by clicking on the help icon.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php _e('Custom dimensions also work with <strong>Matomo</strong> tracking code.', 'wp-seopress'); ?>
    </p>
</div>

<?php echo seopress_tooltip_link($docs['analytics']['custom_dimensions'], __('Guide to create custom dimensions in Google Analytics - new window', 'wp-seopress')); ?>

<?php
}

function print_section_info_google_analytics_matomo()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Matomo', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Use Matomo to track your users with privacy in mind.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php _e('Your <strong>Custom Dimensions</strong> will also work with Matomo tracking code.', 'wp-seopress'); ?>
    </p>
</div>

<?php
}
