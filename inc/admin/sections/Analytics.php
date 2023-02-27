<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_google_analytics_enable()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Google Analytics', 'wp-seopress'); ?>
    </h2>
</div>

<div class="seopress-sub-tabs">
    <a href="#seopress-analytics-general"><?php _e('General', 'wp-seopress'); ?></a> |
    <a href="#seopress-analytics-tracking"><?php _e('Tracking', 'wp-seopress'); ?></a> |
    <a href="#seopress-analytics-events"><?php _e('Events', 'wp-seopress'); ?></a>
    <?php do_action('seopress_analytics_settings_section'); ?>
</div>

<p>
    <?php _e('Link your Google Analytics to your website. The tracking code will be automatically added to your site.', 'wp-seopress'); ?>
</p>
<hr>
<h3 id="seopress-analytics-general"><?php _e('General','wp-seopress'); ?></h3>

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
{ ?>

<hr>
<h3 id="seopress-analytics-tracking">
    <?php _e('Tracking', 'wp-seopress'); ?>
</h3>

<p>
    <?php _e('Configure your Google Analytics tracking code.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_google_analytics_custom_tracking()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Custom Tracking', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Add your own scripts like GTM or Facebook Pixel by copy and paste the provided code to the HEAD/BODY or FOOTER.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice is-warning">
    <p>
        <?php _e('<strong>GA4 or Universal Analytics</strong> codes are <strong>automatically added to your source code</strong> if you have enter your <strong>Measurement ID and / or your Universal Analytics ID</strong> from <strong>General</strong> tab.', 'wp-seopress'); ?>
    </p>
</div>
<?php
}

function print_section_info_google_analytics_events()
{
$docs = seopress_get_docs_links();
?>
<hr>
<h3 id="seopress-analytics-events">
    <?php _e('Events', 'wp-seopress'); ?>
</h3>
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
        <?php _e('Advanced settings', 'wp-seopress'); ?>
    </h2>
</div>

<div class="seopress-sub-tabs">
    <a href="#seopress-analytics-cd"><?php _e('Custom Dimensions', 'wp-seopress'); ?></a> |
    <a href="#seopress-analytics-misc"><?php _e('Misc', 'wp-seopress'); ?></a>
</div>

<div class="seopress-notice">
    <p>
        <?php _e('All advanced settings work with <strong>Google Analytics</strong> and <strong>Matomo</strong> tracking code.', 'wp-seopress'); ?>
    </p>
</div>

<hr>
<h3 id="seopress-analytics-cd"><?php _e('Custom Dimensions','wp-seopress'); ?></h3>

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

<?php echo seopress_tooltip_link($docs['analytics']['custom_dimensions'], __('Guide to create custom dimensions in Google Analytics - new window', 'wp-seopress')); ?>

<?php
}

function print_section_info_google_analytics_advanced()
{
?>
<br>
<hr>
<h3 id="seopress-analytics-misc"><?php _e('Misc','wp-seopress'); ?></h3>

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

<div class="seopress-sub-tabs">
    <a href="#seopress-matomo-tracking"><?php _e('Tracking', 'wp-seopress'); ?></a>
    <?php do_action('seopress_matomo_settings_section'); ?>
</div>

<p>
    <?php _e('Use Matomo to track your users with privacy in mind. We support both On Premise and Cloud installations.', 'wp-seopress'); ?>
</p>

<hr>
<h3 id="seopress-matomo-tracking">
    <?php _e('Tracking', 'wp-seopress'); ?>
</h3>

<div class="seopress-notice">
    <p>
        <?php _e('Your <strong>Custom Dimensions</strong> will also work with Matomo tracking code.', 'wp-seopress'); ?>
    </p>
</div>

<?php
}

function print_section_info_google_analytics_clarity()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Microsoft Clarity', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Use Microsoft Clarity to capture session recordings, get instant heatmaps and powerful Insights for Free. Know how people interact with your site to improve user experience and conversions.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice">
    <p>
        <?php printf(__('Create your first Microsoft Clarity project <a href="%s" target="_blank">here</a>.', 'wp-seopress'), esc_url('https://clarity.microsoft.com/')); ?>
    </p>
</div>

<?php
}
