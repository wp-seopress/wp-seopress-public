<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_google_analytics_enable()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Google Analytics', 'wp-seopress'); ?>
        </h2>
    </div>

    <div class="seopress-sub-tabs">
        <a href="#seopress-analytics-general"><?php esc_attr_e('General', 'wp-seopress'); ?></a> |
        <a href="#seopress-analytics-events"><?php esc_attr_e('Events', 'wp-seopress'); ?></a>
        <?php do_action('seopress_analytics_settings_section'); ?>
    </div>

    <p>
        <?php esc_attr_e('Link your Google Analytics to your website. The tracking code will be automatically added to your site.', 'wp-seopress'); ?>
    </p>
    <hr>
    <h3 id="seopress-analytics-general"><?php esc_attr_e('General', 'wp-seopress'); ?></h3>

<?php
}

function seopress_print_section_info_google_analytics_gdpr()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Cookie bar / GDPR / Google Consent v2', 'wp-seopress'); ?>
        </h2>
    </div>
    <p>
        <?php esc_attr_e('Manage user consent for GDPR and customize your cookie bar easily.', 'wp-seopress'); ?>
    </p>

    <p>
        <?php echo wp_kses_post(__('Works with <strong>Google Analytics</strong> and <strong>Matomo</strong>.', 'wp-seopress')); ?>
    </p>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('We automatically manage <strong>Google Consent v2</strong> with GA4 and our cookie bar.', 'wp-seopress')); ?>
        </p>
    </div>

<?php
}

function seopress_print_section_info_google_analytics_custom_tracking()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Custom Tracking', 'wp-seopress'); ?>
        </h2>
    </div>
    <p>
        <?php esc_attr_e('Add your own scripts like GTM by copy and paste the provided code to the HEAD/BODY or FOOTER.', 'wp-seopress'); ?>
    </p>

    <div class="seopress-notice is-warning">
        <p>
            <?php echo wp_kses_post(__('<strong>Excluding user roles</strong> also works with the <strong>custom tracking scripts</strong> registered below.', 'wp-seopress')); ?>
        </p>
        <p>
            <?php echo wp_kses_post(__('<strong>GA4, Matomo or MS Clarity</strong> codes are <strong>automatically added to your source code</strong> if you have enter your <strong>Measurement ID, Matomo tracking ID and / or MS Clarity project ID</strong> from <strong>Google Analytics / Matomo / Clarity</strong> tabs.', 'wp-seopress')); ?>
        </p>
    </div>
<?php
}

function seopress_print_section_info_google_analytics_events()
{
    $docs = seopress_get_docs_links();
?>
    <hr>
    <h3 id="seopress-analytics-events">
        <?php esc_attr_e('Events', 'wp-seopress'); ?>
    </h3>
    <p>
        <?php esc_attr_e('Track events in Google Analytics.', 'wp-seopress'); ?>
    </p>

    <p class="seopress-help description">
        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo esc_url($docs['analytics']['events']); ?>" target="_blank">
            <?php esc_attr_e('Learn how to track events with Google Analytics', 'wp-seopress'); ?>
        </a>
    </p>
<?php
}

function seopress_print_section_info_google_analytics_custom_dimensions()
{
    $docs = seopress_get_docs_links(); ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Advanced settings', 'wp-seopress'); ?>
        </h2>
    </div>

    <div class="seopress-sub-tabs">
        <a href="#seopress-analytics-cd"><?php esc_attr_e('Custom Dimensions', 'wp-seopress'); ?></a> |
        <a href="#seopress-analytics-misc"><?php esc_attr_e('Misc', 'wp-seopress'); ?></a>
    </div>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('All advanced settings work with <strong>Google Analytics</strong> and <strong>Matomo</strong> tracking codes. Excluding user roles also works with <strong>MS Clarity</strong> and <strong>Custom tracking scripts</strong>.', 'wp-seopress')); ?>
        </p>
    </div>

    <hr>
    <h3 id="seopress-analytics-cd"><?php esc_attr_e('Custom Dimensions', 'wp-seopress'); ?></h3>

    <p>
        <?php esc_attr_e('Configure your Google Analytics custom dimensions.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php esc_attr_e('Custom dimensions and custom metrics are like the default dimensions and metrics in your Analytics account, except you create them yourself.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php esc_attr_e('Use them to collect and analyze data that Analytics doesn\'t automatically track.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php esc_attr_e('Please note that you also have to setup your custom dimensions in your Google Analytics account.', 'wp-seopress'); ?>
    </p>
<?php
}

function seopress_print_section_info_google_analytics_advanced()
{
?>
    <br>
    <hr>
    <h3 id="seopress-analytics-misc"><?php esc_attr_e('Misc', 'wp-seopress'); ?></h3>

<?php
}

function seopress_print_section_info_google_analytics_matomo()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Matomo', 'wp-seopress'); ?>
        </h2>
    </div>

    <div class="seopress-sub-tabs">
        <a href="#seopress-matomo-tracking"><?php esc_attr_e('Tracking', 'wp-seopress'); ?></a>
        <?php do_action('seopress_matomo_settings_section'); ?>
    </div>

    <p>
        <?php esc_attr_e('Use Matomo to track your users with privacy in mind. We support both On Premise and Cloud installations.', 'wp-seopress'); ?>
    </p>

    <hr>
    <h3 id="seopress-matomo-tracking">
        <?php esc_attr_e('Tracking', 'wp-seopress'); ?>
    </h3>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('Your <strong>Custom Dimensions</strong> from Advanced tab will also work with Matomo tracking code.', 'wp-seopress')); ?>
        </p>
    </div>

<?php
}

function seopress_print_section_info_google_analytics_clarity()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Microsoft Clarity', 'wp-seopress'); ?>
        </h2>
    </div>
    <p>
        <?php esc_attr_e('Use Microsoft Clarity to capture session recordings, get instant heatmaps and powerful Insights for Free. Know how people interact with your site to improve user experience and conversions.', 'wp-seopress'); ?>
    </p>

    <div class="seopress-notice">
        <p>
            <?php
            /* translators: %s MS Clarity website URL */
            echo wp_kses_post(sprintf(__('Create your first Microsoft Clarity project <a href="%s" target="_blank">here</a>.', 'wp-seopress'), esc_url('https://clarity.microsoft.com/')));
            ?>
        </p>
    </div>

<?php
}
