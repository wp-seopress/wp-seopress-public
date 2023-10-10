<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_instant_indexing_google_engine_callback()
{
    $options = get_option('seopress_instant_indexing_option_name');

    $search_engines = [
        'google' => 'Google',
        'bing'=> 'Bing / Yandex'
    ];

    if (!empty($search_engines)) {
        foreach ($search_engines as $key => $value) {
            $check = isset($options['engines'][$key]);
            ?>
            <div class="seopress_wrap_single_cpt">
                <label
                    for="seopress_instant_indexing_engines_<?php echo $key; ?>">
                    <input
                        id="seopress_instant_indexing_engines_<?php echo $key; ?>"
                        name="seopress_instant_indexing_option_name[engines][<?php echo $key; ?>]"
                        type="checkbox" <?php if ('1' == $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>
                    <?php echo $value; ?>
                </label>
            </div>
        <?php
            if (isset($options['engines'][$key])) {
                esc_attr($options['engines'][$key]);
            }
        }
    }
}

function seopress_instant_indexing_google_action_callback() {
    $options = get_option('seopress_instant_indexing_option_name');

    $actions = [
        'URL_UPDATED' => __('Update URLs', 'wp-seopress'),
        'URL_DELETED' => esc_attr__('Remove URLs (URL must return a 404 or 410 status code or the page contains <meta name="robots" content="noindex" /> meta tag)', 'wp-seopress'),
    ];

    foreach ($actions as $key => $value) { ?>
<div class="seopress_wrap_single_cpt">

    <?php if (isset($options['seopress_instant_indexing_google_action'])) {
        $check = $options['seopress_instant_indexing_google_action'];
    } else {
        $check = 'URL_UPDATED';
    } ?>

    <label
        for="seopress_instant_indexing_google_action_include_<?php echo $key; ?>">
        <input
            id="seopress_instant_indexing_google_action_include_<?php echo $key; ?>"
            name="seopress_instant_indexing_option_name[seopress_instant_indexing_google_action]" type="radio" <?php if ($key == $check) { ?>
        checked="yes"
        <?php } ?>
        value="<?php echo $key; ?>"/>

        <?php echo $value; ?>
    </label>

    <?php if (isset($options['seopress_instant_indexing_google_action'])) {
        esc_attr($options['seopress_instant_indexing_google_action']);
    } ?>
</div>
<?php }
}

function seopress_instant_indexing_manual_batch_callback() {
    require_once WP_PLUGIN_DIR . '/wp-seopress/vendor/autoload.php';
    $options    = get_option('seopress_instant_indexing_option_name');
    $log        = get_option('seopress_instant_indexing_log_option_name');
    $check      = isset($options['seopress_instant_indexing_manual_batch']) ? esc_attr($options['seopress_instant_indexing_manual_batch']) : null;

    //URLs
    $urls       = isset($log['log']['urls']) ? $log['log']['urls'] : null;
    $date       = isset($log['log']['date']) ? $log['log']['date'] : null;

    //General errors
    $error       = isset($log['error']) ? $log['error'] : null;

    //Bing
    $bing_response       = isset($log['bing']['response']) ? $log['bing']['response'] : null;

    //Google
    $google_response     = isset($log['google']['response']) ? $log['google']['response'] : null;

    printf(
'<textarea id="seopress_instant_indexing_manual_batch" name="seopress_instant_indexing_option_name[seopress_instant_indexing_manual_batch]" rows="20" placeholder="' . esc_html__('Enter one URL per line to submit them to search engines (max 100 URLs)', 'wp-seopress') . '" aria-label="' . __('Enter one URL per line to submit them to search engines (max 100 URLs)', 'wp-seopress') . '">%s</textarea>',
esc_html($check));
?>

<div class="wrap-sp-progress">
    <div class="sp-progress" style="margin:0">
        <div id="seopress_instant_indexing_url_progress" class="sp-progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">1%</div>
    </div>
    <div class="wrap-seopress-counters">
        <div id="seopress_instant_indexing_url_count"></div>
        <strong><?php _e(' / 100 URLs', 'wp-seopress'); ?></strong>
    </div>
</div>

<p>
    <br>
    <button type="button" class="seopress-instant-indexing-batch btn btnPrimary">
        <?php _e('Submit URLs to Google & Bing', 'wp-seopress'); ?>
    </button>

    <span class="spinner"></span>
</p>

<h3><?php _e('Latest indexing request','wp-seopress'); ?></h3>
<p><em><?php echo $date; ?></em></p>

<?php
if (!empty($error)) { ?>
    <span class="indexing-log indexing-failed"></span><?php echo $error; ?>
<?php }
if (!empty($bing_response['response'])) {
    switch ($bing_response['response']['code']) {
        case 200:
            $msg = __('URLs submitted successfully', 'wp-seopress');
            break;
        case 202:
            $msg = __('URL received. IndexNow key validation pending.', 'wp-seopress');
            break;
        case 400:
            $msg = __('Bad request: Invalid format', 'wp-seopress');
            break;
        case 403:
            $msg = __('Forbidden: In case of key not valid (e.g. key not found, file found but key not in the file)', 'wp-seopress');
            break;
        case 422:
            $msg = __('Unprocessable Entity: In case of URLs don’t belong to the host or the key is not matching the schema in the protocol', 'wp-seopress');
            break;
        case 429:
            $msg = __('Too Many Requests (potential Spam)', 'wp-seopress');
            break;
        default:
            $msg = __('Something went wrong', 'wp-seopress');
    } ?>
    <div class="wrap-bing-response">
        <h4><?php _e('Bing Response','wp-seopress'); ?></h4>

        <?php if ($bing_response['response']['code'] == 200 || $bing_response['response']['code'] == 202) { ?>
            <span class="indexing-log indexing-done"></span>
        <?php } else { ?>
            <span class="indexing-log indexing-failed"></span>
        <?php } ?>
        <code><?php echo esc_html($msg); ?></code>
    </div>
<?php }

    if (is_array($google_response) && !empty($google_response)) { ?>
        <div class="wrap-google-response">
            <h4><?php _e('Google Response','wp-seopress'); ?></h4>

            <?php
            $google_exception = $google_response[array_key_first($google_response)];
            if ( is_a( $google_exception, 'Google\Service\Exception' ) ) {
                $error = json_decode($google_exception->getMessage(), true);
                echo '<span class="indexing-log indexing-failed"></span><code>' . esc_html($error['error']['code']) . ' - ' . esc_html($error['error']['message']) . '</code>';
            } elseif (!empty($google_response['error'])) {
                echo '<span class="indexing-log indexing-failed"></span><code>' . esc_html($google_response['error']['code']) . ' - ' . esc_html($google_response['error']['message']) . '</code>';
            } else { ?>
                <p><span class="indexing-log indexing-done"></span><code><?php _e('URLs submitted successfully', 'wp-seopress'); ?></code></p>
                <ul>
                    <?php foreach($google_response as $result) {
                        if ($result) {
                            if (!empty($result->urlNotificationMetadata->latestUpdate["url"]) || !empty($result->urlNotificationMetadata->latestUpdate["type"])) {
                                echo '<li>';
                            }
                            if (!empty($result->urlNotificationMetadata->latestUpdate["url"])) {
                                echo $result->urlNotificationMetadata->latestUpdate["url"];
                            }
                            if (!empty($result->urlNotificationMetadata->latestUpdate["type"])) {
                                echo ' - ';
                                echo '<code>' . $result->urlNotificationMetadata->latestUpdate["type"] . '</code>';
                            }
                            if (!empty($result->urlNotificationMetadata->latestUpdate["url"]) || !empty($result->urlNotificationMetadata->latestUpdate["type"])) {
                                echo '</li>';
                            }
                            if (!empty($result->urlNotificationMetadata->latestRemove["url"]) || !empty($result->urlNotificationMetadata->latestRemove["type"])) {
                                echo '<li>';
                            }
                            if (!empty($result->urlNotificationMetadata->latestRemove["url"])) {
                                echo $result->urlNotificationMetadata->latestRemove["url"];
                            }
                            if (!empty($result->urlNotificationMetadata->latestRemove["type"])) {
                                echo ' - ';
                                echo '<code>' . $result->urlNotificationMetadata->latestRemove["type"] . '</code>';
                            }
                            if (!empty($result->urlNotificationMetadata->latestRemove["url"]) || !empty($result->urlNotificationMetadata->latestRemove["type"])) {
                                echo '</li>';
                            }
                        }
                    } ?>
                </ul>
            <?php } ?>
        </div>
    <?php }
    ?>

    <h4><?php _e('Latest URLs submitted','wp-seopress'); ?></h4>
    <?php if (!empty($urls[0])) { ?>
        <ul>
        <?php foreach($urls as $url) { ?>
            <li>
                <?php echo esc_url($url); ?>
            </li>
        <?php } ?>
        </ul>
    <?php } else {
        _e('None', 'wp-seopress');
    }
}

function seopress_instant_indexing_google_api_key_callback() {
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
    $options = get_option('seopress_instant_indexing_option_name');
    $check   = isset($options['seopress_instant_indexing_google_api_key']) ? esc_attr($options['seopress_instant_indexing_google_api_key']) : null;

    printf(
'<textarea id="seopress_instant_indexing_google_api_key" name="seopress_instant_indexing_option_name[seopress_instant_indexing_google_api_key]" rows="12" placeholder="' . esc_html__('Paste your Google JSON key file here', 'wp-seopress') . '" aria-label="' . __('Paste your Google JSON key file here', 'wp-seopress') . '">%s</textarea>',
esc_html($check)); ?>

<p class="seopress-help description"><?php printf(__('To use the <span class="dashicons dashicons-external"></span><a href="%1$s" target="_blank">Google Indexing API</a> and generate your JSON key file, please <span class="dashicons dashicons-external"></span><a href="%2$s" target="_blank">follow our guide.'), esc_url($docs['indexing_api']['api']), esc_url($docs['indexing_api']['google'])); ?></p>

<?php
}

function seopress_instant_indexing_bing_api_key_callback() {
    $options = get_option('seopress_instant_indexing_option_name');
    $check   = isset($options['seopress_instant_indexing_bing_api_key']) ? esc_attr($options['seopress_instant_indexing_bing_api_key']) : null; ?>

    <input type="text" id="seopress_instant_indexing_bing_api_key" name="seopress_instant_indexing_option_name[seopress_instant_indexing_bing_api_key]"
    placeholder="<?php esc_html_e('Enter your Bing Instant Indexing API', 'wp-seopress'); ?>"
    aria-label="<?php _e('Enter your Bing Instant Indexing API', 'wp-seopress'); ?>"
    value="<?php echo $check; ?>" />

    <button type="button" class="seopress-instant-indexing-refresh-api-key btn btnSecondary"><?php _e('Generate key','wp-seopress'); ?></button>

    <p class="description"><?php _e('The Bing Indexing API key is automatically generated. Click Generate key if you want to recreate it, or if it\'s missing.') ?></p>
    <p class="description"><?php _e('A key should look like this: ', 'wp-seopress'); ?><code>ZjA2NWI3ZWM3MmNhNDRkODliYmY0YjljMzg5YTk2NGE=</code></p>
<?php
}

function seopress_instant_indexing_automate_submission_callback() {
    $options = get_option('seopress_instant_indexing_option_name');

    $check = isset($options['seopress_instant_indexing_automate_submission']); ?>

    <label for="seopress_instant_indexing_automate_submission">
        <input id="seopress_instant_indexing_automate_submission" name="seopress_instant_indexing_option_name[seopress_instant_indexing_automate_submission]" type="checkbox"
        <?php if ('1' == $check) {
            echo 'checked="yes"';
        } ?>
        value="1"/>
        <?php _e('Enable automatic URL submission for IndexNow API', 'wp-seopress'); ?>
    </label>

    <p class="description">
        <?php _e('Notify search engines using IndexNow protocol (currently Bing and Yandex) whenever a post is created, updated or deleted.', 'wp-seopress'); ?>
    </p>

    <?php if (isset($options['seopress_instant_indexing_automate_submission'])) {
        esc_attr($options['seopress_instant_indexing_automate_submission']);
    }
}
