<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_instant_indexing_general()
{
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Instant Indexing', 'wp-seopress'); ?>
        </h2>
    </div>

    <p><?php esc_attr_e('You can use the Indexing API to tell Google & Bing to update or remove pages from the Google / Bing index. The process can takes few minutes. You can submit your URLs in batches of 100 (max 200 request per day for Google).', 'wp-seopress'); ?></p>

    <p class="seopress-help">
        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo esc_url($docs['indexing_api']['google']); ?>" target="_blank"><?php esc_attr_e('401 / 403 error?', 'wp-seopress'); ?></a>
    </p>

    <div class="seopress-notice">
        <h3><?php esc_attr_e('How does this work?', 'wp-seopress'); ?></h3>
        <ol>
            <li><?php echo wp_kses_post(__('Setup your Google / Bing API keys from the <strong>Settings</strong> tab', 'wp-seopress')); ?></li>
            <li><?php echo wp_kses_post(__('<strong>Enter your URLs</strong> to index to the field below', 'wp-seopress')); ?></li>
            <li><strong><?php esc_attr_e('Save changes', 'wp-seopress'); ?></strong></li>
            <li><?php echo wp_kses_post(__('Click <strong>Submit URLs to Google & Bing</strong>', 'wp-seopress')); ?></li>
        </ol>
    </div>

    <?php

    $indexing_plugins = [
        'indexnow/indexnow-url-submission.php'                       => 'IndexNow',
        'bing-webmaster-tools/bing-url-submission.php'               => 'Bing Webmaster Url Submission',
        'fast-indexing-api/instant-indexing.php'                     => 'Instant Indexing',
    ];

    foreach ($indexing_plugins as $key => $value) {
        if (is_plugin_active($key)) { ?>
            <div class="seopress-notice is-warning">
                <h3>
                    <?php
                    /* translators: %s Indexing plugin name */
                    printf(esc_attr__('We noticed that you use <strong>%s</strong> plugin.', 'wp-seopress'), esc_html($value));
                    ?>
                </h3>

                <p><?php printf(esc_attr__('To prevent any conflicts with our Indexing feature, please disable it.', 'wp-seopress')); ?></p>

                <a class="btn btnPrimary" href="<?php echo esc_url(admin_url('plugins.php')); ?>"><?php esc_attr_e('Fix this!', 'wp-seopress'); ?></a>
            </div>
    <?php
        }
    }
}

function seopress_print_section_instant_indexing_settings()
{ ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Settings', 'wp-seopress'); ?>
        </h2>
    </div>
    <p>
        <?php esc_attr_e('Edit your Instant Indexing settings for Google and Bing.', 'wp-seopress'); ?>
    </p>

<?php
}
